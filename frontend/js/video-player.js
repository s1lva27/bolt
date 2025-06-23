// Modern Video Player JavaScript
class ModernVideoPlayer {
  constructor(videoElement) {
    this.video = videoElement;
    this.container = videoElement.closest('.video-container') || this.createContainer();
    this.isPlaying = false;
    this.isDragging = false;
    this.currentTime = 0;
    this.duration = 0;
    this.volume = 1;
    this.isMuted = false;
    this.isFullscreen = false;
    
    this.init();
  }
  
  createContainer() {
    const container = document.createElement('div');
    container.className = 'video-container';
    this.video.parentNode.insertBefore(container, this.video);
    container.appendChild(this.video);
    return container;
  }
  
  init() {
    // Hide native controls
    this.video.controls = false;
    this.video.preload = 'metadata';
    
    this.createControls();
    this.bindEvents();
    this.updateTimeDisplay();
    this.updateVolumeDisplay();
  }
  
  createControls() {
    const controlsHTML = `
      <div class="video-loading"></div>
      <div class="video-controls">
        <div class="progress-container">
          <div class="buffer-progress"></div>
          <div class="progress-bar">
            <div class="progress-handle"></div>
          </div>
        </div>
        <div class="controls-row">
          <div class="controls-left">
            <button class="control-btn play-pause-btn" title="Play/Pause">
              <i class="fas fa-play"></i>
            </button>
            <div class="volume-container">
              <button class="control-btn volume-btn" title="Mute/Unmute">
                <i class="fas fa-volume-up"></i>
              </button>
              <div class="volume-slider">
                <div class="volume-progress"></div>
              </div>
            </div>
            <div class="time-display">
              <span class="current-time">0:00</span> / <span class="duration">0:00</span>
            </div>
          </div>
          <div class="controls-right">
            <button class="control-btn fullscreen-btn" title="Fullscreen">
              <i class="fas fa-expand"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="play-overlay">
        <i class="fas fa-play"></i>
      </div>
    `;
    
    this.container.insertAdjacentHTML('beforeend', controlsHTML);
    this.cacheElements();
  }
  
  cacheElements() {
    this.playPauseBtn = this.container.querySelector('.play-pause-btn');
    this.playOverlay = this.container.querySelector('.play-overlay');
    this.progressContainer = this.container.querySelector('.progress-container');
    this.progressBar = this.container.querySelector('.progress-bar');
    this.progressHandle = this.container.querySelector('.progress-handle');
    this.bufferProgress = this.container.querySelector('.buffer-progress');
    this.volumeBtn = this.container.querySelector('.volume-btn');
    this.volumeSlider = this.container.querySelector('.volume-slider');
    this.volumeProgress = this.container.querySelector('.volume-progress');
    this.currentTimeEl = this.container.querySelector('.current-time');
    this.durationEl = this.container.querySelector('.duration');
    this.fullscreenBtn = this.container.querySelector('.fullscreen-btn');
    this.videoControls = this.container.querySelector('.video-controls');
    this.videoLoading = this.container.querySelector('.video-loading');
  }
  
  bindEvents() {
    // Video events
    this.video.addEventListener('loadedmetadata', () => this.onLoadedMetadata());
    this.video.addEventListener('timeupdate', () => this.onTimeUpdate());
    this.video.addEventListener('progress', () => this.onProgress());
    this.video.addEventListener('ended', () => this.onEnded());
    this.video.addEventListener('play', () => this.onPlay());
    this.video.addEventListener('pause', () => this.onPause());
    this.video.addEventListener('waiting', () => this.onWaiting());
    this.video.addEventListener('canplay', () => this.onCanPlay());
    this.video.addEventListener('loadstart', () => this.onLoadStart());
    
    // Control events
    this.playPauseBtn.addEventListener('click', () => this.togglePlay());
    this.playOverlay.addEventListener('click', () => this.togglePlay());
    this.video.addEventListener('click', () => this.togglePlay());
    
    // Progress bar events
    this.progressContainer.addEventListener('click', (e) => this.onProgressClick(e));
    this.progressContainer.addEventListener('mousedown', (e) => this.onProgressMouseDown(e));
    
    // Volume events
    this.volumeBtn.addEventListener('click', () => this.toggleMute());
    this.volumeSlider.addEventListener('click', (e) => this.onVolumeClick(e));
    
    // Fullscreen events
    this.fullscreenBtn.addEventListener('click', () => this.toggleFullscreen());
    document.addEventListener('fullscreenchange', () => this.onFullscreenChange());
    document.addEventListener('webkitfullscreenchange', () => this.onFullscreenChange());
    document.addEventListener('mozfullscreenchange', () => this.onFullscreenChange());
    
    // Keyboard events
    this.container.addEventListener('keydown', (e) => this.onKeyDown(e));
    this.container.setAttribute('tabindex', '0');
    
    // Mouse events for dragging
    document.addEventListener('mousemove', (e) => this.onMouseMove(e));
    document.addEventListener('mouseup', () => this.onMouseUp());
    
    // Touch events for mobile
    this.progressContainer.addEventListener('touchstart', (e) => this.onTouchStart(e));
    this.progressContainer.addEventListener('touchmove', (e) => this.onTouchMove(e));
    this.progressContainer.addEventListener('touchend', () => this.onTouchEnd());
    
    // Show/hide controls on hover
    this.container.addEventListener('mouseenter', () => this.showControls());
    this.container.addEventListener('mouseleave', () => this.hideControls());
    this.container.addEventListener('mousemove', () => this.showControls());
    
    // Auto-hide controls timer
    this.controlsTimer = null;
  }
  
