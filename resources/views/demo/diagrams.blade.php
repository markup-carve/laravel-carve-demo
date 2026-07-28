@extends('layouts.app')

@section('title', 'Live Diagram Gallery')

@section('body')
<div class="card">
    <h1>Live Diagram Gallery</h1>
    <p>carve-php's <code>FencedRenderExtension</code> ships eight fenced-diagram presets.
       Each claims one or more fence languages and emits a client-hydration element -
       a <code>&lt;pre class="TYPE"&gt;</code> for text-mode presets, or a
       <code>&lt;div class="TYPE"&gt;&lt;script type="application/json"&gt;</code> for the
       JSON-mode ones (Vega-Lite, Chart.js). A browser renderer per type draws the
       result below. This is progressive enhancement: if a library or network call
       fails, the emitted source stays visible - nothing breaks.</p>
    <p>All eight are registered at once through the <code>with_diagrams</code> converter
       profile:</p>
    <pre><code>'with_diagrams' => [
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
    <div class="rendered diagram-draw" data-diagram="{{ $key }}">{!! $entry['html'] !!}</div>
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

@push('scripts')
{{-- UMD libraries expose globals used by the module hydration below. --}}
<script src="https://cdn.jsdelivr.net/npm/vega@5"></script>
<script src="https://cdn.jsdelivr.net/npm/vega-lite@5"></script>
<script src="https://cdn.jsdelivr.net/npm/vega-embed@6"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="https://cdn.jsdelivr.net/npm/wavedrom@3/wavedrom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/wavedrom@3/skins/default.js"></script>
<script src="https://cdn.jsdelivr.net/npm/abcjs@6/dist/abcjs-basic-min.js"></script>
<script type="module">
    // Mermaid and PlantUML are hydrated globally by the layout (they target
    // pre.mermaid / pre.plantuml anywhere in the body), so the gallery only
    // wires the remaining six renderers here. Every renderer is wrapped so a
    // failure leaves the readable <pre>/<div> source in place.

    // Graphviz -> viz.js (WASM), pure in-browser.
    try {
        const { instance } = await import('https://cdn.jsdelivr.net/npm/@viz-js/viz@3/lib/viz-standalone.mjs');
        const viz = await instance();
        document.querySelectorAll('.diagram-draw pre.graphviz').forEach((el) => {
            try {
                const svg = viz.renderSVGElement(el.textContent);
                el.replaceWith(svg);
            } catch (e) { /* keep source */ }
        });
    } catch (e) { /* keep source */ }

    // D2 -> Kroki server (no practical in-browser D2 build).
    document.querySelectorAll('.diagram-draw pre.d2').forEach(async (el) => {
        try {
            const res = await fetch('https://kroki.io/d2/svg', {
                method: 'POST',
                headers: { 'Content-Type': 'text/plain' },
                body: el.textContent,
            });
            if (!res.ok) { return; }
            const svg = await res.text();
            const wrap = document.createElement('div');
            wrap.innerHTML = svg;
            el.replaceWith(wrap);
        } catch (e) { /* keep source */ }
    });

    // Vega-Lite -> vega-embed (reads the JSON spec from the <script> wrapper).
    if (window.vegaEmbed) {
        document.querySelectorAll('.diagram-draw div.vega-lite').forEach((el) => {
            const script = el.querySelector('script[type="application/json"]');
            if (!script) { return; }
            try {
                const spec = JSON.parse(script.textContent);
                const target = document.createElement('div');
                el.replaceWith(target);
                window.vegaEmbed(target, spec, { actions: false });
            } catch (e) { /* keep source */ }
        });
    }

    // Chart.js -> new Chart(canvas, config) from the JSON wrapper. Chart.js is
    // responsive and sizes to its parent, so give it a bounded container or it
    // collapses; maintainAspectRatio:false lets it fill that fixed height.
    if (window.Chart) {
        document.querySelectorAll('.diagram-draw div.chart').forEach((el) => {
            const script = el.querySelector('script[type="application/json"]');
            if (!script) { return; }
            try {
                const cfg = JSON.parse(script.textContent);
                cfg.options = cfg.options || {};
                cfg.options.responsive = true;
                cfg.options.maintainAspectRatio = false;
                const wrap = document.createElement('div');
                wrap.style.position = 'relative';
                wrap.style.height = '320px';
                wrap.style.maxWidth = '480px';
                const canvas = document.createElement('canvas');
                wrap.appendChild(canvas);
                el.replaceWith(wrap);
                new window.Chart(canvas, cfg);
            } catch (e) { /* keep source */ }
        });
    }

    // WaveDrom -> RenderWaveForm into a per-diagram target element.
    if (window.WaveDrom) {
        const render = window.WaveDrom.renderWaveForm || window.WaveDrom.RenderWaveForm;
        document.querySelectorAll('.diagram-draw pre.wavedrom').forEach((el, i) => {
            try {
                const source = JSON.parse(el.textContent);
                const target = document.createElement('div');
                target.id = 'wavedrom_target_' + i;
                el.replaceWith(target);
                render.call(window.WaveDrom, i, source, 'wavedrom_target_');
            } catch (e) { /* keep source */ }
        });
    }

    // ABC notation -> abcjs renders sheet music as SVG.
    if (window.ABCJS) {
        document.querySelectorAll('.diagram-draw pre.abc').forEach((el) => {
            try {
                const source = el.textContent;
                const target = document.createElement('div');
                el.replaceWith(target);
                window.ABCJS.renderAbc(target, source);
            } catch (e) { /* keep source */ }
        });
    }
</script>
@endpush
