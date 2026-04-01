<?php
// Multi-Category News Site
$apiKey = '10a3a1fd0c424fe0850a534b0a1067b9';
$baseUrl = 'https://newsapi.org/v2/everything';

// Get category from URL parameter
$category = $_GET['category'] ?? null;

// If no category specified, redirect to category selection
if (!$category) {
    header('Location: select.php');
    exit;
}

// Define category configurations
$categories = [
    'china-us' => [
        'query' => 'China United States OR China US OR China America OR US China relations OR China trade OR China Taiwan',
        'title' => 'US-China Relations Feed',
        'subtitle' => 'Latest headlines on China-United States relations',
        'byline' => 'Exclusively for John Ruwitch | NPR',
        'header_image' => 'flags-background.jpg'
    ],
    'iran-war' => [
        'query' => 'Iran war OR Middle East conflict OR Iran military OR Iran Israel OR Iran strike',
        'title' => 'Iran War Updates',
        'subtitle' => 'Latest developments in Middle East conflicts',
        'byline' => 'Military and diplomatic coverage',
        'header_image' => 'iran-war-header.jpg'
    ],
    'ai-tech' => [
        'query' => 'artificial intelligence OR AI technology OR machine learning OR OpenAI OR ChatGPT OR AI development',
        'title' => 'AI Technology News',
        'subtitle' => 'Latest breakthroughs in artificial intelligence',
        'byline' => 'Technology and innovation coverage',
        'header_image' => 'ai-tech-header.jpg'
    ],
    'mini-computers' => [
        'query' => 'Raspberry Pi OR single board computer OR mini PC OR embedded computing OR IoT devices',
        'title' => 'Mini-Computer Technology',
        'subtitle' => 'Compact computing solutions and innovations',
        'byline' => 'Hardware and embedded systems news',
        'header_image' => 'mini-computers-header.jpg'
    ]
];

// Get current category config
$currentCategory = $categories[$category] ?? $categories['china-us'];
$query = $currentCategory['query'];
$sortBy = 'publishedAt';
$pageSize = 10;
$language = 'en';

$apiUrl = $baseUrl . '?' . http_build_query([
    'q' => $query,
    'sortBy' => $sortBy,
    'pageSize' => $pageSize,
    'language' => $language,
    'apiKey' => $apiKey
]);

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => ['User-Agent: Mozilla/5.0 (compatible; China-US News Site/1.0)']
    ]
]);

$response = @file_get_contents($apiUrl, false, $context);
$articles = [];

if ($response) {
    $data = json_decode($response, true);
    if ($data && $data['status'] === 'ok') {
        $articles = array_values(array_filter($data['articles'], fn($a) => !empty($a['title']) && !empty($a['url'])));
    }
}

