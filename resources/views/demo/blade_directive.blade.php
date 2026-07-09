@extends('layouts.app')

@section('title', 'Blade Directive Demo')

@section('body')
<div class="card">
    <h1>Blade Directive Demo</h1>
    <p>The <code>@@carve</code> directive converts Carve markup to HTML.</p>
</div>

<div class="card">
    <h2>Basic Usage: @@carve</h2>
    <pre><code>@@carve($content)</code></pre>

    <div class="columns">
        <div>
            <h3>Source (Carve)</h3>
            <pre><code>{{ $carve_content }}</code></pre>
        </div>
        <div>
            <h3>Rendered (HTML)</h3>
            <div class="rendered">
                @carve($carve_content)
            </div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Plain Text: @@carveText</h2>
    <pre><code>@@carveText($content)</code></pre>
    <p>Extracts plain text, useful for search indexing or excerpts:</p>
    <pre><code>@carveText($carve_content)</code></pre>
</div>

<div class="card">
    <h2>Safe Rendering for User Content</h2>
    <p>For user-submitted content, use a safe-mode converter profile via the facade:</p>
    <pre><code>&#123;!! Carve::toHtml($content, 'user_content') !!&#125;</code></pre>

    <div class="columns">
        <div>
            <h3>Source (User Content)</h3>
            <pre><code>{{ $user_content }}</code></pre>
        </div>
        <div>
            <h3>Rendered (Safe Mode)</h3>
            <div class="rendered safe">
                <span class="badge badge-safe">safe_mode: true</span>
                {!! \MarkupCarve\LaravelCarve\Facades\Carve::toHtml($user_content, 'user_content') !!}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Raw Directive</h2>
    <pre><code>@@carveRaw($trustedContent)</code></pre>
    <p>The <code>@@carveRaw</code> directive renders Carve <em>without</em> safe mode — use only for content you fully control.</p>
</div>
@endsection
