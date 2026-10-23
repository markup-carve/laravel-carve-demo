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

    public function test_layout_loads_the_carve_csv_and_tsv_highlighters(): void
    {
        $response = $this->get('/syntax');

        $response->assertSeeInOrder([
            'highlight.min.js',
            'carve-grammars@v0.1.19/highlightjs/carve.js',
            'carve-grammars@v0.1.19/highlightjs/delimited.js',
            'hljs.highlightElement',
        ], escape: false);
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

        $response->assertSee(': A lightweight markup language');
        $response->assertDontSee(':  A lightweight markup language');
        $response->assertSee('<dl>', escape: false);
        $response->assertSee('<dt>Carve</dt>', escape: false);
    }

    public function test_preferred_ordered_list_marker_renders_as_an_ordered_list(): void
    {
        $response = $this->get('/syntax');

        $response->assertSee('. First item');
        $response->assertSee("<ol>\n  <li>First item</li>", escape: false);
    }

    public function test_canonical_table_headers_render_without_a_separator_row(): void
    {
        $response = $this->get('/syntax');

        $response->assertSee('|= Lang |= Status |');
        $this->assertDoesNotMatchRegularExpression(
            '/^\|(?:\s*:?-+:?\s*\|)+$/m',
            $response->getContent(),
        );
        $response->assertSee('<th scope="col">Lang</th>', escape: false);
        $response->assertSee('<th scope="col">Status</th>', escape: false);
    }

    public function test_extension_examples_use_canonical_table_headers(): void
    {
        $response = $this->get('/extensions');

        $response->assertSee('|= Name |= Role |');
        $this->assertDoesNotMatchRegularExpression(
            '/^\|(?:\s*:?-+:?\s*\|)+$/m',
            $response->getContent(),
        );
    }

    public function test_footnotes_render_endnotes_section(): void
    {
        $response = $this->get('/syntax');

        $response->assertSee('role="doc-endnotes"', escape: false);
    }

    public function test_escaped_block_marker_stays_literal(): void
    {
        $response = $this->get('/syntax');

        // The escaped "### This stays literal" must not become an <h3>, while
        // the unescaped heading must.
        $response->assertDontSee('<h3>This stays literal', escape: false);
        $response->assertSee('\#\#\# This stays literal');
        $response->assertSee('This is a real heading');
    }

    public function test_continuation_marker_attaches_the_quote_to_the_item(): void
    {
        $response = $this->get('/syntax');

        $response->assertSeeInOrder(['<li>Install the package', '<blockquote><p>Run composer from the project root.</p></blockquote>', '</li>'], escape: false);
    }

    public function test_csv_and_tsv_fences_render_as_tables(): void
    {
        $response = $this->get('/syntax');

        $response->assertSee('<td>Pear, green</td>', escape: false);
        $response->assertSee('<th scope="col">Lang</th>', escape: false);
    }
}
