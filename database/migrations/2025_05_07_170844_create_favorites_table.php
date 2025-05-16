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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            //  User who marked the product as favorite
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Product that is marked as favorite
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            // Ensure a user can't favorite the same product twice
            $table->unique(['user_id', 'product_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
