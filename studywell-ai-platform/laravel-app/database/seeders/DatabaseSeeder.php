<?php

namespace Database\Seeders;

use App\Models\WellnessSignal;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        collect(range(13, 0))->each(function (int $daysAgo): void {
            WellnessSignal::create([
                'recorded_on' => now()->subDays($daysAgo)->toDateString(),
                'focus_minutes' => fake()->numberBetween(90, 360),
                'sleep_hours' => fake()->randomFloat(1, 5.5, 8.5),
                'mood_score' => fake()->numberBetween(4, 9),
                'water_cups' => fake()->numberBetween(3, 10),
                'screen_time_minutes' => fake()->numberBetween(180, 620),
                'energy_level' => fake()->numberBetween(3, 9),
                'reflection' => fake()->optional()->sentence(),
            ]);
        });
    }
}
