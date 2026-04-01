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
            min-height: 100vh;
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

        /* ── MAIN LAYOUT ── */
        .main-wrapper {
            max-width: 669px;
            margin: 0 auto;
            padding: 40px 20px;
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

        /* ── SUMMARY CONTAINER ── */
        .summary-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ── SUMMARIZED BADGE ── */
        .summarized-badge {
            background: #fff;
            border-radius: 5px;
            padding: 4px 0;
            text-align: center;
            box-shadow: 
                0 6px 12px rgba(0, 0, 0, 0.09),
                0 23px 23px rgba(0, 0, 0, 0.08),
                0 51px 30px rgba(0, 0, 0, 0.05),
                0 90px 36px rgba(0, 0, 0, 0.01),
                0 141px 39px rgba(0, 0, 0, 0);
        }

        .summarized-text {
            font-family: 'Avenir Next', sans-serif;
            font-weight: 400;
            font-size: 14px;
            letter-spacing: 6.86px;
            color: #000;
            margin: 0;
        }

        /* ── MAIN SUMMARY CARD ── */
        .summary-card {
            background: #fff;
            border-radius: 5px;
            padding: 20px 19px;
            box-shadow: 
                0 6px 12px rgba(0, 0, 0, 0.09),
                0 23px 23px rgba(0, 0, 0, 0.08),
                0 51px 30px rgba(0, 0, 0, 0.05),
                0 90px 36px rgba(0, 0, 0, 0.01),
                0 141px 39px rgba(0, 0, 0, 0);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ── HEADLINE SECTION ── */
        .headline-section {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .article-headline {
            font-family: 'Avenir Next', sans-serif;
            font-weight: 700;
            font-size: 26px;
            line-height: 35.52px;
            color: #000;
            margin: 0;
        }

        .executive-summary {
            background: #F8F9FA;
            padding: 11px;
            border-radius: 4px;
            font-family: 'Avenir Next', sans-serif;
            font-weight: 500;
            font-size: 16px;
            line-height: 21.86px;
            color: #000;
            margin: 0;
            text-align: left;
        }

        /* ── KEY POINTS SECTION ── */
        .key-points-section {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .key-points-title {
            font-family: 'Avenir Next', sans-serif;
            font-weight: 700;
            font-size: 21px;
            line-height: 28.69px;
            color: #000;
            margin: 0;
        }

        .key-points-list {
            font-family: 'Avenir Next', sans-serif;
            font-weight: 500;
            font-size: 16px;
            line-height: 21.86px;
            color: #000;
            margin: 0;
            padding-left: 20px;
        }

        .key-points-list li {
            margin-bottom: 4px;
        }

        /* ── DIVIDER ── */
        .divider-section {
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .divider-line {
            width: 100%;
            height: 1px;
            background: #CBCBCB;
            border: none;
        }

        /* ── META SECTION ── */
        .meta-section {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .original-article-link {
            font-family: 'Avenir Next', sans-serif;
            font-weight: 400;
            font-size: 15px;
            line-height: 20.49px;
            color: #000;
            text-decoration: none;
            margin: 0;
        }

        .original-article-link .link-text {
            font-weight: 600;
            color: #FF203E;
        }

        .generation-date {
            font-family: 'Avenir Next', sans-serif;
            font-weight: 500;
            font-size: 12px;
            line-height: 16.39px;
            color: #000;
            margin: 0;
        }

        /* ── AUDIO SECTION ── */
        .audio-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 0;
            gap: 10px;
        }

        .generate-audio-btn {
            background: #FF423D;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 7px 20px;
            font-family: 'Avenir Next', sans-serif;
            font-weight: 600;
            font-size: 15px;
            line-height: 20.49px;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            min-width: fit-content;
        }

        .generate-audio-btn:hover {
            background: #e63946;
            transform: translateY(-1px);
        }

        .generate-audio-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .generate-audio-btn.generated {
            background: #28a745;
        }



        /* ── LOADING & ERROR STATES ── */
        .loading-state {
            text-align: center;
            padding: 60px;
            font-size: 18px;
            color: #666;
            background: #fff;
            border-radius: 5px;
            box-shadow: 
                0 6px 12px rgba(0, 0, 0, 0.09),
                0 23px 23px rgba(0, 0, 0, 0.08),
                0 51px 30px rgba(0, 0, 0, 0.05);
        }

        .error-state {
            text-align: center;
            padding: 60px;
            color: #d32f2f;
            background: #fff;
            border-radius: 5px;
            box-shadow: 
                0 6px 12px rgba(0, 0, 0, 0.09),
                0 23px 23px rgba(0, 0, 0, 0.08),
                0 51px 30px rgba(0, 0, 0, 0.05);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .main-wrapper {
                padding: 20px 16px;
                max-width: 100%;
            }
            
            .summary-card {
                padding: 16px 15px;
            }
            
            .article-headline {
                font-size: 22px;
                line-height: 30px;
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
    
    <div class="loading-state" id="loadingState">
        Loading summary...<br>
        <small style="margin-top: 10px; display: block;">Checking for existing summary</small>
    </div>
    
    <div class="error-state" id="errorState" style="display: none;">
        <h3>Could not generate summary</h3>
        <p id="errorMessage">Unable to access or summarize this article.</p>
        <p style="margin-top: 20px;">
            <a href="<?= htmlspecialchars($url) ?>" target="_blank" style="color: #FF423D; text-decoration: none;">Read original article →</a>
        </p>
    </div>
    
    <div class="summary-container" id="summaryContainer" style="display: none;">
        <!-- Summarized Badge -->
        <div class="summarized-badge">
            <p class="summarized-text">SUMMARIZED</p>
        </div>
        
        <!-- Main Summary Card -->
        <div class="summary-card">
            <!-- Headline Section -->
            <div class="headline-section" id="headlineSection">
                <!-- Will be populated with article headline and executive summary -->
            </div>
            
            <!-- Key Points Section -->
            <div class="key-points-section" id="keyPointsSection" style="display: none;">
                <h3 class="key-points-title">Key Points</h3>
                <ul class="key-points-list" id="keyPointsList">
                    <!-- Will be populated with bullet points -->
                </ul>
            </div>
            
            <!-- Divider -->
            <div class="divider-section">
                <hr class="divider-line">
            </div>
            
            <!-- Meta Section -->
            <div class="meta-section">
                <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="original-article-link">
                    <span class="normal-text">Original article: </span><span class="link-text">Read full article →</span>
                </a>
                <p class="generation-date" id="generationDate">
                    <!-- Will be populated with generation date -->
                </p>
            </div>
            
            <!-- Audio Section -->
            <div class="audio-section">
                <button class="generate-audio-btn" id="generateAudioBtn" onclick="generateAudio()">
                    Generate Audio
                </button>
            </div>
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

        if (!checkResponse.ok) {
            throw new Error(`Server error: ${checkResponse.status} ${checkResponse.statusText}`);
        }
        
        const checkResponseText = await checkResponse.text();
        console.log('Check response:', checkResponseText);
        
        let checkData;
        try {
            checkData = JSON.parse(checkResponseText);
        } catch (e) {
            console.error('Failed to parse check-summary response:', checkResponseText);
            throw new Error('Invalid response from summary check service');
        }
        
        if (checkData.exists) {
            console.log('Found existing summary:', checkData);
            displaySummary({
                summary: checkData.summary,
                timestamp: checkData.timestamp * 1000 // Convert to JS timestamp
            });
            return;
        }

        // Update loading message for new summary generation
        document.getElementById('loadingState').innerHTML = `
            Generating new AI summary...<br>
            <small style="margin-top: 10px; display: block;">This article hasn't been summarized yet</small>
        `;

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
    const summaryContainer = document.getElementById('summaryContainer');
    
    loadingState.style.display = 'none';
    summaryContainer.style.display = 'block';
    
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
    
    // Extract article title from URL or use default
    const articleTitle = 'News Story Headline'; // Could extract from URL params or summary
    
    // Populate headline section
    const headlineSection = document.getElementById('headlineSection');
    headlineSection.innerHTML = `
        <h1 class="article-headline">${articleTitle}</h1>
        <div class="executive-summary">${executiveSummary}</div>
    `;
    
    // Populate key points if available
    if (keyPoints) {
        const keyPointsSection = document.getElementById('keyPointsSection');
        const keyPointsList = document.getElementById('keyPointsList');
        
        // Parse bullet points
        const points = keyPoints.split(/[•\n]/).filter(p => p.trim());
        keyPointsList.innerHTML = '';
        
        points.forEach(point => {
            if (point.trim()) {
                const li = document.createElement('li');
                li.textContent = point.trim();
                keyPointsList.appendChild(li);
            }
        });
        
        if (points.length > 0) {
            keyPointsSection.style.display = 'flex';
        }
    }
    
    // Set generation date
    const generationDate = document.getElementById('generationDate');
    generationDate.textContent = `Summary generated: ${new Date(summaryData.timestamp).toLocaleDateString()}`;
    
}

function showError(message) {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('errorState').style.display = 'block';
    document.getElementById('errorMessage').textContent = message;
}

// Check if audio already exists
async function checkExistingAudio() {
    try {
        const response = await fetch('check-audio.php', {
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
            console.warn('Failed to parse audio check response:', responseText);
            return; // Keep default "Generate Audio" button
        }

        if (data.exists) {
            // Audio already exists, update button to play it
            const button = document.getElementById('generateAudioBtn');
            const articleTitle = document.querySelector('.article-headline')?.textContent || 'Article Summary';
            
            button.textContent = 'Play Audio';
            button.style.background = '#28a745';
            button.onclick = () => {
                if (window.playArticleAudio) {
                    // Check if this is already the current track playing
                    const currentTrack = localStorage.getItem('newsAudioPlayer');
                    if (currentTrack) {
                        const state = JSON.parse(currentTrack);
                        if (state.currentTrack === data.audioUrl && globalAudioPlayer && !globalAudioPlayer.player.paused) {
                            // Already playing this track, just show the player
                            globalAudioPlayer.showPlayer();
                            return;
                        }
                    }
                    window.playArticleAudio(data.audioUrl, articleTitle, 'AI Generated Summary');
                }
            };
        }
    } catch (error) {
        console.warn('Failed to check existing audio:', error);
        // Keep default "Generate Audio" button
    }
}

// Audio generation function
async function generateAudio() {
    const button = document.getElementById('generateAudioBtn');
    
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

        // Get article title for player
        const articleTitle = document.querySelector('.article-headline')?.textContent || 'Article Summary';
        
        // Load audio into persistent player and start playing
        if (window.playArticleAudio) {
            window.playArticleAudio(data.audioUrl, articleTitle, 'AI Generated Summary');
        }
        
        // Update button to show it's available
        button.textContent = '🎵 Playing Audio';
        button.style.background = '#28a745';
        
        // Change button to show player control
        setTimeout(() => {
            button.textContent = 'Audio Ready';
            button.onclick = () => {
                if (window.playArticleAudio) {
                    window.playArticleAudio(data.audioUrl, articleTitle, 'AI Generated Summary');
                }
            };
        }, 2000);

    } catch (error) {
        alert('Failed to generate audio: ' + error.message);
        button.textContent = 'Generate Audio';
        button.disabled = false;
    }
}

// Start loading when page loads
loadSummary();

// Always check for existing audio regardless of summary status
checkExistingAudio();
</script>

<!-- Persistent Audio Player -->
<script src="persistent-player.js"></script>

</body>
</html>