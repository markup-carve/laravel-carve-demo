<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class OptionsTest extends TestCase
{
    public function test_options_page_lists_profiles_and_renders_each_setting(): void
    {
        $response = $this->get('/options')->assertOk();

        $this->assertContains('config_snippets', $response->viewData('profiles'));
        $this->assertSame('default', $response->viewData('default_profile'));
        $settings = $response->viewData('settings');
        $this->assertStringContainsString('<a href="">mail</a>', $settings['url_policy']['html']);
        $this->assertStringContainsString('<section id="uber-uns">', $settings['heading_ids']['html']);
        $this->assertStringContainsString('<h1 id="Über-uns">', $settings['sections']['html']);
        $this->assertStringContainsString('"quoted" -- with a dash', $settings['smart_typography']['html']);
        $this->assertStringContainsString('aria-label="Anmerkungen"', $settings['labels']['html']);
        $this->assertStringContainsString('strictLosses', $settings['strict_losses']['error']);
    }

    public function test_options_page_shows_macros_tree_formatter_and_custom_writer(): void
    {
        $response = $this->get('/options')->assertOk();

        $this->assertSame("<p><strong>Str</strong> macro</p>", $response->viewData('str_html'));
        $this->assertSame("Stringable macro\n", $response->viewData('str_text'));
        $this->assertContains('  heading level=1', $response->viewData('tree'));
        $this->assertStringStartsWith("= Notes\n", $response->viewData('formatted_source'));
        $this->assertStringContainsString("```php\necho 1;\n```\n\n|= Name |= Qty |\n| Apple | 3 |", $response->viewData('formatted_source'));
        $this->assertSame(0, $response->viewData('fmt_check_exit'));
        $this->assertSame("  - A static Carve view\n", $response->viewData('outline'));
    }
}
