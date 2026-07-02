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
        Schema::create('budget_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('budget_amount', 10, 2);
            $table->foreignId('selected_menu_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->json('recommendation_data')->nullable();
            $table->timestamp('created_at')->nullable();
            
            // Composite index for user history queries
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_histories');
    }
};
