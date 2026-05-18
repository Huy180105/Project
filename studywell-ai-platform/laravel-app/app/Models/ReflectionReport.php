<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReflectionReport extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'summary',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }
}
