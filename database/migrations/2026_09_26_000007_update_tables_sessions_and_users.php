<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update tables to support individual per-table pricing for the 4 gameplays
        Schema::table('tables', function (Blueprint $table) {
            $table->decimal('rate_century', 10, 2)->default(10.00)->after('type'); // per minute
            $table->decimal('rate_6ball', 10, 2)->default(130.00)->after('rate_century'); // per frame
            $table->decimal('rate_10ball', 10, 2)->default(150.00)->after('rate_6ball'); // per frame
            $table->decimal('rate_oneball', 10, 2)->default(120.00)->after('rate_10ball'); // per frame
        });

        // 2. Update game_sessions for the 4 gameplays
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->string('game_type')->default('6_ball')->after('table_id'); // 'century', '6_ball', '10_ball', 'one_ball'
            $table->decimal('rate_applied', 10, 2)->default(130.00)->after('game_type');
        });

        // 3. Update users table for username-based login
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['rate_century', 'rate_6ball', 'rate_10ball', 'rate_oneball']);
        });

        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropColumn(['game_type', 'rate_applied']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
