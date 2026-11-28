@extends('layouts.app')

@section('title', 'Live Diagram Gallery')

@section('body')
<div class="card">
    <h1>Live Diagram Gallery</h1>
    <p>carve-php's <code>FencedRender</code> extension ships eight fenced-diagram presets.
       Each claims one or more fence languages and emits a client-hydration element -
       a <code>&lt;pre class="TYPE"&gt;</code> for text-mode presets, or a
       <code>&lt;div class="TYPE"&gt;&lt;script type="application/json"&gt;</code> for the
       JSON-mode ones (Vega-Lite, Chart.js). A browser renderer per type draws the
       result below. This is progressive enhancement: if a library or network call
       fails, the emitted source stays visible - nothing breaks.</p>
    <p>All eight are registered at once through the <code>with_diagrams</code> converter
       profile:</p>
    <pre><code>'with_diagrams' => [
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
],</code></pre>
</div>

@foreach ($diagrams as $key => $entry)
<div class="card">
    <h2>{{ $entry['title'] }}</h2>
    <p>{{ $entry['blurb'] }}</p>
    <p class="renderer-note">Browser renderer: {{ $entry['renderer'] }}</p>
    <div class="columns">
        <div>
            <h3>Carve source</h3>
            <pre><code>{{ $entry['source'] }}</code></pre>
        </div>
        <div>
            <h3>Emitted markup</h3>
            <pre><code>{{ $entry['html'] }}</code></pre>
        </div>
    </div>
    <h3>Drawn result</h3>
    <div class="rendered carve diagram-draw" data-diagram="{{ $key }}">{!! $entry['html'] !!}</div>
</div>
@endforeach

<div class="card">
    <h2>Sanitized SVG Image Fence</h2>
    <p>The <code>img_fence</code> shorthand renders an <code>img</code> (alias
       <code>image</code>) fence: the SVG body is sanitized and emitted as a sandboxed
       <code>data:image/svg+xml</code> <code>&lt;img&gt;</code>. The <code>&lt;script&gt;</code>
       and inline <code>onclick</code> handler in the source below are stripped by the
       sanitizer - inspect the rendered <code>&lt;img&gt;</code> to confirm. No client
       library or external service is needed; this one renders fully server-side.</p>
    <div class="columns">
        <div>
            <h3>Carve source</h3>
            <pre><code>{{ $img_fence_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered (sandboxed &lt;img&gt;)</h3>
            <div class="rendered carve">{!! $img_fence_html !!}</div>
        </div>
    </div>
    <h3>Configuration</h3>
    <pre><code>'with_img_fence' => [
    'raw_html' => false,
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

@push('scripts')
@include('demo.partials.diagram-renderers', ['scope' => '.diagram-draw'])
@endpush
