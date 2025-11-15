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
        Schema::create('sales_bot_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_bot_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->onDelete('set null');
            $table->string('customer_phone');
            $table->enum('interaction_type', [
                'product_view', 
                'add_to_cart', 
                'remove_from_cart', 
                'order_placed', 
                'inquiry', 
                'reminder_clicked',
                'upsell_viewed',
                'upsell_purchased'
            ]);
            $table->json('interaction_data'); // Product info, message details, etc.
            $table->string('session_id')->nullable(); // Group related interactions
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamps();

            $table->index(['tenant_id', 'sales_bot_id', 'interaction_type']);
            $table->index(['customer_phone', 'interaction_type']);
            $table->index(['session_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_bot_interactions');
    }
};
