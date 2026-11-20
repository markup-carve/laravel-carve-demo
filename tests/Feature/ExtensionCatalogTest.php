<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Carve\ExtensionCatalog;
use MarkupCarve\LaravelCarve\Service\CarveConverter;
use MarkupCarve\LaravelCarve\Service\ExtensionFactory;
use Tests\TestCase;

class ExtensionCatalogTest extends TestCase
{
    public function test_catalog_has_a_sample_for_every_extension_name(): void
    {
        $this->assertSame(ExtensionFactory::types(), array_keys(ExtensionCatalog::entries()));
    }

    public function test_every_sample_renders_differently_with_its_extension(): void
    {
        $factory = new ExtensionFactory();
        $core = new CarveConverter();
        foreach (ExtensionCatalog::entries() as $name => $entry) {
            $rendered = (new CarveConverter(options: ['extensions' => $factory->create($entry['config'])]))->render($entry['source']);
            $this->assertNotSame($core->toHtml($entry['source']), $rendered->html, "{$name} sample does not use its extension");
            $this->assertSame([], $rendered->warnings, "{$name} sample renders with warnings");
        }
    }

    public function test_catalog_page_renders_every_entry(): void
    {
        $response = $this->get('/extension-catalog')->assertOk();

        foreach (ExtensionFactory::types() as $name) {
            $response->assertSee('id="ext-'.$name.'"', false);
        }
        $response->assertSee('<a href="/extension-catalog?wiki=home" class="wikilink" data-wikilink="Home">Home</a>', false);
        $response->assertSee('<details class="spoiler">', false);
    }
}
