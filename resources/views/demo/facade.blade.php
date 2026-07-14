@extends('layouts.app')

@section('title', 'Carve Facade Demo')

@php
    use MarkupCarve\LaravelCarve\Facades\Carve;
@endphp

@section('body')
<div class="card">
    <h1>Carve Facade Demo</h1>
    <p>The <code>Carve</code> facade is ideal for inline rendering and quick one-liners.</p>
</div>

<div class="card">
    <h2>Basic Usage</h2>
    <pre><code>&#123;!! Carve::toHtml('/emphasis/ and *strong*') !!&#125;</code></pre>
    <p><strong>Result:</strong></p>
    <div class="rendered">
        {!! Carve::toHtml('/emphasis/ and *strong*') !!}
    </div>
</div>

<div class="card">
    <h2>Inline Examples</h2>
    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f0f0f0;">
                <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Carve Input</th>
                <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Rendered Output</th>
            </tr>
        </thead>
        <tbody>
            @foreach ([
                '*strong text*',
                '_emphasized text_',
                '`inline code`',
                '[link](https://github.com/markup-carve/carve)',
                'H{,2,}O',
                'E=mc{^2^}',
                '{=highlighted=}',
                '{-deleted-} {+inserted+}',
            ] as $snippet)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><code>{{ $snippet }}</code></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><div class="rendered">{!! Carve::toHtml($snippet) !!}</div></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="card">
    <h2>Plain Text</h2>
    <pre><code>&#123;&#123; Carve::toText('# Hello *World*') &#125;&#125;</code></pre>
    <p><strong>Result:</strong> <code>{{ trim(Carve::toText('# Hello *World*')) }}</code></p>
</div>

<div class="card">
    <h2>With Converter Profile</h2>
    <p>Pass a converter name as the second argument to use a different profile:</p>
    <pre><code>&#123;!! Carve::toHtml($content, 'user_content') !!&#125;</code></pre>
    <p>See <a href="{{ route('safe_mode') }}">Safe Mode Demo</a> for XSS protection examples.</p>
</div>
@endsection
