@extends('layouts.app')

@section('title', 'Safe Rendering Demo')

@section('body')
<div class="card">
    <h1>Safe Rendering Demo</h1>
    <p>Carve 0.2 keeps raw HTML off by default (<code>raw_html</code> is false), so raw HTML in user input is escaped. The default URL policy also drops unsafe schemes such as <code>javascript:</code> from links and image sources, whether or not raw HTML is on.</p>
    <p><strong>Protects against:</strong> <code>&lt;script&gt;</code>, <code>&lt;img onerror&gt;</code>, <code>&lt;div onmouseover&gt;</code>, and other HTML injection.</p>
</div>

<div class="card">
    <h2>Malicious Input</h2>
    <pre><code>{{ $source }}</code></pre>
</div>

<div class="columns">
    <div class="card">
        <h2><span class="badge badge-unsafe">UNSAFE</span> trusted Profile</h2>
        <pre><code>'raw_html' => true</code></pre>
        <div class="warning">
            <strong>Warning:</strong> Raw HTML passes through!
        </div>
        <h3>Raw HTML Output:</h3>
        <pre><code>{{ $unsafe_html }}</code></pre>
    </div>

    <div class="card">
        <h2><span class="badge badge-safe">SAFE</span> user_content Profile</h2>
        <pre><code>'raw_html' => false</code></pre>
        <div class="safe">
            <strong>Protected:</strong> HTML is escaped, scripts neutralized.
        </div>
        <h3>Rendered Output:</h3>
        <div class="rendered carve">
            {!! $safe_html !!}
        </div>
    </div>
</div>

<div class="card">
    <h2>Configuration</h2>
    <pre><code>// config/carve.php
return [
    'converters' => [
        'trusted' => [
            'raw_html' => true,  // For trusted content (admin, CMS)
        ],
        'user_content' => [
            'raw_html' => false, // For user-submitted content (the default)
        ],
    ],
];</code></pre>

    <h3>Usage in Blade</h3>
    <pre><code>{{-- Trusted content - explicitly selected converter --}}
&#123;!! Carve::toHtml($article->body, 'trusted') !!&#125;

{{-- User content - safe converter via facade --}}
&#123;!! Carve::toHtml($comment->body, 'user_content') !!&#125;</code></pre>

    <h3>Usage in Services</h3>
    <pre><code>use MarkupCarve\LaravelCarve\Service\CarveManager;

public function render(CarveManager $carve, string $content): string
{
    return $carve->toHtml($content, 'user_content');
}</code></pre>
</div>
@endsection
