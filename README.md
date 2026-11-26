# Laravel Carve Demo

A runnable application demonstrating [markup-carve/laravel-carve](https://github.com/markup-carve/laravel-carve).

**[View the live demo →](https://markup-carve.github.io/laravel-carve-demo/)**

## Requirements

- PHP 8.4.1+ for the checked-in dependency lockfile
- PHP DOM extension for the HTML import example
- Composer

## Installation

```bash
# Clone the repository
git clone https://github.com/markup-carve/laravel-carve-demo.git
cd laravel-carve-demo

# Install dependencies
composer install
```

The `post-create-project-cmd` scripts already generate an app key and create the SQLite database file; if you clone instead of using `composer create-project`, run these manually:

```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --graceful
```

## Running the Demo

```bash
php artisan serve
```

Then open <http://127.0.0.1:8000> in your browser.

## Screenshots

See the [screenshot gallery](docs/screenshots/) for the home page, Includes, the Blade directive, and the diagram gallery.


## Demo Pages

| Route | Description |
|-------|-------------|
| `/` | Home: overview of all pages |
| `/blade-directive` | Using the `@carve` / `@carveRaw` / `@carveText` Blade directives |
| `/facade` | Using the `Carve` facade for inline rendering |
| `/authoring` | Rendered metadata, Eloquent casts, Blade components, static documents, the comment profile, imports and CLI tools |
| `/service` | Injecting `CarveConverterInterface` and `CarveManager` into services |
| `/form` | Form validation with the `ValidCarve` rule |
| `/safe-mode` | Raw HTML off and the URL policy for untrusted content |
| `/static-mode` | Interactive and static (print) rendering of the same source |
| `/plain-text` | Extracting plain text for search/excerpts |
| `/includes` | Trusted file-backed content, a named region, source and CSV data included as code, CSV records and columns picked with `@rows` and `@cols`, and configuration-backed snippets |
| `/extensions` | The extensions the demo's converter profiles enable, with their configuration |
| `/diagrams` | Eight fenced diagram languages hydrated in the browser, and sanitized SVG image fences |
| `/syntax` | Carve 0.2 syntax samples in canonical form |
| `/render-targets` | One source rendered as HTML, plain text, Markdown and ANSI |
| `/editor-preview` | Trusted symbols and source-line metadata for synchronized previews |
| `/options` | Profiles, render settings (URL policy, heading ids, sections, typography, labels, strict losses), `Str` macros, the parsed tree, `Carve::format()` and a custom output writer |
| `/extension-catalog` | Every extension name of `ExtensionFactory::types()`, each rendered from a sample |

## Features Demonstrated

### Blade Directives

```blade
{{-- Default profile: raw HTML off --}}
@carve($article->body)

{{-- Trusted content, raw HTML passthrough --}}
@carveRaw($trustedContent)

{{-- Plain text, HTML-escaped --}}
@carveText($article->body)
```

### Facade

```blade
{!! Carve::toHtml($content) !!}
{!! Carve::toHtml($userContent, 'user_content') !!}
{{ Carve::toText($content) }}
```

### Service Injection

```php
use MarkupCarve\LaravelCarve\Service\CarveConverterInterface;
use MarkupCarve\LaravelCarve\Service\CarveManager;

class ArticleController
{
    public function __construct(
        private CarveConverterInterface $carve,
        private CarveManager $manager,
    ) {}

    public function show(Article $article): View
    {
        return view('articles.show', [
            'html' => $this->carve->toHtml($article->body),
            'comment_html' => $this->manager->toHtml($article->comment, 'user_content'),
        ]);
    }
}
```

### Validation

```php
use MarkupCarve\LaravelCarve\Rules\ValidCarve;

$request->validate([
    'body' => ['required', 'string', new ValidCarve()],
    'comment' => ['nullable', 'string', new ValidCarve(strict: true)],
]);
```

### Multiple Converter Profiles

```php
// config/carve.php
return [
    'converters' => [
        'default' => [
            'raw_html' => true,
            'extensions' => [
                ['type' => 'autolink'],
                ['type' => 'smart_quotes'],
                ['type' => 'heading_permalinks'],
            ],
        ],
        'user_content' => [
            'raw_html' => false,
        ],
        'with_mentions' => [
            'social' => ['mentionUrl' => 'https://github.com/{name}'],
        ],
    ],
];
```

## Extensions

The demo's converter profiles in `config/carve.php` enable these Carve extensions:

- Autolink: Converts bare URLs to links
- Code Group: Tabbed code blocks (`::: code-group`)
- Default Attributes: Auto-applied `loading="lazy"`, etc.
- Details: Native `<details>`/`<summary>` disclosure blocks
- External Links: `target="_blank"` + `rel` attributes
- Heading Permalinks: Anchor links on headings
- Image Fence: `img` / `image` fences render sanitized SVG as a sandboxed `data:image/svg+xml` `<img>` (`img_fence`)
- PlantUML: `plantuml` / `puml` fenced diagrams (`plantuml`)
- Semantic Spans: `samp`, `var`, `cite` and `dfn` span keys
- Smart Quotes: Typographic (curly) quotes
- Table of Contents: placed at a `::: toc` block (`toc_placement`)
- Wikilinks: `:wiki[Page Name]` links through a URL template

Admonitions, frontmatter and `@mentions` are core Carve 0.2 syntax and need no
extension; mentions get their URL from the `social` setting.

Every extension name is listed at runtime by
`MarkupCarve\LaravelCarve\Service\ExtensionFactory::types()`; the
`/extension-catalog` page renders a sample for each.

### Runtime note: PlantUML rendering

The `plantuml` shorthand renders server-side to `<pre class="plantuml">` (the same
graceful-degradation shape as Mermaid); the diagram source is always visible even
with no client renderer. The `/diagrams` page hydrates that source into an image in
the browser using the public PlantUML server (`https://www.plantuml.com/plantuml/`),
so live diagram rendering needs outbound network access to that host (or a
self-hosted PlantUML server). Without it, the page still renders; the diagram
source simply stays as text. No PlantUML binary is required by the demo itself.

## Configuration

See `config/carve.php` for the full example configuration covering every profile used by the demo.

The `editor_preview` profile demonstrates the `symbols` and
`source_lines` options. Symbol values are trusted raw HTML configuration and
must never be populated from user input. Source-line mode adds 1-based
`data-source-line` attributes to rendered blocks for editor scroll sync.
The demo also enables the self-contained `array` cache store; its feature tests
render identical source through the default and trusted profiles to prove cached HTML
cannot leak between converter configurations.

## Authoring examples

On `main` Composer installs laravel-carve and carve-php from their `next2`
branches (Carve 0.2, unreleased); the lockfile pins the commits used by the demo
and static export. The `0.1` branch keeps the 0.1 demo on published packages.

The `/authoring` page runs `Carve::render()` and displays JSON frontmatter and
collected headings. An unsaved `CarvePost` model demonstrates `AsCarve` without
a database write, and `<x-carve>` renders its value. The static document example
uses `view()->file()` under `resources/carve`, inside the configured include root.

The comment converter uses the Carve `comment` profile with raw HTML off. Live
validation examples demonstrate forbidden headings, an 80-character limit and
lint findings for Markdown spelling, and a `%% carve-lint-disable-next-line`
comment silences one of them. Markdown and HTML imports show the source they
produce and its output through the default converter; with the `gfm` flavor a
GitHub alert blockquote imports as an admonition.

Run the file tools locally:

```sh
php artisan carve:render resources/carve/authoring-guide.crv --format=html
php artisan carve:render resources/carve/authoring-guide.crv --format=text
php artisan carve:convert README.md --output=storage/app/readme.crv
php artisan carve:lint resources/carve --include-root=resources/carve
php artisan carve:fmt resources/carve --check
```

The Authoring page also appears in the static export. Its results are generated
when exporting; the CLI examples run only in a local checkout.

## Ecosystem

Part of the [Carve organization](https://github.com/markup-carve). See
[awesome-carve](https://github.com/markup-carve/awesome-carve) for the full
list of implementations, editor plugins and integrations.
