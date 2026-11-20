@extends('layouts.app')

@section('title', 'Profiles and Options')

@section('body')
<div class="card">
    <h1>Profiles and Options</h1>
    <p>Every converter profile in <code>config/carve.php</code> maps its snake_case settings to Carve 0.2 render options. This page renders one source under each setting and shows the other adapter APIs: the <code>Str</code> macros, the parsed tree, the formatter and a custom output writer.</p>
    <pre><code>Carve::profiles();          // {{ implode(', ', $profiles) }}
Carve::getDefaultProfile(); // {{ $default_profile }}</code></pre>
</div>

<div class="card">
    <h2>Render settings</h2>
    <p>The default profile renders the source like this:</p>
    <div class="columns">
        <div><h3>Source</h3><pre><code>{{ $source }}</code></pre></div>
        <div><h3>Default profile</h3><div class="rendered">{!! $default_html !!}</div></div>
    </div>
    <pre><code>use MarkupCarve\LaravelCarve\Service\CarveConverter;
use MarkupCarve\LaravelCarve\Service\RenderPolicy;

$converter = new CarveConverter(options: RenderPolicy::options($settings));</code></pre>
    @foreach ($settings as $key => $setting)
        <h3 id="setting-{{ $key }}">{{ $setting['title'] }}</h3>
        <p>{{ $setting['blurb'] }}</p>
        <div class="columns">
            <div><pre><code>{{ var_export($setting['config'], true) }}</code></pre></div>
            <div>
                @if ($setting['error'] !== null)
                    <p class="setting-error"><code>{{ $setting['error'] }}</code></p>
                @else
                    <pre><code>{{ $setting['html'] }}</code></pre>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <h2>Str and Stringable macros</h2>
    <pre><code>Str::carve('*Str* macro');
Str::of('/Stringable/ macro')->carveText();</code></pre>
    <div class="rendered">{!! $str_html !!}</div>
    <p><code>{{ trim($str_text) }}</code></p>
</div>

<div class="card">
    <h2>Parsed tree</h2>
    <p><code>Carve::parse()</code> returns the Carve 0.2 AST as <code>MarkupCarve\Carve\Node</code> objects.</p>
    <pre><code>Carve::parse("= Title\n\nA *strong* word.\n");</code></pre>
    <pre><code>{{ implode("\n", $tree) }}</code></pre>
</div>

<div class="card">
    <h2>Canonical formatting</h2>
    <p><code>Carve::format()</code> prints the canonical source that <code>carve fmt</code> writes. Here a Markdown-style heading, a spaced fence language and unpadded table cells take their canonical Carve 0.2 spelling.</p>
    <div class="columns">
        <div><h3>Input</h3><pre><code>{{ $messy_source }}</code></pre></div>
        <div><h3>Carve::format()</h3><pre><code>{{ $formatted_source }}</code></pre></div>
    </div>
    <p>The <code>carve:fmt</code> Artisan command checks or rewrites files; the demo's own <code>resources/carve</code> sources pass the check.</p>
    <pre><code>php artisan carve:fmt resources/carve --check
php artisan carve:fmt resources/carve --write --align-tables</code></pre>
</div>

<div class="card">
    <h2>Custom output writer</h2>
    <p>An <code>OutputWriter</code> adds a target next to HTML, plain text, Markdown and ANSI. <code>App\Carve\OutlineWriter</code> prints the heading outline; <code>renderFileAs()</code> writes a file with it.</p>
    <pre><code>$converter = new CarveConverter();
$converter->registerWriter(new OutlineWriter());
$converter->renderFileAs(resource_path('carve/authoring-guide.crv'), 'outline');</code></pre>
    <pre><code>{{ $outline }}</code></pre>
    <p>A profile can list writer instances under <code>writers</code>, or a customizer can call <code>registerWriter()</code> through <code>Carve::extend()</code>.</p>
</div>
@endsection
