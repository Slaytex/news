<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$input = json_decode(file_get_contents('php://input'), true);
$url = $input['url'] ?? '';

if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid URL']));
}

try {
    // For now, return a mock summary to test the flow
    $summary = "EXECUTIVE SUMMARY:\nThis is a test summary for the article at " . $url . ". The summarization feature is currently in development.\n\nKEY POINTS:\n• Test point 1\n• Test point 2\n• Test point 3";
    
    // Save summary to server
    $urlHash = md5($url);
    $summaryData = [
        'url' => $url,
        'summary' => $summary,
        'timestamp' => time(),
        'originalLength' => 1000
    ];
    
    $summariesFile = __DIR__ . '/data/summaries.json';
    $summaries = [];
    
    if (file_exists($summariesFile)) {
        $summaries = json_decode(file_get_contents($summariesFile), true) ?: [];
    }
    
    $summaries[$urlHash] = $summaryData;
    file_put_contents($summariesFile, json_encode($summaries, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'originalLength' => 1000
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>