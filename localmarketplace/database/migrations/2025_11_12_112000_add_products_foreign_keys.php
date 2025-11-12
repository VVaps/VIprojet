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
        Schema::table('products', function (Blueprint $table) {
            // Add foreign key constraint for artisan_id
            if (!Schema::hasColumn('products', 'artisan_id')) {
                $table->unsignedBigInteger('artisan_id')->nullable();
            }
            
            // Add foreign key constraint
            $table->foreign('artisan_id')->references('id')->on('artisans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['artisan_id']);
            $table->dropColumn('artisan_id');
        });
    }
};