<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wellness_signals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('recorded_on')->index();
            $table->unsignedSmallInteger('focus_minutes')->default(0);
            $table->decimal('sleep_hours', 4, 1);
            $table->unsignedTinyInteger('mood_score');
            $table->unsignedTinyInteger('water_cups')->nullable();
            $table->unsignedSmallInteger('screen_time_minutes')->nullable();
            $table->unsignedTinyInteger('energy_level')->nullable();
            $table->text('reflection')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellness_signals');
    }
};
