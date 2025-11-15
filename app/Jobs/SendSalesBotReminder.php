<?php

namespace App\Jobs;

use App\Models\Tenant\SalesBotReminder;
use App\Traits\WhatsApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSalesBotReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WhatsApp;

    private $reminder;

    public function __construct(SalesBotReminder $reminder)
    {
        $this->reminder = $reminder;
    }

    public function handle()
    {
        try {
            if ($this->reminder->status !== 'scheduled') {
                Log::info("Reminder {$this->reminder->id} is not scheduled, skipping");
                return;
            }

            $salesBot = $this->reminder->salesBot;
            
            // Check if bot is within working hours
            if (!$salesBot->isWithinWorkingHours()) {
                // Reschedule for next working hour
                $this->reminder->update([
                    'scheduled_at' => $this->getNextWorkingHour($salesBot)
                ]);
                Log::info("Reminder {$this->reminder->id} rescheduled due to working hours");
                return;
            }

            // Prepare message with variables
            $message = $this->replaceReminderVariables(
                $this->reminder->message_template,
                $this->reminder->variables ?? []
            );

            // Send WhatsApp message
            $messageData = [
                'rel_type' => 'guest',
                'rel_id' => $this->reminder->contact_id ?? '',
                'reply_text' => $message,
                'bot_header' => '',
                'bot_footer' => '',
                'tenant_id' => $this->reminder->tenant_id,
            ];

            // Get tenant WhatsApp phone number ID
            $phoneNumberId = $this->getTenantWhatsAppNumberId($this->reminder->tenant_id);
            
            if (!$phoneNumberId) {
                throw new \Exception('No WhatsApp phone number configured for tenant');
            }

            $result = $this->sendMessage(
                $this->reminder->customer_phone,
                $messageData,
                $phoneNumberId
            );

            if ($result['status']) {
                $this->reminder->markAsSent();
                
                // Track the reminder interaction
                $salesBot->interactions()->create([
                    'tenant_id' => $salesBot->tenant_id,
                    'customer_phone' => $this->reminder->customer_phone,
                    'interaction_type' => 'reminder_clicked',
                    'interaction_data' => [
                        'reminder_id' => $this->reminder->id,
                        'reminder_type' => $this->reminder->type,
                    ]
                ]);

                Log::info("Sales Bot reminder {$this->reminder->id} sent successfully");
            } else {
                throw new \Exception($result['message'] ?? 'Failed to send message');
            }

        } catch (\Exception $e) {
            $this->reminder->markAsFailed($e->getMessage());
            
            // Retry if possible
            if ($this->reminder->canRetry()) {
                $this->reminder->scheduleRetry();
                Log::warning("Sales Bot reminder {$this->reminder->id} failed, scheduled for retry: " . $e->getMessage());
            } else {
                Log::error("Sales Bot reminder {$this->reminder->id} failed permanently: " . $e->getMessage());
            }
        }
    }

    private function replaceReminderVariables(string $template, array $variables): string
    {
        $message = $template;
        
        foreach ($variables as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $message;
    }

    private function getTenantWhatsAppNumberId(int $tenantId): ?string
    {
        // This should be implemented based on your tenant WhatsApp configuration
        // For now, returning a placeholder
        return config('whatsapp.default_phone_number_id');
    }

    private function getNextWorkingHour($salesBot): \Carbon\Carbon
    {
        $workingHours = $salesBot->working_hours;
        $timezone = $workingHours['timezone'] ?? 'UTC';
        $startTime = $workingHours['start'] ?? '09:00';
        
        $tomorrow = now($timezone)->addDay();
        return $tomorrow->setTimeFromTimeString($startTime);
    }

    public function failed(\Exception $exception)
    {
        Log::error("Sales Bot reminder job failed permanently: " . $exception->getMessage(), [
            'reminder_id' => $this->reminder->id,
            'exception' => $exception
        ]);
    }
}
