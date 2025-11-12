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
            // First check if id_user exists and user_id doesn't
            if (Schema::hasColumn('artisans', 'id_user') && !Schema::hasColumn('artisans', 'user_id')) {
                // Remove the old foreign key constraint
                $table->dropForeign(['id_user']);
                
                // Rename the column
                $table->renameColumn('id_user', 'user_id');
                
                // Add the new foreign key constraint
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            // Check if user_id exists and id_user doesn't
            if (Schema::hasColumn('artisans', 'user_id') && !Schema::hasColumn('artisans', 'id_user')) {
                // Remove the foreign key constraint
                $table->dropForeign(['user_id']);
                
                // Rename back
                $table->renameColumn('user_id', 'id_user');
                
                // Add the old foreign key constraint back
                $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }
};