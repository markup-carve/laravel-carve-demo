<?php

declare(strict_types=1);

return [

    // The demo's trusted, file-backed documents. String rendering stays literal.
    'include_root' => resource_path('carve'),

    // Application-owned, trusted snippets for the Includes page. A resolver
    // can use this same contract for rows from a database or CMS.
    'include_snippets' => [
        'account-status' => <<<'CARVE'
        ::: note "Account status"
        You have *3 pending reviews* in the current workspace.
        :::
        CARVE,
        'support-hours' => 'Support is available Monday to Friday, 09:00 to 17:00 UTC.',
    ],

    'converters' => [

        // Default: safe rendering (raw HTML off) with handy extensions.
        // The @carve directive uses this converter; use @carveRaw for
        // trusted content that needs raw HTML passthrough.
        'default' => [
            'raw_html' => false,
            'extensions' => [
                'details',
                'code_group',
                ['type' => 'autolink'],
                ['type' => 'external_links'],
                ['type' => 'smart_quotes'],
                [
                    'type' => 'heading_permalinks',
                    'symbol' => '#',
                    'css_class' => 'heading-anchor',
                ],
            ],
        ],

        // Print/PDF/email target: graceful degradation (spec static mode)
        'print' => [
            'raw_html' => false,
            'mode' => 'static',
            'extensions' => [
                'details',
                'code_group',
            ],
        ],

        // Restricted comment markup with strict safe mode.
        'comment' => [
            'raw_html' => false,
            'profile' => 'comment',
        ],

        // User-submitted content: safe mode ON (XSS protection)
        'user_content' => [
            'raw_html' => false,
        ],

        // Explicit raw-HTML passthrough for trusted administrator-authored
        // content only. Never select this profile for user submissions.
        'trusted' => [
            'raw_html' => true,
        ],

        // Editor preview profile: trusted symbol replacements and source-line
        // markers for scroll synchronization. Symbol HTML is configuration,
        // never user input.
        'editor_preview' => [
            'raw_html' => false,
            'symbols' => [
                'spark' => '<span class="carve-symbol" aria-label="spark">✦</span>',
            ],
            'source_lines' => true,
        ],

        // Resolves include directives from include_snippets above; the
        // resolver is attached in AppServiceProvider with CarveManager::extend.
        'config_snippets' => [
            'raw_html' => false,
        ],

        'with_mentions' => [
            'raw_html' => true,
            'extensions' => [
            ],
            'social' => ['mentionUrl' => 'https://github.com/{name}'],
        ],

        'with_toc' => [
            'raw_html' => true,
            'extensions' => [
                ['type' => 'table_of_contents', 'css_class' => 'toc'],
                // Expands ::: toc placement blocks in the source document.
                'toc_placement',
                ['type' => 'heading_permalinks'],
            ],
        ],

        'with_wikilinks' => [
            'raw_html' => true,
            'extensions' => [
                [
                    'type' => 'wikilinks',
                    'url_template' => '/extensions?wiki={page}',
                    'css_class' => 'wiki-link',
                ],
            ],
        ],

        'with_default_attrs' => [
            'raw_html' => true,
            'extensions' => [
                [
                    'type' => 'default_attributes',
                    'defaults' => [
                        'image' => [
                            'loading' => 'lazy',
                            'decoding' => 'async',
                        ],
                        'table' => [
                            'class' => 'table table-striped',
                        ],
                        'link' => [
                            'class' => 'styled-link',
                        ],
                    ],
                ],
            ],
        ],

        'with_frontmatter' => [
            'raw_html' => true,
            'extensions' => [
                // Frontmatter is core 0.2 syntax (C2-B-161): no registration.
            ],
        ],

        'with_semantic' => [
            'raw_html' => true,
            'extensions' => [
                ['type' => 'semantic_span'],
            ],
        ],

        'with_code_group' => [
            'raw_html' => true,
            'extensions' => [
                ['type' => 'code_group'],
            ],
        ],

        'with_mermaid' => [
            'raw_html' => true,
            'extensions' => [
                ['type' => 'mermaid'],
            ],
        ],

        'with_admonition' => [
            'raw_html' => true,
            'extensions' => [
                // Admonitions are core 0.2 syntax: no registration.
            ],
        ],

        // PlantUML fenced diagrams. The `plantuml` shorthand registers the
        // FencedRenderExtension::plantuml() preset, which claims `plantuml` and
        // `puml` fences and emits <pre class="plantuml"> for a client-side
        // PlantUML renderer to hydrate (see the runtime note in the README).
        'with_plantuml' => [
            'raw_html' => true,
            'extensions' => [
                'plantuml',
            ],
        ],

        // Sanitized SVG image fences. The `img_fence` shorthand registers the
        // carve-php ImgFenceExtension: an `img` / `image` fence renders its SVG
        // body sandboxed into a `data:image/svg+xml` <img>, with scripts and
        // active content stripped. Safe under untrusted input, so safe_mode
        // stays on.
        'with_img_fence' => [
            'raw_html' => false,
            'extensions' => [
                'img_fence',
            ],
        ],

        // Full fenced-diagram gallery. Registers every FencedRenderExtension
        // preset so all eight diagram languages emit their client-hydration
        // markup at once. Text-mode presets emit <pre class="TYPE">source</pre>;
        // JSON-mode presets (vega-lite, chart) emit
        // <div class="TYPE"><script type="application/json">spec</script></div>.
        // A browser renderer per type (loaded on the Diagrams page) draws each
        // one; without it the source stays visible (graceful degradation).
        // safe_mode is off so the JSON <script> wrappers survive - the sources
        // here are trusted author content, not user input.
        'with_diagrams' => [
            'raw_html' => true,
            'extensions' => [
                ['type' => 'fenced_render', 'language' => 'mermaid'],
                ['type' => 'fenced_render', 'language' => 'd2'],
                ['type' => 'fenced_render', 'language' => ['dot', 'graphviz'], 'css_class' => 'graphviz'],
                ['type' => 'fenced_render', 'language' => ['plantuml', 'puml'], 'css_class' => 'plantuml'],
                ['type' => 'fenced_render', 'language' => 'vega-lite', 'content_mode' => 'json'],
                ['type' => 'fenced_render', 'language' => 'wavedrom'],
                ['type' => 'fenced_render', 'language' => 'chart', 'content_mode' => 'json'],
                ['type' => 'fenced_render', 'language' => 'abc'],
            ],
        ],

        // Smart-typography-only profile used by the Syntax showcase: shows the
        // core Carve grammar (inline literal, definition lists, footnotes,
        // tight/loose lists, strict column-0) with dash-run and quote
        // typography enabled, but no link/heading decoration to add noise.
        'syntax' => [
            'raw_html' => false,
            'extensions' => [
                ['type' => 'smart_quotes'],
            ],
        ],

    ],

    'cache' => [
        // The in-memory store keeps the runnable demo self-contained while
        // exercising the integration's converter-aware render cache.
        'enabled' => true,
        'store' => 'array',
    ],

];
