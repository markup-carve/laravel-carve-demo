<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MarkupCarve\LaravelCarve\Casts\AsCarve;

class CarvePost extends Model
{
    protected $fillable = ['body'];

    protected function casts(): array
    {
        return ['body' => AsCarve::class];
    }
}
