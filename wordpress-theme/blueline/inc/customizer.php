<?php
/**
 * Customizer-instellingen: kleuren, typografie en alle homepage-secties.
 *
 * @package Blueline
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Standaardwaarden voor alle instellingen.
 *
 * @return array
 */
function blueline_defaults() {
	return array(
		// Kleuren.
		'blueline_color_primary'   => '#1d4ed8',
		'blueline_color_secondary' => '#0ea5e9',
		'blueline_color_dark'      => '#0b1b3f',
		'blueline_color_light'     => '#f1f6ff',
		'blueline_color_text'      => '#334155',
		'blueline_radius'          => 14,

		// Header.
		'blueline_header_cta_text' => __( 'Neem contact op', 'blueline' ),
		'blueline_header_cta_url'  => '#contact',
		'blueline_topbar_show'     => true,
		'blueline_topbar_phone'    => '+31 (0)20 123 45 67',
		'blueline_topbar_email'    => 'info@voorbeeld.nl',
		'blueline_topbar_text'     => __( 'Jouw partner in IT-talent en oplossingen', 'blueline' ),

		// Hero.
		'blueline_hero_show'       => true,
		'blueline_hero_eyebrow'    => __( 'IT-dienstverlening', 'blueline' ),
		'blueline_hero_title'      => __( 'Wij verbinden IT-talent met ambitieuze organisaties', 'blueline' ),
		'blueline_hero_text'       => __( 'Detachering, consultancy, training en outsourcing. Met de juiste mensen en kennis brengen we jouw IT-organisatie naar een hoger niveau.', 'blueline' ),
		'blueline_hero_btn1_text'  => __( 'Onze diensten', 'blueline' ),
		'blueline_hero_btn1_url'   => '#diensten',
		'blueline_hero_btn2_text'  => __( 'Bekijk vacatures', 'blueline' ),
		'blueline_hero_btn2_url'   => '#nieuws',
		'blueline_hero_image'      => '',
		'blueline_hero_overlay'    => 85,

		// Diensten.
		'blueline_services_show'   => true,
		'blueline_services_title'  => __( 'Onze diensten', 'blueline' ),
		'blueline_services_intro'  => __( 'Van een enkele specialist tot een compleet team: wij leveren maatwerk.', 'blueline' ),

		// Over ons.
		'blueline_about_show'      => true,
		'blueline_about_eyebrow'   => __( 'Over ons', 'blueline' ),
		'blueline_about_title'     => __( 'Mensen maken het verschil in IT', 'blueline' ),
		'blueline_about_text'      => __( 'Wij geloven dat technologie pas waarde krijgt door de mensen die ermee werken. Daarom investeren we in de groei van onze professionals en in duurzame relaties met onze opdrachtgevers.', 'blueline' ),
		'blueline_about_points'    => __( "Persoonlijke begeleiding\nGecertificeerde specialisten\nLanding binnen enkele weken\nLangdurige samenwerkingen", 'blueline' ),
		'blueline_about_image'     => '',
		'blueline_about_btn_text'  => __( 'Meer over ons', 'blueline' ),
		'blueline_about_btn_url'   => '#',

		// Cijfers.
		'blueline_stats_show'      => true,

		// Nieuws.
		'blueline_news_show'       => true,
		'blueline_news_title'      => __( 'Laatste nieuws & vacatures', 'blueline' ),
		'blueline_news_count'      => 3,

		// CTA.
		'blueline_cta_show'        => true,
		'blueline_cta_title'       => __( 'Klaar voor de volgende stap?', 'blueline' ),
		'blueline_cta_text'        => __( 'Of je nu talent zoekt of een nieuwe uitdaging: we denken graag met je mee.', 'blueline' ),
		'blueline_cta_btn_text'    => __( 'Plan een kennismaking', 'blueline' ),
		'blueline_cta_btn_url'     => 'mailto:info@voorbeeld.nl',

		// Footer.
		'blueline_footer_about'    => __( 'Wij leveren IT-professionals en oplossingen die écht passen bij jouw organisatie.', 'blueline' ),
		'blueline_footer_address'  => "Voorbeeldstraat 1\n1234 AB Amsterdam",
		'blueline_footer_copy'     => '',
		'blueline_social_linkedin' => '#',
		'blueline_social_facebook' => '',
		'blueline_social_instagram' => '',
		'blueline_social_x'        => '',

		// Typografie.
		'blueline_font_heading'    => 'Plus Jakarta Sans',
		'blueline_font_body'       => 'Inter',
	);
}

