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
        Schema::table('Problems', function (Blueprint $table) {
            $table->string('difficulty')->default('easy');
            $table->integer('time_limit')->default(1000); // milliseconds
            $table->integer('memory_limit')->default(128); // MB
            $table->boolean('is_public')->default(true);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Problems', function (Blueprint $table) {
            $table->dropColumn([
                'difficulty',
                'time_limit',
                'memory_limit',
                'is_public',
                'created_at',
                'updated_at'
            ]);
        });
    }
};
