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
        Schema::create('sales_bot_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_bot_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->onDelete('set null');
            $table->string('order_number')->unique();
            $table->string('customer_phone');
            $table->string('customer_name')->nullable();
            $table->json('products'); // Array of {product_id, name, price, quantity}
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->json('delivery_info')->nullable(); // {address, phone, preferred_time}
            $table->string('sheet_row_id')->nullable(); // Reference to Google Sheets row
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->timestamp('synced_at')->nullable(); // Last sync with Google Sheets

            $table->index(['tenant_id', 'sales_bot_id', 'status']);
            $table->index(['customer_phone', 'status']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_bot_orders');
    }
};
