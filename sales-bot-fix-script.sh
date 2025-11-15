#!/bin/bash

echo "🔧 Sales Bot System - Comprehensive Fix Script"
echo "=============================================="

# Step 1: Install Google API Client
echo "📦 Installing Google API Client packages..."
composer require google/apiclient google/apiclient-services --no-interaction

# Step 2: Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear  
php artisan route:clear
php artisan view:clear

# Step 3: Refresh autoloader
echo "🔄 Refreshing autoloader..."
composer dump-autoload

# Step 4: Run migrations (if needed)
echo "🗃️ Running migrations..."
php artisan migrate --force

# Step 5: Check system requirements
echo "✅ Verifying system requirements..."
php artisan sales-bot:setup

echo ""
echo "🎉 Sales Bot System Fix Complete!"
echo "=================================="
echo "✅ Google API Client packages installed"
echo "✅ Laravel caches cleared" 
echo "✅ Routes refreshed"
echo "✅ Autoloader updated"
echo ""
echo "🚀 You can now access your Sales Bot at: /sales-bot"
echo ""
echo "📋 Next Steps:"
echo "1. Create Google Sheets credentials (if not done)"
echo "2. Share your Google Sheet with service account"
echo "3. Test the Sales Bot creation flow"
echo ""
