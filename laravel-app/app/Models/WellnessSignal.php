<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WellnessSignal extends Model
{
    protected $fillable = [
        'user_id',
        'recorded_on',
        'focus_minutes',
        'sleep_hours',
        'mood_score',
        'water_cups',
        'screen_time_minutes',
        'energy_level',
        'reflection',
    ];

    protected function casts(): array
    {
        return [
            'recorded_on' => 'date',
            'focus_minutes' => 'integer',
            'sleep_hours' => 'float',
            'mood_score' => 'integer',
            'water_cups' => 'integer',
            'screen_time_minutes' => 'integer',
            'energy_level' => 'integer',
        ];
    }
}
