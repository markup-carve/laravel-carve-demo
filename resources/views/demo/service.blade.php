@extends('layouts.app')

@section('title', 'Service Demo')

@section('body')
<div class="card">
    <h1>Service Injection Demo</h1>
    <p>Inject <code>CarveConverterInterface</code> or <code>CarveManager</code> directly into your controllers and services.</p>
</div>

<div class="card">
    <h2>Default Converter</h2>
    <pre><code>use MarkupCarve\LaravelCarve\Service\CarveConverterInterface;

public function show(CarveConverterInterface $carve): View
{
    $html = $carve->toHtml($content);
    $text = $carve->toText($content);
}</code></pre>

    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $carve_source }}</code></pre>
        </div>
        <div>
            <h3>toHtml() Output</h3>
            <div class="rendered">
                {!! $html !!}
            </div>
        </div>
    </div>

    <h3>toText() Output</h3>
    <pre><code>{{ $text }}</code></pre>
</div>

<div class="card">
    <h2>Named Converter via CarveManager (Safe Mode)</h2>
    <pre><code>use MarkupCarve\LaravelCarve\Service\CarveManager;

public function show(CarveManager $manager): View
{
    $html = $manager->toHtml($userContent, 'user_content');
}</code></pre>

    <div class="columns">
        <div>
            <h3>Source (User Content)</h3>
            <pre><code>{{ $user_source }}</code></pre>
        </div>
        <div>
            <h3>Safe Mode Output</h3>
            <div class="rendered safe">
                <span class="badge badge-safe">XSS Protected</span>
                {!! $safe_html !!}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Configuration</h2>
    <pre><code>// config/carve.php
return [
    'converters' => [
        'default' => [
            'safe_mode' => false,
        ],
        'user_content' => [
            'safe_mode' => true,
        ],
    ],
    'cache' => [
        'enabled' => true,
        'store' => null,
    ],
];</code></pre>
</div>
@endsection
