/**
 * Anna Baylis — Page Transition System
 *
 * Modelled directly on the working demo-transition approach:
 *   - Web Animations API for the block animation (no GSAP dependency)
 *   - GSAP used only for theme scroll/reveal reinit after swap
 *   - async/await flat flow — no floating promises
 *   - pointerEvents to block interaction (simpler than overflow:hidden)
 *   - data-link NOT required — intercepts all qualifying <a> clicks
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  /* ─── Config ──────────────────────────────────────────────────────────── */
  var OVERLAY_ID      = 'page-transition';
  var CONTENT_ID      = 'main-content';
  var BLOCK_COLOR     = '#007063';
  var BLOCK_COUNT     = 8;
  var ENTER_DURATION  = 650;   // ms per block (Web Animations API)
  var EXIT_DURATION   = 650;
  var STAGGER_DELAY   = 40;    // ms between each block — tight enough to avoid visible gaps
  var EASE            = 'cubic-bezier(.77,0,.18,1)';
  var DEBUG           = false;

  /* ─── Helpers ─────────────────────────────────────────────────────────── */
  function log() {
    if (DEBUG) console.log.apply(console, ['[Transition]'].concat([].slice.call(arguments)));
  }

  function isInternal(anchor) {
    if (!anchor || anchor.tagName !== 'A') return false;
    var href   = anchor.getAttribute('href') || '';
    var target = anchor.getAttribute('target') || '';

    if (!href || href === '#' || href.charAt(0) === '#') return false;
    if (target === '_blank')     return false;
    if (anchor.hasAttribute('download')) return false;
    if (anchor.hasAttribute('data-no-transition')) return false;
    if (/^(mailto|tel|javascript):/i.test(href)) return false;
    if (/\/(wp-admin|wp-login)/i.test(href))     return false;
    if (/\.(pdf|zip|docx?|xlsx?|pptx?|rar|gz|mp3|mp4|mov|avi)$/i.test(href)) return false;

    // Must be same origin
    try {
      var a = new URL(anchor.href, window.location.href);
      if (a.origin !== window.location.origin) return false;
      // Skip if same path+query (only a hash difference)
      if (a.pathname === window.location.pathname && a.search === window.location.search) return false;
    } catch (e) { return false; }

    return true;
  }

  /* ─── Overlay + blocks setup ──────────────────────────────────────────── */
  var overlay = document.getElementById(OVERLAY_ID);
  var blocks  = [];

  function setupOverlay() {
    if (!overlay) {
      log('Overlay not found');
      return false;
    }
    // Use existing columns already in the HTML
    blocks = [].slice.call(overlay.querySelectorAll('.transition-column'));
    if (!blocks.length) {
      // Fallback: create them dynamically
      for (var i = 0; i < BLOCK_COUNT; i++) {
        var d = document.createElement('div');
        d.className = 'transition-column';
        overlay.appendChild(d);
        blocks.push(d);
      }
    }
    blocks.forEach(function (b) {
      b.style.backgroundColor = BLOCK_COLOR;
      b.style.transform = 'translateY(100%)';  // parked below
    });
    log('Overlay ready, blocks:', blocks.length);
    return true;
  }

  /* ─── Animation (Web Animations API — no GSAP dependency) ────────────── */
  function animateBlocks(enter) {
    // Cancel any running animations on each block before starting new ones.
    // This prevents fill:'forwards' from the enter animation conflicting with exit.
    blocks.forEach(function (b) {
      b.getAnimations().forEach(function (a) { a.cancel(); });
    });

    var from = enter ? 'translateY(100%)' : 'translateY(0%)';
    var to   = enter ? 'translateY(0%)'   : 'translateY(-100%)';

    return Promise.all(blocks.map(function (block, i) {
      // Set the start position explicitly as an inline style so the
      // cancelled prior animation doesn't leave a stale committed value.
      block.style.transform = from;

      return block.animate(
        [{ transform: from }, { transform: to }],
        {
          duration: enter ? ENTER_DURATION : EXIT_DURATION,
          delay:    i * STAGGER_DELAY,
          fill:     'forwards',
          easing:   EASE,
        }
      ).finished;
    }));
  }

  /* ─── Re-park blocks after exit ──────────────────────────────────────── */
  function resetBlocks() {
    blocks.forEach(function (b) {
      // Cancel committed fill so inline style takes over cleanly
      b.getAnimations().forEach(function (a) { a.cancel(); });
      b.style.transform = 'translateY(100%)';
    });
  }


  /* ─── Prefetch cache ──────────────────────────────────────────────────── */
  var prefetchCache = {};

  function prefetch(url) {
    if (prefetchCache[url]) return;
    prefetchCache[url] = fetch(url).then(function (r) { return r.text(); }).catch(function () { return null; });
  }

  function getHTML(url) {
    if (prefetchCache[url]) {
      log('Using prefetch for:', url);
      return prefetchCache[url];
    }
    return fetch(url).then(function (r) {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.text();
    });
  }

  /* ─── CSS injection ──────────────────────────────────────────────────── */
  // Collect hrefs of all stylesheets already in the document so we never
  // load the same file twice across navigations.
  var loadedStyles = {};
  (function () {
    document.querySelectorAll('link[rel="stylesheet"]').forEach(function (el) {
      if (el.href) loadedStyles[el.href] = true;
    });
  })();

  /**
   * Find <link rel="stylesheet"> tags in the fetched document that aren't
   * already in the current page's <head> and inject them, waiting for each
   * to load before resolving so the swap is never unstyled.
   */
  function injectNewStyles(fetchedDoc) {
    var newLinks = [].slice.call(fetchedDoc.querySelectorAll('link[rel="stylesheet"]'));
    var pending  = [];

    newLinks.forEach(function (link) {
      var href = link.href;
      if (!href || loadedStyles[href]) return;  // already loaded

      loadedStyles[href] = true;
      log('Injecting stylesheet:', href);

      var el    = document.createElement('link');
      el.rel    = 'stylesheet';
      el.href   = href;
      if (link.id)    el.id    = link.id;
      if (link.media) el.media = link.media;

      pending.push(new Promise(function (resolve) {
        el.addEventListener('load',  resolve, { once: true });
        el.addEventListener('error', resolve, { once: true }); // don't block on 404
      }));

      document.head.appendChild(el);
    });

    return pending.length ? Promise.all(pending) : Promise.resolve();
  }

  /* ─── Core navigation ─────────────────────────────────────────────────── */
  var busy = false;

  async function navigate(url, push) {
    if (busy) return;
    busy = true;
    document.body.style.pointerEvents = 'none';

    log('Navigate →', url);

    // Kick off fetch immediately (runs in parallel with enter animation)
    var htmlPromise = getHTML(url);

    // Animate blocks in
    await animateBlocks(true);

    // Wait for the page HTML
    var html;
    try {
      html = await htmlPromise;
    } catch (err) {
      log('Fetch failed, hard nav:', err);
      window.location.href = url;
      return;
    }

    if (!html) {
      window.location.href = url;
      return;
    }

    // Parse and extract content
    var doc     = new DOMParser().parseFromString(html, 'text/html');
    var newMain = doc.getElementById(CONTENT_ID);

    if (!newMain) {
      log('Could not find #' + CONTENT_ID + ' — hard nav');
      window.location.href = url;
      return;
    }

    // Inject any page-specific stylesheets from the new page that
    // aren't already loaded. WordPress enqueues per-page CSS conditionally
    // so each destination may have different <link> tags in its <head>.
    await injectNewStyles(doc);

    // Swap content
    var currentMain = document.getElementById(CONTENT_ID);
    if (currentMain) {
      currentMain.replaceWith(newMain);
    }

    // Update metadata
    document.title = doc.title || '';
    if (doc.body && doc.body.className) {
      var keep = ['is-transitioning'];
      document.body.className = doc.body.className;
      keep.forEach(function (c) { document.body.classList.add(c); });
    }

    // Push history
    if (push !== false) {
      history.pushState({ url: url }, document.title, url);
    }

    // Scroll to top
    window.scrollTo(0, 0);

    // Wait for images in the new content to decode
    var imgs = [].slice.call(document.querySelectorAll('#' + CONTENT_ID + ' img'));
    await Promise.all(imgs.map(function (img) {
      if (img.complete) return Promise.resolve();
      if (typeof img.decode === 'function') return img.decode().catch(function () {});
      return new Promise(function (resolve) {
        img.addEventListener('load',  resolve, { once: true });
        img.addEventListener('error', resolve, { once: true });
      });
    }));

    // Reinitialize theme modules
    reinitTheme();

    // Animate blocks out
    await animateBlocks(false);

    // Reset blocks back below viewport — defer one frame so the last
    // block's exit paint completes before we cancel its fill.
    requestAnimationFrame(function () { resetBlocks(); });

    document.body.style.pointerEvents = '';
    document.body.classList.remove('is-transitioning');
    busy = false;
    log('Navigation complete');
  }


  /* ─── Theme reinitialization after swap ───────────────────────────────── */
  function reinitTheme() {
    // GSAP: kill old ScrollTriggers, rerun all animation modules
    if (typeof gsap !== 'undefined') {
      if (typeof ScrollTrigger !== 'undefined') {
        ScrollTrigger.getAll().forEach(function (st) { st.kill(); });
        if (typeof ScrollTrigger.clearScrollMemory === 'function') ScrollTrigger.clearScrollMemory();
      }
      if (typeof window.annaHeroInit === 'function')          window.annaHeroInit();
      if (typeof window.annaScrollTriggersInit === 'function') window.annaScrollTriggersInit();
      if (typeof window.annaParallaxInit === 'function')       window.annaParallaxInit();
    }

    // Scroll reveal (IntersectionObserver)
    reinitScrollReveal();

    // Header: close open dropdowns, update active state
    document.querySelectorAll('.anna-nav__item--has-children.is-open').forEach(function (item) {
      item.classList.remove('is-open');
      var link = item.querySelector('.anna-nav__link--parent');
      if (link) link.setAttribute('aria-expanded', 'false');
    });
    var currentPath = window.location.pathname;
    document.querySelectorAll('.anna-nav__link, .anna-mobile-nav__link').forEach(function (link) {
      try {
        var lp = new URL(link.href, window.location.href).pathname;
        var active = lp === currentPath || (lp !== '/' && currentPath.indexOf(lp) === 0);
        link.classList.toggle('is-active', active);
        var li = link.closest('li');
        if (li) li.classList.toggle('current-menu-item', active);
      } catch (e) {}
    });

    // Discovery-call links → new tab
    var durl = window.annaTheme && window.annaTheme.discoveryCallUrl
      ? window.annaTheme.discoveryCallUrl.replace(/\/$/, '') : null;
    if (durl) {
      document.querySelectorAll('a[href]').forEach(function (a) {
        if (a.getAttribute('href').replace(/\/$/, '') === durl) {
          a.target = '_blank'; a.rel = 'noopener noreferrer';
        }
      });
    }

    // Dispatch event for any other modules
    document.dispatchEvent(new CustomEvent('pageLoaded', {
      bubbles: true, detail: { url: window.location.href }
    }));

    log('Theme reinit done, pageLoaded dispatched');
  }

  function reinitScrollReveal() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    document.querySelectorAll('.anna-main section').forEach(function (el) {
      if (!/\banna-reveal/.test(el.className)) el.classList.add('anna-reveal');
    });
    var els = document.querySelectorAll('.anna-reveal, .anna-reveal--left, .anna-reveal--right, .anna-reveal--scale, .anna-stagger');
    if (!els.length) return;
    if (!('IntersectionObserver' in window)) {
      els.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { entry.target.classList.add('is-visible'); obs.unobserve(entry.target); }
      });
    }, { rootMargin: '0px 0px -20px 0px', threshold: 0 });
    els.forEach(function (el) { el.classList.remove('is-visible'); obs.observe(el); });
  }


  /* ─── Click interception ──────────────────────────────────────────────── */
  function bindClicks() {
    document.addEventListener('click', function (e) {
      var anchor = e.target.closest('a');
      if (!anchor || !isInternal(anchor)) return;
      e.preventDefault();
      navigate(anchor.href, true);
    });
  }

  /* ─── Prefetch on hover/focus/touch ──────────────────────────────────── */
  function bindPrefetch() {
    document.addEventListener('mouseover', function (e) {
      var a = e.target.closest('a');
      if (a && isInternal(a)) prefetch(a.href);
    }, { passive: true });
    document.addEventListener('touchstart', function (e) {
      var a = e.target.closest('a');
      if (a && isInternal(a)) prefetch(a.href);
    }, { passive: true });
    document.addEventListener('focusin', function (e) {
      var a = e.target.closest('a');
      if (a && isInternal(a)) prefetch(a.href);
    }, { passive: true });
  }

  /* ─── History (back/forward) ──────────────────────────────────────────── */
  function bindHistory() {
    history.replaceState({ url: window.location.href }, document.title, window.location.href);
    window.addEventListener('popstate', function (e) {
      var url = (e.state && e.state.url) ? e.state.url : window.location.href;
      navigate(url, false);
    });
  }

  /* ─── Screen-reader announcer ─────────────────────────────────────────── */
  function setupAnnouncer() {
    var el = document.createElement('div');
    el.setAttribute('aria-live', 'polite');
    el.setAttribute('aria-atomic', 'true');
    el.setAttribute('role', 'status');
    el.style.cssText = 'position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;border:0';
    document.body.appendChild(el);
    document.addEventListener('pageLoaded', function () {
      el.textContent = '';
      requestAnimationFrame(function () { el.textContent = 'Navigated to ' + document.title; });
    });
  }

  /* ─── Bootstrap ───────────────────────────────────────────────────────── */
  function init() {
    if (!setupOverlay()) return;   // no overlay = disabled
    bindClicks();
    bindPrefetch();
    bindHistory();
    setupAnnouncer();
    log('Page transition system ready');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Public API for manual config/debug
  window.AnnaTransition = {
    navigate: navigate,
    setColor: function (c) { BLOCK_COLOR = c; blocks.forEach(function (b) { b.style.backgroundColor = c; }); },
    setDebug: function (v) { DEBUG = v; },
  };

})();
