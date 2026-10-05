<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CarvePost;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use MarkupCarve\LaravelCarve\Facades\Carve;
use MarkupCarve\LaravelCarve\RenderedCarve;
use Tests\TestCase;

class AuthoringTest extends TestCase
{
    public function test_authoring_page_renders_metadata_component_imports_and_document_view(): void
    {
        $response = $this->get('/authoring');
        $response->assertOk()
            ->assertSee('Authoring in Laravel')
            ->assertSee('<strong>Release notes</strong>', false)
            ->assertSee('A static Carve view')
            ->assertSee('<strong>bold</strong>', false)
            ->assertSee('carve:render');

        $document = $response->viewData('document');
        $this->assertInstanceOf(RenderedCarve::class, $document);
        $this->assertSame('Release notes', $document->meta('title'));
        $this->assertSame('Demo editor', $document->meta('author'));
        $this->assertSame(['Release notes', 'Review checklist'], array_column($document->toc(), 'text'));
        $this->assertSame($document->source, $response->viewData('post')->getAttributes()['body']);
        $this->assertStringContainsString($document->html, $response->getContent());
        preg_match_all('/\sid="([^"]+)"/', $response->getContent(), $ids);
        $this->assertSame($ids[1], array_values(array_unique($ids[1])), 'Rendered previews must not duplicate heading IDs');
        foreach ($response->viewData('imports') as $import) {
            $this->assertStringContainsString('<strong>bold</strong>', $import['rendered']->html);
        }
    }

    public function test_cast_preserves_source_when_a_rendered_value_is_assigned(): void
    {
        $source = 'An /original/ source.';
        $post = new CarvePost(['body' => Carve::render($source)]);

        $this->assertSame($source, $post->getAttributes()['body']);
        $this->assertInstanceOf(RenderedCarve::class, $post->body);
        $this->assertStringContainsString('<em>original</em>', $post->body->html);
        $this->assertFalse($post->exists);
    }

    public function test_comment_examples_show_preset_character_limit_and_lint_rejections(): void
    {
        $response = $this->get('/authoring')->assertOk();
        $samples = collect($response->viewData('validation'))->keyBy('label');

        $this->assertTrue($samples['Comment']['accepted']);
        $this->assertTrue($samples['Character boundary']['accepted']);
        $this->assertSame(160, strlen($samples['Character boundary']['source']));
        foreach (['Heading', 'Character limit', 'Markdown spelling'] as $label) {
            $this->assertFalse($samples[$label]['accepted']);
            $this->assertNotEmpty($samples[$label]['errors']);
        }
        $this->assertStringContainsString('not allowed by the comment preset', implode(' ', $samples['Heading']['errors']));
        $this->assertStringContainsString('must not exceed 80 characters', implode(' ', $samples['Character limit']['errors']));
        $this->assertStringContainsString('renders as literal asterisks', implode(' ', $samples['Markdown spelling']['errors']));
        $this->assertSame(81, mb_strlen($samples['Character limit']['source']));
        $this->assertStringNotContainsString('<h1', $response->viewData('comment')->html);
        $this->assertStringContainsString('# Reader heading', $response->viewData('comment')->html);
        $this->assertStringContainsString('<em>clear</em>', $response->viewData('comment')->html);
        $this->assertNotEmpty($response->viewData('lint_findings'));
    }

    public function test_document_commands_render_lint_and_convert_files(): void
    {
        $document = resource_path('carve/authoring-guide.crv');
        $this->assertSame(0, Artisan::call('carve:render', ['path' => $document, '--format' => 'text']));
        $this->assertStringContainsString('A static Carve view', Artisan::output());
        $this->assertSame(0, Artisan::call('carve:render', ['path' => $document, '--format' => 'html']));
        $this->assertStringContainsString('<h2', Artisan::output());
        $this->assertSame(0, Artisan::call('carve:lint', ['paths' => [$document]]));

        $directory = storage_path('framework/authoring-command-test');
        File::ensureDirectoryExists($directory);
        try {
            File::put($directory.'/bad.crv', '**Markdown bold**');
            $this->assertSame(1, Artisan::call('carve:lint', ['paths' => [$directory.'/bad.crv']]));
            $this->assertStringContainsString('literal asterisks', Artisan::output());
            File::put($directory.'/sample.md', '**Imported bold**');
            $this->assertSame(0, Artisan::call('carve:convert', [
                'path' => $directory.'/sample.md',
                '--output' => $directory.'/sample.crv',
            ]));
            $this->assertStringContainsString('<strong>Imported bold</strong>', Carve::toHtml(File::get($directory.'/sample.crv')));
        } finally {
            File::deleteDirectory($directory);
        }
    }
}
