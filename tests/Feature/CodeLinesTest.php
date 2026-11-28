<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class CodeLinesTest extends TestCase
{
    public function test_line_options_reach_the_pre_element(): void
    {
        $response = $this->get('/code-lines');

        $response->assertStatus(200);
        $response->assertSee('<pre title="src/Invoice.php" class="line-numbers" data-line-start="40" data-highlight="3,6-7">', false);
        $response->assertSee('<pre data-highlight="2"><code class="language-js">', false);
        $response->assertSee('enhanceCodeBlocks(document', false);
    }

    public function test_catalog_hydrates_diagram_fences(): void
    {
        $this->get('/extension-catalog')
            ->assertStatus(200)
            ->assertSee("root.querySelectorAll('div.chart')", false);
    }
}
