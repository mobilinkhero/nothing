# Sales Bot System - Setup Instructions

## 🎉 Complete Sales Bot System

Your Sales Bot system has been successfully created! Here's what's been implemented:

### ✅ Features Included:
- **Tenant-specific Sales Bot** - Each tenant has their own isolated bot
- **Google Sheets Integration** - Product sync and order storage
- **Automated Reminders** - Cart abandonment, follow-ups, upselling
- **Order Management** - Complete order tracking with status updates
- **Analytics & Reporting** - Customer behavior tracking
- **WhatsApp Integration** - Native WhatsApp Business API support

### 📁 Files Created:
- **5 Database Tables** - All properly created and indexed
- **5 Eloquent Models** - Complete with relationships
- **Controller & Routes** - Full CRUD operations
- **Services** - Google Sheets & Business logic
- **Jobs** - Automated reminder processing
- **Views** - Dashboard and setup forms
- **Navigation** - Added to tenant sidebar

## 🚀 Setup Instructions:

### 1. **Database Setup** ✅ COMPLETED
```sql
-- Tables are already created via SQL
```

### 2. **Google Sheets Setup** (Required)
```bash
# Add your Google service account JSON file to:
storage/app/google-sheets-credentials.json
```

### 3. **Queue Setup** (Required)
```bash
# Start queue processing for automated reminders
php artisan queue:work
```

### 4. **Cron Job Setup** (Recommended)
Add to your crontab for automated reminder processing:
```bash
* * * * * php /path/to/your/app/artisan process-sales-bot-reminders
```

### 5. **Test the System**
```bash
# Run the setup verification command
php artisan sales-bot:setup
```

## 🎯 How to Use:

### 1. **Access Sales Bot**
- Navigate to your tenant dashboard
- Click on "Sales Bot" in the sidebar
- Click "Setup Sales Bot" if none exists

### 2. **Configure Your Bot**
- **Name**: Give your bot a descriptive name
- **Google Sheet ID**: Extract from your sheet URL
- **Sheet Names**: Default are "Products" and "Orders"
- **Working Hours**: Optional bot availability window
- **Reminders**: Configure follow-up intervals

### 3. **Setup Google Sheets**
Create a Google Sheet with these columns in "Products" sheet:
```
Name | Description | Price | Currency | Category | Stock Quantity | Images | Tags | Available | Upsell Products
```

### 4. **Test Connection**
- Use the "Test Connection" button in setup
- Sync products using "Sync Products" button

## 📊 Google Sheets Structure:

### Products Sheet:
| Column | Description | Example |
|--------|-------------|---------|
| Name | Product name | "iPhone 15 Pro" |
| Description | Product details | "Latest smartphone" |
| Price | Price in numbers | 999.99 |
| Currency | Currency code | "USD" |
| Category | Product category | "Electronics" |
| Stock Quantity | Available qty | 50 |
| Images | Comma-separated URLs | "url1,url2" |
| Tags | Comma-separated tags | "phone,apple" |
| Available | true/false | "true" |
| Upsell Products | Product IDs | "2,3,4" |

### Orders Sheet:
Auto-populated when orders are placed via WhatsApp.

## 🔧 Advanced Configuration:

### Reminder Templates:
Customize in the bot settings:
- **Cart Abandonment**: "Hi {customer_name}! You left items in cart..."
- **Order Follow-up**: "Thanks for order #{order_number}..."
- **Upselling**: "Based on your purchase, you might like..."

### Working Hours:
- Set specific hours when bot responds
- Configure timezone
- Outside hours: reminders are rescheduled

### Analytics:
- View product performance
- Track customer interactions
- Monitor reminder effectiveness
- Revenue reporting

## 🚨 Troubleshooting:

### Common Issues:
1. **Google Sheets Access Denied**
   - Check service account permissions
   - Ensure sheet is shared with service account email

2. **Reminders Not Sending**
   - Verify queue is running: `php artisan queue:work`
   - Check job failures: `php artisan queue:failed`

3. **Navigation Not Showing**
   - Clear cache: `php artisan cache:clear`
   - Check feature flags in admin

4. **Orders Not Syncing**
   - Verify Google Sheets write permissions
   - Check WhatsApp webhook configuration

## 🎯 Next Steps:

1. **Create your first Sales Bot**
2. **Setup Google Sheets with products**
3. **Test the complete flow**
4. **Configure reminders and upselling**
5. **Monitor analytics and optimize**

## 🆘 Support:

The system is now production-ready with complete tenant isolation, automated processing, and comprehensive error handling.

**Enjoy your new Sales Bot system!** 🎉
