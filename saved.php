<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Articles - News Feed</title>
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
        }

        /* ── HEADER ── */
        .site-header {
            width: 100%;
            height: 250px;
            background-color: #FF423D;
            background-image: url('https://create.pixelchemi.st/images/generated_1775002247355.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .header-content {
            position: relative;
            z-index: 1;
            max-width: 1366px;
            margin: 0 auto;
            padding: 0 40px;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 130px;
        }

        .header-title {
            font-size: 34px;
            font-weight: 700;
            color: #fff;
            line-height: 1.1;
            margin-bottom: 8px;
            background: #000;
            padding: 4px 11px;
            display: inline-block;
        }

        .header-subtitle {
            font-size: 11px;
            font-weight: 400;
            color: #fff;
            margin-bottom: 4px;
            background: #000;
            padding: 4px 11px;
            display: inline-block;
        }

        .header-byline {
            font-size: 11px;
            font-weight: 400;
            color: #fff;
            background: #000;
            padding: 4px 11px;
            display: inline-block;
        }

        /* ── LAYOUT ── */
        .main-wrapper {
            max-width: 1366px;
            margin: 0 auto;
            padding: 40px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ── CARD ── */
        .card {
            background: #fff;
            padding: 19px 19px 19px 19px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 239px;
            text-decoration: none;
            color: #000;
            transition: box-shadow 0.15s ease;
            position: relative;
        }

        .card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            margin-bottom: 16px;
            border-radius: 4px;
        }

        .card-title {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 12px;
            color: #000;
        }

        .card-description {
            font-size: 13px;
            font-weight: 500;
            line-height: 1.5;
            color: #000;
            flex-grow: 1;
        }

        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
        }

        .card-source {
            font-size: 16px;
            font-weight: 500;
        }

        .card-date {
            font-size: 16px;
            font-weight: 500;
        }

        .heart-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #ff4444;
        }

        .heart-btn:hover {
            background: #fff;
            transform: scale(1.1);
        }

        .heart-btn.saved {
            color: #ff4444;
            background: #fff;
        }

        .summarize-btn {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 10;
            font-weight: 500;
        }

        .summarize-btn:hover {
            background: #333;
            transform: translateY(-1px);
        }

        .summarize-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .summarize-btn.view-summary {
            background: #FF423D;
        }

        .summarize-btn.view-summary:hover {
            background: #e63946;
        }

        .audio-indicator {
            position: absolute;
            top: 50px;
            right: 15px;
            background: #28a745;
            color: #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            z-index: 10;
        }

        /* ── ROWS ── */
        .two-col-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* ── EMPTY STATE ── */
        .no-articles {
            background: #fff;
            padding: 60px 40px;
            text-align: center;
            font-size: 16px;
            font-weight: 500;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .main-wrapper {
                padding: 20px 16px;
            }
            .two-col-row {
                grid-template-columns: 1fr;
            }
            .header-title {
                font-size: 26px;
            }
            .card-title {
                font-size: 20px;
            }
            .card-description, .card-source, .card-date {
                font-size: 14px;
            }
            .site-header {
                background-image: url('background-img.mobile.jpg');
                background-size: cover;
                background-position: center;
            }
        }
    </style>
</head>
<body>

<header class="site-header">
    <div class="header-content">
        <div class="header-title">Saved Articles</div>
        <div class="header-subtitle">Your bookmarked news stories</div>
        <div class="header-byline">Click the heart to remove from saved</div>
    </div>
</header>

<div class="main-wrapper" id="articles-container">
    <div class="no-articles" id="empty-state">
        <p>No saved articles yet!</p>
        <p style="margin-top: 10px; font-size: 14px; color: #666;">
            <a href="select.php" style="color: #FF423D;">Browse news categories</a> and save articles you want to read later.
        </p>
    </div>
</div>