  showControls() {
    this.videoControls.classList.add('visible');
    clearTimeout(this.controlsTimer);
    
    if (this.isPlaying) {
      this.controlsTimer = setTimeout(() => {
        this.hideControls();
      }, 3000);
    }
  }
  
  hideControls() {
    if (!this.container.matches(':hover') && this.isPlaying) {
      this.videoControls.classList.remove('visible');
    }
  }
  
  onLoadStart() {
    this.container.classList.add('loading');
  }
  
  onWaiting() {
    this.container.classList.add('loading');
  }
  
  onCanPlay() {
    this.container.classList.remove('loading');
  }
  
  onLoadedMetadata() {
    this.duration = this.video.duration;
    this.updateTimeDisplay();
    this.updateVolumeDisplay();
    this.container.classList.remove('loading');
  }
  
  onTimeUpdate() {
    if (!this.isDragging) {
      this.currentTime = this.video.currentTime;
      this.updateProgressBar();
      this.updateTimeDisplay();
    }
  }
  
  onProgress() {
    if (this.video.buffered.length > 0) {
      const bufferedEnd = this.video.buffered.end(this.video.buffered.length - 1);
      const bufferedPercent = (bufferedEnd / this.duration) * 100;
      this.bufferProgress.style.width = `${bufferedPercent}%`;
    }
  }
  
  onPlay() {
    this.isPlaying = true;
    this.container.classList.add('playing');
    this.container.classList.remove('paused');
    this.playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
    this.playPauseBtn.title = 'Pause';
    this.showControls();
  }
  
  onPause() {
    this.isPlaying = false;
    this.container.classList.add('paused');
    this.container.classList.remove('playing');
    this.playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
    this.playPauseBtn.title = 'Play';
    this.showControls();
    clearTimeout(this.controlsTimer);
  }
  
  onEnded() {
    this.isPlaying = false;
    this.container.classList.add('paused');
    this.container.classList.remove('playing');
    this.playPauseBtn.innerHTML = '<i class="fas fa-replay"></i>';
    this.playPauseBtn.title = 'Replay';
    this.showControls();
    clearTimeout(this.controlsTimer);
  }
  
  togglePlay() {
    if (this.video.paused || this.video.ended) {
      this.video.play().catch(e => console.log('Play failed:', e));
    } else {
      this.video.pause();
    }
  }
  
  onProgressClick(e) {
    const rect = this.progressContainer.getBoundingClientRect();
    const percent = (e.clientX - rect.left) / rect.width;
    this.seekTo(percent);
  }
  
  onProgressMouseDown(e) {
    this.isDragging = true;
    this.onProgressClick(e);
    e.preventDefault();
  }
  
  onMouseMove(e) {
    if (this.isDragging) {
      const rect = this.progressContainer.getBoundingClientRect();
      const percent = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
      this.seekTo(percent);
    }
  }
  
  onMouseUp() {
    this.isDragging = false;
  }
  
  onTouchStart(e) {
    this.isDragging = true;
    const touch = e.touches[0];
    const rect = this.progressContainer.getBoundingClientRect();
    const percent = (touch.clientX - rect.left) / rect.width;
    this.seekTo(percent);
    e.preventDefault();
  }
  
  onTouchMove(e) {
    if (this.isDragging) {
      e.preventDefault();
      const touch = e.touches[0];
      const rect = this.progressContainer.getBoundingClientRect();
      const percent = Math.max(0, Math.min(1, (touch.clientX - rect.left) / rect.width));
      this.seekTo(percent);
    }
  }
  
  onTouchEnd() {
    this.isDragging = false;
  }
  
  seekTo(percent) {
    const time = percent * this.duration;
    this.video.currentTime = time;
    this.currentTime = time;
    this.updateProgressBar();
    this.updateTimeDisplay();
  }
  
  updateProgressBar() {
    const percent = (this.currentTime / this.duration) * 100;
    this.progressBar.style.width = `${percent}%`;
  }
  
