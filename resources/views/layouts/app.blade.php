<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Laravel Carve Demo')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>📝</text></svg>">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        nav {
            background: #333;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
        }
        nav a:hover { background: #555; }
        nav a.active { background: #ff2d20; }
        .repo-links { margin: -10px 0 20px; text-align: right; font-size: 14px; }
        .repo-links a { margin-left: 12px; }
        h1, h2, h3 { color: #333; }
        .card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h2 { margin-top: 0; border-bottom: 2px solid #ff2d20; padding-bottom: 10px; }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }
        code { font-family: 'SF Mono', Monaco, monospace; font-size: 14px; }
        .rendered {
            background: #fafafa;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
        }
        .rendered h1, .rendered h2, .rendered h3 { color: #333; }
        .rendered > :first-child { margin-top: 0; }
        .rendered > :last-child { margin-bottom: 0; }
        .rendered ul, .rendered ol { padding-left: 1.5rem; }
        .rendered li > input[type="checkbox"] { margin: 0 0.5rem 0 0; vertical-align: -0.1em; }
        .rendered li:has(> input[type="checkbox"]) { list-style: none; }
        .rendered li[data-task-state="-"] { text-decoration: line-through; opacity: 0.65; }
        .rendered li[data-task-state=">"]::before { content: "↪"; margin-left: -1.25rem; margin-right: 0.35rem; }
        .rendered blockquote {
            border-left: 4px solid #ff2d20;
            margin: 0;
            padding-left: 15px;
            color: #666;
        }
        .rendered dl { margin: 1em 0; }
        .rendered dt { font-weight: 700; }
        .rendered dd { margin: 0 0 0.75em 1.5em; }
        .rendered table { border-collapse: collapse; width: 100%; }
        .rendered th, .rendered td { border: 1px solid #ddd; padding: 6px 12px; }
        .rendered th { background: #f1f1f1; text-align: left; }
        .rendered hr { border: 0; border-top: 1px solid #ddd; margin: 1.5em 0; }
        .rendered img, .rendered svg, .rendered canvas { max-width: 100%; height: auto; }
        .rendered pre.mermaid, .diagram-draw > pre[class] { background: #fff; color: #222; border: 1px dashed #ddd; }
        .rendered pre.mermaid[data-processed] { border: 0; padding: 0; }
        .rendered pre.mermaid[data-processed] svg { display: block; margin: 0 auto; }
        .columns { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .columns { grid-template-columns: 1fr; } }
        form label { display: block; font-weight: bold; margin-top: 15px; }
        form input[type="text"], form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
        }
        form textarea { resize: vertical; font-family: 'SF Mono', Monaco, monospace; }
        form button {
            margin-top: 15px;
            background: #ff2d20;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        form button:hover { background: #cc2419; }
        .error { color: #c00; font-size: 14px; margin-top: 5px; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 6px; }
        .safe { background: #d4edda; border: 1px solid #28a745; padding: 15px; border-radius: 6px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-safe { background: #28a745; color: #fff; }
        .badge-unsafe { background: #dc3545; color: #fff; }
        /* Admonition styles */
        .admonition {
            border-left: 4px solid #ff2d20;
            background: #fff8f8;
            padding: 12px 16px;
            margin: 1em 0;
            border-radius: 4px;
        }
        .admonition-title { font-weight: bold; margin: 0 0 8px 0; }
        /* Code Group Extension Styles */
        .code-group {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin: 1em 0;
        }
        .code-group-radio { display: none; }
        .code-group-label {
            display: inline-block;
            padding: 10px 20px;
            background: #f5f5f5;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s, border-color 0.2s;
        }
        .code-group-label:hover { background: #e8e8e8; }
        .code-group-panel { display: none; }
        .code-group-panel pre { margin: 0; border-radius: 0; }
        .code-group-radio:nth-of-type(1):checked ~ .code-group-label:nth-of-type(1),
        .code-group-radio:nth-of-type(2):checked ~ .code-group-label:nth-of-type(2),
        .code-group-radio:nth-of-type(3):checked ~ .code-group-label:nth-of-type(3),
        .code-group-radio:nth-of-type(4):checked ~ .code-group-label:nth-of-type(4),
        .code-group-radio:nth-of-type(5):checked ~ .code-group-label:nth-of-type(5) {
            background: #fff;
            border-bottom-color: #ff2d20;
        }
        .code-group-radio:nth-of-type(1):checked ~ .code-group-panel:nth-of-type(1),
        .code-group-radio:nth-of-type(2):checked ~ .code-group-panel:nth-of-type(2),
        .code-group-radio:nth-of-type(3):checked ~ .code-group-panel:nth-of-type(3),
        .code-group-radio:nth-of-type(4):checked ~ .code-group-panel:nth-of-type(4),
        .code-group-radio:nth-of-type(5):checked ~ .code-group-panel:nth-of-type(5) {
            display: block;
        }
        /* Extension type chips (Diagrams page) */
        .types-grid { display: flex; flex-wrap: wrap; gap: 8px; margin: 1em 0; }
        .type-chip {
            display: inline-block;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px 10px;
            font-size: 13px;
            color: #333;
        }
        /* PlantUML fence: rendered image scales to its container */
        .plantuml-img { max-width: 100%; height: auto; }
        /* Diagram gallery: drawn result container + rendered SVG/canvas scaling */
        .diagram-draw { min-height: 60px; }
        .diagram-draw svg, .diagram-draw canvas { max-width: 100%; height: auto; }
        .diagram-draw .kroki-img { max-width: 100%; height: auto; }
        .renderer-note { font-size: 13px; color: #666; margin: 4px 0 12px; }
        /* Terminal block for the ANSI render target */
        .terminal {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: 'SF Mono', Monaco, monospace;
            font-size: 14px;
            line-height: 1.5;
            white-space: pre;
        }
        .static-preview { background: #e8f0fe; border: 1px solid #7aa2e3; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; }
    </style>
    @stack('head')
</head>
<body>
    @if (config('demo-pages.static_export'))
        <div class="static-preview"><strong>Static preview.</strong> Server-backed interactions are available only in a local checkout.</div>
    @endif
    <nav>
        <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Home</a>
        <a href="{{ route('blade_directive') }}" @class(['active' => request()->routeIs('blade_directive')])>Blade Directive</a>
        <a href="{{ route('facade') }}" @class(['active' => request()->routeIs('facade')])>Facade</a>
        <a href="{{ route('service') }}" @class(['active' => request()->routeIs('service')])>Service</a>
        <a href="{{ route('form') }}" @class(['active' => request()->routeIs('form*')])>Form</a>
        <a href="{{ route('safe_mode') }}" @class(['active' => request()->routeIs('safe_mode')])>Safe Mode</a>
        <a href="{{ route('static_mode') }}" @class(['active' => request()->routeIs('static_mode')])>Static Mode</a>
        <a href="{{ route('plain_text') }}" @class(['active' => request()->routeIs('plain_text')])>Plain Text</a>
        <a href="{{ route('includes') }}" @class(['active' => request()->routeIs('includes')])>Includes</a>
        <a href="{{ route('extensions') }}" @class(['active' => request()->routeIs('extensions')])>Extensions</a>
        <a href="{{ route('diagrams') }}" @class(['active' => request()->routeIs('diagrams')])>Diagrams</a>
        <a href="{{ route('syntax') }}" @class(['active' => request()->routeIs('syntax')])>Syntax</a>
        <a href="{{ route('render_targets') }}" @class(['active' => request()->routeIs('render_targets')])>Render Targets</a>
        <a href="{{ route('editor_preview') }}" @class(['active' => request()->routeIs('editor_preview')])>Editor Preview</a>
    </nav>
    <div class="repo-links">
        <a href="https://github.com/markup-carve/laravel-carve-demo">Demo source</a>
        <a href="https://github.com/markup-carve/laravel-carve">laravel-carve package</a>
    </div>
    @yield('body')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16/dist/contrib/auto-render.min.js"
            onload="renderMathInElement(document.body, {delimiters: [{left: '\\(', right: '\\)', display: false}, {left: '\\[', right: '\\]', display: true}]});"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/styles/github.min.css">
    <script src="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/highlight.min.js"></script>
    <script type="module">
        import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.esm.min.mjs';
        mermaid.initialize({ startOnLoad: true, theme: 'neutral' });
        // highlight fenced code blocks (language-* classes from Carve), but
        // leave mermaid sources alone
        document.querySelectorAll('pre code[class*="language-"]').forEach((el) => {
            window.hljs.highlightElement(el);
        });
        // Hydrate PlantUML fences (<pre class="plantuml">) into rendered SVG via
        // the public PlantUML server, using its ~h hex source encoding. This is a
        // progressive enhancement: without it the diagram source stays visible.
        document.querySelectorAll('pre.plantuml').forEach((el) => {
            const source = el.textContent.trim();
            if (!source) { return; }
            const hex = Array.from(new TextEncoder().encode(source))
                .map((b) => b.toString(16).padStart(2, '0')).join('');
            const img = document.createElement('img');
            img.alt = 'PlantUML diagram';
            img.className = 'plantuml-img';
            // Only swap in the rendered image once it has actually loaded. If the
            // PlantUML server is unreachable (offline, CSP, blocked host), keep the
            // readable source <pre> in place - graceful degradation, no broken image.
            img.addEventListener('load', () => el.replaceWith(img));
            img.src = 'https://www.plantuml.com/plantuml/svg/~h' + hex;
        });
    </script>
    @stack('scripts')
</body>
</html>
