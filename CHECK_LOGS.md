# 🔍 Check Google Sheets Error Logs

## Quick Commands to Check Logs:

### Windows (PowerShell):
```powershell
# View last 50 lines of Laravel log
Get-Content "c:\wamp64\www\storage\logs\laravel-$(Get-Date -Format 'yyyy-MM-dd').log" -Tail 50

# Search for Google Sheets errors
Select-String -Path "c:\wamp64\www\storage\logs\laravel-*.log" -Pattern "Google Sheets|createSheet|ensureSheetExists"
```

### Linux/Mac:
```bash
# View last 50 lines of today's log
tail -50 storage/logs/laravel-$(date +%Y-%m-%d).log

# Search for Google Sheets errors
grep -r "Google Sheets\|createSheet\|ensureSheetExists" storage/logs/
```

## Run the Test Again:
```bash
php artisan sales-bot:test-sheets 1kdxtkELVKb5AYi7zmY5_1aj7pePQeNsOTqrREvR6L4E
```

## What to Look For:
- **"Attempting to create sheet 'Orders'"** - Shows if creation attempt starts
- **"Google Sheets API Error"** - Shows specific API errors
- **"Successfully created sheet"** - Confirms successful creation
- **"Alternative sheet creation"** - Shows if fallback method is used

## Common Issues & Solutions:

### 1. Permission Error:
```
Error: "The caller does not have permission"
```
**Solution:** Make sure the Google Service Account has Editor access to your spreadsheet

### 2. Sheet Already Exists:
```
Error: "Sheet with name 'Orders' already exists"
```
**Solution:** This is actually good - the sheet exists but headers might be missing

### 3. Invalid Range:
```
Error: "Unable to parse range"
```
**Solution:** This is the original error - should be fixed with the new code

## Check Current Implementation:
The enhanced GoogleSheetsService now:
- ✅ Uses detailed logging for troubleshooting
- ✅ Has fallback method for sheet creation
- ✅ Verifies sheet creation after API calls
- ✅ Handles both Google API exceptions and general exceptions
