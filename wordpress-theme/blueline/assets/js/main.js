/**
 * Blueline: mobiel menu, sticky header, scroll-animaties en tellers.
 */
( function () {
	'use strict';

	document.documentElement.classList.add( 'js' );

	// Mobiel menu.
	var toggle = document.querySelector( '.menu-toggle' );
	var nav = document.getElementById( 'site-navigation' );
	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function () {
			var open = nav.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
		nav.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( 'a' ) && nav.classList.contains( 'is-open' ) ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	}

	// Schaduw onder de header na scrollen.
	var header = document.getElementById( 'masthead' );
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	// Tellers laten oplopen.
	function countUp( el ) {
		var target = parseInt( el.getAttribute( 'data-target' ), 10 ) || 0;
		var start = null;
		var duration = 1600;
		function step( ts ) {
			if ( ! start ) {
				start = ts;
			}
			var p = Math.min( ( ts - start ) / duration, 1 );
			el.textContent = Math.round( target * ( 1 - Math.pow( 1 - p, 3 ) ) );
			if ( p < 1 ) {
				requestAnimationFrame( step );
			}
		}
		requestAnimationFrame( step );
	}

	// Elementen onder de vouw laten inschuiven zodra ze in beeld komen;
	// wat bij het laden al zichtbaar is, blijft gewoon staan.
	var items = document.querySelectorAll( '.reveal' );
	var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	if ( reduced || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}
				entry.target.classList.remove( 'is-pending' );
				entry.target.querySelectorAll( '.js-count' ).forEach( countUp );
				observer.unobserve( entry.target );
			} );
		},
		{ threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
	);

	items.forEach( function ( el, i ) {
		if ( el.getBoundingClientRect().top < window.innerHeight ) {
			return;
		}
		el.classList.add( 'is-pending' );
		el.style.transitionDelay = ( i % 4 ) * 80 + 'ms';
		observer.observe( el );
	} );
} )();
