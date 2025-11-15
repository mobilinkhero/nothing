<?php

namespace App\Models\Tenant;

use App\Models\BaseModel;
use App\Models\Tenant\Contact;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesBotInteraction extends BaseModel
{
    use BelongsToTenant, HasFactory;

    protected $table = 'sales_bot_interactions';

    protected $fillable = [
        'tenant_id',
        'sales_bot_id',
        'contact_id',
        'customer_phone',
        'interaction_type',
        'interaction_data',
        'session_id',
        'metadata',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'sales_bot_id' => 'integer',
        'contact_id' => 'integer',
        'interaction_data' => 'array',
        'metadata' => 'array',
    ];

    public function salesBot(): BelongsTo
    {
        return $this->belongsTo(SalesBot::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Scope for interactions by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('interaction_type', $type);
    }

    /**
     * Scope for interactions by customer
     */
    public function scopeByCustomer($query, $phone)
    {
        return $query->where('customer_phone', $phone);
    }

    /**
     * Scope for interactions in session
     */
    public function scopeInSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope for recent interactions
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Get customer's interaction history
     */
    public static function getCustomerHistory($phone, $salesBotId, $days = 30)
    {
        return static::where('customer_phone', $phone)
                    ->where('sales_bot_id', $salesBotId)
                    ->where('created_at', '>=', now()->subDays($days))
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get customer's interests based on interactions
     */
    public static function getCustomerInterests($phone, $salesBotId)
    {
        $interactions = static::where('customer_phone', $phone)
                             ->where('sales_bot_id', $salesBotId)
                             ->whereIn('interaction_type', ['product_view', 'add_to_cart', 'order_placed'])
                             ->get();

        $interests = [];
        foreach ($interactions as $interaction) {
            $data = $interaction->interaction_data;
            if (isset($data['category'])) {
                $interests[] = $data['category'];
            }
            if (isset($data['tags'])) {
                $interests = array_merge($interests, $data['tags']);
            }
        }

        return array_unique($interests);
    }
}
