<?php

declare(strict_types=1);

namespace Tests\Feature;

use MarkupCarve\LaravelCarve\Service\CarveManager;
use Tests\TestCase;

class DiagramsTest extends TestCase
{
    public function test_diagrams_page_renders_successfully(): void
    {
        $response = $this->get('/diagrams');

        $response->assertStatus(200);
        $response->assertSee('Live Diagram Gallery');
    }

    /**
     * Every FencedRenderExtension preset must emit its client-hydration markup:
     * a <pre class="TYPE"> for text-mode presets and a <div class="TYPE"> with a
     * JSON <script> for the json-mode ones.
     */
    public function test_all_eight_diagram_types_emit_hydration_markup(): void
    {
        $response = $this->get('/diagrams');

        // Text-mode presets: <pre class="TYPE">.
        foreach (['mermaid', 'd2', 'graphviz', 'plantuml', 'wavedrom', 'abc'] as $type) {
            $response->assertSee('<pre class="' . $type . '">', escape: false);
        }

        // JSON-mode presets: <div class="TYPE"><script type="application/json">.
        foreach (['vega-lite', 'chart'] as $type) {
            $response->assertSee(
                '<div class="' . $type . '"><script type="application/json">',
                escape: false,
            );
        }
    }

    public function test_img_fence_renders_sandboxed_svg_image(): void
    {
        $response = $this->get('/diagrams');

        // The img_fence shorthand emits a sandboxed data:image/svg+xml <img>.
        $response->assertSee('<img src="data:image/svg+xml', escape: false);
        // The alt fallback / explicit alt is carried through.
        $response->assertSee('alt="Carve logo mark"', escape: false);
    }

    public function test_img_fence_strips_active_svg_content(): void
    {
        $source = <<<'CARVE'
        {alt="probe"}
        ``` img
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><rect width="10" height="10"/><script>alert('boom')</script></svg>
        ```
        CARVE;

        $html = app(CarveManager::class)->toHtml($source, 'with_img_fence');

        // Rendered as a sandboxed data URI, with the <script> stripped by the
        // sanitizer so nothing active survives into the emitted image.
        $this->assertStringContainsString('data:image/svg+xml', $html);
        $this->assertStringNotContainsString('script', $html);
        $this->assertStringNotContainsString('boom', $html);
    }

    public function test_available_shorthand_types_are_listed(): void
    {
        $response = $this->get('/diagrams');

        $response->assertSee('Available Shorthand Types');
        // Both 0.1.3 shorthands appear in ExtensionFactory::types().
        $response->assertSee('plantuml');
        $response->assertSee('img_fence');
    }
}
