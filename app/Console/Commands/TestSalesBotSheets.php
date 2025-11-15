<?php

namespace App\Console\Commands;

use App\Services\GoogleSheetsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestSalesBotSheets extends Command
{
    protected $signature = 'sales-bot:test-sheets {spreadsheet_id}';
    protected $description = 'Test Google Sheets connection and create required sheets for Sales Bot';

    private $googleSheetsService;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        parent::__construct();
        $this->googleSheetsService = $googleSheetsService;
    }

    public function handle()
    {
        $spreadsheetId = $this->argument('spreadsheet_id');
        
        $this->info("Testing Google Sheets connection for: {$spreadsheetId}");
        
        try {
            // Test basic connection
            $this->info('1. Testing basic connection...');
            if (!$this->googleSheetsService->validateSpreadsheet($spreadsheetId)) {
                $this->error('❌ Cannot access spreadsheet. Check permissions and sheet ID.');
                return 1;
            }
            $this->info('✅ Basic connection successful');
            
            // Get existing sheets
            $this->info('2. Getting existing sheets...');
            $existingSheets = $this->googleSheetsService->getSheetNames($spreadsheetId);
            $this->info('Found sheets: ' . implode(', ', $existingSheets));
            
            // Create Products sheet if needed
            $this->info('3. Setting up Products sheet...');
            if ($this->googleSheetsService->createProductsHeaders($spreadsheetId, 'Products')) {
                $this->info('✅ Products sheet ready');
            } else {
                $this->error('❌ Failed to setup Products sheet');
            }
            
            // Create Orders sheet if needed
            $this->info('4. Setting up Orders sheet...');
            if ($this->googleSheetsService->createOrdersHeaders($spreadsheetId, 'Orders')) {
                $this->info('✅ Orders sheet ready');
            } else {
                $this->error('❌ Failed to setup Orders sheet');
            }
            
            // Test order saving
            $this->info('5. Testing order save functionality...');
            $testOrderData = [
                'order_number' => 'TEST-' . time(),
                'customer_name' => 'Test Customer',
                'customer_phone' => '+1234567890',
                'total_amount' => '99.99',
                'currency' => 'USD',
                'status' => 'pending',
                'products_summary' => 'Test Product (x1)',
                'created_at' => date('Y-m-d H:i:s'),
                'delivery_info' => ['address' => 'Test Address'],
                'customer_notes' => 'Test order from diagnostic command',
            ];
            
            if ($this->googleSheetsService->saveOrder($spreadsheetId, $testOrderData, 'Orders')) {
                $this->info('✅ Test order saved successfully');
            } else {
                $this->error('❌ Failed to save test order');
            }
            
            // Final check
            $this->info('6. Final verification...');
            $finalSheets = $this->googleSheetsService->getSheetNames($spreadsheetId);
            $this->info('Current sheets: ' . implode(', ', $finalSheets));
            
            if (in_array('Products', $finalSheets) && in_array('Orders', $finalSheets)) {
                $this->info('🎉 Sales Bot sheets are ready!');
                return 0;
            } else {
                $this->error('❌ Setup incomplete. Missing required sheets.');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Sales Bot sheets test failed: ' . $e->getMessage());
            return 1;
        }
    }
}
