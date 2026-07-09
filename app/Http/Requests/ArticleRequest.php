<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MarkupCarve\LaravelCarve\Rules\ValidCarve;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', new ValidCarve()],
            'comment' => ['nullable', 'string', new ValidCarve(strict: true)],
        ];
    }
}
