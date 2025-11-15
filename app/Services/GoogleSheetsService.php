<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleSheetsService
{
    private $client;
    private $sheetsService;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Sales Bot');
        $this->client->setScopes([Sheets::SPREADSHEETS]);
        $this->client->setAccessType('offline');
        
        // Set up authentication - you'll need to configure this
        $this->client->setAuthConfig(storage_path('app/google-sheets-credentials.json'));
        
        $this->sheetsService = new Sheets($this->client);
    }

    /**
     * Read data from Google Sheets
     */
    public function readSheet(string $spreadsheetId, string $range): array
    {
        try {
            $response = $this->sheetsService->spreadsheets_values->get($spreadsheetId, $range);
            return $response->getValues() ?? [];
        } catch (Exception $e) {
            Log::error('Google Sheets read error: ' . $e->getMessage());
            throw new Exception('Failed to read from Google Sheets: ' . $e->getMessage());
        }
    }

    /**
     * Write data to Google Sheets
     */
    public function writeSheet(string $spreadsheetId, string $range, array $values): bool
    {
        try {
            $body = new ValueRange([
                'values' => $values
            ]);

            $params = [
                'valueInputOption' => 'RAW'
            ];

            $this->sheetsService->spreadsheets_values->update(
                $spreadsheetId,
                $range,
                $body,
                $params
            );

            return true;
        } catch (Exception $e) {
            Log::error('Google Sheets write error: ' . $e->getMessage());
            throw new Exception('Failed to write to Google Sheets: ' . $e->getMessage());
        }
    }

    /**
     * Append data to Google Sheets
     */
    public function appendSheet(string $spreadsheetId, string $range, array $values): bool
    {
        try {
            $body = new ValueRange([
                'values' => $values
            ]);

            $params = [
                'valueInputOption' => 'RAW',
                'insertDataOption' => 'INSERT_ROWS'
            ];

            $this->sheetsService->spreadsheets_values->append(
                $spreadsheetId,
                $range,
                $body,
                $params
            );

            return true;
        } catch (Exception $e) {
            Log::error('Google Sheets append error: ' . $e->getMessage());
            throw new Exception('Failed to append to Google Sheets: ' . $e->getMessage());
        }
    }

    /**
     * Get products from Google Sheets
     */
    public function getProducts(string $spreadsheetId, string $sheetName = 'Products'): array
    {
        $range = $sheetName . '!A:J'; // Adjust columns as needed
        $rows = $this->readSheet($spreadsheetId, $range);
        
        if (empty($rows)) {
            return [];
        }

        // Assume first row is headers
        $headers = array_shift($rows);
        $products = [];

        foreach ($rows as $index => $row) {
            $product = [];
            foreach ($headers as $i => $header) {
                $product[strtolower(str_replace(' ', '_', $header))] = $row[$i] ?? '';
            }
            $product['sheet_row_id'] = $index + 2; // +2 because we removed header and sheets are 1-indexed
            $products[] = $product;
        }

        return $products;
    }

    /**
     * Save order to Google Sheets
     */
    public function saveOrder(string $spreadsheetId, array $orderData, string $sheetName = 'Orders'): bool
    {
        // Prepare order data for sheets
        $values = [[
            $orderData['order_number'] ?? '',
            $orderData['customer_name'] ?? '',
            $orderData['customer_phone'] ?? '',
            $orderData['total_amount'] ?? '',
            $orderData['currency'] ?? '',
            $orderData['status'] ?? '',
            $orderData['products_summary'] ?? '',
            $orderData['created_at'] ?? date('Y-m-d H:i:s'),
            $orderData['delivery_info']['address'] ?? '',
            $orderData['customer_notes'] ?? '',
        ]];

        $range = $sheetName . '!A:J';
        return $this->appendSheet($spreadsheetId, $range, $values);
    }

    /**
     * Create headers for products sheet if not exists
     */
    public function createProductsHeaders(string $spreadsheetId, string $sheetName = 'Products'): bool
    {
        $headers = [
            ['Name', 'Description', 'Price', 'Currency', 'Category', 'Stock Quantity', 'Images', 'Tags', 'Available', 'Upsell Products']
        ];

        $range = $sheetName . '!A1:J1';
        return $this->writeSheet($spreadsheetId, $range, $headers);
    }

    /**
     * Create headers for orders sheet if not exists
     */
    public function createOrdersHeaders(string $spreadsheetId, string $sheetName = 'Orders'): bool
    {
        $headers = [
            ['Order Number', 'Customer Name', 'Customer Phone', 'Total Amount', 'Currency', 'Status', 'Products', 'Created At', 'Delivery Address', 'Notes']
        ];

        $range = $sheetName . '!A1:J1';
        return $this->writeSheet($spreadsheetId, $range, $headers);
    }

    /**
     * Validate spreadsheet access
     */
    public function validateSpreadsheet(string $spreadsheetId): bool
    {
        try {
            $this->sheetsService->spreadsheets->get($spreadsheetId);
            return true;
        } catch (Exception $e) {
            Log::error('Google Sheets validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update product stock in Google Sheets
     */
    public function updateProductStock(string $spreadsheetId, int $rowId, int $newStock, string $sheetName = 'Products'): bool
    {
        try {
            // Assuming stock is in column F (6th column)
            $range = $sheetName . '!F' . $rowId;
            $values = [[$newStock]];
            
            return $this->writeSheet($spreadsheetId, $range, $values);
        } catch (Exception $e) {
            Log::error('Stock update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get sheet names from spreadsheet
     */
    public function getSheetNames(string $spreadsheetId): array
    {
        try {
            $spreadsheet = $this->sheetsService->spreadsheets->get($spreadsheetId);
            $sheets = $spreadsheet->getSheets();
            
            $sheetNames = [];
            foreach ($sheets as $sheet) {
                $sheetNames[] = $sheet->getProperties()->getTitle();
            }
            
            return $sheetNames;
        } catch (Exception $e) {
            Log::error('Get sheet names error: ' . $e->getMessage());
            return [];
        }
    }
}
