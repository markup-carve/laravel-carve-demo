<?php

declare(strict_types=1);

namespace App\Carve;

use MarkupCarve\Carve\Carve;
use MarkupCarve\Carve\Node;
use MarkupCarve\Carve\Output\OutputWriter;

/**
 * A host output target: the heading outline of a document, one indented line per heading.
 */
final class OutlineWriter implements OutputWriter
{
    public function target(): string
    {
        return 'outline';
    }

    /**
     * @param array<string, mixed> $options
     */
    public function write(Node $tree, array $options = []): string
    {
        $lines = [];
        $visit = static function (Node $node) use (&$visit, &$lines): void {
            if ($node->type === 'heading') {
                $level = (int) ($node->f['level'] ?? 1);
                $lines[] = str_repeat('  ', $level - 1).'- '.Carve::plainText($node->children ?? []);
            }
            foreach ($node->children ?? [] as $child) {
                $visit($child);
            }
        };
        $visit($tree);

        return implode("\n", $lines)."\n";
    }
}
