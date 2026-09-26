<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_code')->unique()->index(); // e.g. "SESS-1024"
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('tables')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('price_per_round', 10, 2); // Locked historical rate
            $table->unsignedInteger('rounds')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('payment_status')->default('unpaid')->index(); // 'unpaid', 'paid'
            $table->timestamp('payment_time')->nullable();
            $table->string('payment_method')->default('cash'); // 'cash', 'card', 'online', 'other'
            $table->string('status')->default('active')->index(); // 'active', 'completed', 'cancelled'
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add foreign key constraint to tables for current_session_id
        Schema::table('tables', function (Blueprint $table) {
            $table->foreign('current_session_id')->references('id')->on('game_sessions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['current_session_id']);
        });
        Schema::dropIfExists('game_sessions');
    }
};