$lastUpdated = gmdate('Y-m-d H:i:s') . ' UTC';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>US-China Relations Feed</title>
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

        /* ── HEADER ── */
        .site-header {
            width: 100%;
            height: 250px;
            background-color: #FF423D;
            background-image: url('<?= $currentCategory['header_image'] ?>');
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

        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            margin-bottom: 16px;
            border-radius: 4px;
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
            z-index: 10;
        }

        .heart-btn:hover {
            background: #fff;
            transform: scale(1.1);
        }

        .heart-btn.saved {
            color: #ff4444;
            background: #fff;
        }

        .card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
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

        /* ── ROWS ── */
        .two-col-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* ── TIMESTAMP ── */
        .timestamp-bar {
            background: #EBEBEB;
            padding: 13px 19px;
            font-family: 'Andale Mono', 'Courier New', monospace;
            font-size: 15px;
            color: #000;
            text-align: center;
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
                background-size: cover;
                background-position: center;
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

<header class="site-header">
    <div class="header-content">
        <div class="header-title"><?= htmlspecialchars($currentCategory['title']) ?></div>
        <div class="header-subtitle"><?= htmlspecialchars($currentCategory['subtitle']) ?></div>
        <div class="header-byline"><?= htmlspecialchars($currentCategory['byline']) ?></div>
    </div>
</header>

<div class="main-wrapper">
    <?php if (empty($articles)): ?>
        <div class="no-articles">No recent articles found. Please check back later.</div>
    <?php else: ?>
        <?php
        $featured = $articles[0];
        $pairs    = array_chunk(array_slice($articles, 1), 2);
        ?>

        <!-- Featured story (full width) -->
        <div class="card" onclick="window.open('<?= htmlspecialchars($featured['url']) ?>', '_blank')">
            <button class="heart-btn" onclick="event.stopPropagation(); toggleSave(this, <?= htmlspecialchars(json_encode($featured), ENT_QUOTES, 'UTF-8') ?>)">🤍</button>
            <?php if (!empty($featured['urlToImage'])): ?>
                <img src="<?= htmlspecialchars($featured['urlToImage']) ?>" alt="Article image" class="card-image" onerror="this.style.display='none'">
            <?php endif; ?>
            <div>
                <div class="card-title"><?= htmlspecialchars($featured['title']) ?></div>
                <?php if (!empty($featured['description'])): ?>
                    <div class="card-description"><?= htmlspecialchars($featured['description']) ?></div>
                <?php endif; ?>
            </div>
            <div class="card-meta">
                <span class="card-source"><?= htmlspecialchars($featured['source']['name'] ?? 'Unknown') ?></span>
                <span class="card-date"><?= date('F j, Y', strtotime($featured['publishedAt'])) ?></span>
            </div>
        </div>

        <?php foreach ($pairs as $i => $pair): ?>

            <div class="two-col-row">
                <?php foreach ($pair as $article): ?>
                    <div class="card" onclick="window.open('<?= htmlspecialchars($article['url']) ?>', '_blank')">
                        <button class="heart-btn" onclick="event.stopPropagation(); toggleSave(this, <?= htmlspecialchars(json_encode($article), ENT_QUOTES, 'UTF-8') ?>)">🤍</button>
                        <?php if (!empty($article['urlToImage'])): ?>
                            <img src="<?= htmlspecialchars($article['urlToImage']) ?>" alt="Article image" class="card-image" onerror="this.style.display='none'">
                        <?php endif; ?>
                        <div>
                            <div class="card-title"><?= htmlspecialchars($article['title']) ?></div>
                            <?php if (!empty($article['description'])): ?>
                                <div class="card-description"><?= htmlspecialchars($article['description']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="card-meta">
                            <span class="card-source"><?= htmlspecialchars($article['source']['name'] ?? 'Unknown') ?></span>
                            <span class="card-date"><?= date('F j, Y', strtotime($article['publishedAt'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>

        <div class="timestamp-bar">Last updated: <?= $lastUpdated ?></div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="select.php" style="display: inline-block; background: #000; color: #fff; padding: 12px 24px; text-decoration: none; font-weight: 500;">← Back to Categories</a>
        </div>
    <?php endif; ?>
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

// Save/unsave articles
function toggleSave(button, article) {
    let saved = JSON.parse(localStorage.getItem('savedArticles') || '[]');
    const exists = saved.find(a => a.url === article.url);
    
    if (exists) {
        // Remove from saved
        saved = saved.filter(a => a.url !== article.url);
        button.innerHTML = '🤍';
        button.classList.remove('saved');
    } else {
        // Add to saved
        saved.push(article);
        button.innerHTML = '❤️';
        button.classList.add('saved');
    }
    
    localStorage.setItem('savedArticles', JSON.stringify(saved));
}

// Check saved status on load
window.addEventListener('load', function() {
    const saved = JSON.parse(localStorage.getItem('savedArticles') || '[]');
    const heartButtons = document.querySelectorAll('.heart-btn');
    
    heartButtons.forEach(button => {
        const card = button.closest('.card');
        const url = card.onclick.toString().match(/window\.open\('([^']+)'/);
        if (url && saved.find(a => a.url === url[1])) {
            button.innerHTML = '❤️';
            button.classList.add('saved');
        }
    });
});
</script>

<!-- Persistent Audio Player -->
<script src="persistent-player.js"></script>

</body>
</html>
