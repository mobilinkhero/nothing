<?php

namespace App\Services;

use App\Models\Tenant\SalesBot;
use App\Models\Tenant\SalesBotProduct;
use App\Models\Tenant\SalesBotOrder;
use App\Models\Tenant\SalesBotReminder;
use App\Models\Tenant\SalesBotInteraction;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SalesBotService
{
    private $googleSheetsService;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        $this->googleSheetsService = $googleSheetsService;
    }

    /**
     * Sync products from Google Sheets
     */
    public function syncProducts(SalesBot $salesBot): array
    {
        try {
            if (!$salesBot->google_sheet_id) {
                throw new \Exception('No Google Sheet ID configured');
            }

            $products = $this->googleSheetsService->getProducts(
                $salesBot->google_sheet_id,
                $salesBot->products_sheet_name
            );

            $synced = 0;
            $errors = [];

            foreach ($products as $productData) {
                try {
                    $this->createOrUpdateProduct($salesBot, $productData);
                    $synced++;
                } catch (\Exception $e) {
                    $errors[] = "Product '{$productData['name']}': " . $e->getMessage();
                }
            }

            return [
                'success' => true,
                'synced' => $synced,
                'errors' => $errors,
                'total' => count($products)
            ];

        } catch (\Exception $e) {
            Log::error('Product sync failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'synced' => 0,
                'errors' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Create or update product from sheet data
     */
    private function createOrUpdateProduct(SalesBot $salesBot, array $data): SalesBotProduct
    {
        $product = SalesBotProduct::updateOrCreate(
            [
                'sales_bot_id' => $salesBot->id,
                'sheet_row_id' => $data['sheet_row_id']
            ],
            [
                'tenant_id' => $salesBot->tenant_id,
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? '',
                'price' => (float) ($data['price'] ?? 0),
                'currency' => $data['currency'] ?? 'USD',
                'category' => $data['category'] ?? '',
                'images' => $this->parseImages($data['images'] ?? ''),
                'tags' => $this->parseTags($data['tags'] ?? ''),
                'stock_quantity' => $data['stock_quantity'] ? (int) $data['stock_quantity'] : null,
                'is_available' => $this->parseBoolean($data['available'] ?? 'true'),
                'upsell_products' => $this->parseUpsellProducts($data['upsell_products'] ?? ''),
                'synced_at' => now(),
            ]
        );

        return $product;
    }

    /**
     * Create order and save to Google Sheets
     */
    public function createOrder(SalesBot $salesBot, array $orderData): SalesBotOrder
    {
        $order = SalesBotOrder::create([
            'tenant_id' => $salesBot->tenant_id,
            'sales_bot_id' => $salesBot->id,
            'contact_id' => $orderData['contact_id'] ?? null,
            'customer_phone' => $orderData['customer_phone'],
            'customer_name' => $orderData['customer_name'] ?? '',
            'products' => $orderData['products'],
            'total_amount' => $orderData['total_amount'],
            'currency' => $orderData['currency'] ?? 'USD',
            'customer_notes' => $orderData['customer_notes'] ?? '',
            'delivery_info' => $orderData['delivery_info'] ?? [],
        ]);

        // Save to Google Sheets
        if ($salesBot->google_sheet_id) {
            try {
                $this->saveOrderToSheets($salesBot, $order);
            } catch (\Exception $e) {
                Log::error('Failed to save order to sheets: ' . $e->getMessage());
            }
        }

        // Track interaction
        $this->trackInteraction($salesBot, $orderData['customer_phone'], 'order_placed', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'products' => $order->products
        ]);

        // Schedule follow-up reminder
        $this->scheduleOrderFollowUp($salesBot, $order);

        return $order;
    }

    /**
     * Save order to Google Sheets
     */
    private function saveOrderToSheets(SalesBot $salesBot, SalesBotOrder $order): void
    {
        $productsText = collect($order->products)->map(function ($product) {
            return $product['name'] . ' (x' . $product['quantity'] . ')';
        })->join(', ');

        $orderData = [
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'total_amount' => $order->total_amount,
            'currency' => $order->currency,
            'status' => $order->status,
            'products_summary' => $productsText,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'delivery_info' => $order->delivery_info,
            'customer_notes' => $order->customer_notes,
        ];

        $this->googleSheetsService->saveOrder(
            $salesBot->google_sheet_id,
            $orderData,
            $salesBot->orders_sheet_name
        );
    }

    /**
     * Track customer interaction
     */
    public function trackInteraction(SalesBot $salesBot, string $customerPhone, string $type, array $data, string $sessionId = null): SalesBotInteraction
    {
        return SalesBotInteraction::create([
            'tenant_id' => $salesBot->tenant_id,
            'sales_bot_id' => $salesBot->id,
            'customer_phone' => $customerPhone,
            'interaction_type' => $type,
            'interaction_data' => $data,
            'session_id' => $sessionId ?? Str::uuid(),
        ]);
    }

    /**
     * Schedule order follow-up reminder
     */
    public function scheduleOrderFollowUp(SalesBot $salesBot, SalesBotOrder $order): void
    {
        $reminderSettings = $salesBot->reminder_settings ?? $salesBot->getDefaultReminderSettings();
        $template = $reminderSettings['message_templates']['order_follow_up'] ?? 
                   'Thanks for your order #{order_number}! Is there anything else you need?';

        SalesBotReminder::create([
            'tenant_id' => $salesBot->tenant_id,
            'sales_bot_id' => $salesBot->id,
            'contact_id' => $order->contact_id,
            'customer_phone' => $order->customer_phone,
            'type' => 'order_follow_up',
            'trigger_data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'products' => $order->products
            ],
            'message_template' => $template,
            'variables' => [
                'customer_name' => $order->customer_name,
                'order_number' => $order->order_number,
                'total_amount' => $order->formatted_total
            ],
            'scheduled_at' => now()->addDays(1), // Follow up after 1 day
        ]);
    }

    /**
     * Schedule cart abandonment reminder
     */
    public function scheduleCartAbandonmentReminder(SalesBot $salesBot, string $customerPhone, array $cartData): void
    {
        $reminderSettings = $salesBot->reminder_settings ?? $salesBot->getDefaultReminderSettings();
        $template = $reminderSettings['message_templates']['cart_abandonment'] ?? 
                   'Hi {customer_name}! You left some items in your cart. Complete your order now: {products}';

        SalesBotReminder::create([
            'tenant_id' => $salesBot->tenant_id,
            'sales_bot_id' => $salesBot->id,
            'customer_phone' => $customerPhone,
            'type' => 'cart_abandonment',
            'trigger_data' => $cartData,
            'message_template' => $template,
            'variables' => [
                'customer_name' => $cartData['customer_name'] ?? 'there',
                'products' => $this->formatProductsForMessage($cartData['products'] ?? [])
            ],
            'scheduled_at' => now()->addHours(2), // Remind after 2 hours
        ]);
    }

    /**
     * Get product recommendations for upselling
     */
    public function getProductRecommendations(SalesBot $salesBot, string $customerPhone, int $limit = 3): array
    {
        $customerInterests = SalesBotInteraction::getCustomerInterests($customerPhone, $salesBot->id);
        
        $query = $salesBot->activeProducts()->inStock();
        
        if (!empty($customerInterests)) {
            $query->where(function ($q) use ($customerInterests) {
                $q->whereIn('category', $customerInterests)
                  ->orWhere(function ($subQ) use ($customerInterests) {
                      foreach ($customerInterests as $interest) {
                          $subQ->orWhereJsonContains('tags', $interest);
                      }
                  });
            });
        }

        return $query->limit($limit)->get()->toArray();
    }

    /**
     * Process upselling for customer
     */
    public function processUpselling(SalesBot $salesBot, SalesBotOrder $order): void
    {
        $upsellSettings = $salesBot->upselling_settings ?? $salesBot->getDefaultUpsellSettings();
        $delayDays = $upsellSettings['delay_days'] ?? 7;

        $recommendations = $this->getProductRecommendations($salesBot, $order->customer_phone);
        
        if (!empty($recommendations)) {
            $template = $salesBot->reminder_settings['message_templates']['upsell'] ?? 
                       'Hi {customer_name}! Based on your recent purchase, you might also like: {recommended_products}';

            SalesBotReminder::create([
                'tenant_id' => $salesBot->tenant_id,
                'sales_bot_id' => $salesBot->id,
                'contact_id' => $order->contact_id,
                'customer_phone' => $order->customer_phone,
                'type' => 'upsell',
                'trigger_data' => [
                    'original_order_id' => $order->id,
                    'recommended_products' => $recommendations
                ],
                'message_template' => $template,
                'variables' => [
                    'customer_name' => $order->customer_name,
                    'recommended_products' => $this->formatProductsForMessage($recommendations)
                ],
                'scheduled_at' => now()->addDays($delayDays),
            ]);
        }
    }

    /**
     * Helper methods
     */
    private function parseImages(string $imagesStr): array
    {
        if (empty($imagesStr)) return [];
        return array_map('trim', explode(',', $imagesStr));
    }

    private function parseTags(string $tagsStr): array
    {
        if (empty($tagsStr)) return [];
        return array_map('trim', explode(',', $tagsStr));
    }

    private function parseBoolean(string $value): bool
    {
        return in_array(strtolower(trim($value)), ['true', '1', 'yes', 'on']);
    }

    private function parseUpsellProducts(string $productsStr): array
    {
        if (empty($productsStr)) return [];
        return array_map('intval', array_filter(explode(',', $productsStr)));
    }

    private function formatProductsForMessage(array $products): string
    {
        return collect($products)->map(function ($product) {
            return $product['name'] ?? 'Product';
        })->join(', ');
    }
}
