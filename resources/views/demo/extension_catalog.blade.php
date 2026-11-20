@extends('layouts.app')

@section('title', 'Extension Catalog')

@section('body')
<div class="card">
    <h1>Extension Catalog</h1>
    <p>Every extension name that a profile's <code>extensions</code> list accepts, from <code>ExtensionFactory::types()</code>, rendered with a sample. A name is enough; an array passes options by their snake_case factory parameter names.</p>
    <pre><code>'extensions' => [
    'details',
    ['type' => 'heading_levels', 'offset' => 1],
],</code></pre>
    <p>{{ count($types) }} names: @foreach ($types as $type)<a href="#ext-{{ $type }}"><code>{{ $type }}</code></a>@if (!$loop->last), @endif @endforeach</p>
</div>

@foreach ($entries as $name => $entry)
<div class="card" id="ext-{{ $name }}">
    <h2><code>{{ $name }}</code></h2>
    <p>{{ $entry['blurb'] }}</p>
    <pre><code>{{ var_export($entry['config'], true) }}</code></pre>
    <div class="columns">
        <div><h3>Source</h3><pre><code>{{ $entry['source'] }}</code></pre></div>
        <div><h3>Rendered</h3><div class="rendered">{!! $entry['html'] !!}</div></div>
    </div>
</div>
@endforeach
@endsection
