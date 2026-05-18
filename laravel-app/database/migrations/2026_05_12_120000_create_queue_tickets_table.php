<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('patient_name');
            $table->string('channel')->default('kiosk');
            $table->string('service_type')->default('BHYT');
            $table->unsignedSmallInteger('priority_level')->default(0);
            $table->unsignedInteger('ticket_number')->nullable();
            $table->string('status')->default('waiting');
            $table->unsignedSmallInteger('estimated_wait')->nullable();
            $table->text('notes')->nullable();
            $table->string('external_reference')->nullable();
            $table->timestamps();

            $table->index(['status', 'ticket_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};
