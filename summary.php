<?php
$url = $_GET['url'] ?? '';
if (!$url) {
    header('Location: saved.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Summary - News Feed</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Avenir Next', 'Avenir', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #E1E1E1;
            color: #000;
            position: relative;
        }

        /* ── HAMBURGER & NAVIGATION ── */
        .hamburger {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .hamburger:hover {
            transform: scale(1.1);
        }

        .nav-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .nav-overlay.active {
            display: flex;
        }

        .nav-menu {
            text-align: center;
        }

        .nav-item {
            display: block;
            color: #fff;
            font-size: 48px;
            font-weight: 700;
            text-decoration: none;
            margin: 20px 0;
            transition: color 0.3s ease;
        }

        .nav-item:hover {
            color: #FF423D;
        }

        .nav-close {
            position: absolute;
            top: 60px;
            background: none;
            border: none;
            color: #fff;
            font-size: 60px;
            cursor: pointer;
            font-weight: 300;
        }

        /* ── LAYOUT ── */
        .main-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
        }

        /* ── SUMMARY CONTAINER ── */
        .summary-container {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .loading-state {
            text-align: center;
            padding: 60px;
            font-size: 18px;
            color: #666;
        }

        .error-state {
            text-align: center;
            padding: 60px;
            color: #d32f2f;
        }

        .summary-content h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #000;
        }

        .executive-summary {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .key-points ul {
            list-style: none;
            padding: 0;
        }

        .key-points li {
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 12px;
            padding-left: 20px;
            position: relative;
        }

        .key-points li:before {
            content: '•';
            color: #FF423D;
            font-size: 18px;
            position: absolute;
            left: 0;
        }

        .article-meta {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }

        .article-link {
            color: #FF423D;
            text-decoration: none;
        }

        .article-link:hover {
            text-decoration: underline;
        }

        .audio-controls {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
            text-align: center;
        }

        .audio-button {
            background: #FF423D;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-right: 10px;
        }

        .audio-button:hover {
            background: #e63946;
        }

        .audio-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .audio-player {
            margin-top: 15px;
            width: 100%;
        }

        .back-button {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 12px 24px;
            text-decoration: none;
            font-weight: 500;
            border-radius: 4px;
            margin-bottom: 30px;
            transition: background 0.2s ease;
        }

        .back-button:hover {
            background: #333;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .main-wrapper {
                padding: 20px 16px;
            }
            
            .summary-container {
                padding: 24px;
            }
            
            .nav-item {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>

<!-- Hamburger Button -->
<button class="hamburger" onclick="toggleNav()">☰</button>

<!-- Navigation Overlay -->
<div class="nav-overlay" id="navOverlay">
    <div class="nav-menu">
        <a href="select.php" class="nav-item">Choose your news</a>
        <a href="saved.php" class="nav-item">Saved Articles</a>
    </div>
    <button class="nav-close" onclick="toggleNav()">×</button>
</div>

<div class="main-wrapper">
    <a href="saved.php" class="back-button">← Back to Saved Articles</a>
    
    <div class="summary-container">
        <div class="loading-state" id="loadingState">
            Generating AI summary...<br>
            <small style="margin-top: 10px; display: block;">This may take a moment</small>
        </div>
        
        <div class="error-state" id="errorState" style="display: none;">
            <h3>Could not generate summary</h3>
            <p id="errorMessage">Unable to access or summarize this article.</p>
            <p style="margin-top: 20px;">
                <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="article-link">Read original article →</a>
            </p>
        </div>
        
        <div class="summary-content" id="summaryContent" style="display: none;">
            <!-- Summary will be loaded here -->
        </div>
        
        <div class="audio-controls" id="audioControls" style="display: none;">
            <button class="audio-button" id="generateAudioBtn" onclick="generateAudio()">🔊 Generate Audio</button>
            <audio class="audio-player" id="audioPlayer" controls style="display: none;">
                Your browser does not support the audio element.
            </audio>
        </div>
    </div>
</div>

<script>
// Navigation toggle
function toggleNav() {
    const overlay = document.getElementById('navOverlay');
    overlay.classList.toggle('active');
}

// Close nav when clicking outside
document.getElementById('navOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        toggleNav();
    }
});

// Load and display summary
const articleUrl = <?= json_encode($url) ?>;

async function loadSummary() {
    try {
        // Check if summary already exists on server
        const checkResponse = await fetch('check-summary.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ url: articleUrl })
        });

        const checkResponseText = await checkResponse.text();
        let checkData;
        try {
            checkData = JSON.parse(checkResponseText);
        } catch (e) {
            console.warn('Failed to parse check-summary response:', checkResponseText.substring(0, 100));
            checkData = { exists: false };
        }
        
        if (checkData.exists) {
            displaySummary({
                summary: checkData.summary,
                timestamp: checkData.timestamp * 1000 // Convert to JS timestamp
            });
            return;
        }

        // Generate new summary
        const response = await fetch('scrape-article-python.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ url: articleUrl })
        });

        const responseText = await response.text();
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            throw new Error('Server returned non-JSON response: ' + responseText.substring(0, 100));
        }

        if (!data.success) {
            throw new Error(data.error || 'Unknown error');
        }

        displaySummary({
            summary: data.summary,
            timestamp: Date.now()
        });

    } catch (error) {
        showError(error.message);
    }
}

