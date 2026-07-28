@extends('layouts.app')

@section('title', 'Carve Syntax & What is New')

@section('body')
<div class="card">
    <h1>Carve Syntax &amp; What is New</h1>
    <p>Carve is a post-Markdown markup language: one syntax, one meaning, the same HTML
       from every implementation. This page shows the newest addition -
       <strong>inline literals</strong> - alongside the convergence behaviours worth
       knowing when you author Carve. Every sample below is rendered live through the
       <code>syntax</code> converter profile.</p>
</div>

@foreach ($samples as $sample)
<div class="card">
    <h2>{{ $sample['title'] }}</h2>
    <p>{{ $sample['blurb'] }}</p>
    <div class="columns">
        <div>
            <h3>Carve source</h3>
            <pre><code>{{ $sample['source'] }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $sample['html'] !!}</div>
        </div>
    </div>
</div>
@endforeach
@endsection
