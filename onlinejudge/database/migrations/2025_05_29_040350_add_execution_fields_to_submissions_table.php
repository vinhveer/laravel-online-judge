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
        Schema::table('Submissions', function (Blueprint $table) {
            $table->integer('execution_time')->nullable(); // milliseconds
            $table->integer('memory_used')->nullable(); // KB
            $table->text('error_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Submissions', function (Blueprint $table) {
            $table->dropColumn([
                'execution_time',
                'memory_used',
                'error_message'
            ]);
        });
    }
};
