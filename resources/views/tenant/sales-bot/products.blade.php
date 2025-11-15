<x-app-layout>
    <x-slot:title>
        Products - {{ $salesBot->name }}
    </x-slot:title>
    
    <div class="mx-auto h-full">
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto bg-white dark:bg-gray-800">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-2 rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-300 mr-3">
                                <x-heroicon-o-cube class="w-6 h-6" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Products</h1>
                                <p class="text-gray-600 dark:text-gray-400">Manage your Sales Bot products</p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="syncProducts()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-150 ease-in-out">
                                <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                                Sync Products
                            </button>
                            <a href="{{ tenant_route('tenant.sales-bot.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-500 transition duration-150 ease-in-out">
                                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                                Back to Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- Search and Filters -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Products</label>
                                <input type="text" id="searchInput" placeholder="Search by name..." class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <select id="categoryFilter" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                    <option value="">All Categories</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Availability</label>
                                <select id="availabilityFilter" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                    <option value="">All Products</option>
                                    <option value="1">Available</option>
                                    <option value="0">Unavailable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Products List -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                        @if($products->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Sync</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($products as $product)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($product->images && count($product->images) > 0)
                                                        <img class="h-10 w-10 rounded-lg object-cover mr-3" src="{{ $product->images[0] }}" alt="{{ $product->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 mr-3 flex items-center justify-center">
                                                            <x-heroicon-o-photo class="w-5 h-5 text-gray-400" />
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($product->description, 50) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->currency }} {{ number_format($product->price, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                    {{ $product->category ?: 'Uncategorized' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($product->stock_quantity !== null)
                                                    <span class="text-sm text-gray-900 dark:text-white">{{ $product->stock_quantity }}</span>
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Unlimited</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_available ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                                    {{ $product->is_available ? 'Available' : 'Unavailable' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $product->synced_at ? $product->synced_at->diffForHumans() : 'Never' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                                {{ $products->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <x-heroicon-o-cube class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" />
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Products Found</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Sync your products from Google Sheets to get started.</p>
                                <button onclick="syncProducts()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-150 ease-in-out">
                                    <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                                    Sync Products
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function syncProducts() {
        if (confirm('This will sync products from your Google Sheet. Continue?')) {
            fetch('{{ tenant_route("tenant.sales-bot.sync-products", $salesBot) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Sync failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during sync');
            });
        }
    }
    </script>
</x-app-layout>
