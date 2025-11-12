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
        Schema::table('artisans', function (Blueprint $table) {
            // Check if user_id column exists
            if (!Schema::hasColumn('artisans', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            if (Schema::hasColumn('artisans', 'user_id')) {
                $table->renameColumn('user_id', 'id_user');
                $table->dropForeign(['user_id']);
            }
        });
    }
};