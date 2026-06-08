/**
 * Single post — sticky prev/next navigation.
 *
 * The nav bar is fixed to the bottom of the viewport while the user reads.
 * Once the page has scrolled far enough that the nav's natural DOM position
 * comes into view, it detaches and flows normally in the document.
 *
 * @package Anna_Baylis
 */
( function () {
	'use strict';

	const nav      = document.getElementById( 'anna-post-nav' );
	const sentinel = document.getElementById( 'anna-post-nav-sentinel' );

	if ( ! nav || ! sentinel ) {
		return;
	}

	let navHeight    = 0;
	let ticking      = false;
	let isSticky     = false;

	function measureNav() {
		// Temporarily un-stick to measure natural height.
		nav.classList.remove( 'is-sticky' );
		sentinel.classList.remove( 'is-active' );
		sentinel.style.height = '';
		navHeight = nav.offsetHeight;
	}

	function update() {
		const sentinelTop = sentinel.getBoundingClientRect().top;
		const windowH     = window.innerHeight;

		// Sentinel is above the bottom of the viewport → go sticky.
		// Sentinel is at or below the bottom               → release.
		const shouldStick = sentinelTop > windowH - navHeight - 8;

		if ( shouldStick && ! isSticky ) {
			isSticky = true;
			sentinel.style.height = navHeight + 'px';
			sentinel.classList.add( 'is-active' );
			nav.classList.add( 'is-sticky' );
		} else if ( ! shouldStick && isSticky ) {
			isSticky = false;
			sentinel.style.height = '';
			sentinel.classList.remove( 'is-active' );
			nav.classList.remove( 'is-sticky' );
		}

		ticking = false;
	}

	function onScroll() {
		if ( ! ticking ) {
			requestAnimationFrame( update );
			ticking = true;
		}
	}

	// Initial setup.
	measureNav();
	update();

	window.addEventListener( 'scroll', onScroll, { passive: true } );
	window.addEventListener( 'resize', function () {
		measureNav();
		update();
	} );
}() );
