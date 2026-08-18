<?php

declare(strict_types=1);

namespace Tests\Feature;

use MarkupCarve\LaravelCarve\Service\CarveManager;
use Tests\TestCase;

class EditorPreviewTest extends TestCase
{
    public function test_editor_preview_demonstrates_symbols_and_source_lines(): void
    {
        $response = $this->get('/editor-preview');

        $response->assertOk();
        $response->assertSee('aria-label="spark"', escape: false);
        $response->assertSee('data-source-line="1"', escape: false);
    }

    public function test_safe_mode_rejects_a_dangerous_later_srcset_candidate(): void
    {
        $source = '![unsafe](safe.png){srcset="safe.png 1x, javascript:alert(1) 2x"}';
        $html = $this->app->make(CarveManager::class)->toHtml($source, 'user_content');

        self::assertStringContainsString('srcset=""', $html);
        self::assertStringNotContainsString('javascript:', $html);
    }

    public function test_shared_cache_keeps_converter_profiles_isolated(): void
    {
        $source = "```=html\n<strong>trusted</strong>\n```";
        $manager = $this->app->make(CarveManager::class);

        $safe = $manager->toHtml($source, 'user_content');
        $trusted = $manager->toHtml($source, 'trusted');

        self::assertNotSame($safe, $trusted);
        self::assertStringContainsString('<strong>trusted</strong>', $trusted);
        self::assertStringNotContainsString('<strong>trusted</strong>', $safe);
    }
}