/**
 * Standaarden voor de herhaalbare items (diensten en cijfers).
 *
 * @return array
 */
function blueline_repeat_defaults() {
	return array(
		'services' => array(
			1 => array( 'icon' => 'users', 'title' => __( 'Detachering', 'blueline' ), 'text' => __( 'Ervaren IT-professionals die direct inzetbaar zijn binnen jouw organisatie.', 'blueline' ) ),
			2 => array( 'icon' => 'lightbulb', 'title' => __( 'Consultancy', 'blueline' ), 'text' => __( 'Advies en begeleiding bij strategie, architectuur en implementatie.', 'blueline' ) ),
			3 => array( 'icon' => 'graduation', 'title' => __( 'Training', 'blueline' ), 'text' => __( 'Opleidingstrajecten die jouw team klaarstomen voor de toekomst.', 'blueline' ) ),
			4 => array( 'icon' => 'cloud', 'title' => __( 'Outsourcing', 'blueline' ), 'text' => __( 'Wij nemen IT-taken of complete projecten zorgeloos van je over.', 'blueline' ) ),
		),
		'stats'    => array(
			1 => array( 'number' => '250', 'suffix' => '+', 'label' => __( 'IT-professionals', 'blueline' ) ),
			2 => array( 'number' => '120', 'suffix' => '+', 'label' => __( 'Opdrachtgevers', 'blueline' ) ),
			3 => array( 'number' => '10', 'suffix' => '', 'label' => __( 'Jaar ervaring', 'blueline' ) ),
			4 => array( 'number' => '98', 'suffix' => '%', 'label' => __( 'Klanttevredenheid', 'blueline' ) ),
		),
	);
}

/**
 * Een thema-instelling ophalen met de juiste standaardwaarde.
 *
 * @param string $key Instellingsnaam.
 * @return mixed
 */
