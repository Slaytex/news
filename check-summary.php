<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$url = $input['url'] ?? '';

if (!$url) {
    exit(json_encode(['exists' => false]));
}

$urlHash = md5($url);
$summariesFile = __DIR__ . '/data/summaries.json';

if (!file_exists($summariesFile)) {
    exit(json_encode(['exists' => false]));
}

$summaries = json_decode(file_get_contents($summariesFile), true) ?: [];

if (isset($summaries[$urlHash])) {
    echo json_encode([
        'exists' => true,
        'summary' => $summaries[$urlHash]['summary'],
        'timestamp' => $summaries[$urlHash]['timestamp']
    ]);
} else {
    echo json_encode(['exists' => false]);
}
?>