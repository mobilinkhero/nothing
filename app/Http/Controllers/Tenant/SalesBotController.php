<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\SalesBot;
use App\Models\Tenant\SalesBotProduct;
use App\Models\Tenant\SalesBotOrder;
use App\Models\Tenant\SalesBotReminder;
use App\Services\SalesBotService;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant;

class SalesBotController extends Controller
{
    private $salesBotService;
    private $googleSheetsService;

    public function __construct(SalesBotService $salesBotService, GoogleSheetsService $googleSheetsService)
    {
        $this->salesBotService = $salesBotService;
        $this->googleSheetsService = $googleSheetsService;
    }

    /**
     * Display Sales Bot dashboard
     */
    public function index()
    {
        $salesBot = SalesBot::where('tenant_id', Tenant::current()->id)->first();
        
        $stats = [];
        if ($salesBot) {
            $stats = [
                'total_products' => $salesBot->products()->count(),
                'active_products' => $salesBot->activeProducts()->count(),
                'total_orders' => $salesBot->orders()->count(),
                'pending_orders' => $salesBot->pendingOrders()->count(),
                'monthly_revenue' => $salesBot->orders()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('total_amount'),
                'scheduled_reminders' => $salesBot->scheduledReminders()->count(),
            ];
        }

        return view('tenant.sales-bot.index', compact('salesBot', 'stats'));
    }

    /**
     * Show configuration form
     */
    public function create()
    {
        return view('tenant.sales-bot.create');
    }

    /**
     * Store new Sales Bot configuration
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'google_sheet_id' => 'required|string',
            'products_sheet_name' => 'required|string|max:100',
            'orders_sheet_name' => 'required|string|max:100',
            'working_hours.start' => 'nullable|string',
            'working_hours.end' => 'nullable|string',
            'working_hours.timezone' => 'nullable|string',
            'reminder_settings.intervals' => 'nullable|array',
            'reminder_settings.intervals.*' => 'integer|min:1',
            'upselling_settings.delay_days' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Validate Google Sheets access
            if (!$this->googleSheetsService->validateSpreadsheet($request->google_sheet_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to access the specified Google Sheet. Please check the Sheet ID and permissions.',
                ], 422);
            }

            DB::beginTransaction();

            // Create a temporary instance to get default settings
            $tempBot = new SalesBot();
            
            $salesBot = SalesBot::create([
                'tenant_id' => Tenant::current()->id,
                'name' => $request->name,
                'description' => $request->description,
                'google_sheet_id' => $request->google_sheet_id,
                'products_sheet_name' => $request->products_sheet_name ?? 'Products',
                'orders_sheet_name' => $request->orders_sheet_name ?? 'Orders',
                'working_hours' => $request->working_hours,
                'reminder_settings' => array_merge(
                    $tempBot->getDefaultReminderSettings(),
                    $request->reminder_settings ?? []
                ),
                'upselling_settings' => array_merge(
                    $tempBot->getDefaultUpsellSettings(),
                    $request->upselling_settings ?? []
                ),
                'is_active' => true,
            ]);

            // Create sheet headers if needed
            $this->googleSheetsService->createProductsHeaders($salesBot->google_sheet_id, $salesBot->products_sheet_name);
            $this->googleSheetsService->createOrdersHeaders($salesBot->google_sheet_id, $salesBot->orders_sheet_name);

            // Initial product sync
            $syncResult = $this->salesBotService->syncProducts($salesBot);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales Bot created successfully!',
                'data' => [
                    'sales_bot' => $salesBot,
                    'sync_result' => $syncResult
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Sales Bot: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show Sales Bot configuration
     */
    public function show(SalesBot $salesBot)
    {
        // Tenant access is now handled by route model binding
        $salesBot->load(['products', 'orders', 'reminders']);
        
        return view('tenant.sales-bot.show', compact('salesBot'));
    }

    /**
     * Show edit form
     */
    public function edit(SalesBot $salesBot)
    {
        // Tenant access is now handled by route model binding
        return view('tenant.sales-bot.edit', compact('salesBot'));
    }

