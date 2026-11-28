{{-- Client-side renderers for diagram fences. $scope selects the containers to hydrate. --}}
{{-- UMD libraries expose globals used by the module hydration below. --}}
<script src="https://cdn.jsdelivr.net/npm/vega@5"></script>
<script src="https://cdn.jsdelivr.net/npm/vega-lite@5"></script>
<script src="https://cdn.jsdelivr.net/npm/vega-embed@6"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="https://cdn.jsdelivr.net/npm/wavedrom@3/wavedrom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/wavedrom@3/skins/default.js"></script>
<script src="https://cdn.jsdelivr.net/npm/abcjs@6/dist/abcjs-basic-min.js"></script>
<script type="module">
    const roots = [...document.querySelectorAll(@json($scope))];
    const root = { querySelectorAll: (sel) => roots.flatMap((r) => [...r.querySelectorAll(sel)]) };
    // Mermaid and PlantUML are hydrated globally by the layout (they target
    // pre.mermaid / pre.plantuml anywhere in the body), so the gallery only
    // wires the remaining six renderers here. Every renderer is wrapped so a
    // failure leaves the readable <pre>/<div> source in place.

    // Graphviz -> viz.js (WASM), pure in-browser.
    try {
        const { instance } = await import('https://cdn.jsdelivr.net/npm/@viz-js/viz@3/lib/viz-standalone.mjs');
        const viz = await instance();
        root.querySelectorAll('pre.graphviz').forEach((el) => {
            try {
                const svg = viz.renderSVGElement(el.textContent);
                el.replaceWith(svg);
            } catch (e) { /* keep source */ }
        });
    } catch (e) { /* keep source */ }

    // D2 -> Kroki server (no practical in-browser D2 build).
    root.querySelectorAll('pre.d2').forEach(async (el) => {
        try {
            const res = await fetch('https://kroki.io/d2/svg', {
                method: 'POST',
                headers: { 'Content-Type': 'text/plain' },
                body: el.textContent,
            });
            if (!res.ok) { return; }
            const svg = await res.text();
            const wrap = document.createElement('div');
            wrap.style.maxWidth = '420px';
            wrap.innerHTML = svg;
            el.replaceWith(wrap);
        } catch (e) { /* keep source */ }
    });

    // Vega-Lite -> vega-embed (reads the JSON spec from the <script> wrapper).
    if (window.vegaEmbed) {
        root.querySelectorAll('div.vega-lite').forEach((el) => {
            const script = el.querySelector('script[type="application/json"]');
            if (!script) { return; }
            try {
                const spec = JSON.parse(script.textContent);
                // Without a width Vega-Lite sizes the chart to its narrowest band.
                spec.width = spec.width ?? 'container';
                const target = document.createElement('div');
                target.style.width = '100%';
                target.style.maxWidth = '480px';
                el.replaceWith(target);
                window.vegaEmbed(target, spec, { actions: false });
            } catch (e) { /* keep source */ }
        });
    }

    // Chart.js -> new Chart(canvas, config) from the JSON wrapper. Chart.js is
    // responsive and sizes to its parent, so give it a bounded container or it
    // collapses; maintainAspectRatio:false lets it fill that fixed height.
    if (window.Chart) {
        root.querySelectorAll('div.chart').forEach((el) => {
            const script = el.querySelector('script[type="application/json"]');
            if (!script) { return; }
            try {
                const cfg = JSON.parse(script.textContent);
                cfg.options = cfg.options || {};
                cfg.options.responsive = true;
                cfg.options.maintainAspectRatio = false;
                // A dataset without a label would show "undefined" in the legend.
                if (!(cfg.data?.datasets ?? []).some((set) => set.label)) {
                    cfg.options.plugins = { ...cfg.options.plugins, legend: { display: false } };
                }
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
        root.querySelectorAll('pre.wavedrom').forEach((el, i) => {
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
        root.querySelectorAll('pre.abc').forEach((el) => {
            try {
                const source = el.textContent;
                const target = document.createElement('div');
                el.replaceWith(target);
                window.ABCJS.renderAbc(target, source, { responsive: 'resize' });
            } catch (e) { /* keep source */ }
        });
    }
</script>
