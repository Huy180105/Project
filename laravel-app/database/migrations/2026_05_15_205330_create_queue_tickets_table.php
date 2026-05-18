<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Queue tickets are created by 2026_05_12_120000_create_queue_tickets_table.
        // This migration is intentionally kept as a no-op because a duplicate table
        // creation breaks fresh test databases and existing installations.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
