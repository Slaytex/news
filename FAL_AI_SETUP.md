# fal.ai TTS Setup Instructions

To enable audio generation for article summaries, you need to set up fal.ai:

## 1. Get a fal.ai API Key
1. Visit [fal.ai](https://fal.ai) and create an account
2. Go to your dashboard and generate an API key
3. Copy the API key

## 2. Add API Key to generate-audio.php
1. Open `/var/www/news/generate-audio.php`
2. Find the line: `$falApiKey = ''; // TODO: Add your fal.ai API key here`
3. Replace it with: `$falApiKey = 'your-actual-api-key-here';`

## 3. Test the Feature
1. Save an article from any news category
2. Click "Summarize" to generate the summary
3. Click "View Summary" to open the summary page
4. Click "🔊 Generate Audio" to create the audio version
5. Use the audio player to listen to the summary

## Voice Options
You can change the TTS voice in `generate-audio.php` by modifying the `voice` parameter:
- `'alloy'` - Balanced voice
- `'echo'` - Clear and confident
- `'fable'` - Warm and storytelling
- `'nova'` - Youthful and energetic (default)
- `'onyx'` - Deep and authoritative
- `'shimmer'` - Soft and gentle

## Audio Storage
- Generated audio files are saved in `/var/www/news/data/audio/`
- Files are cached to avoid regenerating the same summary
- Each audio file is named with the MD5 hash of the article URL

## Cost Considerations
fal.ai charges for TTS generation. Check their pricing page for current rates. Audio files are cached locally to minimize API usage.