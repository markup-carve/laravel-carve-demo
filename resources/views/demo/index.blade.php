@extends('layouts.app')

@section('title', 'Laravel Carve Demo')

@section('body')
<div class="card">
    <h1>Laravel Carve Demo</h1>
    <p>This demo application showcases all features of the <code>markup-carve/laravel-carve</code> package.</p>
    <p>
        <a href="https://github.com/markup-carve/laravel-carve" target="_blank">GitHub</a>
        ·
        <a href="https://carve.net" target="_blank">Carve</a>
        ·
        Laravel {{ app()->version() }}
    </p>
</div>

<div class="columns">
    <div class="card">
        <h2>@@carve Directive</h2>
        <p>Convert Carve to HTML using the <code>@@carve</code> Blade directive.</p>
        <pre><code>@@carve($article->body)</code></pre>
        <p><a href="{{ route('blade_directive') }}">View Demo &rarr;</a></p>
    </div>

    <div class="card">
        <h2>Carve Facade</h2>
        <p>Use <code>Carve::toHtml()</code> for inline strings.</p>
        <pre><code>&#123;!! Carve::toHtml($content) !!&#125;</code></pre>
        <p><a href="{{ route('facade') }}">View Demo &rarr;</a></p>
    </div>

    <div class="card">
        <h2>Service Injection</h2>
        <p>Inject <code>CarveConverterInterface</code> or <code>CarveManager</code>.</p>
        <pre><code>$html = $carve->toHtml($content);</code></pre>
        <p><a href="{{ route('service') }}">View Demo &rarr;</a></p>
    </div>

    <div class="card">
        <h2>Form Integration</h2>
        <p>Use <code>ValidCarve</code> rule in Form Requests.</p>
        <pre><code>'body' => [new ValidCarve()]</code></pre>
        <p><a href="{{ route('form') }}">View Demo &rarr;</a></p>
    </div>

    <div class="card">
        <h2>Safe Mode</h2>
        <p>XSS protection for untrusted user content.</p>
        <pre><code>'safe_mode' => true</code></pre>
        <p><a href="{{ route('safe_mode') }}">View Demo &rarr;</a></p>
    </div>

    <div class="card">
        <h2>Static Mode</h2>
        <p>Graceful degradation for print, PDF and email targets.</p>
        <pre><code>'mode' => 'static'</code></pre>
        <p><a href="{{ route('static_mode') }}">View Demo &rarr;</a></p>
    </div>

    <div class="card">
        <h2>Plain Text</h2>
        <p>Extract plain text for indexing or previews.</p>
        <pre><code>@@carveText($article->body)</code></pre>
        <p><a href="{{ route('plain_text') }}">View Demo &rarr;</a></p>
    </div>

    <div class="card">
        <h2>Extensions</h2>
        <p>All 17 extensions: autolinks, mentions, TOC, wikilinks, admonitions, tabs, code groups &amp; more.</p>
        <pre><code>'extensions' => [
    ['type' => 'mentions'],
    ['type' => 'table_of_contents'],
]</code></pre>
        <p><a href="{{ route('extensions') }}">View Demo &rarr;</a></p>
    </div>
</div>
@endsection
