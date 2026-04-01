#!/usr/bin/env python3
"""
Article scraper and summarizer using proper libraries
Much more reliable than PHP-based scraping
"""

import sys
import json
import requests
import time
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse
import re
import os

def extract_article_content(url):
    """Extract main content from article URL"""
    headers = {
        'User-Agent': 'Mozilla/5.0 (compatible; NewsBot/1.0; +https://johnnews.pixelchemi.st)',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language': 'en-US,en;q=0.5',
        'Accept-Encoding': 'gzip, deflate',
        'Connection': 'keep-alive',
    }
    
    try:
        # Fetch the page
        response = requests.get(url, headers=headers, timeout=30)
        response.raise_for_status()
        
        # Parse with BeautifulSoup
        soup = BeautifulSoup(response.content, 'html.parser')
        
        # Remove unwanted elements
        for element in soup(['script', 'style', 'nav', 'header', 'footer', 'sidebar', 'aside']):
            element.decompose()
        
        # Try different content selectors in order of preference
        content_selectors = [
            'article',
            '[role="main"]',
            '.article-content',
            '.post-content',
            '.entry-content', 
            '.content',
            'main',
            '.article-body',
            '.story-body'
        ]
        
        content_element = None
        for selector in content_selectors:
            content_element = soup.select_one(selector)
            if content_element:
                break
        
        # Fallback to body if no specific content area found
        if not content_element:
            content_element = soup.body or soup
        
        # Extract text
        text = content_element.get_text(separator=' ', strip=True)
        
        # Clean up whitespace
        text = re.sub(r'\s+', ' ', text)
        text = text.strip()
        
        # Limit length for API efficiency
        text = text[:8000]
        
        if len(text) < 100:
            raise Exception("Could not extract meaningful content from article")
            
        return text
        
    except Exception as e:
        raise Exception(f"Failed to scrape article: {str(e)}")

def summarize_with_openai(text):
    """Generate summary using OpenAI API"""
    
    # OpenAI API key from environment
    api_key = os.getenv('OPENAI_API_KEY', 'YOUR_OPENAI_API_KEY_HERE')
    
    headers = {
        'Authorization': f'Bearer {api_key}',
        'Content-Type': 'application/json'
    }
    
    data = {
        'model': 'gpt-3.5-turbo',
        'messages': [
            {
                'role': 'system',
                'content': 'You are a professional news summarizer. Create clear, concise summaries that capture the essential information and key points of news articles.'
            },
            {
                'role': 'user',
                'content': f"""Please provide a concise summary of this news article in the following format:

EXECUTIVE SUMMARY:
[One paragraph summary of the main points and key findings]

KEY POINTS:
• [First key point]
• [Second key point] 
• [Third key point]
• [Additional points as needed]

Article text:
{text}"""
            }
        ],
        'max_tokens': 500,
        'temperature': 0.3
    }
    
    try:
        response = requests.post(
            'https://api.openai.com/v1/chat/completions',
            headers=headers,
            json=data,
            timeout=60
        )
        response.raise_for_status()
        
        result = response.json()
        
        if 'error' in result:
            raise Exception(f"OpenAI API error: {result['error']['message']}")
        
        if 'choices' not in result or not result['choices']:
            raise Exception("No response from OpenAI")
            
        summary = result['choices'][0]['message']['content']
        return summary
        
    except Exception as e:
        raise Exception(f"Failed to generate summary: {str(e)}")

def save_summary(url, summary, original_length):
    """Save summary to JSON file"""
    import hashlib
    
    url_hash = hashlib.md5(url.encode()).hexdigest()
    
    summary_data = {
        'url': url,
        'summary': summary,
        'timestamp': int(time.time()),
        'originalLength': original_length
    }
    
    # Load existing summaries
    summaries_file = '/var/www/news/data/summaries.json'
    
    try:
        with open(summaries_file, 'r') as f:
            summaries = json.load(f)
    except (FileNotFoundError, json.JSONDecodeError):
        summaries = {}
    
    # Add new summary
    summaries[url_hash] = summary_data
    
    # Save back to file
    os.makedirs('/var/www/news/data', exist_ok=True)
    with open(summaries_file, 'w') as f:
        json.dump(summaries, f, indent=2)
    
    return summary_data

def main():
    """Main function to handle scraping and summarization"""
    try:
        # Read JSON input from stdin
        input_data = json.loads(sys.stdin.read())
        url = input_data.get('url', '')
        
        if not url:
            raise Exception("No URL provided")
        
        # Validate URL
        from urllib.parse import urlparse
        parsed = urlparse(url)
        if not parsed.scheme or not parsed.netloc:
            raise Exception("Invalid URL format")
        
        # Extract article content
        text = extract_article_content(url)
        
        # Generate summary
        summary = summarize_with_openai(text)
        
        # Save summary
        result = save_summary(url, summary, len(text))
        
        # Return success
        output = {
            'success': True,
            'summary': summary,
            'originalLength': len(text)
        }
        
        print(json.dumps(output))
        
    except Exception as e:
        # Return error
        output = {
            'success': False,
            'error': str(e)
        }
        print(json.dumps(output))
        sys.exit(1)

if __name__ == '__main__':
    main()