<script>
// Load and display saved articles
function loadSavedArticles() {
    const saved = JSON.parse(localStorage.getItem('savedArticles') || '[]');
    const container = document.getElementById('articles-container');
    const emptyState = document.getElementById('empty-state');
    
    if (saved.length === 0) {
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    
    // Create featured + pairs layout like main site
    const featured = saved[0];
    const pairs = [];
    for (let i = 1; i < saved.length; i += 2) {
        pairs.push(saved.slice(i, i + 2));
    }
    
    let html = '';
    
    // Featured article
    html += createArticleCard(featured, true);
    
    // Pairs
    pairs.forEach(pair => {
        html += '<div class="two-col-row">';
        pair.forEach(article => {
            html += createArticleCard(article, false);
        });
        html += '</div>';
    });
    
    // Back link
    html += `
        <div style="text-align: center; margin-top: 20px;">
            <a href="select.php" style="display: inline-block; background: #000; color: #fff; padding: 12px 24px; text-decoration: none; font-weight: 500;">← Back to Categories</a>
        </div>
    `;
    
    container.innerHTML = html;
}

function createArticleCard(article, isFeatured) {
    const imageHtml = article.urlToImage ? 
        `<img src="${article.urlToImage}" alt="Article image" class="card-image" onerror="this.style.display='none'">` : '';
    
    // We'll check server-side summary status after rendering
    const cardId = 'card-' + btoa(article.url).replace(/[^a-zA-Z0-9]/g, '');
    
    return `
        <div class="card" onclick="window.open('${article.url}', '_blank')" style="position: relative;" id="${cardId}">
            <button class="heart-btn saved" onclick="event.preventDefault(); event.stopPropagation(); removeArticle('${article.url}')">❤️</button>
            <button class="summarize-btn" onclick="event.preventDefault(); event.stopPropagation(); summarizeArticle('${article.url}', this)" data-url="${article.url}">Checking...</button>
            ${imageHtml}
            <div>
                <div class="card-title">${article.title}</div>
                <div class="card-description">${article.description || ''}</div>
            </div>
            <div class="card-meta">
                <span class="card-source">${article.source.name || 'Unknown'}</span>
                <span class="card-date">${new Date(article.publishedAt).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
            </div>
        </div>
    `;
}

function removeArticle(url) {
    let saved = JSON.parse(localStorage.getItem('savedArticles') || '[]');
    saved = saved.filter(article => article.url !== url);
    localStorage.setItem('savedArticles', JSON.stringify(saved));
    loadSavedArticles(); // Reload the page
}

async function checkSummaryStatus(url, button) {
    try {
        const response = await fetch('check-summary.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ url: url })
        });

        const responseText = await response.text();
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            // If JSON parsing fails, assume summary doesn't exist
            console.warn('Failed to parse check-summary response:', responseText.substring(0, 100));
            throw new Error('Invalid response from server');
        }
        
        if (data.exists) {
            button.textContent = 'View Summary';
            button.className = 'summarize-btn view-summary';
            button.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                viewSummary(url);
            };
            
            // Also check if audio exists and show indicator
            checkAudioIndicator(url, button);
        } else {
            button.textContent = 'Summarize';
            button.className = 'summarize-btn';
        }
    } catch (error) {
        button.textContent = 'Summarize';
        button.className = 'summarize-btn';
    }
}

async function checkAudioIndicator(url, button) {
    try {
        const audioResponse = await fetch('check-audio.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ url: url })
        });

        const audioData = await audioResponse.json();
        if (audioData.exists) {
            // Add audio indicator to the card
            const card = button.closest('.card');
            if (card && !card.querySelector('.audio-indicator')) {
                const indicator = document.createElement('div');
                indicator.className = 'audio-indicator';
                indicator.innerHTML = '🎵';
                indicator.title = 'Audio available';
                card.appendChild(indicator);
            }
        }
    } catch (error) {
        console.warn('Failed to check audio availability:', error);
    }
}

async function summarizeArticle(url, button) {
    button.disabled = true;
    button.textContent = 'Summarizing...';
    
    try {
        const response = await fetch('scrape-article-python.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ url: url })
        });

        const responseText = await response.text();
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            throw new Error('Server returned non-JSON response: ' + responseText.substring(0, 100));
        }

        if (!data.success) {
            throw new Error(data.error || 'Failed to summarize');
        }

        // Update button
        button.textContent = 'View Summary';
        button.className = 'summarize-btn view-summary';
        button.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            viewSummary(url);
        };

    } catch (error) {
        alert('Failed to summarize article: ' + error.message);
        button.textContent = 'Summarize';
    }
    
    button.disabled = false;
}

function viewSummary(url) {
    window.open('summary-redesigned.php?url=' + encodeURIComponent(url), '_blank');
}

// Load articles when page loads
loadSavedArticles();

// Check summary status for all articles after loading
setTimeout(() => {
    const buttons = document.querySelectorAll('.summarize-btn[data-url]');
    buttons.forEach(button => {
        const url = button.getAttribute('data-url');
        if (url) {
            checkSummaryStatus(url, button);
        }
    });
}, 500);
</script>

<!-- Persistent Audio Player -->
<script src="persistent-player.js"></script>

</body>
</html>