<?php

declare(strict_types=1);

return [
    'static_export' => (bool) env('STATIC_EXPORT', false),
    'pages' => [
        'home' => 'static',
        'blade_directive' => 'static',
        'facade' => 'static',
        'service' => 'static',
        'form' => 'local-only',
        'safe_mode' => 'static',
        'static_mode' => 'static',
        'plain_text' => 'static',
        'includes' => 'static',
        'extensions' => 'static',
        'diagrams' => 'browser-enhanced',
        'syntax' => 'static',
        'render_targets' => 'static',
        'editor_preview' => 'browser-enhanced',
    ],
];
