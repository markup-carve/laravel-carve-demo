@extends('layouts.app')

@section('title', 'Code Line Options')

@section('body')
<div class="card">
    <h1>Code line options</h1>
    <p>Three attributes on the line above a fence: <code>.line-numbers</code>, <code>data-line-start</code> and
       <code>data-highlight</code>. laravel-carve passes them to <code>&lt;pre&gt;</code> unchanged. This page
       highlights with highlight.js in the browser, then a small script splits the lines, writes the gutter and
       marks the selected lines.</p>
    <p><code>data-highlight</code> takes 1-based numbers and <code>a-b</code> ranges, counted from the first line
       of the block, not from <code>data-line-start</code>. A caption keeps the options on <code>&lt;pre&gt;</code>.</p>
</div>

<div class="card">
    <h2>Rendered</h2>
    <div class="rendered carve">{!! $html !!}</div>
</div>

<div class="columns">
    <div class="card">
        <h2>Source</h2>
        <pre><code>{{ $source }}</code></pre>
    </div>
    <div class="card">
        <h2>HTML from the engine</h2>
        <p>The line options stay on <code>&lt;pre&gt;</code>; the line spans come from the browser script.</p>
        <pre><code>{{ $html }}</code></pre>
    </div>
</div>
@endsection
