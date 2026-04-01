<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$url = $input['url'] ?? '';

if (!$url) {
    exit(json_encode(['exists' => false]));
}

$urlHash = md5($url);
$audioFile = __DIR__ . '/data/audio/' . $urlHash . '.mp3';

if (file_exists($audioFile) && filesize($audioFile) > 0) {
    echo json_encode([
        'exists' => true,
        'audioUrl' => 'data/audio/' . $urlHash . '.mp3',
        'fileSize' => filesize($audioFile)
    ]);
} else {
    echo json_encode(['exists' => false]);
}
?>