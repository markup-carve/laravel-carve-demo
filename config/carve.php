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

    ],

    'cache' => [
        'enabled' => false,
        'store' => null,
    ],

];
