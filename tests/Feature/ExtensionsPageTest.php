<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ExtensionsPageTest extends TestCase
{
    public function test_wiki_links_toc_and_frontmatter_use_the_carve_02_forms(): void
    {
        $response = $this->get('/extensions')->assertOk();

        $this->assertStringContainsString(
            '<a href="/extensions?wiki=api-reference" class="wiki-link" data-wikilink="API Reference">the API docs</a>',
            $response->viewData('wikilinks_html'),
        );
        $this->assertSame(1, substr_count($response->viewData('toc_html'), '<nav class="toc"'));
        $this->assertSame('My Document', $response->viewData('frontmatter_meta')['title']);
        $this->assertStringContainsString('<p class="admonition-title" id="adm-1">Custom Title</p>', $response->viewData('admonition_html'));
        $this->assertStringContainsString('<a class="mention" href="https://github.com/teamlead">@teamlead</a>', $response->viewData('mentions_html'));
    }
}
