<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Carve\ExtensionCatalog;
use Illuminate\View\View;
use MarkupCarve\LaravelCarve\Service\CarveConverter;
use MarkupCarve\LaravelCarve\Service\ExtensionFactory;

class ExtensionCatalogController extends Controller
{
    public function __invoke(ExtensionFactory $factory): View
    {
        $entries = ExtensionCatalog::entries();
        foreach ($entries as &$entry) {
            $converter = new CarveConverter(options: ['extensions' => $factory->create($entry['config'])]);
            $entry['html'] = $converter->toHtml($entry['source']);
        }
        unset($entry);

        return view('demo.extension_catalog', [
            'entries' => $entries,
            'types' => ExtensionFactory::types(),
        ]);
    }
}
