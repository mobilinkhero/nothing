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
        Schema::create('sales_bot_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_bot_id')->constrained()->onDelete('cascade');
            $table->string('sheet_row_id')->nullable(); // Reference to Google Sheets row
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('category')->nullable();
            $table->json('images')->nullable(); // Array of image URLs
            $table->json('tags')->nullable(); // Array of tags for categorization
            $table->integer('stock_quantity')->nullable();
            $table->boolean('is_available')->default(true);
            $table->json('upsell_products')->nullable(); // Array of related product IDs
            $table->timestamps();
            $table->timestamp('synced_at')->nullable(); // Last sync with Google Sheets

            $table->index(['tenant_id', 'sales_bot_id', 'is_available']);
            $table->index(['category', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_bot_products');
    }
};
