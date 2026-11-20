<?php

declare(strict_types=1);

namespace App\Carve;

use MarkupCarve\Carve\Include\IncludeResolver;

/**
 * Serves include directives from snippets kept in configuration instead of files.
 */
final class ConfigIncludeResolver implements IncludeResolver
{
    /** @var array<string, array{path: string, resolved: bool}> */
    private array $dependencies = [];

    /** @param array<string, string> $snippets */
    public function __construct(private readonly array $snippets)
    {
    }

    /**
     * @param array{path: string, options: array<mixed>, section?: mixed, from?: string, budget: int} $request
     *
     * @return array{text: string, id: string}|array{error: string}
     */
    public function resolve(array $request): array
    {
        $path = $request['path'];
        $source = $this->snippets[$path] ?? null;
        $this->dependencies['config:' . $path] = ['path' => 'config:' . $path, 'resolved' => $source !== null];

        return $source === null ? ['error' => 'not_found'] : ['text' => $source, 'id' => 'config:' . $path];
    }

    /** @return list<array{path: string, resolved: bool}> */
    public function dependencies(): array
    {
        return array_values($this->dependencies);
    }
}
