<?php
$summariesFile = __DIR__ . '/data/summaries.json';
$summaries = [];

if (file_exists($summariesFile)) {
    $summaries = json_decode(file_get_contents($summariesFile), true) ?: [];
}

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['hash'])) {
    $hashToDelete = $_POST['hash'];
    if (isset($summaries[$hashToDelete])) {
        unset($summaries[$hashToDelete]);
        file_put_contents($summariesFile, json_encode($summaries, JSON_PRETTY_PRINT));
        header('Location: admin-summaries.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Summary Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .summary-item { 
            border: 1px solid #ddd; 
            margin: 10px 0; 
            padding: 15px; 
            border-radius: 4px; 
        }
        .summary-url { 
            font-weight: bold; 
            color: #0066cc; 
            text-decoration: none; 
        }
        .summary-date { 
            color: #666; 
            font-size: 12px; 
        }
        .delete-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            float: right;
        }
        .stats {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Summary Management</h1>
    
    <div class="stats">
        <strong>Total Summaries:</strong> <?= count($summaries) ?><br>
        <strong>Storage File:</strong> <?= $summariesFile ?>
    </div>
    
    <?php if (empty($summaries)): ?>
        <p>No summaries generated yet.</p>
    <?php else: ?>
        <?php foreach ($summaries as $hash => $summary): ?>
            <div class="summary-item">
                <form method="post" style="display: inline;">
                    <input type="hidden" name="hash" value="<?= htmlspecialchars($hash) ?>">
                    <button type="submit" name="delete" class="delete-btn" 
                            onclick="return confirm('Delete this summary?')">Delete</button>
                </form>
                
                <a href="<?= htmlspecialchars($summary['url']) ?>" target="_blank" class="summary-url">
                    <?= htmlspecialchars(parse_url($summary['url'], PHP_URL_HOST)) ?>
                </a>
                
                <div class="summary-date">
                    Generated: <?= date('Y-m-d H:i:s', $summary['timestamp']) ?>
                    | Length: <?= number_format($summary['originalLength'] ?? 0) ?> chars
                </div>
                
                <div style="margin-top: 10px; font-size: 14px;">
                    <?= nl2br(htmlspecialchars(substr($summary['summary'], 0, 200))) ?>
                    <?php if (strlen($summary['summary']) > 200): ?>...<?php endif; ?>
                </div>
                
                <div style="margin-top: 10px;">
                    <a href="summary.php?url=<?= urlencode($summary['url']) ?>" target="_blank">View Full Summary</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <p><a href="saved.php">← Back to Saved Articles</a></p>
</body>
</html>