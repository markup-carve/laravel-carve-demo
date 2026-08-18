<?php

declare(strict_types=1);

return [

    'converters' => [

        // Default: safe rendering (XSS protection) with handy extensions.
        // The @carve directive uses this converter; use @carveRaw for
        // trusted content that needs raw HTML passthrough.
        'default' => [
            'safe_mode' => true,
            'extensions' => [
                'details',
                ['type' => 'autolink'],
                [
                    'type' => 'external_links',
                    'internal_hosts' => ['localhost', '127.0.0.1', 'example.com'],
                ],
                ['type' => 'smart_quotes'],
                [
                    'type' => 'heading_permalinks',
                    'symbol' => '#',
                    'position' => 'after',
                    'class' => 'heading-anchor',
                ],
            ],
        ],

        // Print/PDF/email target: graceful degradation (spec static mode)
        'print' => [
            'safe_mode' => true,
            'mode' => 'static',
            'extensions' => [
                'details',
            ],
        ],

        // User-submitted content: safe mode ON (XSS protection)
        'user_content' => [
            'safe_mode' => true,
        ],

        // Explicit raw-HTML passthrough for trusted administrator-authored
        // content only. Never select this profile for user submissions.
        'trusted' => [
            'safe_mode' => false,
        ],

        // Editor preview profile: trusted symbol replacements and source-line
        // markers for scroll synchronization. Symbol HTML is configuration,
        // never user input.
        'editor_preview' => [
            'safe_mode' => true,
            'symbols' => [
                'spark' => '<span class="carve-symbol" aria-label="spark">✦</span>',
            ],
            'source_lines' => true,
        ],

        'with_mentions' => [
            'safe_mode' => false,
            'extensions' => [
                [
                    'type' => 'mentions',
                    'mention_url' => 'https://github.com/{name}',
                    'mention_class' => 'mention',
                ],
            ],
        ],

        'with_toc' => [
            'safe_mode' => false,
            'extensions' => [
                ['type' => 'table_of_contents', 'toc_class' => 'toc'],
                // Expands ::: toc placement blocks in the source document.
                'toc_placement',
                ['type' => 'heading_permalinks'],
            ],
        ],

        'with_wikilinks' => [
            'safe_mode' => false,
            'extensions' => [
                [
                    'type' => 'wikilinks',
                    'url_template' => '/wiki/{page}',
                    'link_class' => 'wiki-link',
                ],
            ],
        ],

        'with_default_attrs' => [
            'safe_mode' => false,
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
            'safe_mode' => false,
            'extensions' => [
                [
                    'type' => 'frontmatter',
                    'default_format' => 'yaml',
                    'render_as_comment' => true,
                ],
            ],
        ],

        'with_semantic' => [
            'safe_mode' => false,
            'extensions' => [
                ['type' => 'semantic_span'],
            ],
        ],

        'with_code_group' => [
            'safe_mode' => false,
            'extensions' => [
                ['type' => 'code_group'],
            ],
        ],

        'with_mermaid' => [
            'safe_mode' => false,
            'extensions' => [
                ['type' => 'mermaid'],
            ],
        ],

        'with_admonition' => [
            'safe_mode' => false,
            'extensions' => [
                ['type' => 'admonition'],
            ],
        ],

        // PlantUML fenced diagrams. The `plantuml` shorthand registers the
        // FencedRenderExtension::plantuml() preset, which claims `plantuml` and
        // `puml` fences and emits <pre class="plantuml"> for a client-side
        // PlantUML renderer to hydrate (see the runtime note in the README).
        'with_plantuml' => [
            'safe_mode' => false,
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
            'safe_mode' => true,
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
            'safe_mode' => false,
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
            'safe_mode' => true,
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
