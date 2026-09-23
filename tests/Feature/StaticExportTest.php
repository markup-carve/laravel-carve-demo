<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class StaticExportTest extends TestCase
{
    public function test_static_preview_declares_its_limits(): void
    {
        config(['demo-pages.static_export' => true]);

        $this->get('/')->assertOk()->assertSee('Static preview');
        $this->get('/form')
            ->assertOk()
            ->assertSee('This form works only in a local checkout.')
            ->assertSee('fieldset disabled', false);
    }

    public function test_export_writes_pages_with_deployable_navigation(): void
    {
        $directory = 'storage/framework/static-export-test';
        File::deleteDirectory(base_path($directory));
        config([
            'app.url' => 'https://markup-carve.github.io/laravel-carve-demo',
            'demo-pages.static_export' => true,
            'session.driver' => 'array',
        ]);

        try {
            $this->artisan('demo:export', ['output' => $directory])->assertSuccessful();
            $html = (string) file_get_contents(base_path($directory.'/index.html'));
            $this->assertStringContainsString('https://markup-carve.github.io/laravel-carve-demo/facade', $html);
            $this->assertStringNotContainsString('://localhost', $html);
        } finally {
            File::deleteDirectory(base_path($directory));
        }
    }
}
