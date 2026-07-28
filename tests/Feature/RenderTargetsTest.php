<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class RenderTargetsTest extends TestCase
{
    public function test_render_targets_page_renders_successfully(): void
    {
        $response = $this->get('/render-targets');

        $response->assertStatus(200);
        $response->assertSee('Render Targets');
    }

    public function test_all_four_render_targets_are_present(): void
    {
        $response = $this->get('/render-targets');

        $response->assertSee('toHtml()');
        $response->assertSee('toText()');
        $response->assertSee('toMarkdown()');
        $response->assertSee('toAnsi()');
    }

    public function test_markdown_output_uses_markdown_emphasis(): void
    {
        $response = $this->get('/render-targets');

        // Carve /renders/ (emphasis) becomes Markdown *renders* and *four* (strong)
        // becomes **four** in the toMarkdown() output.
        $response->assertSee('*renders*');
        $response->assertSee('**four**');
    }

    public function test_ansi_output_contains_escape_sequences(): void
    {
        $response = $this->get('/render-targets');

        // The ANSI renderer emits SGR escape sequences (ESC[ ... m).
        $response->assertSee("\033[", escape: false);
    }
}
