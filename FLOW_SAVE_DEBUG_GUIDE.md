# 🔍 Flow Save Debug System

## 📍 What This Does
This system logs **every attempt** to save a flow (successful or failed) to help you understand exactly what's happening when you try to save your flow builder configurations.

## 🌐 How to Access Debug Logs

### **Method 1: Web Interface (Recommended)**
**URL**: `http://your-domain.com/debug-flow-logs`

**Features**:
- ✅ Real-time log viewing
- ✅ Auto-refresh every 10 seconds
- ✅ Clear/Delete all logs button
- ✅ Manual refresh button
- ✅ Syntax highlighting

### **Method 2: File System**
**File Location**: `c:\wamp64\www\botflow_save_debug.log`

**View via SSH**:
```bash
# View all logs
cat botflow_save_debug.log

# View last 50 lines
tail -50 botflow_save_debug.log

# Watch live (real-time)
tail -f botflow_save_debug.log

# Clear logs
> botflow_save_debug.log
```

## 📊 What Gets Logged

### **1. Every Save Request**
```
================================================================================
[2025-11-14 23:17:30] BotFlow Save Request
================================================================================
TIMESTAMP: 2025-11-14 23:17:30
TENANT_ID: 123
HAS_FLOW_DATA: true
HAS_NAME: false
HAS_ID: true
ID_VALUE: 456
REQUEST_DATA:
{
    "id": 456,
    "flow_data": "{\"nodes\":[...],\"edges\":[...]}"
}
================================================================================
```

### **2. Validation Failures** (if any)
```
================================================================================
[2025-11-14 23:17:30] BotFlow Save Validation FAILED
================================================================================
TIMESTAMP: 2025-11-14 23:17:30
VALIDATION_TYPE: Flow Data Save
ERRORS:
{
    "id": [
        "The selected id is invalid."
    ]
}
REQUEST_DATA: {...}
================================================================================
```

### **3. Successful Saves**
```
================================================================================
[2025-11-14 23:17:31] BotFlow Save SUCCESS
================================================================================
TIMESTAMP: 2025-11-14 23:17:31
FLOW_ID: 456
TENANT_ID: 123
MESSAGE: Flow updated successfully
FLOW_NAME: My Test Flow
IS_ACTIVE: true
================================================================================
```

## 🚨 Common Issues & Solutions

### **Issue 1: "The selected id is invalid"**
**Cause**: Flow ID doesn't exist in database
**Solution**: 
1. Check if flow exists: `SELECT * FROM bot_flows WHERE id = YOUR_ID;`
2. Create new flow first, then edit it

### **Issue 2: "The flow data field is required"**
**Cause**: Empty or missing flow_data
**Solution**: Make sure your flow has at least one node before saving

### **Issue 3: "Validation failed - json"**
**Cause**: Malformed JSON in flow_data
**Solution**: Check browser console for JavaScript errors

### **Issue 4: No logs appearing**
**Cause**: Route not working or permissions issue
**Solution**: 
1. Check if route exists: `php artisan route:list | grep debug`
2. Clear route cache: `php artisan route:clear`

## 🛠️ Troubleshooting Steps

### **Step 1: Clear Old Logs**
1. Visit: `http://your-domain.com/debug-flow-logs`
2. Click "🗑️ Clear Logs" button

### **Step 2: Test Save**
1. Go to your Flow Builder
2. Make a small change
3. Click "Save Flow"

### **Step 3: Check Logs Immediately**
1. Refresh the debug logs page
2. Look for the latest entry
3. Check if it shows "Request", "FAILED", or "SUCCESS"

### **Step 4: Analyze Results**

**If you see "BotFlow Save Request" but no SUCCESS/FAILED**:
- Server error occurred
- Check Laravel logs: `tail -f storage/logs/laravel.log`

**If you see "Validation FAILED"**:
- Look at the ERRORS section
- Fix the validation issue (usually missing ID or invalid JSON)

**If you see "SUCCESS"**:
- Save worked! Check your flow in the database
- Verify flow_data was updated

## 🔄 Auto-Refresh Feature
The web interface automatically refreshes every **10 seconds**, so you can:
1. Open the debug page
2. Keep it open in another tab
3. Try saving your flow
4. Watch the logs appear in real-time

## 📁 File Structure
```
c:\wamp64\www\
├── botflow_save_debug.log          # Debug logs (home directory)
├── storage/logs/laravel.log        # Laravel system logs
└── routes/web.php                  # Debug routes added here
```

## 🧹 Maintenance

### **Clear Logs Periodically**
```bash
# Via web interface
http://your-domain.com/debug-flow-logs -> Clear Logs button

# Via SSH
> botflow_save_debug.log
```

### **File Size Monitoring**
If logs get too large, they'll slow down the page. Clear them regularly.

## 🔗 Quick Links

- **Debug Logs**: `http://your-domain.com/debug-flow-logs`
- **Flow Builder**: `http://your-domain.com/{tenant}/admin/bot-flows`
- **Laravel Logs**: `storage/logs/laravel.log`

---

## 🎯 Next Steps

1. **Visit**: `http://your-domain.com/debug-flow-logs`
2. **Clear** old logs if any exist
3. **Try** saving your flow
4. **Check** the logs to see exactly what's happening
5. **Share** the log output if you need help debugging

**This system will show you exactly why your flow isn't saving!** 🔍
