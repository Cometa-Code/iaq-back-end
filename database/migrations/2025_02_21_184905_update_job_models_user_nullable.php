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
        Schema::table('job_models', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            // Modifica a coluna para ser nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Recria a chave estrangeira
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_models', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            // Reverte a coluna para NOT NULL (se originalmente não era nullable)
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            // Recria a chave estrangeira
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
