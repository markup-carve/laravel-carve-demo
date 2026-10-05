<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CarvePost;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use MarkupCarve\LaravelCarve\Facades\Carve;
use MarkupCarve\LaravelCarve\Rules\ValidCarve;

class AuthoringController extends Controller
{
    public function __invoke(): View
    {
        $source = <<<'CARVE'
        ---json
        {"title": "Release notes", "author": "Demo editor"}
        ---

        ## Release notes

        Store the /source/ and render it when needed.

        ### Review checklist

        - Check the original markup.
        - Read the rendered output.
        CARVE;
        $post = new CarvePost(['body' => $source]);
        $commentSource = "# Reader heading\n\nThanks for the /clear/ explanation.";
        $lintSource = 'Use **bold** in Markdown.';
        $markdown = "## Imported Markdown\n\nA **bold** word and an *italic* word.";
        $html = '<h2>Imported HTML</h2><p>A <strong>bold</strong> word.</p>';
        $imports = [
            'Markdown' => ['input' => $markdown, 'source' => Carve::fromMarkdown($markdown)],
            'HTML' => ['input' => $html, 'source' => Carve::fromHtml($html)],
        ];
        foreach ($imports as &$import) {
            $import['rendered'] = Carve::render($import['source']);
        }
        unset($import);

        $validation = [];
        foreach ([
            'Comment' => 'Thanks for the /clear/ explanation.',
            'Heading' => '# A heading is not comment markup',
            'Character boundary' => str_repeat('é', 80),
            'Character limit' => str_repeat('é', 81),
            'Markdown spelling' => $lintSource,
        ] as $label => $input) {
            $validator = Validator::make(['body' => $input], [
                'body' => ['required', 'string', ValidCarve::preset('comment')->maxLength(80)->lint()],
            ]);
            $validation[] = [
                'label' => $label,
                'source' => $input,
                'accepted' => $validator->passes(),
                'errors' => $validator->errors()->all(),
            ];
        }

        return view('demo.authoring', [
            'document' => Carve::render($source),
            'post' => $post,
            'document_view' => view()->file(resource_path('carve/authoring-guide.crv'))->render(),
            'comment_source' => $commentSource,
            'comment' => Carve::render($commentSource, 'comment'),
            'validation' => $validation,
            'imports' => $imports,
            'lint_source' => $lintSource,
            'lint_findings' => Carve::lint($lintSource),
        ]);
    }
}
