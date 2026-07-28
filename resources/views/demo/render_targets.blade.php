@extends('layouts.app')

@section('title', 'Render Targets')

@section('body')
<div class="card">
    <h1>Render Targets</h1>
    <p>One Carve source, four outputs. Beyond <code>toHtml()</code>, laravel-carve exposes
       <code>toText()</code> for indexing, <code>toMarkdown()</code> for interop with
       Markdown tooling, and <code>toAnsi()</code> for terminal output. All four are shown
       below from the same source.</p>
    <pre><code>$carve->toHtml($source);      // web
$carve->toText($source);      // search / excerpts
$carve->toMarkdown($source);  // Markdown interop
$carve->toAnsi($source);      // terminal / CLI</code></pre>
</div>

<div class="card">
    <h2>Carve source</h2>
    <pre><code>{{ $source }}</code></pre>
</div>

<div class="columns">
    <div class="card">
        <h2>toHtml()</h2>
        <div class="rendered">{!! $html !!}</div>
    </div>

    <div class="card">
        <h2>toMarkdown()</h2>
        <pre><code>{{ $markdown }}</code></pre>
    </div>

    <div class="card">
        <h2>toText()</h2>
        <pre><code>{{ $text }}</code></pre>
    </div>

    <div class="card">
        <h2>toAnsi()</h2>
        <p class="renderer-note">ANSI escape sequences, colourised client-side with ansi_up
           (falls back to the raw sequences if the library does not load).</p>
        <pre class="terminal" id="ansi-output">{{ $ansi }}</pre>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    try {
        const { AnsiUp } = await import('https://cdn.jsdelivr.net/npm/ansi_up@6/ansi_up.js');
        const el = document.getElementById('ansi-output');
        if (el) {
            const au = new AnsiUp();
            el.innerHTML = au.ansi_to_html(el.textContent);
        }
    } catch (e) { /* keep raw ANSI visible */ }
</script>
@endpush
