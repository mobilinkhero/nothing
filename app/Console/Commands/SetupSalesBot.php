<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupSalesBot extends Command
{
    protected $signature = 'sales-bot:setup';
    protected $description = 'Setup Sales Bot system for the application';

    public function handle()
    {
        $this->info('Setting up Sales Bot system...');

        // Check if tables exist
        try {
            \DB::table('sales_bots')->count();
            $this->info('✅ Sales Bot tables are ready');
        } catch (\Exception $e) {
            $this->error('❌ Sales Bot tables not found. Please run the SQL migrations first.');
            return 1;
        }

        // Check if Google Sheets credentials exist
        $credentialsPath = storage_path('app/google-sheets-credentials.json');
        if (!file_exists($credentialsPath)) {
            $this->warn('⚠️  Google Sheets credentials not found at: ' . $credentialsPath);
            $this->line('Please add your Google service account JSON file there.');
        } else {
            $this->info('✅ Google Sheets credentials found');
        }

        // Check if routes are loaded
        try {
            route('sales-bot.index');
            $this->info('✅ Sales Bot routes are registered');
        } catch (\Exception $e) {
            $this->warn('⚠️  Sales Bot routes may not be registered. Check tenant routing.');
        }

        $this->info('🚀 Sales Bot setup complete!');
        $this->line('');
        $this->line('Next steps:');
        $this->line('1. Add Google Sheets service account credentials');
        $this->line('2. Configure queue processing: php artisan queue:work');
        $this->line('3. Set up cron job for reminders processing');
        $this->line('4. Visit /sales-bot to create your first Sales Bot');

        return 0;
    }
}
