/**
 * Persistent Audio Player - Modern floating player that stays at bottom
 * Works across all pages using localStorage to maintain state
 */

class PersistentAudioPlayer {
    constructor() {
        this.isPlaying = false;
        this.currentTrack = null;
        this.currentTime = 0;
        this.duration = 0;
        this.volume = 1;
        this.wasPlaying = false;
        this.player = null;
        this.playerElement = null;
        this.storageKey = 'newsAudioPlayer';
        
        this.init();
    }
    
    init() {
        // Load saved state
        this.loadState();
        
        // Create player UI
        this.createPlayer();
        
        // Restore player if there was a track
        if (this.currentTrack) {
            this.showPlayer();
            this.loadTrack(this.currentTrack, false); // Don't auto-play on page load
            
            // Always restore position, and resume if was playing
            this.player.addEventListener('loadeddata', () => {
                if (this.currentTime > 0) {
                    this.player.currentTime = this.currentTime;
                }
                
                // If audio was playing, try to resume (user must have interacted recently)
                if (this.wasPlaying) {
                    // Small delay to ensure everything is loaded
                    setTimeout(() => {
                        this.player.play().catch(e => {
                            console.log('Auto-resume blocked - user interaction required');
                            // Show visual indicator that audio is ready to resume
                            this.isPlaying = false;
                            this.updatePlayButton();
                            
                            // Add pulsing effect to play button to indicate it's ready
                            const playBtn = document.getElementById('player-playpause');
                            if (playBtn) {
                                playBtn.style.animation = 'pulse 1.5s ease-in-out infinite';
                                playBtn.title = 'Click to resume audio';
                            }
                        });
                    }, 100);
                }
            }, { once: true });
        }
        
        // Auto-save state frequently to preserve position
        setInterval(() => this.saveState(), 2000);
        
        // Save state before page unload (multiple events to catch all cases)
        const saveBeforeLeaving = () => {
            if (this.player && this.currentTrack) {
                this.wasPlaying = !this.player.paused;
                this.currentTime = this.player.currentTime || 0;
                this.saveState();
            }
        };
        
        window.addEventListener('beforeunload', saveBeforeLeaving);
        window.addEventListener('unload', saveBeforeLeaving);
        window.addEventListener('pagehide', saveBeforeLeaving);
        
        // Save state when page becomes hidden (mobile tab switching)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && this.player && this.currentTrack) {
                this.wasPlaying = !this.player.paused;
                this.currentTime = this.player.currentTime || 0;
                this.saveState();
            }
        });
        
        // Target navigation elements with more specific detection
        this.attachNavigationListeners();
    }
    
    createPlayer() {
        // Create player container
        const playerHTML = `
            <div id="persistent-audio-player" class="persistent-player" style="display: none;">
                <div class="player-content">
                    <div class="track-info">
                        <div class="track-title" id="player-title">Article Audio</div>
                        <div class="track-subtitle" id="player-subtitle">AI Generated Summary</div>
                    </div>
                    
                    <div class="player-controls">
                        <button class="control-btn" id="player-prev" title="Previous">⏮</button>
                        <button class="control-btn play-pause" id="player-playpause" title="Play">▶️</button>
                        <button class="control-btn" id="player-next" title="Next">⏭</button>
                    </div>
                    
                    <div class="player-progress">
                        <span class="time-current" id="player-current">0:00</span>
                        <div class="progress-container">
                            <div class="progress-bar" id="player-progress">
                                <div class="progress-fill" id="player-progress-fill"></div>
                                <div class="progress-thumb" id="player-progress-thumb"></div>
                            </div>
                        </div>
                        <span class="time-duration" id="player-duration">0:00</span>
                    </div>
                    
                    <div class="player-volume">
                        <button class="volume-btn" id="player-volume-btn" title="Volume">🔊</button>
                        <div class="volume-slider" id="player-volume-slider">
                            <div class="volume-fill" id="player-volume-fill"></div>
                        </div>
                    </div>
                    
                    <button class="close-btn" id="player-close" title="Close">×</button>
                </div>
                
                <audio id="player-audio" preload="metadata"></audio>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', playerHTML);
        this.playerElement = document.getElementById('persistent-audio-player');
        this.player = document.getElementById('player-audio');
        
        this.attachEventListeners();
        this.addStyles();
    }
    
    addStyles() {
        if (document.getElementById('persistent-player-styles')) return;
        
        const styles = `
            <style id="persistent-player-styles">
                .persistent-player {
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
                    border-top: 1px solid #333;
                    z-index: 10000;
                    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3);
                    backdrop-filter: blur(10px);
                    transition: transform 0.3s ease-in-out;
                }
                
                .persistent-player.hidden {
                    transform: translateY(100%);
                }
                
                .player-content {
                    display: flex;
                    align-items: center;
                    padding: 12px 20px;
                    gap: 20px;
                    max-width: 1200px;
                    margin: 0 auto;
                }
                
                .track-info {
                    min-width: 200px;
                    flex: 1;
                }
                
                .track-title {
                    color: #fff;
                    font-weight: 600;
                    font-size: 14px;
                    margin-bottom: 2px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                
                .track-subtitle {
                    color: #999;
                    font-size: 12px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                
                .player-controls {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .control-btn {
                    background: none;
                    border: none;
                    color: #fff;
                    font-size: 14px;
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }
                
                .control-btn:hover {
                    background: rgba(255, 255, 255, 0.1);
                    transform: scale(1.1);
                }
                
                .play-pause {
                    background: #FF423D;
                    font-size: 16px;
                    width: 40px;
                    height: 40px;
                }
                
                .play-pause:hover {
                    background: #e63946;
                }
                
                .player-progress {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    flex: 1;
                    min-width: 200px;
                }
                
                .time-current, .time-duration {
                    color: #ccc;
                    font-size: 12px;
                    font-family: monospace;
                    min-width: 35px;
                }
                
                .progress-container {
                    flex: 1;
                    height: 20px;
                    display: flex;
                    align-items: center;
                    cursor: pointer;
                }
                
                .progress-bar {
                    width: 100%;
                    height: 4px;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 2px;
                    position: relative;
                    overflow: hidden;
                }
                
                .progress-fill {
                    height: 100%;
                    background: #FF423D;
                    border-radius: 2px;
                    width: 0%;
                    transition: width 0.1s ease;
                }
                
                .progress-thumb {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%) translateX(-50%);
                    width: 12px;
                    height: 12px;
                    background: #fff;
                    border-radius: 50%;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
                    left: 0%;
                    opacity: 0;
                    transition: opacity 0.2s ease;
                }
                
                .progress-container:hover .progress-thumb {
                    opacity: 1;
                }
                
                .player-volume {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .volume-btn {
                    background: none;
                    border: none;
                    color: #fff;
                    font-size: 14px;
                    cursor: pointer;
                    padding: 4px;
                }
                
                .volume-slider {
                    width: 60px;
                    height: 4px;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 2px;
                    cursor: pointer;
                    position: relative;
                }
                
                .volume-fill {
                    height: 100%;
                    background: #fff;
                    border-radius: 2px;
                    width: 100%;
                    transition: width 0.1s ease;
                }
                
                .close-btn {
                    background: none;
                    border: none;
                    color: #999;
                    font-size: 18px;
                    cursor: pointer;
                    padding: 4px;
                    width: 24px;
                    height: 24px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: color 0.2s ease;
                }
                
                .close-btn:hover {
                    color: #fff;
                }
                
                /* Pulse animation for resume indicator */
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                    100% { transform: scale(1); }
                }
                
                /* Responsive */
                @media (max-width: 768px) {
                    .player-content {
                        padding: 8px 12px;
                        gap: 12px;
                    }
                    
                    .track-info {
                        min-width: 120px;
                    }
                    
                    .player-progress {
                        min-width: 150px;
                        gap: 8px;
                    }
                    
                    .volume-slider {
                        width: 40px;
                    }
                    
                    .control-btn {
                        width: 32px;
                        height: 32px;
                        font-size: 12px;
                    }
                    
                    .play-pause {
                        width: 36px;
                        height: 36px;
                        font-size: 14px;
                    }
                }
            </style>
        `;
        
        document.head.insertAdjacentHTML('beforeend', styles);
    }
    
    attachEventListeners() {
        // Play/Pause
        document.getElementById('player-playpause').addEventListener('click', () => {
            this.togglePlayPause();
        });
        
        // Progress bar
        const progressContainer = document.querySelector('.progress-container');
        progressContainer.addEventListener('click', (e) => {
            const rect = progressContainer.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            this.seek(percent);
        });
        
        // Volume
        document.getElementById('player-volume-btn').addEventListener('click', () => {
            this.toggleMute();
        });
        
        const volumeSlider = document.getElementById('player-volume-slider');
        volumeSlider.addEventListener('click', (e) => {
            const rect = volumeSlider.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            this.setVolume(percent);
        });
        
        // Close
        document.getElementById('player-close').addEventListener('click', () => {
            this.hidePlayer();
        });
        
        // Audio events
        this.player.addEventListener('loadedmetadata', () => {
            this.duration = this.player.duration;
            this.updateUI();
        });
        
        this.player.addEventListener('timeupdate', () => {
            this.currentTime = this.player.currentTime;
            this.updateProgress();
        });
        
        this.player.addEventListener('ended', () => {
            this.isPlaying = false;
            this.updatePlayButton();
        });
        
        this.player.addEventListener('play', () => {
            this.isPlaying = true;
            this.updatePlayButton();
        });
        
        this.player.addEventListener('pause', () => {
            this.isPlaying = false;
            this.updatePlayButton();
        });
    }
    
    loadTrack(audioUrl, autoPlay = false, title = 'Article Audio', subtitle = 'AI Generated Summary') {
        // If the same track is already loaded and playing, don't interrupt it
        if (this.currentTrack === audioUrl && !this.player.paused) {
            // Just update the UI info if different
            document.getElementById('player-title').textContent = title;
            document.getElementById('player-subtitle').textContent = subtitle;
            return;
        }
        
        this.currentTrack = audioUrl;
        this.player.src = audioUrl;
        
        // Update UI
        document.getElementById('player-title').textContent = title;
        document.getElementById('player-subtitle').textContent = subtitle;
        
        if (autoPlay) {
            this.player.play();
        }
        
        this.saveState();
    }
    
    togglePlayPause() {
        // Remove pulse animation when user interacts
        const playBtn = document.getElementById('player-playpause');
        if (playBtn) {
            playBtn.style.animation = '';
            playBtn.title = this.isPlaying ? 'Pause' : 'Play';
        }
        
        if (this.isPlaying) {
            this.player.pause();
        } else {
            this.player.play();
        }
    }
    
    seek(percent) {
        if (this.duration > 0) {
            this.player.currentTime = this.duration * percent;
        }
    }
    
    setVolume(percent) {
        this.volume = percent;
        this.player.volume = percent;
        document.getElementById('player-volume-fill').style.width = (percent * 100) + '%';
        this.updateVolumeButton();
        this.saveState();
    }
    
    toggleMute() {
        if (this.player.volume > 0) {
            this.player.volume = 0;
            this.updateVolumeButton();
        } else {
            this.player.volume = this.volume;
            this.updateVolumeButton();
        }
    }
    
    updateProgress() {
        if (this.duration > 0) {
            const percent = (this.currentTime / this.duration) * 100;
            document.getElementById('player-progress-fill').style.width = percent + '%';
            document.getElementById('player-progress-thumb').style.left = percent + '%';
        }
        
        document.getElementById('player-current').textContent = this.formatTime(this.currentTime);
        document.getElementById('player-duration').textContent = this.formatTime(this.duration);
    }
    
    updatePlayButton() {
        const btn = document.getElementById('player-playpause');
        btn.textContent = this.isPlaying ? '⏸️' : '▶️';
        btn.title = this.isPlaying ? 'Pause' : 'Play';
    }
    
    updateVolumeButton() {
        const btn = document.getElementById('player-volume-btn');
        if (this.player.volume === 0) {
            btn.textContent = '🔇';
        } else if (this.player.volume < 0.5) {
            btn.textContent = '🔉';
        } else {
            btn.textContent = '🔊';
        }
    }
    
    attachNavigationListeners() {
        // Save audio state before navigation
        const saveBeforeNav = () => {
            if (this.player && this.currentTrack && !this.player.paused) {
                console.log('Navigation click detected, saving audio state...');
                this.wasPlaying = true;
                this.currentTime = this.player.currentTime || 0;
                this.saveState();
            }
        };
        
        // Attach to navigation elements when they appear
        const attachToElements = () => {
            // Hamburger menu items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', saveBeforeNav);
            });
            
            // Back buttons
            document.querySelectorAll('.back-button').forEach(button => {
                button.addEventListener('click', saveBeforeNav);
            });
            
            // Any links to main pages
            document.querySelectorAll('a[href*="select.php"], a[href*="saved.php"], a[href*="index.php"]').forEach(link => {
                link.addEventListener('click', saveBeforeNav);
            });
        };
        
        // Run immediately and also with a delay for dynamically loaded content
        attachToElements();
        setTimeout(attachToElements, 1000);
        
        // Also use event delegation for any navigation
        document.addEventListener('click', (e) => {
            const target = e.target;
            const href = target.href || target.closest('a')?.href;
            
            if (href && (href.includes('select.php') || href.includes('saved.php') || href.includes('index.php')) && 
                this.player && this.currentTrack && !this.player.paused) {
                saveBeforeNav();
            }
        });
    }
    
    updateUI() {
        this.updateProgress();
        this.updatePlayButton();
        this.updateVolumeButton();
        document.getElementById('player-volume-fill').style.width = (this.volume * 100) + '%';
    }
    
    formatTime(seconds) {
        if (!seconds || !isFinite(seconds)) return '0:00';
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return mins + ':' + (secs < 10 ? '0' : '') + secs;
    }
    
    showPlayer() {
        this.playerElement.style.display = 'block';
        // Add bottom padding to body so content isn't hidden
        document.body.style.paddingBottom = '80px';
    }
    
    hidePlayer() {
        this.playerElement.style.display = 'none';
        document.body.style.paddingBottom = '';
        this.currentTrack = null;
        this.player.src = '';
        this.saveState();
    }
    
    saveState() {
        if (this.player && this.currentTrack) {
            this.currentTime = this.player.currentTime || this.currentTime;
            this.wasPlaying = !this.player.paused;
        }
        
        const state = {
            currentTrack: this.currentTrack,
            currentTime: this.currentTime,
            volume: this.volume,
            wasPlaying: this.wasPlaying,
            title: document.getElementById('player-title')?.textContent || 'Article Audio',
            subtitle: document.getElementById('player-subtitle')?.textContent || 'AI Generated Summary'
        };
        localStorage.setItem(this.storageKey, JSON.stringify(state));
    }
    
    loadState() {
        try {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                const state = JSON.parse(saved);
                console.log('Loading player state:', state);
                this.currentTrack = state.currentTrack;
                this.currentTime = state.currentTime || 0;
                this.volume = state.volume || 1;
                this.wasPlaying = state.wasPlaying || false;
            }
        } catch (e) {
            console.warn('Failed to load player state:', e);
        }
    }
}

// Global player instance
let globalAudioPlayer = null;

// Initialize player when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        globalAudioPlayer = new PersistentAudioPlayer();
    });
} else {
    globalAudioPlayer = new PersistentAudioPlayer();
}

// Global function to add audio to the player
window.playArticleAudio = function(audioUrl, title = 'Article Audio', subtitle = 'AI Generated Summary') {
    if (globalAudioPlayer) {
        globalAudioPlayer.showPlayer();
        globalAudioPlayer.loadTrack(audioUrl, true, title, subtitle);
    }
};