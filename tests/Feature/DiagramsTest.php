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
        $response->assertSee('Diagrams &amp; Media', escape: false);
    }

    public function test_plantuml_fence_renders_hydration_element(): void
    {
        $response = $this->get('/diagrams');

        // The plantuml shorthand emits <pre class="plantuml"> for client hydration.
        $response->assertSee('<pre class="plantuml">', escape: false);
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
        // Both new 0.1.3 shorthands appear in ExtensionFactory::types().
        $response->assertSee('plantuml');
        $response->assertSee('img_fence');
    }
}
