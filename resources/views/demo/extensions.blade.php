@extends('layouts.app')

@section('title', 'Extensions Demo')

@section('body')
<div class="card">
    <h1>Extensions Demo</h1>
    <p>Carve supports various extensions to enhance markup capabilities. All bundled markup-carve/carve-php extensions are available - autolink, mentions, table of contents, wikilinks, admonitions, tabs, code groups and more.</p>
</div>

<div class="card">
    <h2>Autolink</h2>
    <p>Automatically converts bare URLs to clickable links.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $autolink_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $autolink_html !!}</div>
        </div>
    </div>
    <pre><code>['type' => 'autolink']</code></pre>
</div>

<div class="card">
    <h2>External Links</h2>
    <p>Adds <code>target="_blank"</code> and <code>rel="noopener noreferrer"</code> to external links.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $external_links_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $external_links_html !!}</div>
        </div>
    </div>
    <pre><code>[
    'type' => 'external_links',
    'internal_hosts' => ['localhost', 'example.com'],
]</code></pre>
</div>

<div class="card">
    <h2>Smart Quotes</h2>
    <p>Converts straight quotes to typographic ("curly") quotes.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $smart_quotes_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $smart_quotes_html !!}</div>
        </div>
    </div>
    <pre><code>['type' => 'smart_quotes']</code></pre>
</div>

<div class="card">
    <h2>Heading Permalinks</h2>
    <p>Adds anchor links to headings for easy linking.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $heading_permalinks_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $heading_permalinks_html !!}</div>
        </div>
    </div>
    <pre><code>[
    'type' => 'heading_permalinks',
    'symbol' => '#',
    'position' => 'after',
    'class' => 'heading-anchor',
]</code></pre>
</div>

<div class="card">
    <h2>Mentions</h2>
    <p>Converts @username to clickable profile links.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $mentions_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $mentions_html !!}</div>
        </div>
    </div>
    <pre><code>[
    'type' => 'mentions',
    'mention_url' => 'https://github.com/{name}',
    'user_class' => 'mention',
]</code></pre>
</div>

<div class="card">
    <h2>Table of Contents</h2>
    <p>Generates a table of contents from headings. Place it with a <code>::: toc</code> block.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $toc_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $toc_html !!}</div>
        </div>
    </div>
    <pre><code>[
    'type' => 'table_of_contents',
    'toc_class' => 'toc',
]</code></pre>
</div>

<div class="card">
    <h2>Wikilinks</h2>
    <p>Supports <code>[[Page Name]]</code> wiki-style links.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $wikilinks_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $wikilinks_html !!}</div>
        </div>
    </div>
    <pre><code>[
    'type' => 'wikilinks',
    'url_template' => '/wiki/{page}',
    'link_class' => 'wiki-link',
]</code></pre>
</div>

<div class="card">
    <h2>Default Attributes</h2>
    <p>Automatically adds default attributes to elements by type (e.g., lazy loading for images).</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $default_attrs_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered (inspect HTML)</h3>
            <div class="rendered">{!! $default_attrs_html !!}</div>
        </div>
    </div>
    <pre><code>[
    'type' => 'default_attributes',
    'defaults' => [
        'image' => ['loading' => 'lazy', 'decoding' => 'async'],
        'table' => ['class' => 'table table-striped'],
        'link' => ['class' => 'styled-link'],
    ],
]</code></pre>
</div>

<div class="card">
    <h2>Frontmatter</h2>
    <p>Parses YAML/TOML/JSON frontmatter blocks at the start of documents.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $frontmatter_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered (check HTML source for comment)</h3>
            <div class="rendered">{!! $frontmatter_html !!}</div>
        </div>
    </div>
    <pre><code>[
    'type' => 'frontmatter',
    'default_format' => 'yaml',
    'render_as_comment' => true,
]</code></pre>
</div>

<div class="card">
    <h2>Semantic Spans</h2>
    <p>Converts span attributes to semantic HTML5 elements: <code>&lt;kbd&gt;</code>, <code>&lt;dfn&gt;</code>, <code>&lt;abbr&gt;</code>.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $semantic_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $semantic_html !!}</div>
        </div>
    </div>
    <pre><code>['type' => 'semantic_span']</code></pre>
</div>

<div class="card">
    <h2>Mermaid Diagrams</h2>
    <p>A <code>mermaid</code> fence renders as a diagram in the browser; in static
       mode the source is preserved (graceful degradation).</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $mermaid_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $mermaid_html !!}</div>
        </div>
    </div>
    <h3>Configuration</h3>
    <pre><code>'with_mermaid' => [
    'extensions' => [
        ['type' => 'mermaid'],
    ],
],</code></pre>
</div>

<div class="card">
    <h2>Code Group</h2>
    <p>Transforms code-group divs into tabbed code block interfaces. Click the tabs below!</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $code_group_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $code_group_html !!}</div>
        </div>
    </div>
    <pre><code>['type' => 'code_group']</code></pre>
</div>

<div class="card">
    <h2>Admonition</h2>
    <p>Styled callout blocks for notes, tips, warnings, etc.</p>
    <div class="columns">
        <div>
            <h3>Source</h3>
            <pre><code>{{ $admonition_source }}</code></pre>
        </div>
        <div>
            <h3>Rendered</h3>
            <div class="rendered">{!! $admonition_html !!}</div>
        </div>
    </div>
    <pre><code>['type' => 'admonition']</code></pre>
</div>
@endsection
