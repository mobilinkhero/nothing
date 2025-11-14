# 🚀 Live Server: Flow Debug Logging Quick Guide

## 📍 Log File Location on Live Server

```bash
/home/username/public_html/flow_debug.log
```
*Replace `username` and `public_html` with your actual server paths*

## 🔧 Quick Setup (One-Time)

### 1. Ensure Write Permissions
```bash
# SSH into your server
ssh username@yourserver.com

# Navigate to main directory
cd /home/username/public_html

# Make directory writable (if needed)
chmod 755 .

# Or set specific permissions for logging
touch flow_debug.log
chmod 664 flow_debug.log
```

### 2. Test Logging
After deploying your code, trigger any flow and check:
```bash
ls -lh flow_debug.log
```
If the file exists and has content, logging is working!

## 👀 View Logs on Live Server

### Quick Commands

```bash
# View last 50 lines
tail -n 50 flow_debug.log

# View in real-time (keeps updating)
tail -f flow_debug.log

# Search for errors
grep -i "error\|exception" flow_debug.log

# Count error entries
grep -c "EXCEPTION" flow_debug.log

# View specific time range
grep "2025-11-14 22:" flow_debug.log

# View and page through entire file
less flow_debug.log
```

### Using Keyboard (in `less` command)
- **Space**: Next page
- **b**: Previous page
- **/text**: Search for "text"
- **n**: Next search result
- **q**: Quit

## 📥 Download Logs

### Method 1: Via SCP (from your local machine)
```bash
scp username@yourserver.com:/home/username/public_html/flow_debug.log ./flow_debug_live.log
```

### Method 2: Via FTP
1. Open FileZilla or your FTP client
2. Connect to your server
3. Navigate to main directory
4. Download `flow_debug.log`

### Method 3: Via cPanel File Manager
1. Log into cPanel
2. Open File Manager
3. Navigate to `public_html` (or your root)
4. Right-click `flow_debug.log` → Download

## 🧪 Test Quick Replies on Live

### Step 1: Trigger the Flow
Send a WhatsApp message that triggers your Quick Replies flow

### Step 2: Check Logs Immediately
```bash
tail -n 100 flow_debug.log
```

### Step 3: Look For These Entries
```
=== [LIVE SERVER] ... ===
MESSAGE: Flow Execution Started
...
MESSAGE: Quick Replies: Starting
...
MESSAGE: Quick Replies: Payload built
...
MESSAGE: Quick Replies: API Response
```

### Step 4: Check for Errors
```bash
# See if any errors occurred
grep -A 10 "EXCEPTION" flow_debug.log

# Or check last errors
tail -n 200 flow_debug.log | grep -i "error"
```

## 🐛 Common Issues & Solutions

### Issue 1: Log File Not Created
**Cause**: Directory not writable
**Solution**:
```bash
# Check permissions
ls -la flow_debug.log

# Fix permissions
chmod 664 flow_debug.log
# or
chmod 775 /home/username/public_html
```

### Issue 2: Log File Empty
**Cause**: Flow not triggering or logging disabled
**Solution**:
1. Trigger the flow manually via WhatsApp
2. Check if PHP errors are preventing execution:
```bash
tail -f /home/username/public_html/storage/logs/laravel.log
```

### Issue 3: Permission Denied
**Cause**: Web server doesn't have write access
**Solution**:
```bash
# Change ownership to web server user
sudo chown www-data:www-data flow_debug.log
# or for some servers
sudo chown nobody:nobody flow_debug.log
```

### Issue 4: Log File Too Large
**Solution**:
```bash
# Check size
ls -lh flow_debug.log

# Keep only last 200 lines
tail -n 200 flow_debug.log > temp.log && mv temp.log flow_debug.log

# Or rotate the log
mv flow_debug.log flow_debug_$(date +%Y%m%d).log
touch flow_debug.log
chmod 664 flow_debug.log
```

## 🔍 Debugging Quick Replies Issues

### Check Payload Structure
```bash
# Look for the payload that was sent
grep -A 20 "Quick Replies: Payload built" flow_debug.log | tail -25
```

### Check API Response
```bash
# See what WhatsApp API returned
grep -A 15 "Quick Replies: API Response" flow_debug.log | tail -20
```

### Find All Quick Replies Attempts
```bash
# List all Quick Replies executions
grep "Quick Replies:" flow_debug.log
```

## 📊 Log Analysis

### Count Messages by Type
```bash
grep "Flow Execution Started" flow_debug.log | grep -o '"node_type":"[^"]*"' | sort | uniq -c
```

### Find Slowest Operations
```bash
# Memory usage over time
grep "MEMORY:" flow_debug.log
```

### Track Specific User
```bash
# Replace with actual phone number
grep "+1234567890" flow_debug.log
```

## 🧹 Clean Up Old Logs

### Automated Cleanup (Cron Job)
```bash
# Edit crontab
crontab -e

# Add this line to clear logs weekly (Sunday midnight)
0 0 * * 0 cd /home/username/public_html && tail -n 500 flow_debug.log > temp.log && mv temp.log flow_debug.log

# Or delete monthly
0 0 1 * * rm /home/username/public_html/flow_debug.log
```

## 📱 Monitor on Mobile

### Using SSH Apps
- **iOS**: Termius, Prompt
- **Android**: Termux, JuiceSSH

Example in Termux:
```bash
ssh username@server.com
cd /path/to/app
tail -f flow_debug.log
```

## 🔐 Security Best Practices

### 1. Protect Log File
```bash
# Move to protected directory if contains sensitive data
mv flow_debug.log storage/logs/

# Restrict access via .htaccess (if in public_html)
echo "deny from all" > .htaccess
```

### 2. Rotate Regularly
```bash
# Weekly rotation script
#!/bin/bash
LOG_DIR="/home/username/public_html"
DATE=$(date +%Y%m%d)
mv $LOG_DIR/flow_debug.log $LOG_DIR/flow_debug_$DATE.log
touch $LOG_DIR/flow_debug.log
chmod 664 $LOG_DIR/flow_debug.log
# Delete logs older than 30 days
find $LOG_DIR -name "flow_debug_*.log" -mtime +30 -delete
```

### 3. Exclude from Backups (Optional)
Add to `.gitignore`:
```
flow_debug.log
flow_debug_*.log
```

## 💡 Pro Tips

1. **Use `less +G`** to jump to end of file:
   ```bash
   less +G flow_debug.log
   ```

2. **Color-code errors** (if you have `grep` with color support):
   ```bash
   grep --color=always -i "error\|exception" flow_debug.log | less -R
   ```

3. **Create an alias** for quick access:
   ```bash
   # Add to ~/.bashrc
   alias flowlog='cd /home/username/public_html && tail -f flow_debug.log'
   
   # Then just type:
   flowlog
   ```

4. **Watch for new entries**:
   ```bash
   watch -n 2 'tail -n 20 flow_debug.log'
   ```

## ❓ Need Help?

If you encounter issues:
1. Copy the relevant log entries (around the error)
2. Note the exact timestamp
3. Include any error messages from `storage/logs/laravel.log`
4. Share with your development team

---

**Remember**: The log file shows `[LIVE SERVER]` prefix for all entries on production!
