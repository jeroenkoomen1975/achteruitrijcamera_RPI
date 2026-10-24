/**
 * Koomen.IT: syntax-kleuring voor C++ (Arduino/ESP32) en Python, en de
 * typ-animatie van het codevenster in de hero.
 */
( function () {
	'use strict';

	var KEYWORDS = {
		cpp: 'if else for while do return break continue switch case default const static void bool int float double char long unsigned auto class struct new delete true false nullptr include define uint8_t uint16_t uint32_t',
		python: 'def return if elif else for while in not and or is None True False class import from as with try except finally raise lambda yield pass break continue global async await self'
	};

	function esc( s ) {
		return s.replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
	}

	function highlight( code, lang ) {
		var kw = ( KEYWORDS[ lang ] || KEYWORDS.cpp ).split( ' ' );
		var comment = 'python' === lang ? '#[^\\n]*' : '\\/\\/[^\\n]*|\\/\\*[\\s\\S]*?\\*\\/';
		var pre = 'cpp' === lang ? '|^[ \\t]*#\\w+' : '';
		var re = new RegExp(
			'(' + comment + ')' +                                            // 1 commentaar
			'|("""[\\s\\S]*?"""|[fbr]?"(?:\\\\.|[^"\\\\\\n])*"|[fbr]?\'(?:\\\\.|[^\'\\\\\\n])*\'|<[\\w./]+\\.h>)' + // 2 tekst
			'|(\\b\\d+(?:\\.\\d+)?(?:e-?\\d+)?\\b|\\b0x[0-9a-fA-F]+\\b)' +    // 3 getal
			'|(@\\w+' + pre + ')' +                                          // 4 decorator / preprocessor
			'|([A-Za-z_]\\w*)(?=\\s*\\()' +                                  // 5 functie
			'|([A-Za-z_]\\w*)',                                              // 6 woord
			'gm'
		);
		var out = '';
		var last = 0;
		code.replace( re, function ( m, com, str, num, dec, fn, word, offset ) {
			out += esc( code.slice( last, offset ) );
			last = offset + m.length;
			var cls = '';
			if ( com ) {
				cls = 'com';
			} else if ( str ) {
				cls = 'str';
			} else if ( num ) {
				cls = 'num';
			} else if ( dec ) {
				cls = 'pre';
			} else if ( fn ) {
				cls = kw.indexOf( fn ) > -1 ? 'kw' : 'fn';
			} else if ( kw.indexOf( word ) > -1 ) {
				cls = 'kw';
			} else if ( /^[A-Z][A-Z0-9_]+$/.test( word ) ) {
				cls = 'const';
			} else if ( /^[A-Z]/.test( word ) ) {
				cls = 'type';
			}
			out += cls ? '<span class="tok-' + cls + '">' + esc( m ) + '</span>' : esc( m );
			return m;
		} );
		return out + esc( code.slice( last ) );
	}

	window.koomenitHighlight = highlight;

	document.querySelectorAll( 'code[data-lang]' ).forEach( function ( el ) {
		el.innerHTML = highlight( el.textContent, el.getAttribute( 'data-lang' ) );
	} );

	// Code laten intypen; de gekleurde opmaak blijft tijdens het typen staan.
	var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	if ( reduced ) {
		return;
	}

	document.querySelectorAll( '.editor--animate' ).forEach( function ( editor ) {
		var code = editor.querySelector( 'code' );
		var gutter = editor.querySelector( '.editor__gutter' );
		var status = editor.querySelector( '.editor__ok' );
		if ( ! code ) {
			return;
		}

		var walker = document.createTreeWalker( code, NodeFilter.SHOW_TEXT );
		var nodes = [];
		var texts = [];
		var total = 0;
		while ( walker.nextNode() ) {
			nodes.push( walker.currentNode );
			texts.push( walker.currentNode.data );
			total += walker.currentNode.data.length;
		}
		var gutterLines = gutter ? gutter.innerHTML.split( '<br>' ) : [];

		var caret = document.createElement( 'span' );
		caret.className = 'editor__caret';
		caret.setAttribute( 'aria-hidden', 'true' );

		function render( shown ) {
			var left = shown;
			var lines = 1;
			var last = null;
			nodes.forEach( function ( node, i ) {
				var take = Math.max( 0, Math.min( texts[ i ].length, left ) );
				node.data = texts[ i ].slice( 0, take );
				left -= take;
				lines += ( node.data.match( /\n/g ) || [] ).length;
				if ( take > 0 ) {
					last = node;
				}
			} );
			if ( last ) {
				last.parentNode.insertBefore( caret, last.nextSibling );
			} else {
				code.insertBefore( caret, code.firstChild );
			}
			if ( gutter ) {
				gutter.innerHTML = gutterLines.slice( 0, lines ).join( '<br>' );
			}
		}

		render( 0 );
		if ( status ) {
			status.textContent = status.getAttribute( 'data-pending' ) || 'bezig…';
			status.classList.add( 'is-running' );
		}
		editor.classList.add( 'is-typing' );

		// Ongeveer 2,5 seconden, ongeacht de lengte; korte code iets rustiger.
		var duration = Math.min( 2600, Math.max( 1200, total * 12 ) );
		var start = null;
		function step( ts ) {
			if ( null === start ) {
				start = ts;
			}
			var p = Math.min( 1, ( ts - start ) / duration );
			render( Math.round( total * p ) );
			if ( p < 1 ) {
				requestAnimationFrame( step );
				return;
			}
			editor.classList.remove( 'is-typing' );
			if ( gutter ) {
				gutter.innerHTML = gutterLines.join( '<br>' );
			}
			if ( status ) {
				setTimeout( function () {
					status.textContent = status.getAttribute( 'data-final' );
					status.classList.remove( 'is-running' );
				}, 500 );
			}
		}
		setTimeout( function () {
			requestAnimationFrame( step );
		}, 400 );
	} );
} )();
