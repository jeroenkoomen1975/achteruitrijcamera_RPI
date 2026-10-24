<?php
/**
 * Customizer-instellingen: kleuren, typografie en alle homepage-secties.
 *
 * @package KoomenIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Standaardwaarden voor alle instellingen.
 *
 * @return array
 */
function koomenit_defaults() {
	return array(
		// Kleuren (afgeleid van het Koomen.IT-logo).
		'koomenit_color_primary'   => '#0e2a47',
		'koomenit_color_secondary' => '#1f8a8a',
		'koomenit_color_steel'     => '#4c7a9e',
		'koomenit_color_node'      => '#8b97a3',
		'koomenit_color_dark'      => '#0b0e14',
		'koomenit_color_light'     => '#f2f5f8',
		'koomenit_color_text'      => '#3d4a58',
		'koomenit_radius'          => 10,

		// Header.
		'koomenit_header_cta_text' => __( 'Neem contact op', 'koomenit' ),
		'koomenit_header_cta_url'  => '#contact',
		'koomenit_topbar_show'     => true,
		'koomenit_topbar_phone'    => '',
		'koomenit_topbar_email'    => 'info@koomen.it',
		'koomenit_topbar_text'     => __( 'Training, Onderwijs & Advies in IT', 'koomenit' ),

		// Hero.
		'koomenit_hero_show'       => true,
		'koomenit_hero_eyebrow'    => __( 'Training · Onderwijs · Advies', 'koomenit' ),
		'koomenit_hero_title'      => __( 'IT-kennis die blijft hangen', 'koomenit' ),
		'koomenit_hero_text'       => __( 'Praktijkgerichte trainingen, lessen en advies over IT. Voor professionals, teams en onderwijsinstellingen die technologie écht willen begrijpen en toepassen.', 'koomenit' ),
		'koomenit_hero_btn1_text'  => __( 'Bekijk het aanbod', 'koomenit' ),
		'koomenit_hero_btn1_url'   => '#aanbod',
		'koomenit_hero_btn2_text'  => __( 'Plan een kennismaking', 'koomenit' ),
		'koomenit_hero_btn2_url'   => '#contact',
		'koomenit_hero_image'      => '',
		'koomenit_hero_overlay'    => 85,
		'koomenit_hero_logo'       => true,

		// Aanbod.
		'koomenit_services_show'   => true,
		'koomenit_services_title'  => __( 'Aanbod', 'koomenit' ),
		'koomenit_services_intro'  => __( 'Drie manieren om IT-kennis over te dragen, steeds afgestemd op de groep en het doel.', 'koomenit' ),

		// Over.
		'koomenit_about_show'      => true,
		'koomenit_about_eyebrow'   => __( 'Over Koomen.IT', 'koomenit' ),
		'koomenit_about_title'     => __( 'Techniek uitleggen zodat het klikt', 'koomenit' ),
		'koomenit_about_text'      => __( 'Goede IT begint bij begrip. Koomen.IT vertaalt complexe onderwerpen naar heldere uitleg en concrete oefeningen, zodat deelnemers de kennis direct kunnen toepassen in hun eigen werk.', 'koomenit' ),
		'koomenit_about_points'    => __( "Praktijkgericht en concreet\nAfgestemd op het niveau van de groep\nVoor onderwijs én bedrijfsleven\nPersoonlijk contact", 'koomenit' ),
		'koomenit_about_image'     => '',
		'koomenit_about_btn_text'  => __( 'Meer over Koomen.IT', 'koomenit' ),
		'koomenit_about_btn_url'   => '#',

		// Cijfers.
		'koomenit_stats_show'      => true,

		// Nieuws.
		'koomenit_news_show'       => true,
		'koomenit_news_title'      => __( 'Nieuws & kennisbank', 'koomenit' ),
		'koomenit_news_count'      => 3,

		// CTA.
		'koomenit_cta_show'        => true,
		'koomenit_cta_title'       => __( 'Samen aan de slag?', 'koomenit' ),
		'koomenit_cta_text'        => __( 'Benieuwd wat een training, les of adviestraject voor jouw organisatie kan betekenen? Neem vrijblijvend contact op.', 'koomenit' ),
		'koomenit_cta_btn_text'    => __( 'Neem contact op', 'koomenit' ),
		'koomenit_cta_btn_url'     => 'mailto:info@koomen.it',

		// Footer.
		'koomenit_footer_about'    => __( 'Koomen.IT verzorgt training, onderwijs en advies op het gebied van IT.', 'koomenit' ),
		'koomenit_footer_address'  => '',
		'koomenit_footer_copy'     => '',
		'koomenit_social_linkedin' => '#',
		'koomenit_social_facebook' => '',
		'koomenit_social_instagram' => '',
		'koomenit_social_x'        => '',

		// Typografie.
		'koomenit_font_heading'    => 'Inter',
		'koomenit_font_body'       => 'Inter',
	);
}

