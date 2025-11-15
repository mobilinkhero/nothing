<x-app-layout>
    <x-slot:title>
        Orders - {{ $salesBot->name }}
    </x-slot:title>
    
    <div class="mx-auto h-full">
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto bg-white dark:bg-gray-800">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-2 rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-300 mr-3">
                                <x-heroicon-o-shopping-cart class="w-6 h-6" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Orders</h1>
                                <p class="text-gray-600 dark:text-gray-400">Manage your Sales Bot orders</p>
                            </div>
                        </div>
                        <a href="{{ tenant_route('tenant.sales-bot.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-500 transition duration-150 ease-in-out">
                            <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                            Back to Dashboard
                        </a>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Customer</label>
                                <input type="text" id="customerSearch" placeholder="Search by customer name or phone..." class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order Status</label>
                                <select id="statusFilter" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="processing">Processing</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Range</label>
                                <select class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Orders List -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                        @if($orders->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Products</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($orders as $order)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">#{{ $order->order_number }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->customer_name ?: 'Anonymous' }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->customer_phone }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    @if($order->products && count($order->products) > 0)
                                                        @foreach($order->products as $product)
                                                            <div class="mb-1">{{ $product['name'] ?? 'Product' }} (x{{ $product['quantity'] ?? 1 }})</div>
                                                        @endforeach
                                                    @else
                                                        <span class="text-gray-500 dark:text-gray-400">No products</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->currency }} {{ number_format($order->total_amount, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @switch($order->status)
                                                        @case('pending')
                                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                                            @break
                                                        @case('confirmed')
                                                            bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                                                            @break
                                                        @case('processing')
                                                            bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400
                                                            @break
                                                        @case('shipped')
                                                            bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400
                                                            @break
                                                        @case('delivered')
                                                            bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                                            @break
                                                        @case('cancelled')
                                                            bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                                            @break
                                                        @default
                                                            bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                                                    @endswitch
                                                ">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $order->created_at->format('M j, Y') }}
                                                <div class="text-xs">{{ $order->created_at->format('g:i A') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button onclick="updateOrderStatus({{ $order->id }}, '{{ $order->status }}')" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                        Edit
                                                    </button>
                                                    <button onclick="viewOrderDetails({{ $order->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                        View
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                                {{ $orders->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <x-heroicon-o-shopping-cart class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" />
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Orders Found</h3>
                                <p class="text-gray-600 dark:text-gray-400">Orders will appear here once customers start purchasing through your Sales Bot.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Update Order Status</h3>
                    <form id="statusForm">
                        <input type="hidden" id="orderId">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select id="newStatus" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Internal Notes</label>
                            <textarea id="internalNotes" rows="3" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" placeholder="Add any internal notes..."></textarea>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeStatusModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-500">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function updateOrderStatus(orderId, currentStatus) {
        document.getElementById('orderId').value = orderId;
        document.getElementById('newStatus').value = currentStatus;
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }

    function viewOrderDetails(orderId) {
        // For now, just alert - could expand to show detailed modal
        alert('Order details functionality coming soon!');
    }

    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const orderId = document.getElementById('orderId').value;
        const status = document.getElementById('newStatus').value;
        const notes = document.getElementById('internalNotes').value;
        
        fetch(`{{ tenant_route("tenant.sales-bot.orders.update-status", [$salesBot, "ORDER_ID"]) }}`.replace('ORDER_ID', orderId), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status,
                internal_notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order status updated successfully!');
                location.reload();
            } else {
                alert('Failed to update order status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating order status');
        })
        .finally(() => {
            closeStatusModal();
        });
    });
    </script>
</x-app-layout>
