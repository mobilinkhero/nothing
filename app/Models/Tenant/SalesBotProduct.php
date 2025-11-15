<?php

namespace App\Models\Tenant;

use App\Models\BaseModel;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesBotProduct extends BaseModel
{
    use BelongsToTenant, HasFactory;

    protected $table = 'sales_bot_products';

    protected $fillable = [
        'tenant_id',
        'sales_bot_id',
        'sheet_row_id',
        'name',
        'description',
        'price',
        'currency',
        'category',
        'images',
        'tags',
        'stock_quantity',
        'is_available',
        'upsell_products',
        'synced_at',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'sales_bot_id' => 'integer',
        'price' => 'decimal:2',
        'images' => 'array',
        'tags' => 'array',
        'stock_quantity' => 'integer',
        'is_available' => 'boolean',
        'upsell_products' => 'array',
        'synced_at' => 'datetime',
    ];

    public function salesBot(): BelongsTo
    {
        return $this->belongsTo(SalesBot::class);
    }

    /**
     * Get the main product image
     */
    public function getMainImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(): bool
    {
        if ($this->stock_quantity === null) {
            return true; // Unlimited stock
        }

        return $this->stock_quantity > 0;
    }

    /**
     * Get upsell products
     */
    public function getUpsellProducts()
    {
        if (!$this->upsell_products) {
            return collect();
        }

        return static::whereIn('id', $this->upsell_products)
            ->where('is_available', true)
            ->get();
    }

    /**
     * Scope for available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for products in stock
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('stock_quantity')
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    /**
     * Scope for products by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for products with tags
     */
    public function scopeWithTags($query, array $tags)
    {
        return $query->where(function ($q) use ($tags) {
            foreach ($tags as $tag) {
                $q->orWhereJsonContains('tags', $tag);
            }
        });
    }
}
