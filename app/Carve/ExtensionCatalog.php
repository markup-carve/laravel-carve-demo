<?php

declare(strict_types=1);

namespace App\Carve;

/**
 * One sample per extension name that laravel-carve's ExtensionFactory accepts.
 * The catalog test fails when the package adds a name without a sample here.
 */
final class ExtensionCatalog
{
    /**
     * @return array<string, array{config: string|array<string, mixed>, blurb: string, source: string}>
     */
    public static function entries(): array
    {
        return [
            'abc' => [
                'config' => 'abc',
                'blurb' => 'ABC music notation fence for a client-side renderer such as abcjs.',
                'source' => <<<'CARVE'
                ```abc
                X:1
                K:C
                C D E F | G A B c |]
                ```
                CARVE,
            ],
            'autolink' => [
                'config' => 'autolink',
                'blurb' => 'Turns bare URLs and email addresses in text into links.',
                'source' => 'Read https://github.com/markup-carve or write to hello@example.com.',
            ],
            'chart' => [
                'config' => 'chart',
                'blurb' => 'Chart.js configuration fence, emitted as JSON for a client-side renderer.',
                'source' => <<<'CARVE'
                ```chart
                {"type": "bar", "data": {"labels": ["A", "B"], "datasets": [{"data": [3, 5]}]}}
                ```
                CARVE,
            ],
            'citations' => [
                'config' => 'citations',
                'blurb' => 'Resolves :cite[key] against a references directive and numbers the citations.',
                'source' => <<<'CARVE'
                Carve has one grammar :cite[spec]{locator="sec. 2"}.

                ::: references
                :: spec
                : The Carve 0.2 specification.
                :::
                CARVE,
            ],
            'code_callouts' => [
                'config' => 'code_callouts',
                'blurb' => 'Turns <1> markers at line ends into callouts that the list below the block explains.',
                'source' => <<<'CARVE'
                ```php
                $html = Carve::toHtml($source); <1>
                echo $html; <2>
                ```

                1. Render the source.
                2. Print the result.
                CARVE,
            ],
            'code_group' => [
                'config' => 'code_group',
                'blurb' => 'Shows the code blocks of a code-group div as tabs labeled by their bracket labels.',
                'source' => <<<'CARVE'
                ::: code-group
                ```bash [Composer]
                composer require markup-carve/laravel-carve
                ```

                ```bash [npm]
                npm install @markup-carve/carve-js
                ```
                :::
                CARVE,
            ],
            'color_swatch' => [
                'config' => 'color_swatch',
                'blurb' => 'Puts a color chip in front of a :color[...] value.',
                'source' => 'The brand color is :color[#ff2d20].',
            ],
            'd2' => [
                'config' => 'd2',
                'blurb' => 'D2 diagram fence for a client-side or server-side D2 renderer.',
                'source' => <<<'CARVE'
                ```d2
                Author -> Carve: markup
                ```
                CARVE,
            ],
            'default_attributes' => [
                'config' => ['type' => 'default_attributes', 'defaults' => ['table' => ['class' => 'table']]],
                'blurb' => 'Adds configured attributes to every node of a type, here a class on each table.',
                'source' => <<<'CARVE'
                |= Name |= Role |
                | Alice | Admin |
                CARVE,
            ],
            'details' => [
                'config' => 'details',
                'blurb' => 'Renders a details div as a native disclosure with its quoted title as the summary.',
                'source' => <<<'CARVE'
                ::: details "Why one grammar?"
                Every engine renders the same HTML.
                :::
                CARVE,
            ],
            'external_links' => [
                'config' => 'external_links',
                'blurb' => 'Adds target and rel to every http and https link.',
                'source' => 'Visit [GitHub](https://github.com/markup-carve).',
            ],
            'fenced_render' => [
                'config' => ['type' => 'fenced_render', 'language' => 'flow', 'css_class' => 'flowchart'],
                'blurb' => 'The generic fence renderer behind the diagram presets, here for a custom flow language.',
                'source' => <<<'CARVE'
                ```flow
                start -> render -> done
                ```
                CARVE,
            ],
            'glossary' => [
                'config' => 'glossary',
                'blurb' => 'Links each :term[...] to its entry in a glossary directive.',
                'source' => <<<'CARVE'
                A :term[profile] limits the markup.

                ::: glossary
                :: profile
                : A named set of allowed constructs.
                :::
                CARVE,
            ],
            'graphviz' => [
                'config' => 'graphviz',
                'blurb' => 'Graphviz DOT fence for a client-side renderer.',
                'source' => <<<'CARVE'
                ```graphviz
                digraph { Carve -> HTML }
                ```
                CARVE,
            ],
            'heading_numbers' => [
                'config' => 'heading_numbers',
                'blurb' => 'Numbers the headings hierarchically.',
                'source' => <<<'CARVE'
                = Overview

                == Install

                == Configure
                CARVE,
            ],
            'heading_permalinks' => [
                'config' => 'heading_permalinks',
                'blurb' => 'Appends a permalink anchor to each heading.',
                'source' => '= Install',
            ],
            'heading_reference' => [
                'config' => 'heading_reference',
                'blurb' => 'Marks an undefined reference link that resolves to a heading with a class.',
                'source' => <<<'CARVE'
                = Install

                See [Install][].
                CARVE,
            ],
            'img_fence' => [
                'config' => 'img_fence',
                'blurb' => 'Sanitizes the SVG of an img fence and emits it as a data URL image.',
                'source' => <<<'CARVE'
                ```img
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="40" height="40"><circle cx="10" cy="10" r="8" fill="#ff2d20"/></svg>
                ```
                CARVE,
            ],
            'index_terms' => [
                'config' => 'index_terms',
                'blurb' => 'Collects :index[...] terms into an index directive with back-links.',
                'source' => <<<'CARVE'
                Carve renders :index[HTML] and plain text.

                ::: index

                :::
                CARVE,
            ],
            'link_policy' => [
                'config' => 'link_policy',
                'blurb' => 'Unlinks links whose scheme or domain the policy denies, here a javascript: link.',
                'source' => 'A [safe link](https://example.com) and a [script link](javascript:alert(1)).',
            ],
            'list_table' => [
                'config' => 'list_table',
                'blurb' => 'Builds a table from a nested list: each outer item is a row.',
                'source' => <<<'CARVE'
                ::: list-table
                - - Name
                  - Role
                - - Alice
                  - Admin
                :::
                CARVE,
            ],
            'math_block' => [
                'config' => 'math_block',
                'blurb' => 'Renders a math fence as display math for MathJax or KaTeX.',
                'source' => <<<'CARVE'
                ```math
                E = mc^2
                ```
                CARVE,
            ],
            'mermaid' => [
                'config' => 'mermaid',
                'blurb' => 'Mermaid fence for mermaid.js.',
                'source' => <<<'CARVE'
                ```mermaid
                graph LR
                    A --> B
                ```
                CARVE,
            ],
            'plantuml' => [
                'config' => 'plantuml',
                'blurb' => 'PlantUML fence (also puml) for a PlantUML server or client renderer.',
                'source' => <<<'CARVE'
                ```plantuml
                Alice -> Bob: hello
                ```
                CARVE,
            ],
            'semantic_span' => [
                'config' => 'semantic_span',
                'blurb' => 'Adds the samp, var, cite and dfn span keys.',
                'source' => 'Type [ls]{samp} to list the files in [dir]{var}.',
            ],
            'smart_quotes' => [
                'config' => ['type' => 'smart_quotes', 'locale' => 'de'],
                'blurb' => 'Locale-aware quotes, here German.',
                'source' => 'Sie sagte "Hallo" und \'tschüss\'.',
            ],
            'spoiler' => [
                'config' => 'spoiler',
                'blurb' => 'Hides an inline :spoiler[...] or a spoiler div until the reader opens it.',
                'source' => <<<'CARVE'
                The ending is :spoiler[a twist].

                ::: spoiler "Plot"
                Everyone was a robot.
                :::
                CARVE,
            ],
            'table_of_contents' => [
                'config' => 'table_of_contents',
                'blurb' => 'Prepends a table of contents built from the headings.',
                'source' => <<<'CARVE'
                = Install

                == Requirements
                CARVE,
            ],
            'tabs' => [
                'config' => 'tabs',
                'blurb' => 'Renders a tabs div of tab divs as CSS-only tabs.',
                'source' => <<<'CARVE'
                :::: tabs
                ::: tab "Install"
                Run composer require.
                :::

                ::: tab "Use"
                Call Carve::toHtml().
                :::
                ::::
                CARVE,
            ],
            'toc_placement' => [
                'config' => 'toc_placement',
                'blurb' => 'Puts the table of contents where a toc div stands.',
                'source' => <<<'CARVE'
                Intro.

                ::: toc

                :::

                = Install
                CARVE,
            ],
            'vega_lite' => [
                'config' => 'vega_lite',
                'blurb' => 'Vega-Lite specification fence, emitted as JSON.',
                'source' => <<<'CARVE'
                ```vega-lite
                {"mark": "bar", "data": {"values": [{"a": 1}]}, "encoding": {"y": {"field": "a", "type": "quantitative"}}}
                ```
                CARVE,
            ],
            'wavedrom' => [
                'config' => 'wavedrom',
                'blurb' => 'WaveDrom timing diagram fence, emitted as JSON.',
                'source' => <<<'CARVE'
                ```wavedrom
                {"signal": [{"name": "clk", "wave": "p..."}]}
                ```
                CARVE,
            ],
            'wikilinks' => [
                'config' => ['type' => 'wikilinks', 'url_template' => '/extension-catalog?wiki={page}'],
                'blurb' => 'Links :wiki[Page] and :wiki[Page|label] through a URL template.',
                'source' => 'See :wiki[Home] and :wiki[API Reference|the API].',
            ],
        ];
    }
}
