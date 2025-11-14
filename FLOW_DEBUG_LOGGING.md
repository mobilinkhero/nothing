# Flow Builder Debug Logging

## 📋 Overview
The Flow Builder now includes comprehensive debug logging to help troubleshoot issues during flow execution on both **local** and **live servers**.

## 📍 Log File Locations

### **Primary Location** (Main Directory)
```
flow_debug.log
```
Located in your application's root directory (same level as `app/`, `public/`, `storage/`)

**Local Server**: `c:\wamp64\www\flow_debug.log`
**Live Server**: `/home/username/public_html/flow_debug.log` (or your server's root path)

### **Fallback Location** (if main directory not writable)
```
storage/logs/flow_debug.log
```

The system automatically detects if the main directory is writable and falls back to `storage/logs/` if needed.

## 🔍 What Gets Logged

### All Flow Executions
- Node type being executed
- Recipient phone number
- Phone number ID
- Contact ID
- Node data structure

### Quick Replies Specific
- **Starting**: When Quick Replies node begins processing
  - Full node data
  - Phone number details
- **Validation**: Check if replies are configured correctly
- **Payload Building**: The complete WhatsApp API payload
  - Number of buttons
  - Message content
- **API Communication**: 
  - API loaded status
  - Request details
- **API Response**:
  - Success/failure status
  - Response code (200, 400, etc.)
  - Full response body
- **Exceptions**: Full error details with stack trace

### Tag Management & Variable Management
- Similar detailed logging for all operations
- Error tracking with full context

## 📖 How to Use

### 1. Run Your Test
```
1. Open WhatsApp and send a message to trigger your flow
2. The flow will execute and log everything
```

### 2. Check the Log File

#### **On Local Server (Windows)**
```powershell
# View latest entries
Get-Content c:\wamp64\www\flow_debug.log -Tail 50

# View all
Get-Content c:\wamp64\www\flow_debug.log

# Monitor in real-time
Get-Content c:\wamp64\www\flow_debug.log -Wait -Tail 20
```

#### **On Live Server (Linux/SSH)**
```bash
# Navigate to main directory
cd /home/username/public_html

# View latest entries
tail -n 50 flow_debug.log

# View all
cat flow_debug.log

# Monitor in real-time
tail -f flow_debug.log

# Search for errors
grep -i "error\|exception" flow_debug.log

# View with line numbers
nl flow_debug.log | tail -50
```

#### **Via FTP/File Manager**
1. Connect to your server via FTP or cPanel File Manager
2. Navigate to the main directory (root of your application)
3. Download `flow_debug.log` file
4. Open with a text editor

### 3. Read the Log Format
```
=== [LIVE SERVER] 2025-11-14 21:55:30 ===
MESSAGE: Quick Replies: Starting
DATA: {
    "to": "+1234567890",
    "nodeData": {
        "message": "Choose an option",
        "replies": [...]
    },
    "phoneNumberId": "123456789"
}
MEMORY: 25.50 MB
LOG FILE: /home/username/public_html/flow_debug.log
====================================================================================================

```

**Note**: Local server logs show `[LOCAL]` instead of `[LIVE SERVER]`

## 🔧 Troubleshooting Guide

### Issue: "No valid quick replies configured"
**Log Entry**: Look for `Quick Replies: No valid replies`
**Solution**: Check that reply text fields are not empty

### Issue: API Error (400, 401, 403, 500)
**Log Entry**: Look for `Quick Replies: API Response` with `response_code`
**Possible Causes**:
- **400**: Invalid payload format
- **401**: Authentication issue (check phone number ID)
- **403**: Permission denied
- **500**: WhatsApp API server error

**Solution**: Check the `response_body` in logs for exact error message

### Issue: Exception Thrown
**Log Entry**: Look for `Quick Replies: EXCEPTION`
**Details**: Full error message and stack trace provided
**Solution**: Share the exception details for debugging

## 🧹 Log Maintenance

### Clear Old Logs
```bash
# Delete the log file
Remove-Item flow_debug.log

# Or clear content
Clear-Content flow_debug.log
```

### Monitor in Real-Time
```bash
# Windows PowerShell
Get-Content flow_debug.log -Wait -Tail 20
```

## 🚨 Critical Errors

Critical errors (those containing "error" or "exception") are also logged to Laravel's standard log at:
```
storage/logs/laravel.log
```

## 📊 Log Entry Structure

Each log entry contains:
- **Timestamp**: Exact time of the event
- **Message**: What happened
- **Data**: Contextual information (JSON formatted)
- **Memory**: Current memory usage
- **Separator**: Visual separator between entries

## 💡 Tips

1. **Before Testing**: Clear or delete the log file for clean results
2. **After Error**: Check the most recent entries (bottom of file)
3. **Share Logs**: When reporting issues, share the relevant log section
4. **Privacy**: Log file may contain phone numbers - sanitize before sharing

## 🔐 Security Note

The `flow_debug.log` file may contain sensitive information:
- Phone numbers
- User messages
- API credentials (masked in logs)

**Recommendation**: Add `flow_debug.log` to `.gitignore` and don't commit it to version control.

## 📝 Example Debug Session

```
=== [LIVE SERVER] 2025-11-14 21:55:30 ===
MESSAGE: Flow Execution Started
DATA: {
    "node_type": "quickReplies",
    "to": "+1234567890",
    "phone_number_id": "123456789",
    "contact_id": "456",
    "node_data_keys": ["message", "replies", "trackAnalytics"]
}
MEMORY: 25.30 MB
LOG FILE: /home/username/public_html/flow_debug.log
====================================================================================================

=== [LIVE SERVER] 2025-11-14 21:55:30 ===
MESSAGE: Quick Replies: Starting
DATA: {
    "to": "+1234567890",
    "nodeData": { ... },
    "phoneNumberId": "123456789"
}
MEMORY: 25.35 MB
LOG FILE: /home/username/public_html/flow_debug.log
====================================================================================================

=== [LIVE SERVER] 2025-11-14 21:55:30 ===
MESSAGE: Quick Replies: Payload built
DATA: {
    "payload": {
        "messaging_product": "whatsapp",
        "type": "interactive",
        "interactive": { ... }
    },
    "buttons_count": 3
}
MEMORY: 25.40 MB
LOG FILE: /home/username/public_html/flow_debug.log
====================================================================================================

=== [LIVE SERVER] 2025-11-14 21:55:31 ===
MESSAGE: Quick Replies: API Response
DATA: {
    "result": {
        "status": true,
        "responseCode": 200
    },
    "success": true,
    "response_code": 200,
    "response_body": "{\"messages\":[{\"id\":\"wamid.xxx\"}]}"
}
MEMORY: 25.50 MB
LOG FILE: /home/username/public_html/flow_debug.log
====================================================================================================

```

---

**Need Help?** If you're stuck, share the log entries around the time of the error for faster troubleshooting!
