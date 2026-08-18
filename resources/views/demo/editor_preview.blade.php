@extends('layouts.app')

@section('title', 'Editor Preview Metadata')

@section('body')
<div class="card">
    <h1>Editor Preview Metadata</h1>
    <p>The <code>editor_preview</code> converter maps the trusted <code>:spark:</code>
       symbol and enables source-line attributes for editor scroll synchronization.</p>
    <pre><code>'symbols' => [
    'spark' => '&lt;span class="carve-symbol"&gt;✦&lt;/span&gt;',
],
'source_lines' => true,</code></pre>
</div>

<div class="columns">
    <div class="card">
        <h2>Carve source</h2>
        <pre><code>{{ $source }}</code></pre>
    </div>
    <div class="card">
        <h2>Rendered preview</h2>
        <div class="rendered">{!! $html !!}</div>
        <h3>Generated HTML</h3>
        <pre><code>{{ $html }}</code></pre>
    </div>
</div>

<div class="card">
    <p><strong>Security boundary:</strong> symbol values are trusted configuration
       and are inserted as raw HTML. Never populate them from user content.</p>
</div>
@endsection
