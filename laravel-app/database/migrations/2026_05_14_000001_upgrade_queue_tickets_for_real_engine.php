<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->string('queue_number')->nullable()->after('ticket_number');
            $table->string('department')->default('Nội tổng quát')->after('service_type');
            $table->string('payment_status')->default('pending')->after('priority_level');
            $table->timestamp('activated_at')->nullable()->after('estimated_wait');
            $table->timestamp('called_at')->nullable()->after('activated_at');
            $table->timestamp('completed_at')->nullable()->after('called_at');
            $table->timestamp('no_show_at')->nullable()->after('completed_at');

            $table->index(['department', 'status', 'priority_level']);
            $table->index(['payment_status', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->dropIndex(['department', 'status', 'priority_level']);
            $table->dropIndex(['payment_status', 'status']);

            $table->dropColumn([
                'queue_number',
                'department',
                'payment_status',
                'activated_at',
                'called_at',
                'completed_at',
                'no_show_at',
            ]);
        });
    }
};
