<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed: ' . $_SERVER['REQUEST_METHOD']);
    }

    // Get input
    $inputRaw = file_get_contents('php://input');
    if (!$inputRaw) {
        throw new Exception('No input data received');
    }

    $input = json_decode($inputRaw, true);
    if (!$input) {
        throw new Exception('Invalid JSON input: ' . $inputRaw);
    }

    $url = $input['url'] ?? '';
    if (!$url) {
        throw new Exception('No URL provided in input: ' . print_r($input, true));
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid URL: ' . $url);
    }

    // Try to fetch the URL
    $html = @file_get_contents($url);
    if (!$html) {
        throw new Exception('Failed to fetch URL: ' . $url);
    }

    $textLength = strlen(strip_tags($html));

    echo json_encode([
        'success' => true,
        'message' => 'Successfully fetched and processed',
        'url' => $url,
        'html_length' => strlen($html),
        'text_length' => $textLength,
        'input_received' => $input
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
        'input_raw' => file_get_contents('php://input'),
        'headers' => getallheaders()
    ]);
}
?>