<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthLog extends Model
{
    protected $fillable = [
        'user_id',
        'log_date',
        'heart_rate',
        'sleep_hours',
        'water_intake',
        'calories',
        'symptoms',
        'mood',
    ];
}