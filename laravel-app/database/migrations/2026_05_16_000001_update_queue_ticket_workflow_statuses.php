<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->timestamp('missed_at')->nullable()->after('called_at');
        });

        DB::table('queue_tickets')->where('status', 'waiting')->where('payment_status', 'pending')->update(['status' => 'waiting_payment']);
        DB::table('queue_tickets')->where('status', 'waiting')->where('payment_status', '!=', 'pending')->update(['status' => 'ready']);
        DB::table('queue_tickets')->where('status', 'active')->update(['status' => 'serving']);
        DB::table('queue_tickets')->where('status', 'served')->update(['status' => 'completed']);
        DB::table('queue_tickets')->where('status', 'no_show')->update(['status' => 'missed']);
    }

    public function down(): void
    {
        DB::table('queue_tickets')->where('status', 'waiting_payment')->update(['status' => 'waiting']);
        DB::table('queue_tickets')->where('status', 'ready')->update(['status' => 'waiting']);
        DB::table('queue_tickets')->where('status', 'serving')->update(['status' => 'active']);
        DB::table('queue_tickets')->where('status', 'completed')->update(['status' => 'served']);
        DB::table('queue_tickets')->where('status', 'missed')->update(['status' => 'no_show']);

        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->dropColumn('missed_at');
        });
    }
};
