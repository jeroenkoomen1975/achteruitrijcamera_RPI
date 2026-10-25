/**
 * jroen.nl: logo kiezen bij projecten en websites met de WordPress-mediakiezer.
 */
( function ( $ ) {
	'use strict';

	$( document ).on( 'click', '.jroen-logo-select', function ( e ) {
		e.preventDefault();
		var box = $( this ).closest( '.jroen-logo' );
		var frame = wp.media( {
			title: jroenLogo.title,
			button: { text: jroenLogo.button },
			library: { type: 'image' },
			multiple: false
		} );
		frame.on( 'select', function () {
			var a = frame.state().get( 'selection' ).first().toJSON();
			var url = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
			box.find( 'input[name="jroen_logo"]' ).val( a.id );
			box.find( '.jroen-logo__preview' ).empty().append( $( '<img>', { src: url, alt: '', css: { maxWidth: '100%', height: 'auto' } } ) );
			box.find( '.jroen-logo-select' ).text( jroenLogo.change );
			box.find( '.jroen-logo-remove' ).show();
		} );
		frame.open();
	} );

	$( document ).on( 'click', '.jroen-logo-remove', function ( e ) {
		e.preventDefault();
		var box = $( this ).closest( '.jroen-logo' );
		box.find( 'input[name="jroen_logo"]' ).val( '' );
		box.find( '.jroen-logo__preview' ).empty();
		box.find( '.jroen-logo-select' ).text( jroenLogo.choose );
		$( this ).hide();
	} );
} )( jQuery );
