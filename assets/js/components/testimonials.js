/**
 * Testimonials Carousel JS
 *
 * Custom GSAP-driven carousel for testimonials.
 * Accessible: keyboard nav, pause on focus/hover, aria-live.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

(function () {
  'use strict';

  var track    = document.getElementById('testimonial-track');
  var prevBtn  = document.getElementById('testimonial-prev');
  var nextBtn  = document.getElementById('testimonial-next');
  var dotsWrap = document.getElementById('testimonial-dots');

  if (!track || !prevBtn || !nextBtn) return;

  var slides      = Array.from(track.children);
  var current     = 0;
  var autoplayId  = null;
  var autoDelay   = 5000;
  var isPaused    = false;
  var perView     = 3;
  var gap         = 0;

  function getPerView() {
    var w = window.innerWidth;
    if (w <= 768)  return 1;
    if (w <= 1024) return 2;
    return 3;
  }

  function getGap() {
    var style = getComputedStyle(track);
    return parseFloat(style.gap) || 24;
  }

  function maxIndex() {
    return Math.max(0, slides.length - perView);
  }

  function updateLayout() {
    perView = getPerView();
    gap     = getGap();

    slides.forEach(function (slide) {
      var totalGaps = (perView - 1) * gap;
      slide.style.flex = '0 0 calc((100% - ' + totalGaps + 'px) / ' + perView + ')';
    });

    if (current > maxIndex()) current = maxIndex();
    goTo(current, false);
    renderDots();
  }

  function goTo(index, animate) {
    current = Math.max(0, Math.min(index, maxIndex()));

    var slideWidth = slides[0].offsetWidth;
    var offset     = current * (slideWidth + gap);

    if (animate !== false && typeof gsap !== 'undefined') {
      gsap.to(track, {
        x:        -offset,
        duration: 0.6,
        ease:     'power2.out',
      });
    } else {
      track.style.transform = 'translateX(-' + offset + 'px)';
    }

    prevBtn.disabled = current === 0;
    nextBtn.disabled = current >= maxIndex();

    updateDots();
  }

  function next() { goTo(current + 1); }
  function prev() { goTo(current - 1); }

  // Dots
  function renderDots() {
    if (!dotsWrap) return;
    dotsWrap.innerHTML = '';
    var totalDots = maxIndex() + 1;
    for (var i = 0; i < totalDots; i++) {
      var dot = document.createElement('button');
      dot.className = 'anna-carousel-dot' + (i === current ? ' is-active' : '');
      dot.setAttribute('role', 'tab');
      dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
      dot.setAttribute('data-index', i);
      dot.addEventListener('click', function () {
        goTo(parseInt(this.getAttribute('data-index'), 10));
        resetAutoplay();
      });
      dotsWrap.appendChild(dot);
    }
  }

  function updateDots() {
    if (!dotsWrap) return;
    var dots = dotsWrap.querySelectorAll('.anna-carousel-dot');
    dots.forEach(function (dot, i) {
      dot.classList.toggle('is-active', i === current);
      dot.setAttribute('aria-selected', i === current ? 'true' : 'false');
    });
  }

  // Autoplay
  function startAutoplay() {
    stopAutoplay();
    if (isPaused) return;
    autoplayId = setInterval(function () {
      if (current >= maxIndex()) {
        goTo(0);
      } else {
        next();
      }
    }, autoDelay);
  }

  function stopAutoplay() {
    if (autoplayId) clearInterval(autoplayId);
    autoplayId = null;
  }

  function resetAutoplay() {
    stopAutoplay();
    startAutoplay();
  }

  // Events
  prevBtn.addEventListener('click', function () { prev(); resetAutoplay(); });
  nextBtn.addEventListener('click', function () { next(); resetAutoplay(); });

  // Pause on hover/focus.
  track.addEventListener('mouseenter', function () { isPaused = true; stopAutoplay(); });
  track.addEventListener('mouseleave', function () { isPaused = false; startAutoplay(); });
  track.addEventListener('focusin',    function () { isPaused = true; stopAutoplay(); });
  track.addEventListener('focusout',   function () { isPaused = false; startAutoplay(); });

  // Keyboard nav.
  track.parentElement.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowLeft')  { prev(); resetAutoplay(); }
    if (e.key === 'ArrowRight') { next(); resetAutoplay(); }
  });

  // Touch/swipe support.
  var touchStartX = 0;
  var touchEndX   = 0;

  track.addEventListener('touchstart', function (e) {
    touchStartX = e.changedTouches[0].screenX;
  }, { passive: true });

  track.addEventListener('touchend', function (e) {
    touchEndX = e.changedTouches[0].screenX;
    var diff  = touchStartX - touchEndX;
    if (Math.abs(diff) > 50) {
      diff > 0 ? next() : prev();
      resetAutoplay();
    }
  }, { passive: true });

  // Responsive.
  window.addEventListener('resize', function () {
    updateLayout();
  });

  // Init.
  updateLayout();
  startAutoplay();
})();