    /**
     * Update Sales Bot configuration
     */
    public function update(Request $request, SalesBot $salesBot)
    {
        // Tenant access is now handled by route model binding

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'google_sheet_id' => 'required|string',
            'products_sheet_name' => 'required|string|max:100',
            'orders_sheet_name' => 'required|string|max:100',
            'working_hours.start' => 'nullable|string',
            'working_hours.end' => 'nullable|string',
            'working_hours.timezone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $salesBot->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Sales Bot updated successfully!',
                'data' => $salesBot->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Sales Bot: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync products from Google Sheets
     */
    public function syncProducts($salesBot): JsonResponse
    {
        try {
            // Handle both model objects and IDs (fallback for route binding issues)
            if (!$salesBot instanceof SalesBot) {
                $currentTenant = Tenant::current();
                if (!$currentTenant) {
                    // Fallback: try to get tenant from request subdomain
                    $subdomain = request()->route('subdomain');
                    if ($subdomain) {
                        $currentTenant = Tenant::where('subdomain', $subdomain)->first();
                    }
                }
                
                if (!$currentTenant) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No current tenant found',
                        'debug_info' => [
                            'subdomain' => request()->route('subdomain'),
                            'suggestion' => 'Check tenant middleware and subdomain routing'
                        ]
                    ], 400);
                }
                
                $salesBotId = $salesBot;
                
                // Handle case where route binding passes 'abc' or other non-numeric ID
                if (!is_numeric($salesBotId)) {
                    // Get the first available SalesBot for this tenant as a fallback
                    $salesBot = SalesBot::where('tenant_id', $currentTenant->id)->first();
                    
                    if (!$salesBot) {
                        // Get available SalesBots for debugging
                        $availableBots = SalesBot::where('tenant_id', $currentTenant->id)->get(['id', 'name']);
                        
                        return response()->json([
                            'success' => false,
                            'message' => "No SalesBot found for tenant {$currentTenant->id}. Invalid ID '{$salesBotId}' provided.",
                            'debug_info' => [
                                'requested_salesbot_id' => $salesBotId,
                                'tenant_id' => $currentTenant->id,
                                'tenant_key' => $currentTenant->tenant_key ?? 'Unknown',
                                'available_salesbots' => $availableBots->toArray(),
                                'suggestion' => 'Using the first available SalesBot for this tenant. Please check URL generation.'
                            ]
                        ], 404);
                    }
                    
                    // Log this issue for debugging
                    \Log::warning("SalesBot sync called with invalid ID: {$salesBotId}, using first available SalesBot ID: {$salesBot->id}");
                } else {
                    $salesBot = SalesBot::where('id', $salesBotId)
                        ->where('tenant_id', $currentTenant->id)
                        ->first();
                    
                    if (!$salesBot) {
                        // Get available SalesBots for debugging
                        $availableBots = SalesBot::where('tenant_id', $currentTenant->id)->get(['id', 'name']);
                        
                        return response()->json([
                            'success' => false,
                            'message' => "SalesBot with ID {$salesBotId} not found for tenant {$currentTenant->id}",
                            'debug_info' => [
                                'requested_salesbot_id' => $salesBotId,
                                'tenant_id' => $currentTenant->id,
                                'tenant_key' => $currentTenant->tenant_key ?? 'Unknown',
                                'available_salesbots' => $availableBots->toArray(),
                                'suggestion' => 'Create a SalesBot first or check the correct SalesBot ID'
                            ]
                        ], 404);
                    }
                }
            }

            $result = $this->salesBotService->syncProducts($salesBot);

            return response()->json([
                'success' => true,
                'message' => "Sync completed! {$result['synced']} products synced.",
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get products for Sales Bot
     */
    public function products(SalesBot $salesBot)
    {
        // Tenant access is now handled by route model binding

        $products = $salesBot->products()
            ->when(request('category'), fn($q, $category) => $q->byCategory($category))
            ->when(request('available') !== null, fn($q) => $q->where('is_available', request('available')))
            ->when(request('search'), fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->paginate(20);

        // Return JSON for API requests, view for web requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        }

        return view('tenant.sales-bot.products', compact('salesBot', 'products'));
    }

    /**
     * Get orders for Sales Bot
     */
    public function orders(SalesBot $salesBot)
    {
        // Tenant access is now handled by route model binding

        $orders = $salesBot->orders()
            ->with('contact')
            ->when(request('status'), fn($q, $status) => $q->byStatus($status))
            ->when(request('customer'), fn($q, $customer) => $q->byCustomer($customer))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Return JSON for API requests, view for web requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        }

        return view('tenant.sales-bot.orders', compact('salesBot', 'orders'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, SalesBot $salesBot, SalesBotOrder $order): JsonResponse
    {
        // Tenant access is now handled by route model binding

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'internal_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $order->update([
                'status' => $request->status,
                'internal_notes' => $request->internal_notes,
                $request->status . '_at' => now()
            ]);

            // Schedule upselling if order is delivered
            if ($request->status === 'delivered') {
                $this->salesBotService->processUpselling($salesBot, $order);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully!',
                'data' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics data
     */
    public function analytics(SalesBot $salesBot)
    {
        // Tenant access is now handled by route model binding

        $days = request('days', 30);
        $startDate = now()->subDays($days);

        $analytics = [
            'orders' => [
                'total' => $salesBot->orders()->where('created_at', '>=', $startDate)->count(),
                'revenue' => $salesBot->orders()->where('created_at', '>=', $startDate)->sum('total_amount'),
                'by_status' => $salesBot->orders()
                    ->where('created_at', '>=', $startDate)
                    ->selectRaw('status, count(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'daily' => $salesBot->orders()
                    ->where('created_at', '>=', $startDate)
                    ->selectRaw('DATE(created_at) as date, count(*) as orders, sum(total_amount) as revenue')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
            ],
            'products' => [
                'total' => $salesBot->products()->count(),
                'active' => $salesBot->activeProducts()->count(),
                'top_selling' => $salesBot->orders()
                    ->where('created_at', '>=', $startDate)
                    ->get()
                    ->flatMap(fn($order) => $order->products)
                    ->groupBy('name')
                    ->map(fn($products) => [
                        'name' => $products->first()['name'],
                        'quantity' => $products->sum('quantity'),
                        'revenue' => $products->sum(fn($p) => $p['price'] * $p['quantity'])
                    ])
                    ->sortByDesc('quantity')
                    ->take(10)
                    ->values()
            ],
            'reminders' => [
                'scheduled' => $salesBot->scheduledReminders()->count(),
                'sent' => $salesBot->reminders()->where('status', 'sent')
                    ->where('created_at', '>=', $startDate)->count(),
                'by_type' => $salesBot->reminders()
                    ->where('created_at', '>=', $startDate)
                    ->selectRaw('type, count(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type')
            ]
        ];

        // Return JSON for API requests, view for web requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
        }

        return view('tenant.sales-bot.analytics', compact('salesBot', 'analytics'));
    }

    /**
     * Test Google Sheets connection
     */
    public function testConnection(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'google_sheet_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $isValid = $this->googleSheetsService->validateSpreadsheet($request->google_sheet_id);
            
            if ($isValid) {
                $sheetNames = $this->googleSheetsService->getSheetNames($request->google_sheet_id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful!',
                    'data' => [
                        'sheet_names' => $sheetNames
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to connect to the spreadsheet. Please check the ID and permissions.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
