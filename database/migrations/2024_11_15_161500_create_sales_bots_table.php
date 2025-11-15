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
        Schema::create('sales_bots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('google_sheet_id')->nullable();
            $table->string('products_sheet_name')->default('Products');
            $table->string('orders_sheet_name')->default('Orders');
            $table->json('reminder_settings')->nullable(); // {intervals: [1, 3, 7], message_templates: {}}
            $table->json('upselling_settings')->nullable(); // {delay_days: 7, rules: []}
            $table->json('working_hours')->nullable(); // {start: '09:00', end: '18:00', timezone: 'UTC'}
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_bots');
    }
};