/**
 * Standaarden voor de herhaalbare items (aanbod en cijfers).
 * Een item met een lege titel/omschrijving wordt niet getoond.
 *
 * @return array
 */
function koomenit_repeat_defaults() {
	return array(
		'services' => array(
			1 => array( 'icon' => 'code', 'title' => __( 'Training', 'koomenit' ), 'text' => __( 'IT-trainingen en workshops op maat, van programmeren en cloud tot security. Veel oefenen, weinig sheets.', 'koomenit' ) ),
			2 => array( 'icon' => 'graduation', 'title' => __( 'Onderwijs', 'koomenit' ), 'text' => __( 'Lessen, lesmateriaal en curriculumontwikkeling voor mbo, hbo en bedrijfsopleidingen.', 'koomenit' ) ),
			3 => array( 'icon' => 'lightbulb', 'title' => __( 'Advies', 'koomenit' ), 'text' => __( 'Onafhankelijk advies over IT-architectuur, tooling en het opbouwen van digitale vaardigheden in je team.', 'koomenit' ) ),
			4 => array( 'icon' => 'network', 'title' => '', 'text' => '' ),
		),
		'stats'    => array(
			1 => array( 'number' => '3', 'suffix' => '', 'label' => __( 'Kerngebieden', 'koomenit' ) ),
			2 => array( 'number' => '100', 'suffix' => '%', 'label' => __( 'Maatwerk', 'koomenit' ) ),
			3 => array( 'number' => '1', 'suffix' => '', 'label' => __( 'Vast aanspreekpunt', 'koomenit' ) ),
			4 => array( 'number' => '0', 'suffix' => '', 'label' => '' ),
		),
	);
}

/**
 * Een thema-instelling ophalen met de juiste standaardwaarde.
 *
 * @param string $key Instellingsnaam.
 * @return mixed
 */
