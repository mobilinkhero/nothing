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
        Schema::create('sales_bot_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_bot_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->onDelete('set null');
            $table->string('customer_phone');
            $table->enum('type', ['cart_abandonment', 'order_follow_up', 'upsell', 're_engagement']);
            $table->json('trigger_data'); // Product/order info that triggered the reminder
            $table->text('message_template');
            $table->json('variables')->nullable(); // Variables for message personalization
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['scheduled', 'sent', 'failed', 'cancelled'])->default('scheduled');
            $table->text('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'sales_bot_id', 'status']);
            $table->index(['scheduled_at', 'status']);
            $table->index(['customer_phone', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_bot_reminders');
    }
};
