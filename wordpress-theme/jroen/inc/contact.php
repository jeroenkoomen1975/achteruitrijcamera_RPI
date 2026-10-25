<?php
/**
 * Contactformulier: verwerken en per e-mail versturen via wp_mail().
 *
 * @package Jroen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Onderwerpen uit de Customizer.
 *
 * @return string[]
 */
function jroen_contact_subjects() {
	return array_values( array_filter( array_map( 'trim', explode( "\n", (string) jroen_mod( 'jroen_contact_subjects' ) ) ) ) );
}

/**
 * Bericht verwerken. Werkt voor ingelogde en niet-ingelogde bezoekers.
 */
function jroen_handle_contact() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = remove_query_arg( 'contact', $back );
	$fail = function ( $reason ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'contact', $reason, $back ) . '#contact' );
		exit;
	};

	if ( ! isset( $_POST['jroen_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jroen_contact_nonce'] ) ), 'jroen_contact' ) ) {
		$fail( 'verlopen' );
	}

	// Lokveld: mensen zien dit veld niet, spamrobots vullen het wel in.
	if ( ! empty( $_POST['jroen_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'contact', 'verzonden', $back ) . '#contact' );
		exit;
	}

	$name    = isset( $_POST['jroen_name'] ) ? sanitize_text_field( wp_unslash( $_POST['jroen_name'] ) ) : '';
	$email   = isset( $_POST['jroen_email'] ) ? sanitize_email( wp_unslash( $_POST['jroen_email'] ) ) : '';
	$message = isset( $_POST['jroen_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['jroen_message'] ) ) : '';
	$subject = isset( $_POST['jroen_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['jroen_subject'] ) ) : '';
	if ( ! in_array( $subject, jroen_contact_subjects(), true ) ) {
		$subject = __( 'Bericht via de website', 'jroen' );
	}

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		$fail( 'onvolledig' );
	}

	// Maximaal één bericht per minuut per bezoeker.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'jroen_contact_' . md5( $ip );
	if ( get_transient( $key ) ) {
		$fail( 'te-snel' );
	}
	set_transient( $key, 1, MINUTE_IN_SECONDS );

	$to = jroen_mod( 'jroen_contact_to' );
	$to = is_email( $to ) ? $to : get_option( 'admin_email' );

	$body = sprintf(
		/* translators: 1: naam, 2: e-mailadres, 3: onderwerp, 4: bericht */
		__( "Naam: %1\$s\nE-mail: %2\$s\nOnderwerp: %3\$s\n\n%4\$s", 'jroen' ),
		$name,
		$email,
		$subject,
		$message
	);
	$sent = wp_mail(
		$to,
		'[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . $subject,
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	if ( ! $sent ) {
		$fail( 'mislukt' );
	}
	wp_safe_redirect( add_query_arg( 'contact', 'verzonden', $back ) . '#contact' );
	exit;
}
add_action( 'admin_post_nopriv_jroen_contact', 'jroen_handle_contact' );
add_action( 'admin_post_jroen_contact', 'jroen_handle_contact' );

/**
 * Melding na het versturen.
 *
 * @return array{type:string,text:string}|null
 */
function jroen_contact_notice() {
	$state = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$texts = array(
		'verzonden'  => array( 'ok', __( 'Bedankt voor je bericht! Ik neem zo snel mogelijk contact met je op.', 'jroen' ) ),
		'onvolledig' => array( 'error', __( 'Vul je naam, een geldig e-mailadres en je bericht in.', 'jroen' ) ),
		'te-snel'    => array( 'error', __( 'Je hebt net al een bericht gestuurd. Probeer het over een minuut opnieuw.', 'jroen' ) ),
		'verlopen'   => array( 'error', __( 'Het formulier is verlopen. Laad de pagina opnieuw en probeer het nog eens.', 'jroen' ) ),
		'mislukt'    => array( 'error', __( 'Het versturen is mislukt. Mail me rechtstreeks via het adres hiernaast.', 'jroen' ) ),
	);
	return isset( $texts[ $state ] ) ? array( 'type' => $texts[ $state ][0], 'text' => $texts[ $state ][1] ) : null;
}