function koomenit_mod( $key ) {
	$defaults = koomenit_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Beschikbare iconen voor de dienstenkaarten.
 *
 * @return array
 */
function koomenit_icon_choices() {
	return array(
		'users'      => __( 'Personen', 'koomenit' ),
		'lightbulb'  => __( 'Lamp', 'koomenit' ),
		'graduation' => __( 'Opleiding', 'koomenit' ),
		'cloud'      => __( 'Cloud', 'koomenit' ),
		'shield'     => __( 'Beveiliging', 'koomenit' ),
		'code'       => __( 'Code', 'koomenit' ),
		'server'     => __( 'Server', 'koomenit' ),
		'chart'      => __( 'Grafiek', 'koomenit' ),
		'network'    => __( 'Netwerk', 'koomenit' ),
	);
}

/**
 * Checkbox opschonen.
 *
 * @param mixed $value Waarde.
 * @return bool
 */
function koomenit_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Select opschonen.
 *
 * @param string               $value   Waarde.
 * @param WP_Customize_Setting $setting Instelling.
 * @return string
 */
function koomenit_sanitize_select( $value, $setting ) {
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

/**
 * Customizer registreren.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function koomenit_customize_register( $wp_customize ) {
	$d   = koomenit_defaults();
	$rep = koomenit_repeat_defaults();

	// Hulpfunctie voor het toevoegen van een instelling + control.
	$add = function ( $id, $section, $label, $type = 'text', $sanitize = 'sanitize_text_field', $extra = array() ) use ( $wp_customize, $d ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => isset( $extra['default'] ) ? $extra['default'] : ( isset( $d[ $id ] ) ? $d[ $id ] : '' ),
				'sanitize_callback' => $sanitize,
				'transport'         => isset( $extra['transport'] ) ? $extra['transport'] : 'refresh',
			)
		);
		unset( $extra['default'], $extra['transport'] );
		$wp_customize->add_control( $id, array_merge( array( 'label' => $label, 'section' => $section, 'type' => $type ), $extra ) );
	};

	$wp_customize->add_panel(
		'koomenit_panel',
		array(
			'title'    => __( 'Koomen.IT thema-opties', 'koomenit' ),
			'priority' => 20,
		)
	);

	$sections = array(
		'koomenit_colors'   => __( 'Kleuren & stijl', 'koomenit' ),
		'koomenit_type'     => __( 'Typografie', 'koomenit' ),
		'koomenit_header'   => __( 'Header & topbalk', 'koomenit' ),
		'koomenit_hero'     => __( 'Homepage: Hero', 'koomenit' ),
		'koomenit_services' => __( 'Homepage: Aanbod', 'koomenit' ),
		'koomenit_about'    => __( 'Homepage: Over ons', 'koomenit' ),
		'koomenit_stats'    => __( 'Homepage: Cijfers', 'koomenit' ),
		'koomenit_news'     => __( 'Homepage: Nieuws', 'koomenit' ),
		'koomenit_cta'      => __( 'Homepage: Call-to-action', 'koomenit' ),
		'koomenit_footer'   => __( 'Footer & social media', 'koomenit' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'koomenit_panel' ) );
	}

	// Kleuren.
	$colors = array(
		'koomenit_color_primary'   => __( 'Primaire kleur', 'koomenit' ),
		'koomenit_color_secondary' => __( 'Accentkleur (teal)', 'koomenit' ),
		'koomenit_color_steel'     => __( 'Staalblauw (verlopen, donkere ondergrond)', 'koomenit' ),
		'koomenit_color_node'      => __( 'Netwerk-knooppunten', 'koomenit' ),
		'koomenit_color_dark'      => __( 'Donkere kleur (footer, koppen)', 'koomenit' ),
		'koomenit_color_light'     => __( 'Lichte achtergrond', 'koomenit' ),
		'koomenit_color_text'      => __( 'Tekstkleur', 'koomenit' ),
	);
	foreach ( $colors as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'default' => $d[ $id ], 'sanitize_callback' => 'sanitize_hex_color' ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array( 'label' => $label, 'section' => 'koomenit_colors' ) ) );
	}
	$add( 'koomenit_radius', 'koomenit_colors', __( 'Afronding hoeken (px)', 'koomenit' ), 'range', 'absint', array( 'input_attrs' => array( 'min' => 0, 'max' => 32, 'step' => 1 ) ) );

	// Typografie.
	$fonts = array(
		'Inter'             => 'Inter',
		'Plus Jakarta Sans' => 'Plus Jakarta Sans',
		'Poppins'           => 'Poppins',
		'Montserrat'        => 'Montserrat',
		'Manrope'           => 'Manrope',
		'DM Sans'           => 'DM Sans',
		'Outfit'            => 'Outfit',
		'Open Sans'         => 'Open Sans',
	);
	$add( 'koomenit_font_heading', 'koomenit_type', __( 'Lettertype koppen', 'koomenit' ), 'select', 'koomenit_sanitize_select', array( 'choices' => $fonts ) );
	$add( 'koomenit_font_body', 'koomenit_type', __( 'Lettertype tekst', 'koomenit' ), 'select', 'koomenit_sanitize_select', array( 'choices' => $fonts ) );

	// Header.
	$add( 'koomenit_topbar_show', 'koomenit_header', __( 'Topbalk tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_topbar_text', 'koomenit_header', __( 'Topbalk tekst', 'koomenit' ) );
	$add( 'koomenit_topbar_phone', 'koomenit_header', __( 'Telefoonnummer', 'koomenit' ) );
	$add( 'koomenit_topbar_email', 'koomenit_header', __( 'E-mailadres', 'koomenit' ), 'email', 'sanitize_email' );
	$add( 'koomenit_header_cta_text', 'koomenit_header', __( 'Knoptekst header', 'koomenit' ) );
	$add( 'koomenit_header_cta_url', 'koomenit_header', __( 'Knoplink header', 'koomenit' ), 'url', 'esc_url_raw' );

	// Hero.
	$add( 'koomenit_hero_show', 'koomenit_hero', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_hero_eyebrow', 'koomenit_hero', __( 'Label boven titel', 'koomenit' ) );
	$add( 'koomenit_hero_title', 'koomenit_hero', __( 'Titel', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'koomenit_hero_text', 'koomenit_hero', __( 'Tekst', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'koomenit_hero_btn1_text', 'koomenit_hero', __( 'Knop 1 tekst', 'koomenit' ) );
	$add( 'koomenit_hero_btn1_url', 'koomenit_hero', __( 'Knop 1 link', 'koomenit' ), 'url', 'esc_url_raw' );
	$add( 'koomenit_hero_btn2_text', 'koomenit_hero', __( 'Knop 2 tekst', 'koomenit' ) );
	$add( 'koomenit_hero_btn2_url', 'koomenit_hero', __( 'Knop 2 link', 'koomenit' ), 'url', 'esc_url_raw' );
	$wp_customize->add_setting( 'koomenit_hero_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'koomenit_hero_image', array( 'label' => __( 'Achtergrondafbeelding', 'koomenit' ), 'section' => 'koomenit_hero' ) ) );
	$add( 'koomenit_hero_logo', 'koomenit_hero', __( 'Logo-illustratie rechts tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_hero_overlay', 'koomenit_hero', __( 'Dekking blauwe overlay (%)', 'koomenit' ), 'range', 'absint', array( 'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ) ) );

	// Aanbod.
	$add( 'koomenit_services_show', 'koomenit_services', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_services_title', 'koomenit_services', __( 'Titel', 'koomenit' ) );
	$add( 'koomenit_services_intro', 'koomenit_services', __( 'Introductie', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	foreach ( $rep['services'] as $i => $item ) {
		/* translators: %d: nummer van de dienst */
		$prefix = sprintf( __( 'Aanbod %d', 'koomenit' ), $i );
		$add( "koomenit_service{$i}_icon", 'koomenit_services', $prefix . ' – ' . __( 'icoon', 'koomenit' ), 'select', 'koomenit_sanitize_select', array( 'choices' => koomenit_icon_choices(), 'default' => $item['icon'] ) );
		$add( "koomenit_service{$i}_title", 'koomenit_services', $prefix . ' – ' . __( 'titel', 'koomenit' ), 'text', 'sanitize_text_field', array( 'default' => $item['title'] ) );
		$add( "koomenit_service{$i}_text", 'koomenit_services', $prefix . ' – ' . __( 'tekst', 'koomenit' ), 'textarea', 'sanitize_textarea_field', array( 'default' => $item['text'] ) );
		$add( "koomenit_service{$i}_url", 'koomenit_services', $prefix . ' – ' . __( 'link', 'koomenit' ), 'url', 'esc_url_raw', array( 'default' => '' ) );
	}

	// Over ons.
	$add( 'koomenit_about_show', 'koomenit_about', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_about_eyebrow', 'koomenit_about', __( 'Label boven titel', 'koomenit' ) );
	$add( 'koomenit_about_title', 'koomenit_about', __( 'Titel', 'koomenit' ) );
	$add( 'koomenit_about_text', 'koomenit_about', __( 'Tekst', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'koomenit_about_points', 'koomenit_about', __( 'Voordelen (één per regel)', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$wp_customize->add_setting( 'koomenit_about_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'koomenit_about_image', array( 'label' => __( 'Afbeelding', 'koomenit' ), 'section' => 'koomenit_about' ) ) );
	$add( 'koomenit_about_btn_text', 'koomenit_about', __( 'Knoptekst', 'koomenit' ) );
	$add( 'koomenit_about_btn_url', 'koomenit_about', __( 'Knoplink', 'koomenit' ), 'url', 'esc_url_raw' );

	// Cijfers.
	$add( 'koomenit_stats_show', 'koomenit_stats', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	foreach ( $rep['stats'] as $i => $item ) {
		/* translators: %d: nummer van het cijfer */
		$prefix = sprintf( __( 'Cijfer %d', 'koomenit' ), $i );
		$add( "koomenit_stat{$i}_number", 'koomenit_stats', $prefix . ' – ' . __( 'getal', 'koomenit' ), 'number', 'absint', array( 'default' => $item['number'] ) );
		$add( "koomenit_stat{$i}_suffix", 'koomenit_stats', $prefix . ' – ' . __( 'achtervoegsel (+, %)', 'koomenit' ), 'text', 'sanitize_text_field', array( 'default' => $item['suffix'] ) );
		$add( "koomenit_stat{$i}_label", 'koomenit_stats', $prefix . ' – ' . __( 'omschrijving', 'koomenit' ), 'text', 'sanitize_text_field', array( 'default' => $item['label'] ) );
	}

	// Nieuws.
	$add( 'koomenit_news_show', 'koomenit_news', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_news_title', 'koomenit_news', __( 'Titel', 'koomenit' ) );
	$add( 'koomenit_news_count', 'koomenit_news', __( 'Aantal berichten', 'koomenit' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1, 'max' => 9 ) ) );

	// CTA.
	$add( 'koomenit_cta_show', 'koomenit_cta', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_cta_title', 'koomenit_cta', __( 'Titel', 'koomenit' ) );
	$add( 'koomenit_cta_text', 'koomenit_cta', __( 'Tekst', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'koomenit_cta_btn_text', 'koomenit_cta', __( 'Knoptekst', 'koomenit' ) );
	$add( 'koomenit_cta_btn_url', 'koomenit_cta', __( 'Knoplink', 'koomenit' ), 'url', 'esc_url_raw' );

	// Footer.
	$add( 'koomenit_footer_about', 'koomenit_footer', __( 'Korte omschrijving', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'koomenit_footer_address', 'koomenit_footer', __( 'Adres', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'koomenit_footer_copy', 'koomenit_footer', __( 'Copyrighttekst (leeg = automatisch)', 'koomenit' ) );
	$add( 'koomenit_social_linkedin', 'koomenit_footer', 'LinkedIn URL', 'url', 'esc_url_raw' );
	$add( 'koomenit_social_facebook', 'koomenit_footer', 'Facebook URL', 'url', 'esc_url_raw' );
	$add( 'koomenit_social_instagram', 'koomenit_footer', 'Instagram URL', 'url', 'esc_url_raw' );
	$add( 'koomenit_social_x', 'koomenit_footer', 'X (Twitter) URL', 'url', 'esc_url_raw' );
}
add_action( 'customize_register', 'koomenit_customize_register' );

/**
 * CSS-variabelen uit de Customizer.
 *
 * @return string
 */
function koomenit_customizer_css() {
	$vars = array(
		'--bl-primary'   => koomenit_mod( 'koomenit_color_primary' ),
		'--bl-secondary' => koomenit_mod( 'koomenit_color_secondary' ),
		'--bl-steel'     => koomenit_mod( 'koomenit_color_steel' ),
		'--bl-node'      => koomenit_mod( 'koomenit_color_node' ),
		'--bl-dark'      => koomenit_mod( 'koomenit_color_dark' ),
		'--bl-light'     => koomenit_mod( 'koomenit_color_light' ),
		'--bl-text'      => koomenit_mod( 'koomenit_color_text' ),
		'--bl-radius'    => absint( koomenit_mod( 'koomenit_radius' ) ) . 'px',
		'--bl-overlay'   => min( 100, absint( koomenit_mod( 'koomenit_hero_overlay' ) ) ) / 100,
		'--bl-font-head' => '"' . koomenit_mod( 'koomenit_font_heading' ) . '", system-ui, sans-serif',
		'--bl-font-body' => '"' . koomenit_mod( 'koomenit_font_body' ) . '", system-ui, sans-serif',
	);

	$css = ':root{';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . wp_strip_all_tags( (string) $value ) . ';';
	}
	return $css . '}';
}
