/**
 * Single post — sticky prev/next navigation with smooth fade/slide.
 *
 * Strategy:
 *  - The original #anna-post-nav stays in normal document flow always.
 *  - A cloned sticky bar (.anna-single-nav--sticky) is appended to <body>,
 *    permanently position:fixed at bottom, but hidden via opacity+transform.
 *  - After scrolling past the hero, the clone fades+slides in.
 *  - When the sentinel (just before the original nav) enters the viewport,
 *    the clone fades+slides out — the original nav is now visible.
 *  - No layout shifts because the original nav never leaves the flow.
 *
 * @package Anna_Baylis
 */
(function () {
	'use strict';

	var nav = document.getElementById('anna-post-nav');
	var sentinel = document.getElementById('anna-post-nav-sentinel');
	var hero = document.querySelector('.anna-single-hero');

	if (!nav || !sentinel) {
		return;
	}

	// Build the sticky clone.
	var stickyNav = nav.cloneNode(true);
	stickyNav.removeAttribute('id');
	stickyNav.setAttribute('aria-hidden', 'true'); // original nav is the accessible one
	stickyNav.classList.add('anna-single-nav--sticky');
	document.body.appendChild(stickyNav);

	var isVisible = false;
	var ticking = false;

	function getScrollThreshold() {
		if (hero) {
			return hero.offsetTop + hero.offsetHeight;
		}
		return 400;
	}

	function update() {
		ticking = false;

		var scrolled = window.pageYOffset || document.documentElement.scrollTop;
		var threshold = getScrollThreshold();
		var sentinelRect = sentinel.getBoundingClientRect();
		var viewportBottom = window.innerHeight;

		threshold = threshold - 450;
		// Show sticky when: past the hero AND sentinel still below viewport.
		var shouldShow = scrolled > threshold && sentinelRect.top > viewportBottom;

		if (shouldShow && !isVisible) {
			isVisible = true;
			stickyNav.classList.add('is-visible');
		} else if (!shouldShow && isVisible) {
			isVisible = false;
			stickyNav.classList.remove('is-visible');
		}
	}

	function onScroll() {
		if (!ticking) {
			window.requestAnimationFrame(update);
			ticking = true;
		}
	}

	window.requestAnimationFrame(update);
	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', function () {
		window.requestAnimationFrame(update);
	});

}());
