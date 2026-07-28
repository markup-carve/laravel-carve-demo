@extends('layouts.app')

@section('title', 'Diagrams & Media Demo')

@section('body')
<div class="card">
    <h1>Diagrams &amp; Media</h1>
    <p>Two Tier-3 extensions added in <code>laravel-carve</code> 0.1.3, both registered
       through the <code>ExtensionFactory</code> shorthands: a <strong>PlantUML</strong>
       fenced-diagram preset and a sanitized <strong>SVG image</strong> fence.</p>
</div>

<div class="card">
    <h2>PlantUML Diagram</h2>
    <p>The <code>plantuml</code> shorthand claims <code>plantuml</code> and
       <code>puml</code> fences and emits <code>&lt;pre class="plantuml"&gt;</code> for a
       client-side renderer to hydrate - the same graceful-degradation shape used for
       Mermaid. The diagram below is hydrated in the browser via the public PlantUML
       server; without it the source stays visible (nothing breaks).</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $plantuml_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $plantuml_html !!}</div>
        </div>
    </div>
    <h3>Configuration</h3>
    <pre><code>'with_plantuml' => [
    'extensions' => [
        'plantuml',
    ],
],</code></pre>
</div>

<div class="card">
    <h2>SVG Image Fence</h2>
    <p>The <code>img_fence</code> shorthand renders an <code>img</code> (alias
       <code>image</code>) fence: the SVG body is sanitized and emitted as a sandboxed
       <code>data:image/svg+xml</code> <code>&lt;img&gt;</code>. The <code>&lt;script&gt;</code>
       and inline <code>onclick</code> handler in the source below are stripped by the
       sanitizer - inspect the rendered <code>&lt;img&gt;</code> to confirm. No client
       library or external service is needed.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $img_fence_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered (sandboxed &lt;img&gt;)</h3>
            <div class="rendered">{!! $img_fence_html !!}</div>
        </div>
    </div>
    <h3>Configuration</h3>
    <pre><code>'with_img_fence' => [
    'safe_mode' => true,
    'extensions' => [
        'img_fence',
    ],
],</code></pre>
</div>

<div class="card">
    <h2>Available Shorthand Types</h2>
    <p>Every shorthand the <code>ExtensionFactory</code> understands is discoverable via
       <code>ExtensionFactory::types()</code>. Pass any of these as a bare string (or an
       <code>['type' =&gt; '...']</code> array) in a converter profile's
       <code>extensions</code> list.</p>
    <div class="types-grid">
        @foreach ($extension_types as $type)
            <code class="type-chip">{{ $type }}</code>
        @endforeach
    </div>
    <pre><code>use MarkupCarve\LaravelCarve\Service\ExtensionFactory;

$types = ExtensionFactory::types(); // {{ count($extension_types) }} shorthands</code></pre>
</div>
@endsection
