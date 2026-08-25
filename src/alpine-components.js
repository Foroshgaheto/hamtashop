/**
 * Alpine.js: product card (wishlist, color swatches, sale timer).
 */

export function registerAlpineComponents() {
  document.addEventListener('alpine:init', () => {
    window.Alpine.data('hamtaProductCard', (config = {}) => ({
      productId: config.productId || 0,
      image: config.image || '',
      permalink: config.permalink || '',
      colors: Array.isArray(config.colors) ? config.colors : [],
      selectedColor: '',
      wishlisted: false,
      timer: {
        visible: false,
        label: '00:00:00:00',
      },
      _timerId: null,

      init() {
        this.loadWishlist();
        if (config.timerEnd && config.timerEnd > Math.floor(Date.now() / 1000)) {
          this.startTimer(config.timerEnd);
        }
      },

      destroy() {
        if (this._timerId) {
          clearInterval(this._timerId);
        }
      },

      loadWishlist() {
        try {
          const list = JSON.parse(localStorage.getItem('hamta_wishlist') || '[]');
          this.wishlisted = list.map(String).includes(String(this.productId));
        } catch (e) {
          this.wishlisted = false;
        }
      },

      toggleWishlist() {
        let list = [];
        try {
          list = JSON.parse(localStorage.getItem('hamta_wishlist') || '[]');
        } catch (e) {
          list = [];
        }
        const id = String(this.productId);
        if (list.map(String).includes(id)) {
          list = list.filter((x) => String(x) !== id);
          this.wishlisted = false;
        } else {
          list.push(this.productId);
          this.wishlisted = true;
        }
        localStorage.setItem('hamta_wishlist', JSON.stringify(list));
      },

      selectColor(color) {
        this.selectedColor = color.slug;
        if (color.image) {
          this.image = color.image;
        }
        if (color.permalink) {
          this.permalink = color.permalink;
        }
      },

      startTimer(endTs) {
        const tick = () => {
          const now = Math.floor(Date.now() / 1000);
          let left = endTs - now;
          if (left <= 0) {
            this.timer.visible = false;
            this.timer.label = '00:00:00:00';
            if (this._timerId) clearInterval(this._timerId);
            return;
          }
          this.timer.visible = true;
          const days = Math.floor(left / 86400);
          left %= 86400;
          const hours = Math.floor(left / 3600);
          left %= 3600;
          const mins = Math.floor(left / 60);
          const secs = left % 60;
          const pad = (n) => String(n).padStart(2, '0');
          this.timer.label = `${pad(days)}:${pad(hours)}:${pad(mins)}:${pad(secs)}`;
        };
        tick();
        this._timerId = setInterval(tick, 1000);
      },
    }));
  });
}
