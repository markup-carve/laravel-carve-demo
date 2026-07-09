@extends('layouts.app')

@section('title', 'Safe Mode Demo')

@section('body')
<div class="card">
    <h1>Safe Mode Demo</h1>
    <p>Safe mode escapes raw HTML tags in user input, preventing script injection attacks.</p>
    <p><strong>Protects against:</strong> <code>&lt;script&gt;</code>, <code>&lt;img onerror&gt;</code>, <code>&lt;div onmouseover&gt;</code>, and other HTML injection.</p>
</div>

<div class="card">
    <h2>Malicious Input</h2>
    <pre><code>{{ $source }}</code></pre>
</div>

<div class="columns">
    <div class="card">
        <h2><span class="badge badge-unsafe">UNSAFE</span> Default Profile</h2>
        <pre><code>'safe_mode' => false</code></pre>
        <div class="warning">
            <strong>Warning:</strong> Raw HTML passes through!
        </div>
        <h3>Raw HTML Output:</h3>
        <pre><code>{{ $unsafe_html }}</code></pre>
    </div>

    <div class="card">
        <h2><span class="badge badge-safe">SAFE</span> user_content Profile</h2>
        <pre><code>'safe_mode' => true</code></pre>
        <div class="safe">
            <strong>Protected:</strong> HTML is escaped, scripts neutralized.
        </div>
        <h3>Rendered Output:</h3>
        <div class="rendered">
            {!! $safe_html !!}
        </div>
    </div>
</div>

<div class="card">
    <h2>Configuration</h2>
    <pre><code>// config/carve.php
return [
    'converters' => [
        'default' => [
            'safe_mode' => false, // For trusted content (admin, CMS)
        ],
        'user_content' => [
            'safe_mode' => true,  // For user-submitted content
        ],
    ],
];</code></pre>

    <h3>Usage in Blade</h3>
    <pre><code>{{-- Trusted content - default converter --}}
@@carve($article->body)

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
