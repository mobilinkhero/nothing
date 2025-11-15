<x-app-layout>
    <x-slot:title>
        Setup Sales Bot
    </x-slot:title>
    <div class="mx-auto h-full">
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto bg-white dark:bg-gray-800">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 p-2 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 mr-3">
                            <x-heroicon-o-cog class="w-6 h-6" />
                        </div>
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Setup Sales Bot</h1>
                    </div>
                    
                    <form id="salesBotForm" class="space-y-6">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Bot Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" required
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                    <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Description
                                    </label>
                                    <input type="text" id="description" name="description"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                    <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Google Sheets Configuration -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Google Sheets Integration</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="google_sheet_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Google Sheet ID <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        Extract from URL: https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/edit
                                    </p>
                                    <div class="flex">
                                        <input type="text" id="google_sheet_id" name="google_sheet_id" required
                                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                        <button type="button" onclick="testConnection()"
                                                class="px-4 py-2 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-l-0 border-blue-200 dark:border-blue-800 rounded-r-md transition duration-150 ease-in-out">
                                            <x-heroicon-o-link class="w-4 h-4 inline mr-1" />
                                            Test
                                        </button>
                                    </div>
                                    <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="products_sheet_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Products Sheet Name
                                        </label>
                                        <input type="text" id="products_sheet_name" name="products_sheet_name" value="Products"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                    <div>
                                        <label for="orders_sheet_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Orders Sheet Name
                                        </label>
                                        <input type="text" id="orders_sheet_name" name="orders_sheet_name" value="Orders"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                                    <div class="flex items-center mb-2">
                                        <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" />
                                        <span class="font-medium text-blue-800 dark:text-blue-200">Setup Instructions:</span>
                                    </div>
                                    <ol class="text-blue-700 dark:text-blue-300 text-sm space-y-1 ml-4">
                                        <li>1. Create a Google Sheet with your products</li>
                                        <li>2. Add headers: Name, Description, Price, Currency, Category, Stock Quantity, Images, Tags, Available, Upsell Products</li>
                                        <li>3. Share the sheet with your service account email</li>
                                        <li>4. Copy the sheet ID from the URL and paste above</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Working Hours (Optional)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Start Time
                                    </label>
                                    <input type="time" id="start_time" name="working_hours[start]" value="09:00"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        End Time
                                    </label>
                                    <input type="time" id="end_time" name="working_hours[end]" value="18:00"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Timezone
                                    </label>
                                    <select id="timezone" name="working_hours[timezone]"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                        <option value="UTC">UTC</option>
                                        <option value="America/New_York">Eastern Time</option>
                                        <option value="America/Chicago">Central Time</option>
                                        <option value="America/Denver">Mountain Time</option>
                                        <option value="America/Los_Angeles">Pacific Time</option>
                                        <option value="Europe/London">London</option>
                                        <option value="Asia/Dubai">Dubai</option>
                                        <option value="Asia/Kolkata">India</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Reminder Settings -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reminder Settings</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Reminder Intervals (Days)</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" value="1" id="interval_1" name="reminder_settings[intervals][]" checked
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-600 dark:border-gray-600">
                                        <label for="interval_1" class="ml-2 text-sm text-gray-700 dark:text-gray-300">1 Day</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" value="3" id="interval_3" name="reminder_settings[intervals][]" checked
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-600 dark:border-gray-600">
                                        <label for="interval_3" class="ml-2 text-sm text-gray-700 dark:text-gray-300">3 Days</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" value="7" id="interval_7" name="reminder_settings[intervals][]" checked
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-600 dark:border-gray-600">
                                        <label for="interval_7" class="ml-2 text-sm text-gray-700 dark:text-gray-300">7 Days</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" value="14" id="interval_14" name="reminder_settings[intervals][]"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-600 dark:border-gray-600">
                                        <label for="interval_14" class="ml-2 text-sm text-gray-700 dark:text-gray-300">14 Days</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upselling Settings -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upselling Settings</h3>
                            <div class="max-w-md">
                                <label for="delay_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Upsell Delay (Days)
                                </label>
                                <input type="number" id="delay_days" name="upselling_settings[delay_days]" value="7" min="1"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Days to wait after order completion before sending upsell</p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between pt-6">
                            <a href="{{ tenant_route('tenant.sales-bot.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-600 hover:bg-gray-100 dark:hover:bg-gray-500 transition duration-150 ease-in-out">
                                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                                Cancel
                            </a>
                            <button type="submit" id="submitBtn"
                                    class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-150 ease-in-out">
                                <x-heroicon-o-check class="w-4 h-4 mr-2" />
                                Create Sales Bot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
document.getElementById('salesBotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Creating...';
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Handle checkbox arrays
    const intervals = [];
    document.querySelectorAll('input[name="reminder_settings[intervals][]"]:checked').forEach(cb => {
        intervals.push(parseInt(cb.value));
    });
    data.reminder_settings = { intervals };
    
    fetch('{{ tenant_route("tenant.sales-bot.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sales Bot created successfully!');
            window.location.href = '{{ tenant_route("tenant.sales-bot.index") }}';
        } else {
            alert('Error: ' + data.message);
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.remove('border-gray-300', 'dark:border-gray-600');
                        input.classList.add('border-red-500');
                        const feedback = input.parentNode.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.textContent = data.errors[field][0];
                            feedback.classList.remove('hidden');
                        }
                    }
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the Sales Bot');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

function testConnection() {
    const sheetId = document.getElementById('google_sheet_id').value;
    if (!sheetId) {
        alert('Please enter a Google Sheet ID first');
        return;
    }
    
    fetch('{{ tenant_route("tenant.sales-bot.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ google_sheet_id: sheetId })
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

// Clear validation errors on input
document.querySelectorAll('input, select, textarea').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300', 'dark:border-gray-600');
        const feedback = this.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.classList.add('hidden');
        }
    });
});
</script>
</x-app-layout>
