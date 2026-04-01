<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1);

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
    // Set up context for web scraping
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (compatible; NewsBot/1.0)',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                'Accept-Encoding: gzip, deflate',
                'Connection: keep-alive',
            ],
            'timeout' => 30,
            'follow_location' => true,
            'max_redirects' => 3,
        ]
    ]);

    $html = @file_get_contents($url, false, $context);
    
    if (!$html) {
        throw new Exception('Failed to fetch article content');
    }

    // Extract text content from HTML
    $text = '';
    
    // Remove scripts, styles, and other non-content elements
    $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
    $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
    $html = preg_replace('/<nav\b[^<]*(?:(?!<\/nav>)<[^<]*)*<\/nav>/mi', '', $html);
    $html = preg_replace('/<header\b[^<]*(?:(?!<\/header>)<[^<]*)*<\/header>/mi', '', $html);
    $html = preg_replace('/<footer\b[^<]*(?:(?!<\/footer>)<[^<]*)*<\/footer>/mi', '', $html);
    
    // Try to find main content areas
    $patterns = [
        '/<article[^>]*>(.*?)<\/article>/si',
        '/<main[^>]*>(.*?)<\/main>/si', 
        '/<div[^>]*class="[^"]*content[^"]*"[^>]*>(.*?)<\/div>/si',
        '/<div[^>]*class="[^"]*article[^"]*"[^>]*>(.*?)<\/div>/si',
    ];
    
    $found = false;
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            $text = $matches[1];
            $found = true;
            break;
        }
    }
    
    // If no specific content area found, use body
    if (!$found) {
        if (preg_match('/<body[^>]*>(.*?)<\/body>/si', $html, $matches)) {
            $text = $matches[1];
        } else {
            $text = $html;
        }
    }
    
    // Clean up the text
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    
    // Limit length for API efficiency
    $text = substr($text, 0, 8000);
    
    if (empty($text) || strlen($text) < 100) {
        throw new Exception('Could not extract meaningful content from article');
    }

    // Call OpenAI API for summarization
    $openaiKey = getenv('OPENAI_API_KEY') ?: 'YOUR_OPENAI_API_KEY_HERE';
    
    $prompt = "Please provide a concise summary of this news article in the following format:

EXECUTIVE SUMMARY:
[One paragraph summary of the main points and key findings]

KEY POINTS:
• [First key point]
• [Second key point] 
• [Third key point]
• [Additional points as needed]

Article text:
" . $text;

    $apiData = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a professional news summarizer. Create clear, concise summaries that capture the essential information and key points of news articles.'
            ],
            [
                'role' => 'user', 
                'content' => $prompt
            ]
        ],
        'max_tokens' => 500,
        'temperature' => 0.3
    ];

    $apiContext = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $openaiKey
            ],
            'content' => json_encode($apiData),
            'timeout' => 60,
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents('https://api.openai.com/v1/chat/completions', false, $apiContext);
    
    if ($response === false) {
        $error = error_get_last();
        throw new Exception('Failed to get AI summary: ' . ($error['message'] ?? 'Unknown error'));
    }

    $aiResponse = json_decode($response, true);
    
    if (!$aiResponse) {
        throw new Exception('Failed to decode OpenAI response: ' . substr($response, 0, 500));
    }
    
    if (isset($aiResponse['error'])) {
        throw new Exception('OpenAI API error: ' . $aiResponse['error']['message']);
    }
    
    if (!isset($aiResponse['choices'][0]['message']['content'])) {
        throw new Exception('Invalid OpenAI response structure: ' . json_encode($aiResponse));
    }

    $summary = $aiResponse['choices'][0]['message']['content'];
    
    // Save summary to server
    $urlHash = md5($url);
    $summaryData = [
        'url' => $url,
        'summary' => $summary,
        'timestamp' => time(),
        'originalLength' => strlen($text)
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
        'originalLength' => strlen($text)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>