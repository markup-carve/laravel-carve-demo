# Screenshots

A visual tour of the laravel-carve demo app. Boot it with `php artisan serve`
and click through the pages in the nav.

## Home

Overview of all features.

![Home](home.png)

## Blade Directive

`@carve`, `@carveRaw`, and `@carveText` with side-by-side source and rendered
output.

![Blade Directive](blade-directive.png)

## Live Diagram Gallery

All eight fenced-render presets drawn live in the browser - mermaid, plantuml,
graphviz, d2, vega-lite, wavedrom, chart, abc - plus the sanitized SVG image
fence and the discoverable shorthand types. Each shows the Carve source, the
emitted hydration markup, and the drawn result (the source stays visible if a
renderer fails to load).

![Live Diagram Gallery](diagram-gallery.png)