  updateTimeDisplay() {
    this.currentTimeEl.textContent = this.formatTime(this.currentTime);
    this.durationEl.textContent = this.formatTime(this.duration);
  }
  
  formatTime(seconds) {
    if (isNaN(seconds)) return '0:00';
    
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
  }
  
  toggleMute() {
    if (this.video.muted) {
      this.video.muted = false;
      this.isMuted = false;
    } else {
      this.video.muted = true;
      this.isMuted = true;
    }
    this.updateVolumeDisplay();
  }
  
  onVolumeClick(e) {
    const rect = this.volumeSlider.getBoundingClientRect();
    const percent = (e.clientX - rect.left) / rect.width;
    this.video.volume = Math.max(0, Math.min(1, percent));
    this.volume = this.video.volume;
    this.video.muted = false;
    this.isMuted = false;
    this.updateVolumeDisplay();
  }
  
  updateVolumeDisplay() {
    const volume = this.video.muted ? 0 : this.video.volume;
    this.volumeProgress.style.width = `${volume * 100}%`;
    
    if (volume === 0 || this.video.muted) {
      this.volumeBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
      this.volumeBtn.title = 'Unmute';
    } else if (volume < 0.5) {
      this.volumeBtn.innerHTML = '<i class="fas fa-volume-down"></i>';
      this.volumeBtn.title = 'Mute';
    } else {
      this.volumeBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
      this.volumeBtn.title = 'Mute';
    }
  }
  
  toggleFullscreen() {
    if (!this.isFullscreen) {
      this.enterFullscreen();
    } else {
      this.exitFullscreen();
    }
  }
  
  enterFullscreen() {
    const element = this.container;
    
    if (element.requestFullscreen) {
      element.requestFullscreen();
    } else if (element.webkitRequestFullscreen) {
      element.webkitRequestFullscreen();
    } else if (element.mozRequestFullScreen) {
      element.mozRequestFullScreen();
    } else if (element.msRequestFullscreen) {
      element.msRequestFullscreen();
    }
  }
  
  exitFullscreen() {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    }
  }
  
  onFullscreenChange() {
    this.isFullscreen = !!(document.fullscreenElement || 
                          document.webkitFullscreenElement || 
                          document.mozFullScreenElement || 
                          document.msFullscreenElement);
    
    if (this.isFullscreen) {
      this.fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
      this.fullscreenBtn.title = 'Exit Fullscreen';
      this.container.classList.add('fullscreen');
    } else {
      this.fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
      this.fullscreenBtn.title = 'Fullscreen';
      this.container.classList.remove('fullscreen');
    }
  }
  
  onKeyDown(e) {
    switch (e.code) {
      case 'Space':
        e.preventDefault();
        this.togglePlay();
        break;
      case 'ArrowLeft':
        e.preventDefault();
        this.video.currentTime = Math.max(0, this.video.currentTime - 10);
        break;
      case 'ArrowRight':
        e.preventDefault();
        this.video.currentTime = Math.min(this.duration, this.video.currentTime + 10);
        break;
      case 'ArrowUp':
        e.preventDefault();
        this.video.volume = Math.min(1, this.video.volume + 0.1);
        this.updateVolumeDisplay();
        break;
      case 'ArrowDown':
        e.preventDefault();
        this.video.volume = Math.max(0, this.video.volume - 0.1);
        this.updateVolumeDisplay();
        break;
      case 'KeyM':
        e.preventDefault();
        this.toggleMute();
        break;
      case 'KeyF':
        e.preventDefault();
        this.toggleFullscreen();
        break;
    }
  }
  
  destroy() {
    clearTimeout(this.controlsTimer);
    // Remove event listeners and clean up
    this.video.controls = true;
    const controls = this.container.querySelector('.video-controls');
    const overlay = this.container.querySelector('.play-overlay');
    const loading = this.container.querySelector('.video-loading');
    
    if (controls) controls.remove();
    if (overlay) overlay.remove();
    if (loading) loading.remove();
  }
}

// Initialize video players when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeVideoPlayers();
});

// Function to initialize all video players
function initializeVideoPlayers() {
  const videos = document.querySelectorAll('video:not([data-player-initialized])');
  videos.forEach(video => {
    // Skip if already initialized
    if (video.dataset.playerInitialized) return;
    
    // Mark as initialized
    video.dataset.playerInitialized = 'true';
    
    // Create player instance
    new ModernVideoPlayer(video);
  });
}

// Function to initialize new videos (for dynamically loaded content)
function initializeNewVideos() {
  initializeVideoPlayers();
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { ModernVideoPlayer, initializeVideoPlayers, initializeNewVideos };
}

// Make functions globally available
window.ModernVideoPlayer = ModernVideoPlayer;
window.initializeVideoPlayers = initializeVideoPlayers;
window.initializeNewVideos = initializeNewVideos;