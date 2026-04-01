# China-US Relations News Site

## Overview
A PHP news aggregation site that fetches and displays the latest China-US relations headlines from NewsAPI.org. Built for John Ruwitch's vibecode project.

## Tech Stack
- **Backend**: PHP 8.3 (no framework, single-file app)
- **Frontend**: HTML5 + CSS3, responsive grid layout
- **API**: NewsAPI.org "everything" endpoint (free tier)
- **Server**: Nginx + PHP-FPM
- **Hosting**: Ubuntu Linux, behind Cloudflare Tunnel (home server on Xfinity/Comcast which blocks inbound connections)

## File Structure
```
/var/www/news/
├── index.php      # Entire application (PHP backend + HTML/CSS frontend)
├── CLAUDE.md      # This file
└── README.md      # Project readme
```

## How It Works
- `index.php` fetches 10 latest English articles from NewsAPI on each page load
- Search terms: "China United States", "China US", "China America", "US China relations", "China trade", "China Taiwan"
- Articles are displayed in a responsive card grid with title, description, source, and date
- API key is embedded in `index.php`

## Hosting Setup

### Domain
- **URL**: `https://johnnews.pixelchemi.st`

### Nginx
- Config: `/etc/nginx/sites-available/johnnews.pixelchemi.st`
- Symlinked to `/etc/nginx/sites-enabled/`
- Uses PHP-FPM socket: `/var/run/php/php8.3-fpm.sock`
- Root: `/var/www/news`

### Cloudflare Tunnel
- Tunnel name: `pixelchemist`
- Tunnel ID: `1c8903ac-f4ba-49a0-b7d6-fbd1b90e5f02`
- The tunnel is **remotely managed** — the Cloudflare Zero Trust dashboard/API controls routing, NOT the local `config.yml`
- `johnnews.pixelchemi.st` routes to `http://localhost:80` via the tunnel
- DNS: CNAME pointing to `1c8903ac-f4ba-49a0-b7d6-fbd1b90e5f02.cfargotunnel.com`

### Adding New Subdomains (3 steps)
1. **Cloudflare DNS**: Add CNAME record → `1c8903ac-f4ba-49a0-b7d6-fbd1b90e5f02.cfargotunnel.com` (proxied)
2. **Cloudflare Tunnel**: Add public hostname via Zero Trust dashboard or API (the local config.yml is overridden by remote config)
3. **Nginx**: Create server block config and symlink to sites-enabled

## Development Notes
- No build step — edit `index.php` directly
- No database — all data is fetched live from NewsAPI
- No JavaScript — pure server-side rendering with PHP
- Styles are inline in the PHP file (no separate CSS file)
