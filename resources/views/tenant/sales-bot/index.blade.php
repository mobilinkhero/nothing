@extends('layouts.tenant')

@section('title', 'Sales Bot Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-robot text-primary me-2"></i>
                        Sales Bot Dashboard
                    </h4>
                    @if(!$salesBot)
                        <a href="{{ route('sales-bot.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Setup Sales Bot
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if(!$salesBot)
                        <div class="text-center py-5">
                            <i class="fas fa-robot fa-5x text-muted mb-4"></i>
                            <h3 class="text-muted mb-3">No Sales Bot Configured</h3>
                            <p class="lead text-muted mb-4">
                                Set up your Sales Bot to automate product sales, order management, and customer follow-ups.
                            </p>
                            <a href="{{ route('sales-bot.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Setup Sales Bot Now
                            </a>
                        </div>
                    @else
                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h3 class="mb-0">{{ $stats['total_products'] ?? 0 }}</h3>
                                                <small>Total Products</small>
                                            </div>
                                            <i class="fas fa-box fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h3 class="mb-0">{{ $stats['total_orders'] ?? 0 }}</h3>
                                                <small>Total Orders</small>
                                            </div>
                                            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h3 class="mb-0">${{ number_format($stats['monthly_revenue'] ?? 0, 2) }}</h3>
                                                <small>Monthly Revenue</small>
                                            </div>
                                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h3 class="mb-0">{{ $stats['scheduled_reminders'] ?? 0 }}</h3>
                                                <small>Scheduled Reminders</small>
                                            </div>
                                            <i class="fas fa-clock fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Quick Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <button class="btn btn-outline-primary w-100" onclick="syncProducts()">
                                                    <i class="fas fa-sync me-2"></i>
                                                    Sync Products
                                                </button>
                                            </div>
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <a href="{{ route('sales-bot.products', $salesBot) }}" class="btn btn-outline-info w-100">
                                                    <i class="fas fa-box me-2"></i>
                                                    View Products
                                                </a>
                                            </div>
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <a href="{{ route('sales-bot.orders', $salesBot) }}" class="btn btn-outline-success w-100">
                                                    <i class="fas fa-shopping-cart me-2"></i>
                                                    View Orders
                                                </a>
                                            </div>
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <a href="{{ route('sales-bot.analytics', $salesBot) }}" class="btn btn-outline-secondary w-100">
                                                    <i class="fas fa-chart-line me-2"></i>
                                                    Analytics
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bot Status -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Bot Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-{{ $salesBot->is_active ? 'success' : 'danger' }} me-2">
                                                {{ $salesBot->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            {{ $salesBot->name }}
                                        </div>
                                        <p class="text-muted mb-3">{{ $salesBot->description }}</p>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h6 class="text-success mb-0">{{ $stats['active_products'] ?? 0 }}</h6>
                                                <small class="text-muted">Active Products</small>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-warning mb-0">{{ $stats['pending_orders'] ?? 0 }}</h6>
                                                <small class="text-muted">Pending Orders</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Google Sheets Integration</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <small class="text-muted">Products Sheet:</small>
                                            <br>
                                            <code>{{ $salesBot->products_sheet_name }}</code>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">Orders Sheet:</small>
                                            <br>
                                            <code>{{ $salesBot->orders_sheet_name }}</code>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="testConnection()">
                                            <i class="fas fa-link me-1"></i>Test Connection
                                        </button>
                                        <a href="{{ route('sales-bot.edit', $salesBot) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-cog me-1"></i>Settings
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($salesBot)
<script>
function syncProducts() {
    if (confirm('This will sync products from your Google Sheet. Continue?')) {
        fetch('{{ route("sales-bot.sync-products", $salesBot) }}', {
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
                alert('Sync failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during sync');
        });
    }
}

function testConnection() {
    fetch('{{ route("sales-bot.test-connection") }}', {
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
@endsection
