@extends('layouts.app')

@section('title', 'Authoring Demo')

@section('body')
<div class="card">
    <h1>Authoring in Laravel</h1>
    <p>Store original Carve source, read rendered values and validate content before saving.</p>
</div>

<div class="card">
    <h2>Rendered values and metadata</h2>
    <pre><code>$document = Carve::render($source);
$document->html;
$document->toc();
$document->meta('title');</code></pre>
    <p>Metadata title: <strong>{{ $document->meta('title') }}</strong>. Author: {{ $document->meta('author') }}.</p>
    <div class="columns">
        <div><h3>Original source</h3><pre><code>{{ $document->source }}</code></pre></div>
        <div><h3>Rendered HTML</h3><pre><code>{{ $document->html }}</code></pre></div>
    </div>
    <h3>Collected headings</h3>
    <ul>
        @foreach ($document->toc() as $heading)
            <li>Level {{ $heading['level'] }}: {{ $heading['text'] }} ({{ $heading['id'] }})</li>
        @endforeach
    </ul>
</div>

<div class="card">
    <h2>Eloquent cast and Blade component</h2>
    <p><code>AsCarve</code> keeps source in the model attribute and returns a <code>RenderedCarve</code> value when read. This example uses an unsaved model.</p>
    <pre><code>use MarkupCarve\LaravelCarve\Casts\AsCarve;

protected function casts(): array
{
    return ['body' => AsCarve::class];
}

$post = new CarvePost(['body' => $source]);
&lt;x-carve :source="$post->body" class="rendered" /&gt;</code></pre>
    <h3>Stored attribute</h3>
    <pre><code>{{ $post->getAttributes()['body'] }}</code></pre>
    <h3>Component output</h3>
    <x-carve :source="$post->body" class="rendered" />
</div>

<div class="card">
    <h2>Static document views</h2>
    <p>A <code>.crv</code> view renders a static document. View variables are not interpolated. With includes enabled, the file must be inside the trusted include root.</p>
    <pre><code>view()->file(resource_path('carve/authoring-guide.crv'));</code></pre>
    <div class="rendered">{!! $document_view !!}</div>
</div>

<div class="card">
    <h2>Comment preset and validation</h2>
    <p>The <code>comment</code> profile uses strict safe mode and the comment preset. Disallowed markup becomes text for rendering; validation rejects it before saving.</p>
    <pre><code>'comment' => [
    'safe_mode' => 'strict',
    'preset' => 'comment',
    'on_disallowed' => 'to_text',
]</code></pre>
    <div class="columns">
        <div><h3>Source</h3><pre><code>{{ $comment_source }}</code></pre></div>
        <div><h3>Restricted output</h3><div class="rendered">{{ $comment }}</div></div>
    </div>
    <pre><code>ValidCarve::preset('comment')->maxLength(80)->lint()</code></pre>
    <p>The limit counts characters. Eighty accented characters pass; 81 exceed the limit.</p>
    <div class="rendered validation-results">
    <table>
        <thead><tr><th>Sample</th><th>Source</th><th>Validation result</th></tr></thead>
        <tbody>
            @foreach ($validation as $sample)
                <tr>
                    <td>{{ $sample['label'] }}</td>
                    <td><code>{{ $sample['source'] }}</code></td>
                    <td>
                        {{ $sample['accepted'] ? 'Accepted' : 'Rejected' }}
                        @foreach ($sample['errors'] as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

<div class="card">
    <h2>Import Markdown and HTML</h2>
    <pre><code>$source = Carve::fromMarkdown($markdown);
$source = Carve::fromHtml($html);
$document = Carve::render($source);</code></pre>
    <p>Imports return Carve source. The examples render it through the safe default converter.</p>
    @foreach ($imports as $format => $import)
        <h3>{{ $format }}</h3>
        <div class="columns">
            <div><h4>Input</h4><pre><code>{{ $import['input'] }}</code></pre></div>
            <div><h4>Carve source</h4><pre><code>{{ $import['source'] }}</code></pre></div>
        </div>
        <div class="rendered">{{ $import['rendered'] }}</div>
    @endforeach
</div>

<div class="card">
    <h2>Lint and Artisan tools</h2>
    <pre><code>Carve::lint({{ var_export($lint_source, true) }});</code></pre>
    <ul>
        @foreach ($lint_findings as $finding)
            <li>Line {{ $finding->line }}, column {{ $finding->column }}: {{ $finding->message }} ({{ $finding->rule }})</li>
        @endforeach
    </ul>
    <p>Run these commands in a local checkout. They render, convert or lint files on disk.</p>
    <pre><code>php artisan carve:render resources/carve/authoring-guide.crv --format=html
php artisan carve:render resources/carve/authoring-guide.crv --format=text
php artisan carve:convert README.md --output=storage/app/readme.crv
php artisan carve:lint resources/carve</code></pre>
</div>
@endsection
