<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Carve\OutlineWriter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\View\View;
use MarkupCarve\Carve\Node;
use MarkupCarve\LaravelCarve\Facades\Carve;
use MarkupCarve\LaravelCarve\Service\CarveConverter;
use MarkupCarve\LaravelCarve\Service\RenderPolicy;
use Throwable;

class OptionsController extends Controller
{
    private const SOURCE = <<<'CARVE'
    = Über uns

    A [mail](mailto:team@example.com) and a [web](https://example.com) link, "quoted" -- with a dash.[^1]

    [^1]: Written in Carve.
    CARVE;

    public function __invoke(): View
    {
        $settings = [
            'url_policy' => [
                'title' => 'URL policy',
                'blurb' => 'An allow list keeps only the named schemes; the mailto: link loses its target.',
                'config' => ['url_policy' => ['allow' => ['https']]],
            ],
            'heading_ids' => [
                'title' => 'Heading ids',
                'blurb' => 'ascii-lower folds the generated id to lowercase ASCII.',
                'config' => ['heading_ids' => 'ascii-lower'],
            ],
            'sections' => [
                'title' => 'Sections',
                'blurb' => 'With sections off, headings are not wrapped in section elements and carry the id themselves.',
                'config' => ['sections' => false],
            ],
            'smart_typography' => [
                'title' => 'Smart typography',
                'blurb' => 'Off keeps straight quotes and double hyphens as typed.',
                'config' => ['smart_typography' => false],
            ],
            'labels' => [
                'title' => 'Labels',
                'blurb' => 'Labels translate the text Carve generates, here the endnotes landmark and back-link.',
                'config' => ['labels' => ['endnotes' => 'Anmerkungen', 'footnoteBacklink' => 'Zurück zum Text']],
            ],
            'strict_losses' => [
                'title' => 'Strict losses',
                'blurb' => 'With the comment profile the heading is a loss; strict_losses turns that into an error.',
                'config' => ['profile' => 'comment', 'strict_losses' => true],
            ],
        ];
        foreach ($settings as &$setting) {
            try {
                $setting['html'] = (new CarveConverter(options: RenderPolicy::options($setting['config'])))->toHtml(self::SOURCE);
                $setting['error'] = null;
            } catch (Throwable $exception) {
                $setting['html'] = null;
                $setting['error'] = get_class($exception).': '.$exception->getMessage();
            }
        }
        unset($setting);

        $messy = "# Notes\n``` php\necho 1;\n```\n\n|= Name |= Qty |\n|Apple|3|\n";
        $guide = resource_path('carve/authoring-guide.crv');
        $outline = new CarveConverter();
        $outline->registerWriter(new OutlineWriter());
        $fmtExit = Artisan::call('carve:fmt', ['paths' => [$guide], '--check' => true]);

        return view('demo.options', [
            'profiles' => Carve::profiles(),
            'default_profile' => Carve::getDefaultProfile(),
            'source' => self::SOURCE,
            'default_html' => Carve::toHtml(self::SOURCE),
            'settings' => $settings,
            'str_html' => Str::carve('*Str* macro'),
            'str_text' => (string) Str::of('/Stringable/ macro')->carveText(),
            'tree' => self::outlineTree(Carve::parse("= Title\n\nA *strong* word.\n")),
            'messy_source' => $messy,
            'formatted_source' => Carve::format($messy),
            'fmt_check_exit' => $fmtExit,
            'fmt_check_output' => trim(Artisan::output()),
            'outline' => $outline->renderFileAs($guide, 'outline'),
        ]);
    }

    /**
     * @return list<string>
     */
    private static function outlineTree(Node $node, int $depth = 0): array
    {
        $label = $node->type;
        foreach (['level', 'value'] as $field) {
            if (isset($node->f[$field]) && is_scalar($node->f[$field])) {
                $label .= sprintf(' %s=%s', $field, json_encode($node->f[$field], JSON_UNESCAPED_UNICODE));
            }
        }
        $lines = [str_repeat('  ', $depth).$label];
        foreach ($node->children ?? [] as $child) {
            array_push($lines, ...self::outlineTree($child, $depth + 1));
        }

        return $lines;
    }
}
