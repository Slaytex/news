# China-US Relations News Site

A simple, clean news aggregation website that displays the latest headlines focused on China-United States relations.

## Features

- **Real-time news**: Fetches latest articles from NewsAPI on each page load
- **Responsive design**: Works on desktop and mobile devices
- **Clean interface**: Modern, readable design with hover effects
- **Direct links**: Click any headline to read the full article
- **Source attribution**: Shows news source and publication date

## Technical Stack

- **Backend**: PHP (simple, no frameworks)
- **Frontend**: HTML5, CSS3 with modern responsive design
- **API**: NewsAPI.org for news data
- **Hosting**: Apache web server

## Search Keywords

The site searches for articles containing:
- "China United States"
- "China US" 
- "China America"
- "US China relations"
- "China trade"
- "China Taiwan"

## Setup

1. Ensure PHP is installed and enabled on your web server
2. Place files in your web root directory
3. Make sure your web server can make outbound HTTP requests to NewsAPI
4. The site will automatically fetch and display the latest 10 articles

## API Usage

- Uses NewsAPI.org "everything" endpoint
- Developer tier (free)
- Sorts by publication date (newest first)
- English language articles only

## File Structure

```
/var/www/news/
├── index.php      # Main application file
├── .htaccess      # Apache configuration
└── README.md      # This file
```

Built for John Ruwitch's vibecode project 🚀