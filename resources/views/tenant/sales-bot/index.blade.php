<x-app-layout>
    <x-slot:title>
        Sales Bot Dashboard
    </x-slot:title>
    <div class="mx-auto h-full">
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto bg-white dark:bg-gray-800">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-2 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 mr-3">
                                <x-heroicon-o-cog class="w-6 h-6" />
                            </div>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Sales Bot Dashboard</h1>
                        </div>
                        @if(!$salesBot)
                            <a href="{{ tenant_route('tenant.sales-bot.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-150 ease-in-out">
                                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                                Setup Sales Bot
                            </a>
                        @endif
                    </div>
                    @if(!$salesBot)
                        <div class="text-center py-12">
                            <div class="flex justify-center mb-6">
                                <div class="p-4 rounded-full bg-gray-100 dark:bg-gray-700">
                                    <x-heroicon-o-cog class="w-16 h-16 text-gray-400 dark:text-gray-500" />
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">No Sales Bot Configured</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                                Set up your Sales Bot to automate product sales, order management, and customer follow-ups.
                            </p>
                            <a href="{{ tenant_route('tenant.sales-bot.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 ease-in-out">
                                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                Setup Sales Bot Now
                            </a>
                        </div>
                    @else
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-600 dark:text-blue-400 text-sm font-medium">Total Products</p>
                                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $stats['total_products'] ?? 0 }}</p>
                                    </div>
                                    <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-800">
                                        <x-heroicon-o-cube class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-600 dark:text-green-400 text-sm font-medium">Total Orders</p>
                                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $stats['total_orders'] ?? 0 }}</p>
                                    </div>
                                    <div class="p-2 rounded-full bg-green-100 dark:bg-green-800">
                                        <x-heroicon-o-shopping-cart class="w-6 h-6 text-green-600 dark:text-green-400" />
                                    </div>
                                </div>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-purple-600 dark:text-purple-400 text-sm font-medium">Monthly Revenue</p>
                                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">${{ number_format($stats['monthly_revenue'] ?? 0, 2) }}</p>
                                    </div>
                                    <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-800">
                                        <x-heroicon-o-currency-dollar class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                    </div>
                                </div>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-yellow-600 dark:text-yellow-400 text-sm font-medium">Scheduled Reminders</p>
                                        <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $stats['scheduled_reminders'] ?? 0 }}</p>
                                    </div>
                                    <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-800">
                                        <x-heroicon-o-clock class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <button onclick="syncProducts()" 
                                        class="flex items-center justify-center px-4 py-3 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-lg transition duration-150 ease-in-out">
                                    <x-heroicon-o-arrow-path class="w-5 h-5 mr-2" />
                                    Sync Products
                                </button>
                                <a href="{{ tenant_route('tenant.sales-bot.products', ['salesBot' => $salesBot->id]) }}" 
                                   class="flex items-center justify-center px-4 py-3 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/20 dark:hover:bg-purple-900/30 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded-lg transition duration-150 ease-in-out">
                                    <x-heroicon-o-cube class="w-5 h-5 mr-2" />
                                    View Products
                                </a>
                                <a href="{{ tenant_route('tenant.sales-bot.orders', ['salesBot' => $salesBot->id]) }}" 
                                   class="flex items-center justify-center px-4 py-3 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800 rounded-lg transition duration-150 ease-in-out">
                                    <x-heroicon-o-shopping-cart class="w-5 h-5 mr-2" />
                                    View Orders
                                </a>
                                <a href="{{ tenant_route('tenant.sales-bot.analytics', ['salesBot' => $salesBot->id]) }}" 
                                   class="flex items-center justify-center px-4 py-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-lg transition duration-150 ease-in-out">
                                    <x-heroicon-o-chart-bar class="w-5 h-5 mr-2" />
                                    Analytics
                                </a>
                                <button class="flex items-center justify-center px-4 py-3 bg-orange-50 hover:bg-orange-100 dark:bg-orange-900/20 dark:hover:bg-orange-900/30 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800 rounded-lg transition duration-150 ease-in-out">
                                    <x-heroicon-o-cog class="w-5 h-5 mr-2" />
                                    Settings
                                </button>
                            </div>
                        </div>

                        <!-- Bot Status & Settings -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bot Status</h3>
                                <div class="flex items-center mb-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $salesBot->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                        {{ $salesBot->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">{{ $salesBot->name }}</span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $salesBot->description }}</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                        <div class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $stats['active_products'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Active Products</div>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                        <div class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_orders'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Pending Orders</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Google Sheets Integration</h3>
                                <div class="space-y-3 mb-4">
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Products Sheet:</label>
                                        <div class="mt-1 p-2 bg-gray-50 dark:bg-gray-600 rounded-md">
                                            <code class="text-sm text-gray-800 dark:text-gray-200">{{ $salesBot->products_sheet_name }}</code>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Orders Sheet:</label>
                                        <div class="mt-1 p-2 bg-gray-50 dark:bg-gray-600 rounded-md">
                                            <code class="text-sm text-gray-800 dark:text-gray-200">{{ $salesBot->orders_sheet_name }}</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="testConnection()" 
                                            class="inline-flex items-center px-3 py-2 border border-blue-300 dark:border-blue-700 text-sm font-medium rounded-md text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition duration-150 ease-in-out">
                                        <x-heroicon-o-link class="w-4 h-4 mr-1" />
                                        Test Connection
                                    </button>
                                    <a href="{{ tenant_route('tenant.sales-bot.edit', $salesBot) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-500 transition duration-150 ease-in-out">
                                        <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-1" />
                                        Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@if($salesBot)
<script>
function syncProducts() {
    // Debug: Show the URL being generated
    const url = '{{ $salesBot ? tenant_route("tenant.sales-bot.sync-products", ["salesBot" => $salesBot->id]) : "#" }}';
    console.log('Generated URL:', url);
    alert('URL being used: ' + url);
    
    if (confirm('This will sync products from your Google Sheet. Continue?')) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Products synced successfully! ' + data.message);
                location.reload();
            } else {
                // Enhanced error handling with debug info
                let errorMessage = 'Sync failed: ' + data.message;
                
                if (data.debug_info) {
                    console.error('Debug Info:', data.debug_info);
                    errorMessage += '\n\nDebug Information:';
                    errorMessage += '\nRequested SalesBot ID: ' + data.debug_info.requested_salesbot_id;
                    errorMessage += '\nTenant ID: ' + data.debug_info.tenant_id;
                    
                    if (data.debug_info.available_salesbots && data.debug_info.available_salesbots.length > 0) {
                        errorMessage += '\n\nAvailable SalesBots for your tenant:';
                        data.debug_info.available_salesbots.forEach(bot => {
                            errorMessage += '\n- ID: ' + bot.id + ', Name: ' + bot.name;
                        });
                        errorMessage += '\n\nPlease use the correct SalesBot ID or create a new one.';
                    } else {
                        errorMessage += '\n\nNo SalesBots found for your tenant.';
                        errorMessage += '\nPlease create a SalesBot first.';
                        
                        if (confirm(errorMessage + '\n\nWould you like to create a SalesBot now?')) {
                            window.location.href = '{{ tenant_route("tenant.sales-bot.create") }}';
                            return;
                        }
                    }
                }
                
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during sync. Please check the console for details.');
        });
    }
}

function testConnection() {
    fetch('{{ tenant_route("tenant.sales-bot.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            google_sheet_id: '{{ $salesBot->google_sheet_id }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Connection successful! Found sheets: ' + data.data.sheet_names.join(', '));
        } else {
            alert('Connection failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while testing connection');
    });
}
</script>
@endif
</x-app-layout>
