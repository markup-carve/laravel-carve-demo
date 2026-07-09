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
        .rendered blockquote {
            border-left: 4px solid #ff2d20;
            margin: 0;
            padding-left: 15px;
            color: #666;
        }
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
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Home</a>
        <a href="{{ route('blade_directive') }}" @class(['active' => request()->routeIs('blade_directive')])>Blade Directive</a>
        <a href="{{ route('facade') }}" @class(['active' => request()->routeIs('facade')])>Facade</a>
        <a href="{{ route('service') }}" @class(['active' => request()->routeIs('service')])>Service</a>
        <a href="{{ route('form') }}" @class(['active' => request()->routeIs('form*')])>Form</a>
        <a href="{{ route('safe_mode') }}" @class(['active' => request()->routeIs('safe_mode')])>Safe Mode</a>
        <a href="{{ route('plain_text') }}" @class(['active' => request()->routeIs('plain_text')])>Plain Text</a>
        <a href="{{ route('extensions') }}" @class(['active' => request()->routeIs('extensions')])>Extensions</a>
    </nav>
    @yield('body')
</body>
</html>
