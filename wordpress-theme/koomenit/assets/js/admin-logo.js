/**
 * Koomen.IT: logo kiezen bij projecten en websites met de WordPress-mediakiezer.
 */
( function ( $ ) {
	'use strict';

	$( document ).on( 'click', '.koomenit-logo-select', function ( e ) {
		e.preventDefault();
		var box = $( this ).closest( '.koomenit-logo' );
		var frame = wp.media( {
			title: koomenitLogo.title,
			button: { text: koomenitLogo.button },
			library: { type: 'image' },
			multiple: false
		} );
		frame.on( 'select', function () {
			var a = frame.state().get( 'selection' ).first().toJSON();
			var url = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
			box.find( 'input[name="koomenit_logo"]' ).val( a.id );
			box.find( '.koomenit-logo__preview' ).empty().append( $( '<img>', { src: url, alt: '', css: { maxWidth: '100%', height: 'auto' } } ) );
			box.find( '.koomenit-logo-select' ).text( koomenitLogo.change );
			box.find( '.koomenit-logo-remove' ).show();
		} );
		frame.open();
	} );

	$( document ).on( 'click', '.koomenit-logo-remove', function ( e ) {
		e.preventDefault();
		var box = $( this ).closest( '.koomenit-logo' );
		box.find( 'input[name="koomenit_logo"]' ).val( '' );
		box.find( '.koomenit-logo__preview' ).empty();
		box.find( '.koomenit-logo-select' ).text( koomenitLogo.choose );
		$( this ).hide();
	} );
} )( jQuery );
