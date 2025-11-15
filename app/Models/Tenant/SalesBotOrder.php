<?php

namespace App\Models\Tenant;

use App\Models\BaseModel;
use App\Models\Tenant\Contact;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SalesBotOrder extends BaseModel
{
    use BelongsToTenant, HasFactory;

    protected $table = 'sales_bot_orders';

    protected $fillable = [
        'tenant_id',
        'sales_bot_id',
        'contact_id',
        'order_number',
        'customer_phone',
        'customer_name',
        'products',
        'total_amount',
        'currency',
        'status',
        'customer_notes',
        'internal_notes',
        'delivery_info',
        'sheet_row_id',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'synced_at',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'sales_bot_id' => 'integer',
        'contact_id' => 'integer',
        'products' => 'array',
        'total_amount' => 'decimal:2',
        'delivery_info' => 'array',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    public function salesBot(): BelongsTo
    {
        return $this->belongsTo(SalesBot::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'SB' . date('Ymd') . Str::random(6);
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get formatted total amount with currency
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->total_amount, 2);
    }

    /**
     * Get total quantity of products
     */
    public function getTotalQuantityAttribute(): int
    {
        return collect($this->products)->sum('quantity');
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Mark order as confirmed
     */
    public function markAsConfirmed(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Mark order as shipped
     */
    public function markAsShipped(): void
    {
        $this->update([
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);
    }

    /**
     * Mark order as delivered
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Scope for orders by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for orders by customer
     */
    public function scopeByCustomer($query, $phone)
    {
        return $query->where('customer_phone', $phone);
    }

    /**
     * Scope for recent orders
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
