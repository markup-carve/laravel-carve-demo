@extends('layouts.app')

@section('title', 'File Includes')

@section('body')
<div class="card">
    <h1>File Includes</h1>
    <p>Only the explicit file API expands includes. The configured root keeps every target contained.</p>
    <pre><code>{{ $source }}</code></pre>
    <h2>Rendered result</h2>
    <div class="rendered">{!! $html !!}</div>
    <h2>Dependencies</h2>
    <ul>@foreach ($dependencies as $dependency)<li><code>{{ $dependency['path'] }}</code> — {{ $dependency['resolved'] ? 'resolved' : 'missing' }}</li>@endforeach</ul>
    @if ($warnings !== [])<h2>Warnings</h2><ul>@foreach ($warnings as $warning)<li>{{ $warning['message'] }}</li>@endforeach</ul>@endif
</div>
@endsection
