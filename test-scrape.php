<?php
// Simple test script to check what's happening
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Test basic functionality
    echo json_encode([
        'success' => true,
        'message' => 'Test endpoint working',
        'php_version' => PHP_VERSION,
        'openai_key_present' => !empty(getenv('OPENAI_API_KEY'))
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>