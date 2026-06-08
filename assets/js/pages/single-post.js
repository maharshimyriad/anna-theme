/**
 * Single post — sticky prev/next navigation.
 *
 * Behaviour:
 *  - The nav bar is fixed to the bottom of the viewport while the user reads.
 *  - Once the user scrolls far enough that the nav's natural DOM position
 *    rises into view, it detaches and sits normally in the document.
 *
 * Technique:
 *  - A zero-height sentinel div (#anna-post-nav-sentinel) lives just before
 *    the nav in the DOM, marking the nav's natural top edge.
 *  - When the sentinel is BELOW the viewport bottom → nav is sticky (fixed).
 *  - When the sentinel is AT or ABOVE the viewport bottom → nav is released.
 *  - A spacer equal to the nav's height is inserted while sticky so the
 *    content beneath doesn't jump.
 *
 * @package Anna_Baylis
 */
( function () {
	'use strict';

	var nav      = document.getElementById( 'anna-post-nav' );
	var sentinel = document.getElementById( 'anna-post-nav-sentinel' );

	if ( ! nav || ! sentinel ) {
		return;
	}

	var isSticky   = false;
	var navHeight  = 0;
	var ticking    = false;

	function getNavHeight() {
		// Temporarily remove sticky to measure true height.
		var wasSticky = isSticky;
		if ( wasSticky ) {
			nav.classList.remove( 'is-sticky' );
			sentinel.style.height = '';
		}
		navHeight = nav.getBoundingClientRect().height;
		if ( wasSticky ) {
			nav.classList.add( 'is-sticky' );
			sentinel.style.height = navHeight + 'px';
		}
		return navHeight;
	}

	function update() {
		ticking = false;

		var sentinelRect  = sentinel.getBoundingClientRect();
		var viewportBottom = window.innerHeight;

		// Sentinel top is below the viewport bottom → should be sticky.
		var shouldBeSticky = sentinelRect.top > viewportBottom;

		if ( shouldBeSticky && ! isSticky ) {
			// Measure height before going sticky.
			navHeight = nav.getBoundingClientRect().height;
			isSticky  = true;
			// Set spacer height to prevent layout shift.
			sentinel.style.height = navHeight + 'px';
			nav.classList.add( 'is-sticky' );

		} else if ( ! shouldBeSticky && isSticky ) {
			isSticky = false;
			sentinel.style.height = '';
			nav.classList.remove( 'is-sticky' );
		}
	}

	function onScroll() {
		if ( ! ticking ) {
			window.requestAnimationFrame( update );
			ticking = true;
		}
	}

	// Run once after DOM is ready to set initial state.
	window.requestAnimationFrame( update );

	window.addEventListener( 'scroll', onScroll, { passive: true } );
	window.addEventListener( 'resize', function () {
		getNavHeight();
		update();
	} );

}() );
