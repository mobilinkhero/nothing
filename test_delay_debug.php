<?php
/**
 * Simple test to verify delay node debugging
 */

echo "=== DELAY NODE DEBUG TEST ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Check if debug log file exists and is writable
$debugFile = __DIR__ . '/delay_debug.log';

if (file_exists($debugFile)) {
    echo "✓ Debug log file exists: $debugFile\n";
    
    if (is_writable($debugFile)) {
        echo "✓ Debug log file is writable\n";
        
        // Test writing to debug log
        $testEntry = "\n" . date('Y-m-d H:i:s') . " - TEST ENTRY\n";
        $testEntry .= "Data: " . json_encode(['test' => true, 'timestamp' => time()], JSON_PRETTY_PRINT) . "\n";
        $testEntry .= str_repeat('=', 80) . "\n";
        
        if (file_put_contents($debugFile, $testEntry, FILE_APPEND)) {
            echo "✓ Successfully wrote test entry to debug log\n";
        } else {
            echo "✗ Failed to write test entry to debug log\n";
        }
        
        // Show current file size
        $fileSize = filesize($debugFile);
        echo "Debug log file size: $fileSize bytes\n";
        
    } else {
        echo "✗ Debug log file is not writable\n";
    }
} else {
    echo "✗ Debug log file does not exist: $debugFile\n";
}

echo "\n=== INSTRUCTIONS ===\n";
echo "1. Test your flow with a delay node in WhatsApp\n";
echo "2. Check the debug log: tail -f delay_debug.log\n";
echo "3. Or view the log: cat delay_debug.log\n";
echo "\nThe log will show:\n";
echo "- When flow execution starts\n";
echo "- All nodes being processed\n";  
echo "- Delay node detection\n";
echo "- Actual delay implementation\n";
echo "- Any errors\n";

echo "\n=== END TEST ===\n";
?>
