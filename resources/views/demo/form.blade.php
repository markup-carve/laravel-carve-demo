@extends('layouts.app')

@section('title', 'Form Demo')

@section('body')
<div class="card">
    <h1>Form Validation Demo</h1>
    <p>Use the <code>ValidCarve</code> rule in Form Requests to validate Carve markup.</p>
</div>

<div class="columns">
    <div class="card">
        <h2>Article Form</h2>

        <form method="POST" action="{{ route('form.submit') }}">
            @csrf

            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title', $article->title) }}" placeholder="Enter article title">
            @error('title')<div class="error">{{ $message }}</div>@enderror

            <label for="body">Body (Carve markup)</label>
            <textarea id="body" name="body" rows="10" placeholder="# My Article&#10;&#10;Write your content using *Carve* markup...">{{ old('body', $article->body) }}</textarea>
            @error('body')<div class="error">{{ $message }}</div>@enderror
            <small style="color: #666;">Supports <a href="https://github.com/markup-carve/carve" target="_blank">Carve markup</a></small>

            <label for="comment">Comment (strict Carve validation, safe mode preview)</label>
            <textarea id="comment" name="comment" rows="4" placeholder="Optional comment with strict Carve validation">{{ old('comment', $article->comment) }}</textarea>
            @error('comment')<div class="error">{{ $message }}</div>@enderror
            <small style="color: #666;">Uses strict validation and the <code>user_content</code> profile</small>

            <button type="submit">Preview Article</button>
        </form>
    </div>

    <div class="card">
        <h2>Code</h2>

        <h3>Form Request</h3>
        <pre><code>use MarkupCarve\LaravelCarve\Rules\ValidCarve;

class ArticleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', new ValidCarve()],
            'comment' => ['nullable', 'string', new ValidCarve(strict: true)],
        ];
    }
}</code></pre>

        <h3>Blade Render</h3>
        <pre><code>{{-- Trusted content --}}
@@carve($article->body)

{{-- User content (safe mode) --}}
&#123;!! Carve::toHtml($comment, 'user_content') !!&#125;</code></pre>
    </div>
</div>

@if ($preview)
<div class="card">
    <h2>Preview</h2>

    <h3>{{ $preview['title'] }}</h3>

    <div class="rendered">
        {!! $preview['body_html'] !!}
    </div>

    @if ($preview['comment_html'])
    <h4>Comment</h4>
    <div class="rendered safe">
        <span class="badge badge-safe">safe mode</span>
        {!! $preview['comment_html'] !!}
    </div>
    @endif
</div>
@endif
@endsection
