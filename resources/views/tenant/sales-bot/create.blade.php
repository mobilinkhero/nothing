@extends('layouts.tenant')

@section('title', 'Setup Sales Bot')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-robot text-primary me-2"></i>
                        Setup Sales Bot
                    </h4>
                </div>
                <div class="card-body">
                    <form id="salesBotForm">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Bot Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="description" name="description">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Google Sheets Configuration -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Google Sheets Integration</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="google_sheet_id" class="form-label">
                                                Google Sheet ID <span class="text-danger">*</span>
                                                <small class="text-muted d-block">
                                                    Extract from URL: https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/edit
                                                </small>
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="google_sheet_id" name="google_sheet_id" required>
                                                <button type="button" class="btn btn-outline-primary" onclick="testConnection()">
                                                    <i class="fas fa-link me-1"></i>Test
                                                </button>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="products_sheet_name" class="form-label">Products Sheet Name</label>
                                            <input type="text" class="form-control" id="products_sheet_name" name="products_sheet_name" value="Products">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="orders_sheet_name" class="form-label">Orders Sheet Name</label>
                                            <input type="text" class="form-control" id="orders_sheet_name" name="orders_sheet_name" value="Orders">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Setup Instructions:</strong>
                                    <ol class="mb-0 mt-2">
                                        <li>Create a Google Sheet with your products</li>
                                        <li>Add headers: Name, Description, Price, Currency, Category, Stock Quantity, Images, Tags, Available, Upsell Products</li>
                                        <li>Share the sheet with your service account email</li>
                                        <li>Copy the sheet ID from the URL and paste above</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Working Hours (Optional)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="start_time" class="form-label">Start Time</label>
                                            <input type="time" class="form-control" id="start_time" name="working_hours[start]" value="09:00">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="end_time" class="form-label">End Time</label>
                                            <input type="time" class="form-control" id="end_time" name="working_hours[end]" value="18:00">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Timezone</label>
                                            <select class="form-control" id="timezone" name="working_hours[timezone]">
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
                            </div>
                        </div>

                        <!-- Reminder Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Reminder Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Reminder Intervals (Days)</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="interval_1" name="reminder_settings[intervals][]" checked>
                                                <label class="form-check-label" for="interval_1">1 Day</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="3" id="interval_3" name="reminder_settings[intervals][]" checked>
                                                <label class="form-check-label" for="interval_3">3 Days</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="7" id="interval_7" name="reminder_settings[intervals][]" checked>
                                                <label class="form-check-label" for="interval_7">7 Days</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="14" id="interval_14" name="reminder_settings[intervals][]">
                                                <label class="form-check-label" for="interval_14">14 Days</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upselling Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Upselling Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="delay_days" class="form-label">Upsell Delay (Days)</label>
                                            <input type="number" class="form-control" id="delay_days" name="upselling_settings[delay_days]" value="7" min="1">
                                            <small class="text-muted">Days to wait after order completion before sending upsell</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tenant.sales-bot.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Create Sales Bot
                            </button>
                        </div>
                    </form>
                </div>
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
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Handle checkbox arrays
    const intervals = [];
    document.querySelectorAll('input[name="reminder_settings[intervals][]"]:checked').forEach(cb => {
        intervals.push(parseInt(cb.value));
    });
    data.reminder_settings = { intervals };
    
    fetch('{{ route("tenant.sales-bot.store") }}', {
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
            window.location.href = '{{ route("tenant.sales-bot.index") }}';
        } else {
            alert('Error: ' + data.message);
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.parentNode.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.textContent = data.errors[field][0];
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
    
    fetch('{{ route("tenant.sales-bot.test-connection") }}', {
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
        this.classList.remove('is-invalid');
    });
});
</script>
@endsection
