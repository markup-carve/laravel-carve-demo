<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class SyntaxTest extends TestCase
{
    public function test_syntax_page_renders_successfully(): void
    {
        $response = $this->get('/syntax');

        $response->assertStatus(200);
        $response->assertSee('Carve Syntax');
    }

    public function test_inline_literal_renders_delimiters_as_literal_text(): void
    {
        $response = $this->get('/syntax');

        // !`*strong*` renders the raw delimiters as literal text, with no
        // <strong> and no <code> styling around them.
        $response->assertSee('*strong*');
        $response->assertDontSee('<strong>strong</strong>', escape: false);
    }

    public function test_definition_list_renders_as_dl(): void
    {
        $response = $this->get('/syntax');

        $response->assertSee('<dl>', escape: false);
        $response->assertSee('<dt>Carve</dt>', escape: false);
    }

    public function test_footnotes_render_endnotes_section(): void
    {
        $response = $this->get('/syntax');

        $response->assertSee('role="doc-endnotes"', escape: false);
    }

    public function test_strict_column0_keeps_indented_heading_literal(): void
    {
        $response = $this->get('/syntax');

        // The indented "### This stays literal" must NOT become an <h3>, while
        // the flush-left "# This is a real heading" must.
        $response->assertDontSee('<h3>This stays literal', escape: false);
        $response->assertSee('This is a real heading');
    }
}