function displaySummary(summaryData) {
    const loadingState = document.getElementById('loadingState');
    const summaryContent = document.getElementById('summaryContent');
    const audioControls = document.getElementById('audioControls');
    
    loadingState.style.display = 'none';
    summaryContent.style.display = 'block';
    audioControls.style.display = 'block';
    
    // Parse the summary into executive summary and key points
    let summary = summaryData.summary;
    let executiveSummary = '';
    let keyPoints = '';
    
    if (summary.includes('EXECUTIVE SUMMARY:') && summary.includes('KEY POINTS:')) {
        const parts = summary.split('KEY POINTS:');
        executiveSummary = parts[0].replace('EXECUTIVE SUMMARY:', '').trim();
        keyPoints = parts[1].trim();
    } else {
        executiveSummary = summary;
    }
    
    let html = '<h2>Article Summary</h2>';
    
    if (executiveSummary) {
        html += `<div class="executive-summary">${executiveSummary}</div>`;
    }
    
    if (keyPoints) {
        html += '<div class="key-points"><h3>Key Points:</h3><ul>';
        // Parse bullet points
        const points = keyPoints.split(/[•\n]/).filter(p => p.trim());
        points.forEach(point => {
            if (point.trim()) {
                html += `<li>${point.trim()}</li>`;
            }
        });
        html += '</ul></div>';
    }
    
    html += `
        <div class="article-meta">
            <strong>Original article:</strong> 
            <a href="${articleUrl}" target="_blank" class="article-link">Read full article →</a><br>
            <small>Summary generated: ${new Date(summaryData.timestamp).toLocaleDateString()}</small>
        </div>
    `;
    
    summaryContent.innerHTML = html;
}

function showError(message) {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('errorState').style.display = 'block';
    document.getElementById('errorMessage').textContent = message;
}

// Audio generation function
async function generateAudio() {
    const button = document.getElementById('generateAudioBtn');
    const audioPlayer = document.getElementById('audioPlayer');
    
    button.disabled = true;
    button.textContent = 'Generating Audio...';
    
    try {
        const response = await fetch('generate-audio.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ url: articleUrl })
        });

        const responseText = await response.text();
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            throw new Error('Audio service returned non-JSON response: ' + responseText.substring(0, 100));
        }

        if (!data.success) {
            throw new Error(data.error || 'Failed to generate audio');
        }

        // Show audio player with the generated audio
        audioPlayer.src = data.audioUrl;
        audioPlayer.style.display = 'block';
        
        button.textContent = data.cached ? '🔊 Audio Ready (Cached)' : '🔊 Audio Generated';
        button.style.background = '#28a745';

    } catch (error) {
        alert('Failed to generate audio: ' + error.message);
        button.textContent = '🔊 Generate Audio';
    }
    
    button.disabled = false;
}

// Start loading when page loads
loadSummary();
</script>

</body>
</html>