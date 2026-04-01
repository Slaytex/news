<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Feed - Select Category</title>
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
            background-image: url('https://create.pixelchemi.st/images/generated_1775002378350.jpg');
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

        /* ── CATEGORY CARDS ── */
        .category-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .category-card {
            background: #fff;
            padding: 40px 19px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 200px;
            text-decoration: none;
            color: #000;
            transition: box-shadow 0.15s ease, transform 0.1s ease;
            cursor: pointer;
        }

        .category-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .category-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .category-title {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .category-description {
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            color: #666;
            line-height: 1.4;
        }

        /* ── INTRO SECTION ── */
        .intro-section {
            padding: 40px 19px;
            text-align: center;
        }

        .intro-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #000;
        }

        .intro-subtitle {
            font-size: 16px;
            font-weight: 500;
            color: #666;
            line-height: 1.5;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .main-wrapper {
                padding: 20px 16px;
            }
            .category-grid {
                grid-template-columns: 1fr;
            }
            .header-title {
                font-size: 26px;
            }
            .category-title {
                font-size: 20px;
            }
            .intro-title {
                font-size: 24px;
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
        <div class="header-title">Global News Feed</div>
        <div class="header-subtitle">Latest headlines across key topics</div>
        <div class="header-byline">Choose your news category</div>
    </div>
</header>

<div class="main-wrapper">
    
    <div class="intro-section">
        <div class="intro-title">What news interests you today?</div>
        <div class="intro-subtitle">Select a category below to see the latest headlines</div>
    </div>

    <div class="category-grid">
        
        <a class="category-card" href="index.php?category=china-us">
            <div class="category-icon">🇨🇳🇺🇸</div>
            <div class="category-title">China/US Relations</div>
            <div class="category-description">Trade, diplomacy, and geopolitical developments between China and the United States</div>
        </a>

        <a class="category-card" href="index.php?category=iran-war">
            <div class="category-icon">⚔️</div>
            <div class="category-title">War in Iran</div>
            <div class="category-description">Latest developments in Middle East conflicts and military operations</div>
        </a>

        <a class="category-card" href="index.php?category=ai-tech">
            <div class="category-icon">🤖</div>
            <div class="category-title">AI Technology</div>
            <div class="category-description">Artificial intelligence breakthroughs, policy, and industry developments</div>
        </a>

        <a class="category-card" href="index.php?category=mini-computers">
            <div class="category-icon">🖥️</div>
            <div class="category-title">Mini-Computer Technology</div>
            <div class="category-description">Raspberry Pi, single-board computers, and compact computing solutions</div>
        </a>

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
</script>

<!-- Persistent Audio Player -->
<script src="persistent-player.js"></script>

</body>
</html>