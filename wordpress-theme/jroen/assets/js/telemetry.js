/**
 * jroen.nl: donkere modus en de telemetrie-sectie.
 * De telemetrie haalt echte meetwaarden op als er een JSON-adres is ingesteld;
 * anders draait ze op voorbeelddata (de sectie toont dan het label "Demo-data").
 */
( function () {
	'use strict';

	var root = document.documentElement;

	// ---------- Donkere modus ----------
	document.querySelectorAll( '.theme-toggle' ).forEach( function ( btn ) {
		btn.setAttribute( 'aria-pressed', root.classList.contains( 'dark' ) ? 'true' : 'false' );
		btn.addEventListener( 'click', function () {
			var dark = root.classList.toggle( 'dark' );
			btn.setAttribute( 'aria-pressed', dark ? 'true' : 'false' );
			try {
				localStorage.setItem( 'jroen-theme', dark ? 'dark' : 'light' );
			} catch ( e ) {}
			document.dispatchEvent( new CustomEvent( 'jroen:theme' ) );
		} );
	} );

	// ---------- Telemetrie ----------
	document.querySelectorAll( '[data-telemetry]' ).forEach( function ( section ) {
		var cfg = JSON.parse( section.getAttribute( 'data-telemetry' ) || '{}' );
		var cards = section.querySelectorAll( '.telemetry__card[data-key]' );
		var canvas = section.querySelector( '.js-telemetry-canvas' );
		var ctx = canvas ? canvas.getContext( '2d' ) : null;
		var countEl = section.querySelector( '.js-telemetry-count' );
		var errorEl = section.querySelector( '.telemetry__error' );
		var led = section.querySelector( '.js-relay-led' );
		var relayState = section.querySelector( '.js-relay-state' );
		var relayBtn = section.querySelector( '.js-relay-toggle' );
		var count = 0;
		var history = [];
		var MAX = 40;
		var relay = false;
		var demo = [ 22.4, 48.2, 14 ];

		function fmt( v ) {
			return ( Math.round( v * 10 ) / 10 ).toLocaleString( 'nl-NL', { minimumFractionDigits: v % 1 ? 1 : 0, maximumFractionDigits: 1 } );
		}

		function setRelay( on ) {
			relay = !! on;
			if ( led ) {
				led.classList.toggle( 'is-on', relay );
			}
			if ( relayState ) {
				relayState.textContent = relay ? 'AAN' : 'UIT';
				relayState.classList.toggle( 'is-on', relay );
			}
		}

		function draw() {
			if ( ! ctx || history.length < 2 ) {
				return;
			}
			var ratio = window.devicePixelRatio || 1;
			var rect = canvas.getBoundingClientRect();
			canvas.width = Math.round( rect.width * ratio );
			canvas.height = Math.round( rect.height * ratio );
			var w = canvas.width;
			var h = canvas.height;
			var style = getComputedStyle( section );
			var accent = style.getPropertyValue( '--bl-primary' ).trim() || '#f56b1f';
			var grid = style.getPropertyValue( '--bl-border' ).trim() || '#e5e7eb';
			var min = Math.min.apply( null, history );
			var max = Math.max.apply( null, history );
			var pad = Math.max( 0.5, ( max - min ) * 0.25 );
			min -= pad;
			max += pad;

			ctx.clearRect( 0, 0, w, h );
			ctx.strokeStyle = grid;
			ctx.lineWidth = ratio;
			for ( var g = 1; g < 4; g++ ) {
				ctx.beginPath();
				ctx.moveTo( 0, ( h / 4 ) * g );
				ctx.lineTo( w, ( h / 4 ) * g );
				ctx.stroke();
			}

			var step = w / ( MAX - 1 );
			var offset = ( MAX - history.length ) * step;
			ctx.beginPath();
			history.forEach( function ( v, i ) {
				var x = offset + i * step;
				var y = h - ( ( v - min ) / ( max - min ) ) * h;
				if ( 0 === i ) {
					ctx.moveTo( x, y );
				} else {
					ctx.lineTo( x, y );
				}
			} );
			ctx.strokeStyle = accent;
			ctx.lineWidth = 2.5 * ratio;
			ctx.lineJoin = 'round';
			ctx.stroke();

			ctx.lineTo( w, h );
			ctx.lineTo( offset, h );
			ctx.closePath();
			var fill = ctx.createLinearGradient( 0, 0, 0, h );
			fill.addColorStop( 0, accent + '55' );
			fill.addColorStop( 1, accent + '00' );
			ctx.fillStyle = fill;
			ctx.fill();

			// Laatste meting extra benadrukken.
			var lastY = h - ( ( history[ history.length - 1 ] - min ) / ( max - min ) ) * h;
			ctx.beginPath();
			ctx.arc( w - 4 * ratio, lastY, 4 * ratio, 0, Math.PI * 2 );
			ctx.fillStyle = accent;
			ctx.fill();
		}

		function render( values ) {
			cards.forEach( function ( card, i ) {
				var el = card.querySelector( '.js-telemetry-value' );
				var v = values[ i ];
				el.textContent = ( null === v || undefined === v || isNaN( v ) ) ? '–' : fmt( v );
			} );
			if ( ! isNaN( values[ 0 ] ) ) {
				history.push( values[ 0 ] );
				if ( history.length > MAX ) {
					history.shift();
				}
			}
			count++;
			if ( countEl ) {
				countEl.textContent = count.toLocaleString( 'nl-NL' );
			}
			draw();
		}

		function clamp( v, lo, hi ) {
			return Math.min( hi, Math.max( lo, v ) );
		}

		function tickDemo() {
			demo[ 0 ] = clamp( demo[ 0 ] + ( Math.random() - 0.5 ) * 0.3, 20, 28 );
			demo[ 1 ] = clamp( demo[ 1 ] + ( Math.random() - 0.5 ) * 0.4, 35, 65 );
			demo[ 2 ] = Math.round( 10 + Math.random() * 8 + ( relay ? 6 : 0 ) );
			render( demo.slice() );
		}

		function tickLive() {
			fetch( cfg.url, { cache: 'no-store' } )
				.then( function ( r ) {
					if ( ! r.ok ) {
						throw new Error( r.status );
					}
					return r.json();
				} )
				.then( function ( data ) {
					var values = [];
					cards.forEach( function ( card ) {
						values.push( parseFloat( data[ card.getAttribute( 'data-key' ) ] ) );
					} );
					if ( cfg.relayKey && cfg.relayKey in data ) {
						setRelay( data[ cfg.relayKey ] === true || 1 === data[ cfg.relayKey ] || 'on' === data[ cfg.relayKey ] );
					}
					if ( errorEl ) {
						errorEl.hidden = true;
					}
					render( values );
				} )
				.catch( function () {
					if ( errorEl ) {
						errorEl.hidden = false;
					}
				} );
		}

		if ( relayBtn ) {
			relayBtn.addEventListener( 'click', function () {
				setRelay( ! relay );
			} );
		}
		setRelay( false );

		if ( cfg.url ) {
			tickLive();
			setInterval( tickLive, cfg.interval || 2000 );
		} else {
			// Een korte voorgeschiedenis, zodat de grafiek direct gevuld is.
			for ( var i = 0; i < MAX - 1; i++ ) {
				demo[ 0 ] = clamp( demo[ 0 ] + ( Math.random() - 0.5 ) * 0.3, 20, 28 );
				history.push( demo[ 0 ] );
			}
			tickDemo();
			setInterval( tickDemo, cfg.interval || 2000 );
		}

		window.addEventListener( 'resize', draw );
		document.addEventListener( 'jroen:theme', draw );
	} );
} )();