function blueline_mod( $key ) {
	$defaults = blueline_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Beschikbare iconen voor de dienstenkaarten.
 *
 * @return array
 */
function blueline_icon_choices() {
	return array(
		'users'      => __( 'Personen', 'blueline' ),
		'lightbulb'  => __( 'Lamp', 'blueline' ),
		'graduation' => __( 'Opleiding', 'blueline' ),
		'cloud'      => __( 'Cloud', 'blueline' ),
		'shield'     => __( 'Beveiliging', 'blueline' ),
		'code'       => __( 'Code', 'blueline' ),
		'server'     => __( 'Server', 'blueline' ),
		'chart'      => __( 'Grafiek', 'blueline' ),
	);
}

/**
 * Checkbox opschonen.
 *
 * @param mixed $value Waarde.
 * @return bool
 */
function blueline_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Select opschonen.
 *
 * @param string               $value   Waarde.
 * @param WP_Customize_Setting $setting Instelling.
 * @return string
 */
function blueline_sanitize_select( $value, $setting ) {
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

/**
 * Customizer registreren.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function blueline_customize_register( $wp_customize ) {
	$d   = blueline_defaults();
	$rep = blueline_repeat_defaults();

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
		'blueline_panel',
		array(
			'title'    => __( 'Blueline thema-opties', 'blueline' ),
			'priority' => 20,
		)
	);

	$sections = array(
		'blueline_colors'   => __( 'Kleuren & stijl', 'blueline' ),
		'blueline_type'     => __( 'Typografie', 'blueline' ),
		'blueline_header'   => __( 'Header & topbalk', 'blueline' ),
		'blueline_hero'     => __( 'Homepage: Hero', 'blueline' ),
		'blueline_services' => __( 'Homepage: Diensten', 'blueline' ),
		'blueline_about'    => __( 'Homepage: Over ons', 'blueline' ),
		'blueline_stats'    => __( 'Homepage: Cijfers', 'blueline' ),
		'blueline_news'     => __( 'Homepage: Nieuws', 'blueline' ),
		'blueline_cta'      => __( 'Homepage: Call-to-action', 'blueline' ),
		'blueline_footer'   => __( 'Footer & social media', 'blueline' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'blueline_panel' ) );
	}

	// Kleuren.
	$colors = array(
		'blueline_color_primary'   => __( 'Primaire kleur', 'blueline' ),
		'blueline_color_secondary' => __( 'Accentkleur', 'blueline' ),
		'blueline_color_dark'      => __( 'Donkere kleur (footer, koppen)', 'blueline' ),
		'blueline_color_light'     => __( 'Lichte achtergrond', 'blueline' ),
		'blueline_color_text'      => __( 'Tekstkleur', 'blueline' ),
	);
	foreach ( $colors as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'default' => $d[ $id ], 'sanitize_callback' => 'sanitize_hex_color' ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array( 'label' => $label, 'section' => 'blueline_colors' ) ) );
	}
	$add( 'blueline_radius', 'blueline_colors', __( 'Afronding hoeken (px)', 'blueline' ), 'range', 'absint', array( 'input_attrs' => array( 'min' => 0, 'max' => 32, 'step' => 1 ) ) );

	// Typografie.
	$fonts = array(
		'Plus Jakarta Sans' => 'Plus Jakarta Sans',
		'Inter'             => 'Inter',
		'Poppins'           => 'Poppins',
		'Montserrat'        => 'Montserrat',
		'Manrope'           => 'Manrope',
		'DM Sans'           => 'DM Sans',
		'Outfit'            => 'Outfit',
		'Open Sans'         => 'Open Sans',
	);
	$add( 'blueline_font_heading', 'blueline_type', __( 'Lettertype koppen', 'blueline' ), 'select', 'blueline_sanitize_select', array( 'choices' => $fonts ) );
	$add( 'blueline_font_body', 'blueline_type', __( 'Lettertype tekst', 'blueline' ), 'select', 'blueline_sanitize_select', array( 'choices' => $fonts ) );

	// Header.
	$add( 'blueline_topbar_show', 'blueline_header', __( 'Topbalk tonen', 'blueline' ), 'checkbox', 'blueline_sanitize_checkbox' );
	$add( 'blueline_topbar_text', 'blueline_header', __( 'Topbalk tekst', 'blueline' ) );
	$add( 'blueline_topbar_phone', 'blueline_header', __( 'Telefoonnummer', 'blueline' ) );
	$add( 'blueline_topbar_email', 'blueline_header', __( 'E-mailadres', 'blueline' ), 'email', 'sanitize_email' );
	$add( 'blueline_header_cta_text', 'blueline_header', __( 'Knoptekst header', 'blueline' ) );
	$add( 'blueline_header_cta_url', 'blueline_header', __( 'Knoplink header', 'blueline' ), 'url', 'esc_url_raw' );

	// Hero.
	$add( 'blueline_hero_show', 'blueline_hero', __( 'Sectie tonen', 'blueline' ), 'checkbox', 'blueline_sanitize_checkbox' );
	$add( 'blueline_hero_eyebrow', 'blueline_hero', __( 'Label boven titel', 'blueline' ) );
	$add( 'blueline_hero_title', 'blueline_hero', __( 'Titel', 'blueline' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'blueline_hero_text', 'blueline_hero', __( 'Tekst', 'blueline' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'blueline_hero_btn1_text', 'blueline_hero', __( 'Knop 1 tekst', 'blueline' ) );
	$add( 'blueline_hero_btn1_url', 'blueline_hero', __( 'Knop 1 link', 'blueline' ), 'url', 'esc_url_raw' );
	$add( 'blueline_hero_btn2_text', 'blueline_hero', __( 'Knop 2 tekst', 'blueline' ) );
	$add( 'blueline_hero_btn2_url', 'blueline_hero', __( 'Knop 2 link', 'blueline' ), 'url', 'esc_url_raw' );
	$wp_customize->add_setting( 'blueline_hero_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'blueline_hero_image', array( 'label' => __( 'Achtergrondafbeelding', 'blueline' ), 'section' => 'blueline_hero' ) ) );
	$add( 'blueline_hero_overlay', 'blueline_hero', __( 'Dekking blauwe overlay (%)', 'blueline' ), 'range', 'absint', array( 'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ) ) );

	// Diensten.
	$add( 'blueline_services_show', 'blueline_services', __( 'Sectie tonen', 'blueline' ), 'checkbox', 'blueline_sanitize_checkbox' );
	$add( 'blueline_services_title', 'blueline_services', __( 'Titel', 'blueline' ) );
	$add( 'blueline_services_intro', 'blueline_services', __( 'Introductie', 'blueline' ), 'textarea', 'sanitize_textarea_field' );
	foreach ( $rep['services'] as $i => $item ) {
		/* translators: %d: nummer van de dienst */
		$prefix = sprintf( __( 'Dienst %d', 'blueline' ), $i );
		$add( "blueline_service{$i}_icon", 'blueline_services', $prefix . ' – ' . __( 'icoon', 'blueline' ), 'select', 'blueline_sanitize_select', array( 'choices' => blueline_icon_choices(), 'default' => $item['icon'] ) );
		$add( "blueline_service{$i}_title", 'blueline_services', $prefix . ' – ' . __( 'titel', 'blueline' ), 'text', 'sanitize_text_field', array( 'default' => $item['title'] ) );
		$add( "blueline_service{$i}_text", 'blueline_services', $prefix . ' – ' . __( 'tekst', 'blueline' ), 'textarea', 'sanitize_textarea_field', array( 'default' => $item['text'] ) );
		$add( "blueline_service{$i}_url", 'blueline_services', $prefix . ' – ' . __( 'link', 'blueline' ), 'url', 'esc_url_raw', array( 'default' => '' ) );
	}

	// Over ons.
	$add( 'blueline_about_show', 'blueline_about', __( 'Sectie tonen', 'blueline' ), 'checkbox', 'blueline_sanitize_checkbox' );
	$add( 'blueline_about_eyebrow', 'blueline_about', __( 'Label boven titel', 'blueline' ) );
	$add( 'blueline_about_title', 'blueline_about', __( 'Titel', 'blueline' ) );
	$add( 'blueline_about_text', 'blueline_about', __( 'Tekst', 'blueline' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'blueline_about_points', 'blueline_about', __( 'Voordelen (één per regel)', 'blueline' ), 'textarea', 'sanitize_textarea_field' );
	$wp_customize->add_setting( 'blueline_about_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'blueline_about_image', array( 'label' => __( 'Afbeelding', 'blueline' ), 'section' => 'blueline_about' ) ) );
	$add( 'blueline_about_btn_text', 'blueline_about', __( 'Knoptekst', 'blueline' ) );
	$add( 'blueline_about_btn_url', 'blueline_about', __( 'Knoplink', 'blueline' ), 'url', 'esc_url_raw' );

	// Cijfers.
	$add( 'blueline_stats_show', 'blueline_stats', __( 'Sectie tonen', 'blueline' ), 'checkbox', 'blueline_sanitize_checkbox' );
	foreach ( $rep['stats'] as $i => $item ) {
		/* translators: %d: nummer van het cijfer */
		$prefix = sprintf( __( 'Cijfer %d', 'blueline' ), $i );
		$add( "blueline_stat{$i}_number", 'blueline_stats', $prefix . ' – ' . __( 'getal', 'blueline' ), 'number', 'absint', array( 'default' => $item['number'] ) );
		$add( "blueline_stat{$i}_suffix", 'blueline_stats', $prefix . ' – ' . __( 'achtervoegsel (+, %)', 'blueline' ), 'text', 'sanitize_text_field', array( 'default' => $item['suffix'] ) );
		$add( "blueline_stat{$i}_label", 'blueline_stats', $prefix . ' – ' . __( 'omschrijving', 'blueline' ), 'text', 'sanitize_text_field', array( 'default' => $item['label'] ) );
	}

	// Nieuws.
	$add( 'blueline_news_show', 'blueline_news', __( 'Sectie tonen', 'blueline' ), 'checkbox', 'blueline_sanitize_checkbox' );
	$add( 'blueline_news_title', 'blueline_news', __( 'Titel', 'blueline' ) );
	$add( 'blueline_news_count', 'blueline_news', __( 'Aantal berichten', 'blueline' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1, 'max' => 9 ) ) );

	// CTA.
	$add( 'blueline_cta_show', 'blueline_cta', __( 'Sectie tonen', 'blueline' ), 'checkbox', 'blueline_sanitize_checkbox' );
	$add( 'blueline_cta_title', 'blueline_cta', __( 'Titel', 'blueline' ) );
	$add( 'blueline_cta_text', 'blueline_cta', __( 'Tekst', 'blueline' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'blueline_cta_btn_text', 'blueline_cta', __( 'Knoptekst', 'blueline' ) );
	$add( 'blueline_cta_btn_url', 'blueline_cta', __( 'Knoplink', 'blueline' ), 'url', 'esc_url_raw' );

	// Footer.
	$add( 'blueline_footer_about', 'blueline_footer', __( 'Korte omschrijving', 'blueline' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'blueline_footer_address', 'blueline_footer', __( 'Adres', 'blueline' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'blueline_footer_copy', 'blueline_footer', __( 'Copyrighttekst (leeg = automatisch)', 'blueline' ) );
	$add( 'blueline_social_linkedin', 'blueline_footer', 'LinkedIn URL', 'url', 'esc_url_raw' );
	$add( 'blueline_social_facebook', 'blueline_footer', 'Facebook URL', 'url', 'esc_url_raw' );
	$add( 'blueline_social_instagram', 'blueline_footer', 'Instagram URL', 'url', 'esc_url_raw' );
	$add( 'blueline_social_x', 'blueline_footer', 'X (Twitter) URL', 'url', 'esc_url_raw' );
}
add_action( 'customize_register', 'blueline_customize_register' );

/**
 * CSS-variabelen uit de Customizer.
 *
 * @return string
 */
function blueline_customizer_css() {
	$vars = array(
		'--bl-primary'   => blueline_mod( 'blueline_color_primary' ),
		'--bl-secondary' => blueline_mod( 'blueline_color_secondary' ),
		'--bl-dark'      => blueline_mod( 'blueline_color_dark' ),
		'--bl-light'     => blueline_mod( 'blueline_color_light' ),
		'--bl-text'      => blueline_mod( 'blueline_color_text' ),
		'--bl-radius'    => absint( blueline_mod( 'blueline_radius' ) ) . 'px',
		'--bl-overlay'   => min( 100, absint( blueline_mod( 'blueline_hero_overlay' ) ) ) / 100,
		'--bl-font-head' => '"' . blueline_mod( 'blueline_font_heading' ) . '", system-ui, sans-serif',
		'--bl-font-body' => '"' . blueline_mod( 'blueline_font_body' ) . '", system-ui, sans-serif',
	);

	$css = ':root{';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . wp_strip_all_tags( (string) $value ) . ';';
	}
	return $css . '}';
}
