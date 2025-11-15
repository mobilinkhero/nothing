<?php

namespace App\Models\Tenant;

use App\Models\BaseModel;
use App\Models\Tenant\Contact;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesBotReminder extends BaseModel
{
    use BelongsToTenant, HasFactory;

    protected $table = 'sales_bot_reminders';

    protected $fillable = [
        'tenant_id',
        'sales_bot_id',
        'contact_id',
        'customer_phone',
        'type',
        'trigger_data',
        'message_template',
        'variables',
        'scheduled_at',
        'sent_at',
        'status',
        'failure_reason',
        'retry_count',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'sales_bot_id' => 'integer',
        'contact_id' => 'integer',
        'trigger_data' => 'array',
        'variables' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'retry_count' => 'integer',
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
     * Scope for scheduled reminders
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for reminders due now
     */
    public function scopeDueNow($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('scheduled_at', '<=', now());
    }

    /**
     * Scope for reminders by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark reminder as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark reminder as failed
     */
    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * Check if reminder can be retried
     */
    public function canRetry(): bool
    {
        return $this->retry_count < 3 && $this->status === 'failed';
    }

    /**
     * Schedule retry
     */
    public function scheduleRetry(int $delayMinutes = 30): void
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => now()->addMinutes($delayMinutes),
            'failure_reason' => null,
        ]);
    }
}
