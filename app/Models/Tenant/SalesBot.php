<?php

namespace App\Models\Tenant;

use App\Models\BaseModel;
use App\Traits\BelongsToTenant;
use App\Traits\TracksFeatureUsage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tenant\SalesBotInteraction;

class SalesBot extends BaseModel
{
    use BelongsToTenant, TracksFeatureUsage, HasFactory;

    protected $table = 'sales_bots';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'google_sheet_id',
        'products_sheet_name',
        'orders_sheet_name',
        'reminder_settings',
        'upselling_settings',
        'working_hours',
        'is_active',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'reminder_settings' => 'array',
        'upselling_settings' => 'array',
        'working_hours' => 'array',
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(SalesBotProduct::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SalesBotOrder::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(SalesBotReminder::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(SalesBotInteraction::class);
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->where('is_available', true);
    }

    public function pendingOrders(): HasMany
    {
        return $this->orders()->where('status', 'pending');
    }

    public function scheduledReminders(): HasMany
    {
        return $this->reminders()->where('status', 'scheduled');
    }

    public function getFeatureSlug(): ?string
    {
        return 'sales_bot';
    }

    /**
     * Get default reminder settings
     */
    public function getDefaultReminderSettings(): array
    {
        return [
            'intervals' => [1, 3, 7], // Days
            'message_templates' => [
                'cart_abandonment' => 'Hi {customer_name}! You left some items in your cart. Complete your order now: {products}',
                'order_follow_up' => 'Thanks for your order #{order_number}! Is there anything else you need?',
                'upsell' => 'Hi {customer_name}! Based on your recent purchase, you might also like: {recommended_products}',
                're_engagement' => 'Hi {customer_name}! We have new products you might love. Check them out!'
            ]
        ];
    }

    /**
     * Get default upselling settings
     */
    public function getDefaultUpsellSettings(): array
    {
        return [
            'delay_days' => 7,
            'max_attempts' => 3,
            'rules' => [
                'product_category_match' => true,
                'price_range_similar' => true,
                'purchase_history_based' => true
            ]
        ];
    }

    /**
     * Check if bot is currently within working hours
     */
    public function isWithinWorkingHours(): bool
    {
        if (!$this->working_hours) {
            return true; // Always available if no working hours set
        }

        $settings = $this->working_hours;
        $timezone = $settings['timezone'] ?? 'UTC';
        $start = $settings['start'] ?? '00:00';
        $end = $settings['end'] ?? '23:59';

        $now = now($timezone);
        $startTime = $now->copy()->setTimeFromTimeString($start);
        $endTime = $now->copy()->setTimeFromTimeString($end);

        return $now->between($startTime, $endTime);
    }
}
