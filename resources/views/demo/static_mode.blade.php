@extends('layouts.app')

@section('title', 'Static Mode Demo')

@section('body')
<div class="card">
    <h1>Static Mode (Graceful Degradation)</h1>
    <p>In a script-free target such as print, PDF, or email, interactive constructs
       keep their content and structure while dropping the interaction. The examples
       below include a disclosure and a code group.</p>
    <p>Switch a converter profile to static mode with one config line:</p>
    <pre><code>'mode' => 'static'</code></pre>
</div>

<div class="card">
    <h2>Source</h2>
    <pre><code>{{ $source }}</code></pre>
</div>

<div class="columns">
    <div class="card">
        <h2>Interactive Profile (default)</h2>
        <pre><code>'mode' => 'interactive'</code></pre>
        <h3>Rendered:</h3>
        <div class="rendered">{!! $interactive_html !!}</div>
        <h3>HTML:</h3>
        <pre><code>{{ $interactive_html }}</code></pre>
    </div>

    <div class="card">
        <h2>Static Profile</h2>
        <pre><code>'mode' => 'static'</code></pre>
        <h3>Rendered:</h3>
        <div class="rendered">{!! $static_html !!}</div>
        <h3>HTML:</h3>
        <pre><code>{{ $static_html }}</code></pre>
    </div>
</div>

<div class="card">
    <h2>Configuration</h2>
    <pre><code>// config/carve.php
'converters' => [
    'print' => [
        'safe_mode' => true,
        'mode' => 'static',
        'extensions' => ['details', 'code_group'],
    ],
],</code></pre>
    <p>Then render with the named profile:</p>
    <pre><code>Carve::toHtml($article->body, 'print');</code></pre>
</div>
@endsection
