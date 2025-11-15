# 🔍 **SalesBot Not Found - Diagnostic & Fix**

## 🚨 **Error Details:**
```
No query results for model [App\Models\Tenant\SalesBot]
URL: /abc/sales-bot/2/sync-products
Tenant ID: 1
SalesBot ID: 2 (NOT FOUND)
```

## 🎯 **Root Cause:**
SalesBot with ID 2 doesn't exist for tenant ID 1. This means:
- ✅ **Tenant exists** (ID: 1, subdomain: "abc")
- ❌ **SalesBot ID 2 doesn't exist** for this tenant

## 🔧 **Diagnostic Commands:**

### **1. Check SalesBots for Current Tenant:**
```bash
php artisan sales-bot:debug 1
```

### **2. Check All Tenants and SalesBots:**
```bash
php artisan sales-bot:debug
```

### **3. Check Database Directly:**
```sql
-- Check tenant
SELECT * FROM tenants WHERE id = 1;

-- Check SalesBots for tenant 1
SELECT * FROM sales_bots WHERE tenant_id = 1;

-- Check if SalesBot ID 2 exists anywhere
SELECT * FROM sales_bots WHERE id = 2;
```

## ✅ **Solutions:**

### **Option 1: Create a New SalesBot (Recommended)**
1. **Go to**: `/abc/sales-bot/create`
2. **Fill out the form** with:
   - Name: Your Sales Bot name
   - Google Sheet ID: Your spreadsheet ID
   - Products Sheet: "Products"
   - Orders Sheet: "Orders"
3. **Click "Create"**

### **Option 2: Use Correct SalesBot ID**
If SalesBots exist but wrong ID was used:
1. **Run diagnostic**: `php artisan sales-bot:debug 1`
2. **Find correct ID** from the output
3. **Use correct URL**: `/abc/sales-bot/{correct_id}/sync-products`

### **Option 3: Import/Restore SalesBot**
If SalesBot was accidentally deleted:
1. **Check database backup**
2. **Restore from backup** if needed
3. **Recreate manually** if no backup exists

## 🚨 **Enhanced Error Handling Applied:**

The `syncProducts` method now provides detailed error information:

```json
{
    "success": false,
    "message": "SalesBot with ID 2 not found for tenant 1",
    "debug_info": {
        "requested_salesbot_id": 2,
        "tenant_id": 1,
        "tenant_key": "abc",
        "available_salesbots": [
            // Lists available SalesBots for this tenant
        ],
        "suggestion": "Create a SalesBot first or check the correct SalesBot ID"
    }
}
```

## 🎯 **Immediate Actions:**

### **Step 1: Check Available SalesBots**
```bash
php artisan sales-bot:debug 1
```

### **Step 2: If No SalesBots Found**
- **Create new**: Go to `/abc/sales-bot/create`
- **Setup Google Sheets integration**
- **Test the connection**

### **Step 3: If SalesBots Exist**
- **Use correct ID** from the diagnostic output
- **Update bookmarks/URLs** with correct ID

## 📋 **Prevention:**

### **Bookmark the Dashboard**
Instead of specific SalesBot URLs, bookmark:
- **Dashboard**: `/abc/sales-bot/`
- **Create Page**: `/abc/sales-bot/create`

### **Use Dashboard Navigation**
Always start from the dashboard which automatically uses the correct SalesBot ID.

## 🧪 **Test Commands:**

```bash
# 1. Debug current tenant's SalesBots
php artisan sales-bot:debug 1

# 2. Create test SalesBot (if needed)
# Use the web interface at /abc/sales-bot/create

# 3. Test the new SalesBot
# Use correct ID from step 1
```

## 🎉 **Expected Results:**
- ✅ **Clear diagnostic info** showing available SalesBots
- ✅ **Correct SalesBot ID** identified
- ✅ **Working sync products** functionality
- ✅ **Proper navigation** from dashboard

Run the diagnostic command first to see what SalesBots are available for your tenant! 🚀
