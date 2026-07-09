@extends('layouts.app')

@section('title', 'Plain Text Demo')

@section('body')
<div class="card">
    <h1>Plain Text Extraction</h1>
    <p>Extract plain text from Carve markup for search indexing, excerpts, or previews.</p>
</div>

<div class="columns">
    <div class="card">
        <h2>Source (Carve)</h2>
        <pre><code>{{ $source }}</code></pre>
    </div>

    <div class="card">
        <h2>Plain Text Output</h2>
        <pre><code>{{ $text }}</code></pre>
    </div>
</div>

<div class="card">
    <h2>Usage</h2>

    <h3>In Blade</h3>
    <pre><code>{{-- Directive (HTML-escaped) --}}
@@carveText($article->body)

{{-- Via facade --}}
&#123;&#123; Carve::toText($article->body) &#125;&#125;</code></pre>

    <h3>In Services</h3>
    <pre><code>$text = $carve->toText($carveContent);

// Use for search indexing
Scout::index($article->id, ['content' => $text]);

// Use for excerpts
$excerpt = Str::limit($text, 200);</code></pre>
</div>

<div class="card">
    <h2>Use Cases</h2>
    <ul>
        <li><strong>Search indexing</strong> — Index plain text for full-text search (Laravel Scout, Meilisearch, Algolia)</li>
        <li><strong>Excerpts</strong> — Generate article previews without markup</li>
        <li><strong>Meta descriptions</strong> — Auto-generate SEO descriptions</li>
        <li><strong>Notifications</strong> — Send plain text emails or SMS</li>
        <li><strong>Accessibility</strong> — Provide alt text or screen reader content</li>
    </ul>
</div>
@endsection
