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
    // Get the summary for this URL
    $urlHash = md5($url);
    $summariesFile = __DIR__ . '/data/summaries.json';
    
    if (!file_exists($summariesFile)) {
        throw new Exception('No summary found for this article');
    }
    
    $summaries = json_decode(file_get_contents($summariesFile), true) ?: [];
    
    if (!isset($summaries[$urlHash])) {
        throw new Exception('No summary found for this article');
    }
    
    $summary = $summaries[$urlHash]['summary'];
    
    // Check if audio already exists
    $audioFile = __DIR__ . '/data/audio/' . $urlHash . '.mp3';
    
    if (file_exists($audioFile)) {
        // Return existing audio file URL
        echo json_encode([
            'success' => true,
            'audioUrl' => 'data/audio/' . $urlHash . '.mp3',
            'cached' => true
        ]);
        exit;
    }
    
    // Create audio directory if it doesn't exist
    $audioDir = __DIR__ . '/data/audio/';
    if (!is_dir($audioDir)) {
        if (!mkdir($audioDir, 0775, true)) {
            throw new Exception('Failed to create audio directory');
        }
    }
    
    // Check if directory is writable
    if (!is_writable($audioDir)) {
        throw new Exception('Audio directory is not writable');
    }
    
    // OpenAI API key from environment or config
    $openaiKey = getenv('OPENAI_API_KEY') ?: 'YOUR_OPENAI_API_KEY_HERE';
    
    if (empty($openaiKey)) {
        throw new Exception('OpenAI API key not configured');
    }
    
    // Prepare text for TTS (clean up formatting)
    $ttsText = strip_tags($summary);
    $ttsText = preg_replace('/\s+/', ' ', $ttsText);
    $ttsText = trim($ttsText);
    
    // Limit text length for OpenAI TTS (max 4096 characters)
    if (strlen($ttsText) > 4000) {
        $ttsText = substr($ttsText, 0, 4000) . '...';
    }
    
    // Call OpenAI TTS API
    $ttsData = [
        'model' => 'tts-1',
        'input' => $ttsText,
        'voice' => 'nova', // Options: alloy, echo, fable, onyx, nova, shimmer
        'response_format' => 'mp3',
        'speed' => 1.0
    ];
    
    $ttsContext = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $openaiKey
            ],
            'content' => json_encode($ttsData),
            'timeout' => 60,
            'ignore_errors' => true
        ]
    ]);
    
    // Use OpenAI text-to-speech endpoint
    $audioData = @file_get_contents('https://api.openai.com/v1/audio/speech', false, $ttsContext);
    
    if (!$audioData) {
        $error = error_get_last();
        throw new Exception('Failed to generate audio with OpenAI: ' . ($error['message'] ?? 'Unknown error'));
    }
    
    // Check if response is actually audio data or an error
    if (strlen($audioData) < 1000) {
        // Might be an error response
        $errorResponse = json_decode($audioData, true);
        if ($errorResponse && isset($errorResponse['error'])) {
            throw new Exception('OpenAI TTS error: ' . $errorResponse['error']['message']);
        }
    }
    
    // Save the audio file
    $bytesWritten = file_put_contents($audioFile, $audioData);
    if ($bytesWritten === false) {
        throw new Exception('Failed to save audio file: ' . error_get_last()['message'] ?? 'Unknown error');
    }
    
    if ($bytesWritten === 0) {
        throw new Exception('Saved empty audio file');
    }
    
    // Verify the file was created and is readable
    if (!file_exists($audioFile) || !is_readable($audioFile)) {
        throw new Exception('Audio file was not created properly');
    }
    
    echo json_encode([
        'success' => true,
        'audioUrl' => 'data/audio/' . $urlHash . '.mp3',
        'cached' => false
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>