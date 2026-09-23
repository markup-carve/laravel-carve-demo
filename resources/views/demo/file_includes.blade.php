@extends('layouts.app')

@section('title', 'Includes')

@section('body')
<div class="card">
    <h1>Includes</h1>
    <p>Includes can read trusted files or resolve application-provided snippets. Both paths remain explicit.</p>
    <h2>File-backed content</h2>
    <p><code>toHtmlFileWithReport()</code> expands content inside the configured root.</p>
    <pre><code>{{ $file_source }}</code></pre>
    <h2>Rendered result</h2>
    <div class="rendered">{!! $file_html !!}</div>
    <h2>Dependencies</h2>
    <ul>@foreach ($file_dependencies as $dependency)<li><code>{{ $dependency['path'] }}</code>: {{ $dependency['resolved'] ? 'resolved' : 'missing' }}</li>@endforeach</ul>
    @if ($file_warnings !== [])<h2>Warnings</h2><ul>@foreach ($file_warnings as $warning)<li>{{ $warning['message'] }}</li>@endforeach</ul>@endif

    <h2>Configuration-backed snippets</h2>
    <p>A custom resolver reads this trusted application configuration. The same resolver seam can query a database or another content store.</p>
    <pre><code>{{ $dynamic_source }}</code></pre>
    <h3>Rendered result</h3>
    <div class="rendered">{!! $dynamic_html !!}</div>
    <h3>Resolved snippets</h3>
    <ul>@foreach ($dynamic_dependencies as $dependency)<li><code>{{ $dependency->getTarget() }}</code>: {{ $dependency->isResolved() ? 'resolved' : 'missing' }}</li>@endforeach</ul>
    @if ($dynamic_warnings !== [])<h3>Warnings</h3><ul>@foreach ($dynamic_warnings as $warning)<li>{{ $warning->getMessage() }}</li>@endforeach</ul>@endif
</div>
@endsection
