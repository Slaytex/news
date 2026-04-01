<?php
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
    // Prepare input for Python script
    $pythonInput = json_encode(['url' => $url]);
    
    // Call Python scraper
    $command = 'cd /var/www/news && echo ' . escapeshellarg($pythonInput) . ' | python3 scrape_article.py 2>&1';
    
    $output = shell_exec($command);
    
    if ($output === null) {
        throw new Exception('Failed to execute Python scraper');
    }
    
    // Try to decode JSON response
    $result = json_decode(trim($output), true);
    
    if ($result === null) {
        // If JSON decode failed, return the raw output for debugging
        throw new Exception('Python script error: ' . $output);
    }
    
    // Return the result from Python script
    if (isset($result['success']) && $result['success']) {
        echo json_encode($result);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $result['error'] ?? 'Unknown Python script error'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>