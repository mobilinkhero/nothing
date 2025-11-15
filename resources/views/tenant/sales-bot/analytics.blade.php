<x-app-layout>
    <x-slot:title>
        Analytics - {{ $salesBot->name }}
    </x-slot:title>
    
    <div class="mx-auto h-full">
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto bg-white dark:bg-gray-800">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-2 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-300 mr-3">
                                <x-heroicon-o-chart-bar class="w-6 h-6" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Analytics</h1>
                                <p class="text-gray-600 dark:text-gray-400">Sales Bot performance insights</p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <select id="timeRange" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <option value="7">Last 7 days</option>
                                <option value="30" selected>Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                            <a href="{{ tenant_route('tenant.sales-bot.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-500 transition duration-150 ease-in-out">
                                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                                Back to Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- Overview Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Orders</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analytics['orders']['total'] ?? 0 }}</p>
                                </div>
                                <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900/20">
                                    <x-heroicon-o-shopping-cart class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($analytics['orders']['revenue'] ?? 0, 2) }}</p>
                                </div>
                                <div class="p-2 rounded-full bg-green-100 dark:bg-green-900/20">
                                    <x-heroicon-o-currency-dollar class="w-6 h-6 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Products</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analytics['products']['active'] ?? 0 }}</p>
                                </div>
                                <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900/20">
                                    <x-heroicon-o-cube class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Reminders Sent</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analytics['reminders']['sent'] ?? 0 }}</p>
                                </div>
                                <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900/20">
                                    <x-heroicon-o-bell class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Order Status Distribution -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Status Distribution</h3>
                            @if(!empty($analytics['orders']['by_status']))
                                <div class="space-y-3">
                                    @foreach($analytics['orders']['by_status'] as $status => $count)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $status }}</span>
                                            <div class="flex items-center space-x-3">
                                                <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                    <div class="h-2 rounded-full 
                                                        @switch($status)
                                                            @case('pending')
                                                                bg-yellow-500
                                                                @break
                                                            @case('confirmed')
                                                                bg-blue-500
                                                                @break
                                                            @case('processing')
                                                                bg-purple-500
                                                                @break
                                                            @case('shipped')
                                                                bg-indigo-500
                                                                @break
                                                            @case('delivered')
                                                                bg-green-500
                                                                @break
                                                            @case('cancelled')
                                                                bg-red-500
                                                                @break
                                                            @default
                                                                bg-gray-500
                                                        @endswitch
                                                    " style="width: {{ $analytics['orders']['total'] > 0 ? ($count / $analytics['orders']['total']) * 100 : 0 }}%"></div>
                                                </div>
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">No order data available</p>
                                </div>
                            @endif
                        </div>

                        <!-- Reminder Types -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reminder Activity</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $analytics['reminders']['scheduled'] ?? 0 }}</div>
                                    <div class="text-sm text-blue-700 dark:text-blue-300">Scheduled</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $analytics['reminders']['sent'] ?? 0 }}</div>
                                    <div class="text-sm text-green-700 dark:text-green-300">Sent</div>
                                </div>
                            </div>
                            @if(!empty($analytics['reminders']['by_type']))
                                <div class="mt-4 space-y-2">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">By Type:</h4>
                                    @foreach($analytics['reminders']['by_type'] as $type => $count)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $type) }}</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Top Selling Products -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Selling Products</h3>
                        @if(!empty($analytics['products']['top_selling']) && count($analytics['products']['top_selling']) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity Sold</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($analytics['products']['top_selling'] as $product)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product['name'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ $product['quantity'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($product['revenue'], 2) }}</div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <x-heroicon-o-chart-bar class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-3" />
                                <p class="text-gray-500 dark:text-gray-400">No sales data available yet</p>
                            </div>
                        @endif
                    </div>

                    <!-- Daily Orders Chart Placeholder -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Orders & Revenue</h3>
                        @if(!empty($analytics['orders']['daily']) && count($analytics['orders']['daily']) > 0)
                            <div class="space-y-3">
                                @foreach($analytics['orders']['daily'] as $day)
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $day->date }}</span>
                                        <div class="flex space-x-6">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $day->orders }} orders</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($day->revenue, 2) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <x-heroicon-o-chart-line class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-3" />
                                <p class="text-gray-500 dark:text-gray-400">No daily data available yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('timeRange').addEventListener('change', function() {
        const days = this.value;
        const url = new URL(window.location);
        url.searchParams.set('days', days);
        window.location.href = url.toString();
    });
    </script>
</x-app-layout>
