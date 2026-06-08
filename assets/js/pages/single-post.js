/**
 * Single post — sticky prev/next navigation.
 *
 * Behaviour:
 *  - The nav bar is fixed to the bottom of the viewport ONLY after the user
 *    has scrolled past the article hero (past the fold).
 *  - Once the user scrolls far enough that the nav's natural DOM position
 *    rises into view, it detaches and sits normally in the document.
 *
 * @package Anna_Baylis
 */
( function () {
	'use strict';

	var nav      = document.getElementById( 'anna-post-nav' );
	var sentinel = document.getElementById( 'anna-post-nav-sentinel' );
	var hero     = document.querySelector( '.anna-single-hero' );

	if ( ! nav || ! sentinel ) {
		return;
	}

	var isSticky  = false;
	var navHeight = 0;
	var ticking   = false;

	// How far past the hero the user must scroll before stickiness activates.
	// Defaults to 300px if no hero is found.
	function getScrollThreshold() {
		if ( hero ) {
			return hero.offsetTop + hero.offsetHeight;
		}
		return 200;
	}

	function update() {
		ticking = false;

		var scrolled       = window.pageYOffset || document.documentElement.scrollTop;
		var threshold      = getScrollThreshold();
		var sentinelRect   = sentinel.getBoundingClientRect();
		var viewportBottom = window.innerHeight;

		// Only sticky if user has scrolled past the hero AND
		// the sentinel's natural position is still below the viewport bottom.
		var shouldBeSticky = scrolled > threshold && sentinelRect.top > viewportBottom;

		if ( shouldBeSticky && ! isSticky ) {
			navHeight             = nav.getBoundingClientRect().height;
			isSticky              = true;
			sentinel.style.height = navHeight + 'px';
			nav.classList.add( 'is-sticky' );

		} else if ( ! shouldBeSticky && isSticky ) {
			isSticky              = false;
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

	window.requestAnimationFrame( update );
	window.addEventListener( 'scroll', onScroll, { passive: true } );
	window.addEventListener( 'resize', function () {
		navHeight = 0;
		update();
	} );

}() );
