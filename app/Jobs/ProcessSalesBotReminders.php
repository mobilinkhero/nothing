<?php

namespace App\Jobs;

use App\Models\Tenant\SalesBotReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSalesBotReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $dueReminders = SalesBotReminder::dueNow()->get();
        
        Log::info("Processing {$dueReminders->count()} due Sales Bot reminders");

        foreach ($dueReminders as $reminder) {
            try {
                SendSalesBotReminder::dispatch($reminder);
            } catch (\Exception $e) {
                Log::error("Failed to dispatch reminder {$reminder->id}: " . $e->getMessage());
            }
        }
    }
}
