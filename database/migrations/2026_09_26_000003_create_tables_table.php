<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. "Table 1"
            $table->string('type')->default('Standard Snooker'); // e.g. 'Standard Snooker', 'Tournament Snooker', '8-Ball Pool'
            $table->string('status')->default('available'); // 'available', 'occupied', 'maintenance'
            $table->unsignedBigInteger('current_session_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
