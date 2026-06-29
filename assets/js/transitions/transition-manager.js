/**
 * Anna Baylis — Page Transition System
 *
 * Premium staggered-column overlay transitions with AJAX navigation,
 * prefetching, history support, and full theme reinitialization.
 *
 * Architecture:
 *   Config              — central settings
 *   AnimationManager    — GSAP entrance/exit timelines
 *   PageLoader          — fetch + parse + inject new content
 *   AssetLoader         — wait for images + fonts
 *   PrefetchManager     — eager prefetch on hover/focus/touchstart
 *   HistoryManager      — pushState + popstate
 *   ScrollManager       — scroll-to-top / scroll restoration
 *   ThemeInitializer    — reinit all theme JS modules after swap
 *   NavigationManager   — intercept clicks, coordinate the flow
 *   TransitionManager   — public facade, bootstrap
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  /* ══════════════════════════════════════════════════════════════════════════
     CONFIG
  ══════════════════════════════════════════════════════════════════════════ */
  var Config = {
    columns:         8,
    enterDuration:   0.7,
    exitDuration:    0.7,
    staggerAmount:   0.12,
    ease:            'power4.inOut',
    overlayColor:    '#007063',
    direction:       'up',       // 'up' only — columns rise then fall
    prefetchEnabled: true,
    debug:           false,

    // Selectors
    overlayId:       'page-transition',
    contentId:       'main-content',
    siteWrapperId:   'page',

    // Links matching these patterns are left to the browser
    ignorePatterns: [
      /^#/,
      /^mailto:/i,
      /^tel:/i,
      /^javascript:/i,
      /\/wp-admin/i,
      /\/wp-login/i,
      /\.(pdf|zip|docx?|xlsx?|pptx?|rar|gz|mp3|mp4|mov|avi)$/i,
    ],
  };

  /* ══════════════════════════════════════════════════════════════════════════
     UTILITIES
  ══════════════════════════════════════════════════════════════════════════ */
  function log() {
    if (Config.debug) {
      console.log.apply(console, ['[Transition]'].concat(Array.prototype.slice.call(arguments)));
    }
  }

  function isSameOrigin(url) {
    try {
      var a = new URL(url, window.location.href);
      return a.origin === window.location.origin;
    } catch (e) {
      return false;
    }
  }

  function isIgnoredLink(anchor) {
    if (!anchor || anchor.tagName !== 'A') return true;
    var href = anchor.getAttribute('href') || '';
    var target = anchor.getAttribute('target') || '';

    if (target === '_blank') return true;
    if (!isSameOrigin(href)) return true;
    if (anchor.hasAttribute('data-no-transition')) return true;
    if (anchor.hasAttribute('download')) return true;

    for (var i = 0; i < Config.ignorePatterns.length; i++) {
      if (Config.ignorePatterns[i].test(href)) return true;
    }

    // Same page (ignoring hash portion)
    try {
      var dest = new URL(href, window.location.href);
      if (dest.pathname === window.location.pathname && dest.search === window.location.search) return true;
    } catch (e) { /* ignore */ }

    return false;
  }


  /* ══════════════════════════════════════════════════════════════════════════
     ANIMATION MANAGER
  ══════════════════════════════════════════════════════════════════════════ */
  var AnimationManager = {
    overlay:  null,
    columns:  [],

    init: function () {
      this.overlay = document.getElementById(Config.overlayId);
      if (!this.overlay) return;
      this.columns = Array.prototype.slice.call(
        this.overlay.querySelectorAll('.transition-column')
      );
      // Apply configured color
      this.columns.forEach(function (col) {
        col.style.backgroundColor = Config.overlayColor;
      });
      log('AnimationManager ready, columns:', this.columns.length);
    },

    /**
     * Animate columns from below viewport to cover the screen.
     * Returns a Promise that resolves when the screen is fully covered.
     */
    enter: function () {
      var self = this;
      if (!this.overlay || !this.columns.length) return Promise.resolve();

      return new Promise(function (resolve) {
        // Reduced-motion: skip animation, just show overlay
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
          self.overlay.classList.add('is-active');
          gsap.set(self.columns, { yPercent: 0 });
          resolve();
          return;
        }

        self.overlay.classList.add('is-active');

        gsap.set(self.columns, { yPercent: 100 });

        gsap.to(self.columns, {
          yPercent:  0,
          duration:  Config.enterDuration,
          ease:      Config.ease,
          stagger: {
            amount: Config.staggerAmount,
            from:   'start',
          },
          onComplete: resolve,
        });
      });
    },

    /**
     * Animate columns from full-cover upward off-screen.
     * Returns a Promise that resolves when the overlay is gone.
     */
    exit: function () {
      var self = this;
      if (!this.overlay || !this.columns.length) return Promise.resolve();

      return new Promise(function (resolve) {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
          gsap.set(self.columns, { yPercent: -100 });
          self.overlay.classList.remove('is-active', 'is-loading');
          resolve();
          return;
        }

        self.overlay.classList.remove('is-loading');

        gsap.to(self.columns, {
          yPercent:  -100,
          duration:  Config.exitDuration,
          ease:      Config.ease,
          stagger: {
            amount: Config.staggerAmount,
            from:   'end',
          },
          onComplete: function () {
            gsap.set(self.columns, { yPercent: 100 }); // re-park for next use
            self.overlay.classList.remove('is-active');
            resolve();
          },
        });
      });
    },

    setLoading: function () {
      if (this.overlay) this.overlay.classList.add('is-loading');
    },
  };


  /* ══════════════════════════════════════════════════════════════════════════
     ASSET LOADER
     Wait for images to decode and fonts to be ready.
  ══════════════════════════════════════════════════════════════════════════ */
  var AssetLoader = {

    /**
     * Decode all images inside a container.
     * Falls back gracefully when decode() is unsupported.
     */
    decodeImages: function (container) {
      var imgs = Array.prototype.slice.call(container.querySelectorAll('img'));
      if (!imgs.length) return Promise.resolve();

      var promises = imgs.map(function (img) {
        if (img.complete) return Promise.resolve();
        if (typeof img.decode === 'function') {
          return img.decode().catch(function () { /* ignore errors */ });
        }
        return new Promise(function (resolve) {
          img.addEventListener('load',  resolve, { once: true });
          img.addEventListener('error', resolve, { once: true });
        });
      });

      return Promise.all(promises);
    },

    /**
     * Wait for the document fonts to finish loading.
     */
    waitForFonts: function () {
      if (document.fonts && typeof document.fonts.ready !== 'undefined') {
        return document.fonts.ready;
      }
      return Promise.resolve();
    },

    /**
     * Wait for both images and fonts.
     */
    waitForAssets: function (container) {
      return Promise.all([
        this.decodeImages(container),
        this.waitForFonts(),
      ]);
    },
  };


  /* ══════════════════════════════════════════════════════════════════════════
     PAGE LOADER
     Fetch a URL, parse the HTML, extract content + metadata.
  ══════════════════════════════════════════════════════════════════════════ */
  var PageLoader = {

    _parser: new DOMParser(),

    /**
     * Fetch the given URL and return a parsed result object.
     * Accepts an optional AbortSignal.
     */
    load: function (url, signal) {
      log('Loading page:', url);
      return fetch(url, {
        method:      'GET',
        credentials: 'same-origin',
        signal:      signal || null,
        headers: {
          'X-Anna-Transition': '1',  // lets PHP detect AJAX vs full request
        },
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
          }
          return response.text();
        })
        .then(function (html) {
          return PageLoader._parse(html, url);
        });
    },

    /**
     * Parse raw HTML string and extract replaceable pieces.
     */
    _parse: function (html, url) {
      var doc = PageLoader._parser.parseFromString(html, 'text/html');

      var content = doc.getElementById(Config.contentId);
      var title   = doc.title || '';
      var bodyClass = (doc.body && doc.body.className) || '';
      var lang    = (doc.documentElement && doc.documentElement.getAttribute('lang')) || '';

      if (!content) {
        // Fallback: the page returned something unexpected — do a hard navigation
        log('Could not find #' + Config.contentId + ' in fetched page, falling back');
        return null;
      }

      return { content: content, title: title, bodyClass: bodyClass, lang: lang, url: url };
    },

    /**
     * Swap the current #main-content with parsed result.
     */
    swap: function (parsed) {
      if (!parsed) return false;

      var currentMain = document.getElementById(Config.contentId);
      if (!currentMain) return false;

      // Replace content using DocumentFragment for minimal reflow
      var frag = document.createDocumentFragment();
      frag.appendChild(parsed.content);

      currentMain.parentNode.replaceChild(frag.firstElementChild || frag, currentMain);

      // Update document metadata
      document.title = parsed.title;

      if (parsed.bodyClass) {
        // Preserve any transition/loading classes on body
        var keep = ['is-transitioning', 'is-loading', 'menu-open'];
        var currentKept = keep.filter(function (c) { return document.body.classList.contains(c); });
        document.body.className = parsed.bodyClass;
        currentKept.forEach(function (c) { document.body.classList.add(c); });
      }

      if (parsed.lang) {
        document.documentElement.setAttribute('lang', parsed.lang);
      }

      log('Content swapped, title:', parsed.title);
      return true;
    },
  };


  /* ══════════════════════════════════════════════════════════════════════════
     PREFETCH MANAGER
     Speculatively fetch pages on hover/focus/touchstart.
  ══════════════════════════════════════════════════════════════════════════ */
  var PrefetchManager = {
    _cache:      {},   // url → Promise<parsed|null>
    _controllers: {},  // url → AbortController

    init: function () {
      if (!Config.prefetchEnabled) return;

      // Use passive listeners — zero perf cost
      document.addEventListener('mouseover',   this._onIntent.bind(this), { passive: true });
      document.addEventListener('touchstart',  this._onIntent.bind(this), { passive: true });
      document.addEventListener('focusin',     this._onIntent.bind(this), { passive: true });

      log('PrefetchManager ready');
    },

    _onIntent: function (e) {
      var anchor = e.target.closest('a');
      if (!anchor || isIgnoredLink(anchor)) return;

      var url = anchor.href;
      if (this._cache[url]) return;  // already fetched or in-flight

      log('Prefetching:', url);
      var controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
      if (controller) this._controllers[url] = controller;

      this._cache[url] = PageLoader.load(url, controller ? controller.signal : null)
        .catch(function (err) {
          if (err && err.name === 'AbortError') return null;
          log('Prefetch failed:', url, err);
          return null;
        });
    },

    /**
     * Get cached result for a URL (or null if not cached).
     * Returns a Promise always.
     */
    get: function (url) {
      if (this._cache[url]) {
        log('Using prefetch cache for:', url);
        return this._cache[url];
      }
      return null;
    },

    /**
     * Prime the cache manually (e.g. the current page on back/forward).
     */
    prime: function (url, parsed) {
      this._cache[url] = Promise.resolve(parsed);
    },

    /**
     * Abort all in-flight prefetches that aren't for the given URL.
     */
    abortOthers: function (keepUrl) {
      var self = this;
      Object.keys(this._controllers).forEach(function (url) {
        if (url !== keepUrl && self._controllers[url]) {
          self._controllers[url].abort();
          delete self._controllers[url];
          delete self._cache[url];
        }
      });
    },
  };


  /* ══════════════════════════════════════════════════════════════════════════
     SCROLL MANAGER
  ══════════════════════════════════════════════════════════════════════════ */
  var ScrollManager = {
    _positions: {},  // url → scrollY

    /** Save scroll position for a URL before navigating away. */
    save: function (url) {
      this._positions[url] = window.scrollY;
      log('Scroll saved for', url, ':', window.scrollY);
    },

    /** Restore a previously saved scroll position, or go to top. */
    restore: function (url, instant) {
      var self = this;
      var pos  = (url && typeof this._positions[url] === 'number')
        ? this._positions[url]
        : 0;

      log('Scroll restore for', url, ':', pos);

      // Use requestAnimationFrame so the DOM paint has settled
      requestAnimationFrame(function () {
        if (instant || pos === 0) {
          window.scrollTo(0, pos);
        } else {
          window.scrollTo({ top: pos, behavior: 'smooth' });
        }
      });
    },

    /** Lock/unlock body scroll while overlay is visible. */
    lock: function () {
      document.body.style.overflow = 'hidden';
    },

    unlock: function () {
      document.body.style.overflow = '';
    },
  };

  /* ══════════════════════════════════════════════════════════════════════════
     HISTORY MANAGER
  ══════════════════════════════════════════════════════════════════════════ */
  var HistoryManager = {
    _onPop: null,

    init: function (onPopCallback) {
      this._onPop = onPopCallback;
      window.addEventListener('popstate', this._handlePop.bind(this));

      // Record state for the initial page load
      history.replaceState(
        { url: window.location.href, scrollY: window.scrollY },
        document.title,
        window.location.href
      );

      log('HistoryManager ready');
    },

    push: function (url, title) {
      history.pushState(
        { url: url, scrollY: 0 },
        title || document.title,
        url
      );
    },

    _handlePop: function (e) {
      if (!e.state || !e.state.url) return;
      log('popstate → ', e.state.url);
      var savedScroll = (e.state && typeof e.state.scrollY === 'number') ? e.state.scrollY : 0;
      if (typeof this._onPop === 'function') {
        this._onPop(e.state.url, savedScroll);
      }
    },
  };


  /* ══════════════════════════════════════════════════════════════════════════
     THEME INITIALIZER
     Reinitialize every JS module after content swap.
  ══════════════════════════════════════════════════════════════════════════ */
  var ThemeInitializer = {

    run: function () {
      log('ThemeInitializer: reinitializing modules');

      // ── Scroll reveal (IntersectionObserver) ────────────────────────────
      this._reinitScrollReveal();

      // ── GSAP modules — only if animations are enabled ───────────────────
      var animEnabled = window.annaTheme && window.annaTheme.animationsEnabled !== false;

      if (animEnabled && typeof gsap !== 'undefined') {
        // Kill all existing ScrollTriggers to avoid memory leaks
        if (typeof ScrollTrigger !== 'undefined') {
          ScrollTrigger.getAll().forEach(function (st) { st.kill(); });
          ScrollTrigger.clearScrollMemory();
          ScrollTrigger.refresh();
        }

        if (typeof window.annaHeroInit === 'function') {
          window.annaHeroInit();
        }
        if (typeof window.annaScrollTriggersInit === 'function') {
          window.annaScrollTriggersInit();
        }
        if (typeof window.annaParallaxInit === 'function') {
          window.annaParallaxInit();
        }
      }

      // ── Header / mobile menu ─────────────────────────────────────────────
      this._reinitHeader();

      // ── Stats counter ────────────────────────────────────────────────────
      if (typeof window.annaStatsCounterInit === 'function') {
        window.annaStatsCounterInit();
      }

      // ── Coaching / oasis FAQ ─────────────────────────────────────────────
      if (typeof window.annaCoachingFaqInit === 'function') {
        window.annaCoachingFaqInit();
      }

      // ── Single post sticky nav ───────────────────────────────────────────
      if (typeof window.annaSinglePostInit === 'function') {
        window.annaSinglePostInit();
      }

      // ── Discovery-call links → new tab ───────────────────────────────────
      this._reinitDiscoveryLinks();

      // ── Mailerlite embedded forms ─────────────────────────────────────────
      this._reinitMailerlite();

      // ── Dispatch the global event so any other modules can hook in ───────
      document.dispatchEvent(new CustomEvent('pageLoaded', {
        bubbles:    true,
        cancelable: false,
        detail:     { url: window.location.href },
      }));

      log('ThemeInitializer: done, pageLoaded event dispatched');
    },

    _reinitScrollReveal: function () {
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

      // Auto-add .anna-reveal to sections that don't have it
      document.querySelectorAll('.anna-main section').forEach(function (el) {
        if (!/\banna-reveal/.test(el.className)) {
          el.classList.add('anna-reveal');
        }
      });

      var reveals = document.querySelectorAll(
        '.anna-reveal, .anna-reveal--left, .anna-reveal--right, .anna-reveal--scale, .anna-stagger'
      );
      if (!reveals.length) return;

      if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-visible');
              observer.unobserve(entry.target);
            }
          });
        }, { rootMargin: '0px 0px -20px 0px', threshold: 0 });

        reveals.forEach(function (el) {
          el.classList.remove('is-visible');  // reset for fresh reveal
          observer.observe(el);
        });
      } else {
        reveals.forEach(function (el) { el.classList.add('is-visible'); });
      }
    },

    _reinitHeader: function () {
      // Close any open dropdowns
      document.querySelectorAll('.anna-nav__item--has-children.is-open').forEach(function (item) {
        item.classList.remove('is-open');
        var link = item.querySelector('.anna-nav__link--parent');
        if (link) link.setAttribute('aria-expanded', 'false');
      });

      // Update active nav item based on current URL
      var currentPath = window.location.pathname;
      document.querySelectorAll('.anna-nav__link, .anna-mobile-nav__link').forEach(function (link) {
        try {
          var linkPath = new URL(link.href, window.location.href).pathname;
          var isActive = linkPath === currentPath || (linkPath !== '/' && currentPath.indexOf(linkPath) === 0);
          link.classList.toggle('is-active', isActive);
          var parent = link.closest('li');
          if (parent) parent.classList.toggle('current-menu-item', isActive);
        } catch (e) { /* ignore */ }
      });
    },

    _reinitDiscoveryLinks: function () {
      var discoveryUrl = (window.annaTheme && window.annaTheme.discoveryCallUrl)
        ? window.annaTheme.discoveryCallUrl.replace(/\/$/, '')
        : null;
      if (!discoveryUrl) return;

      document.querySelectorAll('a[href]').forEach(function (link) {
        var href = link.getAttribute('href').replace(/\/$/, '');
        if (href === discoveryUrl) {
          link.setAttribute('target', '_blank');
          link.setAttribute('rel', 'noopener noreferrer');
        }
      });
    },

    _reinitMailerlite: function () {
      // Re-trigger MailerLite embedded form init if available
      if (window.ml && typeof window.ml.init === 'function') {
        try { window.ml.init(); } catch (e) { /* ignore */ }
      }
    },
  };


  /* ══════════════════════════════════════════════════════════════════════════
     NAVIGATION MANAGER
     Intercepts all internal link clicks and coordinates the full flow.
  ══════════════════════════════════════════════════════════════════════════ */
  var NavigationManager = {
    _busy:       false,
    _controller: null,

    init: function () {
      document.addEventListener('click', this._onClick.bind(this), false);
      log('NavigationManager ready');
    },

    _onClick: function (e) {
      // Walk up from event target to find the <a>
      var anchor = e.target.closest('a');
      if (!anchor || isIgnoredLink(anchor)) return;

      e.preventDefault();
      this.navigate(anchor.href, false);
    },

    /**
     * Full transition flow.
     * @param {string}  url       Destination URL
     * @param {boolean} isPopState  True when triggered by back/forward
     * @param {number}  [restoreY]  Scroll position to restore (pop only)
     */
    navigate: function (url, isPopState, restoreY) {
      if (this._busy) {
        log('Navigate suppressed — transition already in progress');
        return;
      }

      this._busy = true;
      document.body.classList.add('is-transitioning');
      ScrollManager.lock();

      log('Navigate →', url, isPopState ? '(popstate)' : '');

      // Save current scroll position before leaving
      ScrollManager.save(window.location.href);

      // Abort any other pending prefetches
      PrefetchManager.abortOthers(url);

      // Cancel previous in-flight load if any
      if (this._controller) {
        this._controller.abort();
      }
      this._controller = typeof AbortController !== 'undefined' ? new AbortController() : null;

      var self     = this;
      var signal   = this._controller ? this._controller.signal : null;

      // Try cache first, otherwise fetch
      var loadPromise = PrefetchManager.get(url) || PageLoader.load(url, signal);

      // ── STEP 1: Start entrance animation immediately ─────────────────────
      var enterPromise = AnimationManager.enter();

      // ── STEP 2: Wait for entrance to finish, then show loading state ─────
      enterPromise.then(function () {
        AnimationManager.setLoading();
        log('Overlay fully covering viewport');

        // ── STEP 3: Wait for content + assets ────────────────────────────
        return loadPromise
          .then(function (parsed) {
            if (!parsed) {
              // Fallback: hard navigation
              log('Parse failed — hard navigating to:', url);
              window.location.href = url;
              return;
            }

            // Swap content while hidden behind overlay
            var swapped = PageLoader.swap(parsed);
            if (!swapped) {
              log('Swap failed — hard navigating');
              window.location.href = url;
              return;
            }

            // Get the new main element for asset waiting
            var newMain = document.getElementById(Config.contentId);

            // Wait for images + fonts in new content
            return AssetLoader.waitForAssets(newMain || document.body)
              .then(function () {
                log('Assets ready — preparing to reveal');

                // ── STEP 4: Update history (not on popstate) ─────────────
                if (!isPopState) {
                  HistoryManager.push(url, document.title);
                }

                // ── STEP 5: Restore scroll ────────────────────────────────
                var scrollTarget = (isPopState && typeof restoreY === 'number') ? restoreY : 0;
                ScrollManager.restore(isPopState ? url : null, true);

                // ── STEP 6: Reinitialize theme JS ─────────────────────────
                ThemeInitializer.run();

                // ── STEP 7: Unlock scroll ─────────────────────────────────
                ScrollManager.unlock();
                document.body.classList.remove('is-transitioning');

                // ── STEP 8: Exit animation ────────────────────────────────
                return AnimationManager.exit();
              });
          });
      })
        .catch(function (err) {
          if (err && err.name === 'AbortError') {
            log('Navigation aborted');
            self._cleanup();
            return;
          }
          log('Navigation error:', err);
          // Hard fallback
          window.location.href = url;
        })
        .then(function () {
          self._cleanup();
        });
    },

    _cleanup: function () {
      this._busy       = false;
      this._controller = null;
      document.body.classList.remove('is-transitioning');
      ScrollManager.unlock();
      log('Navigation cleanup done');
    },
  };


  /* ══════════════════════════════════════════════════════════════════════════
     TRANSITION MANAGER (public facade)
  ══════════════════════════════════════════════════════════════════════════ */
  var TransitionManager = {

    /**
     * Override default config before calling init().
     * E.g. TransitionManager.configure({ columns: 6, overlayColor: '#000' });
     */
    configure: function (overrides) {
      Object.keys(overrides).forEach(function (key) {
        if (Object.prototype.hasOwnProperty.call(Config, key)) {
          Config[key] = overrides[key];
        }
      });
      return this;
    },

    init: function () {
      // Guard: only run once, only on front-end
      if (this._initialized) return;
      if (!document.getElementById(Config.overlayId)) {
        log('Overlay #' + Config.overlayId + ' not found — transition system disabled');
        return;
      }
      if (typeof gsap === 'undefined') {
        log('GSAP not found — transition system disabled');
        return;
      }

      this._initialized = true;

      // Boot all sub-systems
      AnimationManager.init();
      PrefetchManager.init();

      HistoryManager.init(function (url, scrollY) {
        NavigationManager.navigate(url, true, scrollY);
      });

      NavigationManager.init();

      // ── Accessibility: live region ──────────────────────────────────────
      this._initA11y();

      // Listen for pageLoaded to update the announcer
      document.addEventListener('pageLoaded', function (e) {
        var announcer = document.getElementById('page-transition-announcer');
        if (announcer) {
          announcer.textContent = '';  // clear first to re-trigger read
          requestAnimationFrame(function () {
            announcer.textContent = 'Navigated to: ' + document.title;
          });
        }
        // Update header CTA target="_blank" if it links to discovery url
        if (window.annaTheme && window.annaTheme.discoveryCallUrl) {
          document.querySelectorAll('.anna-btn[href]').forEach(function (btn) {
            if (btn.href && btn.href.includes('calendly.com')) {
              btn.setAttribute('target', '_blank');
              btn.setAttribute('rel', 'noopener noreferrer');
            }
          });
        }
      });

      log('TransitionManager initialized');
    },

    _initA11y: function () {
      var announcer = document.getElementById('page-transition-announcer');
      if (!announcer) {
        announcer = document.createElement('div');
        announcer.id = 'page-transition-announcer';
        announcer.setAttribute('aria-live', 'polite');
        announcer.setAttribute('aria-atomic', 'true');
        announcer.setAttribute('role', 'status');
        document.body.appendChild(announcer);
      }
    },

    _initialized: false,
  };

  /* ══════════════════════════════════════════════════════════════════════════
     BOOTSTRAP
     Wait for GSAP (which is deferred) before initializing.
  ══════════════════════════════════════════════════════════════════════════ */
  function bootWhenReady() {
    // If GSAP is already available, go immediately
    if (typeof gsap !== 'undefined') {
      TransitionManager.init();
      return;
    }

    // Otherwise poll briefly — GSAP is deferred so it loads slightly after
    var attempts = 0;
    var poll = setInterval(function () {
      attempts++;
      if (typeof gsap !== 'undefined') {
        clearInterval(poll);
        TransitionManager.init();
      } else if (attempts > 50) {
        clearInterval(poll);
        log('GSAP never loaded — transitions disabled');
      }
    }, 100);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootWhenReady);
  } else {
    bootWhenReady();
  }

  // Expose publicly for manual configure/debug
  window.AnnaTransition = TransitionManager;

})();
