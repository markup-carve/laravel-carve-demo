<?php

declare(strict_types=1);

namespace App\Models;

class Article
{
    public string $title = '';

    public string $body = '';

    public ?string $comment = null;
}
