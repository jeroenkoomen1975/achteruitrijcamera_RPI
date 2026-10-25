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
		// Kleuren (afgeleid van het koomen.it-logo).
		'koomenit_color_primary'   => '#1d63ed',
		'koomenit_color_secondary' => '#3b82f6',
		'koomenit_color_node'      => '#94a3b8',
		'koomenit_color_dark'      => '#1e2430',
		'koomenit_color_ink'       => '#0f172a',
		'koomenit_color_light'     => '#f1f5f9',
		'koomenit_color_text'      => '#334155',
		'koomenit_radius'          => 12,

		// Header.
		'koomenit_header_cta_text' => __( 'Contact', 'koomenit' ),
		'koomenit_header_cta_url'  => '#contact',
		'koomenit_topbar_show'     => false,
		'koomenit_topbar_phone'    => '',
		'koomenit_topbar_email'    => 'info@koomen.it',
		'koomenit_topbar_text'     => __( 'Embedded projecten, websites en software', 'koomenit' ),

		// Hero.
		'koomenit_hero_show'       => true,
		'koomenit_hero_eyebrow'    => __( 'esp32 · arduino · raspberry pi · web', 'koomenit' ),
		'koomenit_hero_title'      => __( 'Waar hardware en software samenkomen', 'koomenit' ),
		'koomenit_hero_text'       => __( 'Een portfolio van embedded projecten, websites en software op ESP32, Arduino en Raspberry Pi. Met code, schema\'s en de keuzes achter elk ontwerp.', 'koomenit' ),
		'koomenit_hero_btn1_text'  => __( 'Bekijk projecten', 'koomenit' ),
		'koomenit_hero_btn1_url'   => '#projecten',
		'koomenit_hero_btn2_text'  => __( 'Over koomen.it', 'koomenit' ),
		'koomenit_hero_btn2_url'   => '#over',
		'koomenit_hero_image'      => '',
		'koomenit_hero_overlay'    => 85,
		'koomenit_hero_visual'     => 'code',
		'koomenit_hero_code_file'  => 'parkeersensor/logica.py',
		'koomenit_hero_code_lang'  => 'python',
		'koomenit_hero_code_start' => 15,
		'koomenit_hero_code'       => "def bepaal_zone(afstand_cm, oranje_grens_cm, rood_grens_cm):\n    \"\"\"Geef de zone terug voor een gemeten afstand.\"\"\"\n    if afstand_cm is None or afstand_cm > oranje_grens_cm:\n        return Zone.GROEN\n    if afstand_cm > rood_grens_cm:\n        return Zone.ORANJE\n    return Zone.ROOD",
		'koomenit_hero_code_env'   => 'Raspberry Pi · Python 3',
		'koomenit_hero_code_status' => __( '13 tests geslaagd', 'koomenit' ),
		'koomenit_hero_code_link_text' => 'Achteruitrij-parkeersensor',
		'koomenit_hero_code_link_url'  => '',
		'koomenit_hero_code_animate'   => true,
		'koomenit_hero_terminal_title' => 'esp32-weerstation · 115200 baud',
		'koomenit_hero_terminal'   => "> wifi verbonden: 192.168.1.42\n> BME280 gevonden op 0x76\n[12:04:01] temp 21.4 °C  vocht 58 %\n[12:04:11] temp 21.5 °C  vocht 57 %\n> MQTT publish: huis/woonkamer ✓",

		// Platforms.
		'koomenit_services_show'   => true,
		'koomenit_services_title'  => __( 'Platforms', 'koomenit' ),
		'koomenit_services_intro'  => __( 'Microcontrollers, single-board computers en de software eromheen.', 'koomenit' ),

		// Projecten.
		'koomenit_projects_show'   => true,
		'koomenit_projects_eyebrow' => __( 'Projecten', 'koomenit' ),
		'koomenit_projects_title'  => __( 'Recente projecten', 'koomenit' ),
		'koomenit_projects_intro'  => __( 'Een selectie van recente projecten. Filter op platform om snel te vinden wat je zoekt.', 'koomenit' ),
		'koomenit_projects_count'  => 3,
		'koomenit_projects_filter' => true,

		// Websites.
		'koomenit_websites_show'    => true,
		'koomenit_websites_eyebrow' => __( 'Websites', 'koomenit' ),
		'koomenit_websites_title'   => __( 'Gebouwde websites', 'koomenit' ),
		'koomenit_websites_intro'   => __( 'Bedrijfswebsites, webshops, webapplicaties en webinterfaces voor hardware. Filter op type.', 'koomenit' ),
		'koomenit_websites_count'   => 3,
		'koomenit_websites_filter'  => true,

		// Over.
		'koomenit_about_show'      => true,
		'koomenit_about_eyebrow'   => __( 'Over koomen.it', 'koomenit' ),
		'koomenit_about_title'     => __( 'Van breadboard tot werkend product', 'koomenit' ),
		'koomenit_about_text'      => __( 'koomen.it combineert hardware en software: sensoren uitlezen, data verwerken en het resultaat tonen op een display of in de browser. Elk project is gedocumenteerd zodat het na te bouwen is.', 'koomenit' ),
		'koomenit_about_points'    => __( "C++ voor ESP32 en Arduino\nPython op de Raspberry Pi\nWebsites, webinterfaces en dashboards\nSensoren, displays en actuatoren", 'koomenit' ),
		'koomenit_about_image'     => '',
		'koomenit_about_btn_text'  => __( 'Code op GitHub', 'koomenit' ),
		'koomenit_about_btn_url'   => 'https://github.com/jeroenkoomen1975',

		// Cijfers.
		'koomenit_stats_show'      => true,

		// Nieuws.
		'koomenit_news_show'       => true,
		'koomenit_news_title'      => __( 'Blog', 'koomenit' ),
		'koomenit_news_count'      => 3,

		// Blogpagina.
		'koomenit_blog_intro'        => __( 'Artikelen over ESP32, Arduino, Raspberry Pi en webontwikkeling.', 'koomenit' ),
		'koomenit_blog_layout'       => 'grid',
		'koomenit_blog_featured'     => true,
		'koomenit_blog_filters'      => true,
		'koomenit_blog_reading_time' => true,
		'koomenit_blog_tags'         => true,
		'koomenit_blog_sidebar'      => true,
		'koomenit_blog_toc'          => true,
		'koomenit_blog_related'      => true,

		// CTA.
		'koomenit_cta_show'        => true,
		'koomenit_cta_title'       => __( 'Vragen of samenwerken?', 'koomenit' ),
		'koomenit_cta_text'        => __( 'Voor vragen over een project, samenwerking of maatwerk: neem gerust contact op.', 'koomenit' ),
		'koomenit_cta_btn_text'    => __( 'Neem contact op', 'koomenit' ),
		'koomenit_cta_btn_url'     => 'mailto:info@koomen.it',

		// Footer.
		'koomenit_footer_about'    => __( 'Portfolio met embedded projecten, websites en software op ESP32, Arduino en Raspberry Pi.', 'koomenit' ),
		'koomenit_footer_address'  => '',
		'koomenit_footer_copy'     => '',
		'koomenit_social_github'   => 'https://github.com/jeroenkoomen1975',
		'koomenit_social_linkedin' => '',
		'koomenit_social_youtube'  => '',
		'koomenit_social_instagram' => '',

		// Typografie.
		'koomenit_font_heading'    => 'Inter',
		'koomenit_font_body'       => 'Inter',
	);
}

/**
 * Standaarden voor de herhaalbare items (platforms en cijfers).
 * Een item met een lege titel/omschrijving wordt niet getoond.
 *
 * @return array
 */
function koomenit_repeat_defaults() {
	return array(
		'services' => array(
			1 => array( 'icon' => 'wifi', 'title' => 'ESP32', 'text' => __( 'Wifi en Bluetooth aan boord: ideaal voor sensoren die hun metingen naar een webinterface of MQTT sturen.', 'koomenit' ) ),
			2 => array( 'icon' => 'chip', 'title' => 'Arduino', 'text' => __( 'Snel prototypen met sensoren, motoren en relais. Eenvoudig, betrouwbaar en zuinig.', 'koomenit' ) ),
			3 => array( 'icon' => 'cpu', 'title' => 'Raspberry Pi', 'text' => __( 'Een complete Linux-computer voor Python, displays, camera\'s en lokale webservers.', 'koomenit' ) ),
			4 => array( 'icon' => 'monitor', 'title' => __( 'Web & software', 'koomenit' ), 'text' => __( 'Webinterfaces, dashboards en programma\'s die de hardware aansturen en de data laten zien.', 'koomenit' ) ),
		),
		'stats'    => array(
			1 => array( 'source' => 'projects', 'number' => '', 'suffix' => '', 'label' => __( 'Projecten', 'koomenit' ) ),
			2 => array( 'source' => 'platforms', 'number' => '', 'suffix' => '', 'label' => __( 'Platforms', 'koomenit' ) ),
			3 => array( 'source' => 'techniques', 'number' => '', 'suffix' => '', 'label' => __( 'Technieken & sensoren', 'koomenit' ) ),
			4 => array( 'source' => 'websites', 'number' => '', 'suffix' => '', 'label' => __( 'Websites', 'koomenit' ) ),
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
 * Beschikbare iconen voor de platformkaarten.
 *
 * @return array
 */
function koomenit_icon_choices() {
	return array(
		'wifi'        => __( 'Wifi (ESP32)', 'koomenit' ),
		'chip'        => __( 'Chip (Arduino)', 'koomenit' ),
		'cpu'         => __( 'Processor (Raspberry Pi)', 'koomenit' ),
		'monitor'     => __( 'Scherm (web)', 'koomenit' ),
		'terminal'    => __( 'Terminal (software)', 'koomenit' ),
		'thermometer' => __( 'Thermometer (sensor)', 'koomenit' ),
		'code'        => __( 'Code', 'koomenit' ),
		'network'     => __( 'Netwerk', 'koomenit' ),
		'server'      => __( 'Server', 'koomenit' ),
		'shield'      => __( 'Beveiliging', 'koomenit' ),
		'lightbulb'   => __( 'Lamp', 'koomenit' ),
		'graduation'  => __( 'Opleiding', 'koomenit' ),
	);
}

/**
 * Bronnen voor de cijfers.
 *
 * @return array
 */
function koomenit_stat_sources() {
	return array(
		'projects'   => __( 'Automatisch: aantal projecten', 'koomenit' ),
		'platforms'  => __( 'Automatisch: aantal platforms', 'koomenit' ),
		'techniques' => __( 'Automatisch: aantal technieken', 'koomenit' ),
		'websites'   => __( 'Automatisch: aantal websites', 'koomenit' ),
		'manual'     => __( 'Zelf invullen', 'koomenit' ),
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
 * Code uit het codevenster opschonen: regels en inspringing blijven staan,
 * de tekst wordt bij het tonen geëscapet.
 *
 * @param string $value Waarde.
 * @return string
 */
function koomenit_sanitize_code( $value ) {
	$value = str_replace( array( "\r\n", "\r", "\0" ), array( "\n", "\n", '' ), (string) $value );
	return current_user_can( 'unfiltered_html' ) ? $value : wp_kses( $value, array() );
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
		'koomenit_services' => __( 'Homepage: Platforms', 'koomenit' ),
		'koomenit_projects' => __( 'Homepage: Projecten', 'koomenit' ),
		'koomenit_websites' => __( 'Homepage: Websites', 'koomenit' ),
		'koomenit_about'    => __( 'Homepage: Over', 'koomenit' ),
		'koomenit_stats'    => __( 'Homepage: Cijfers', 'koomenit' ),
		'koomenit_news'     => __( 'Homepage: Blog', 'koomenit' ),
		'koomenit_cta'      => __( 'Homepage: Call-to-action', 'koomenit' ),
		'koomenit_blog'     => __( 'Blogpagina en berichten', 'koomenit' ),
		'koomenit_footer'   => __( 'Footer & social media', 'koomenit' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'koomenit_panel' ) );
	}

	// Kleuren.
	$colors = array(
		'koomenit_color_primary'   => __( 'Primair blauw', 'koomenit' ),
		'koomenit_color_secondary' => __( 'Lichtblauw accent', 'koomenit' ),
		'koomenit_color_node'      => __( 'Netwerk-knooppunten', 'koomenit' ),
		'koomenit_color_dark'      => __( 'Donkere achtergrond (hero, footer)', 'koomenit' ),
		'koomenit_color_ink'       => __( 'Koppen', 'koomenit' ),
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
	$add(
		'koomenit_hero_visual',
		'koomenit_hero',
		__( 'Illustratie rechts', 'koomenit' ),
		'select',
		'koomenit_sanitize_select',
		array(
			'choices' => array(
				'code'     => __( 'Codevenster', 'koomenit' ),
				'terminal' => __( 'Seriële monitor', 'koomenit' ),
				'logo'     => __( 'Logo', 'koomenit' ),
				'none'     => __( 'Geen', 'koomenit' ),
			),
		)
	);
	$add( 'koomenit_hero_code_file', 'koomenit_hero', __( 'Codevenster: bestandsnaam', 'koomenit' ) );
	$add(
		'koomenit_hero_code_lang',
		'koomenit_hero',
		__( 'Codevenster: taal', 'koomenit' ),
		'select',
		'koomenit_sanitize_select',
		array(
			'choices' => array(
				'python' => 'Python',
				'cpp'    => 'C++ / Arduino',
			),
		)
	);
	$add( 'koomenit_hero_code', 'koomenit_hero', __( 'Codevenster: code', 'koomenit' ), 'textarea', 'koomenit_sanitize_code', array( 'input_attrs' => array( 'style' => 'font-family:monospace;min-height:180px' ) ) );
	$add( 'koomenit_hero_code_start', 'koomenit_hero', __( 'Codevenster: eerste regelnummer', 'koomenit' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1 ) ) );
	$add( 'koomenit_hero_code_env', 'koomenit_hero', __( 'Codevenster: statusbalk links', 'koomenit' ) );
	$add( 'koomenit_hero_code_status', 'koomenit_hero', __( 'Codevenster: statusbalk rechts (groen)', 'koomenit' ) );
	$add( 'koomenit_hero_code_link_text', 'koomenit_hero', __( 'Codevenster: project waar de code uit komt', 'koomenit' ) );
	$add(
		'koomenit_hero_code_link_url',
		'koomenit_hero',
		__( 'Codevenster: link naar dat project', 'koomenit' ),
		'url',
		'esc_url_raw',
		array( 'description' => __( 'Leeg = automatisch naar het project met deze titel (als dat bestaat).', 'koomenit' ) )
	);
	$add( 'koomenit_hero_code_animate', 'koomenit_hero', __( 'Codevenster: code laten intypen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_hero_terminal_title', 'koomenit_hero', __( 'Seriële monitor: titel', 'koomenit' ) );
	$add( 'koomenit_hero_terminal', 'koomenit_hero', __( 'Seriële monitor: regels (één per regel)', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$wp_customize->add_setting( 'koomenit_hero_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'koomenit_hero_image', array( 'label' => __( 'Achtergrondafbeelding', 'koomenit' ), 'section' => 'koomenit_hero' ) ) );
	$add( 'koomenit_hero_overlay', 'koomenit_hero', __( 'Dekking donkere overlay (%)', 'koomenit' ), 'range', 'absint', array( 'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ) ) );

	// Platforms.
	$add( 'koomenit_services_show', 'koomenit_services', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_services_title', 'koomenit_services', __( 'Titel', 'koomenit' ) );
	$add( 'koomenit_services_intro', 'koomenit_services', __( 'Introductie', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	foreach ( $rep['services'] as $i => $item ) {
		/* translators: %d: nummer van de kaart */
		$prefix = sprintf( __( 'Kaart %d', 'koomenit' ), $i );
		$add( "koomenit_service{$i}_icon", 'koomenit_services', $prefix . ' – ' . __( 'icoon', 'koomenit' ), 'select', 'koomenit_sanitize_select', array( 'choices' => koomenit_icon_choices(), 'default' => $item['icon'] ) );
		$add( "koomenit_service{$i}_title", 'koomenit_services', $prefix . ' – ' . __( 'titel', 'koomenit' ), 'text', 'sanitize_text_field', array( 'default' => $item['title'] ) );
		$add( "koomenit_service{$i}_text", 'koomenit_services', $prefix . ' – ' . __( 'tekst', 'koomenit' ), 'textarea', 'sanitize_textarea_field', array( 'default' => $item['text'] ) );
		$add(
			"koomenit_service{$i}_url",
			'koomenit_services',
			$prefix . ' – ' . __( 'link', 'koomenit' ),
			'url',
			'esc_url_raw',
			array(
				'default'     => '',
				'description' => __( 'Leeg = automatisch naar het platform met dezelfde naam (als dat bestaat).', 'koomenit' ),
			)
		);
	}

	// Projecten.
	$add( 'koomenit_projects_show', 'koomenit_projects', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_projects_eyebrow', 'koomenit_projects', __( 'Label boven titel', 'koomenit' ) );
	$add( 'koomenit_projects_title', 'koomenit_projects', __( 'Titel', 'koomenit' ) );
	$add( 'koomenit_projects_intro', 'koomenit_projects', __( 'Introductie', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'koomenit_projects_count', 'koomenit_projects', __( 'Aantal projecten', 'koomenit' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1, 'max' => 12 ) ) );
	$add( 'koomenit_projects_filter', 'koomenit_projects', __( 'Filterknoppen per platform tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );

	// Websites.
	$add( 'koomenit_websites_show', 'koomenit_websites', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_websites_eyebrow', 'koomenit_websites', __( 'Label boven titel', 'koomenit' ) );
	$add( 'koomenit_websites_title', 'koomenit_websites', __( 'Titel', 'koomenit' ) );
	$add( 'koomenit_websites_intro', 'koomenit_websites', __( 'Introductie', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'koomenit_websites_count', 'koomenit_websites', __( 'Aantal websites', 'koomenit' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1, 'max' => 12 ) ) );
	$add( 'koomenit_websites_filter', 'koomenit_websites', __( 'Filterknoppen per websitetype tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );

	// Over.
	$add( 'koomenit_about_show', 'koomenit_about', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_about_eyebrow', 'koomenit_about', __( 'Label boven titel', 'koomenit' ) );
	$add( 'koomenit_about_title', 'koomenit_about', __( 'Titel', 'koomenit' ) );
	$add( 'koomenit_about_text', 'koomenit_about', __( 'Tekst', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'koomenit_about_points', 'koomenit_about', __( 'Expertise (één per regel)', 'koomenit' ), 'textarea', 'sanitize_textarea_field' );
	$wp_customize->add_setting( 'koomenit_about_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'koomenit_about_image', array( 'label' => __( 'Foto', 'koomenit' ), 'section' => 'koomenit_about' ) ) );
	$add( 'koomenit_about_btn_text', 'koomenit_about', __( 'Knoptekst', 'koomenit' ) );
	$add( 'koomenit_about_btn_url', 'koomenit_about', __( 'Knoplink', 'koomenit' ), 'url', 'esc_url_raw' );

	// Cijfers.
	$add( 'koomenit_stats_show', 'koomenit_stats', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	foreach ( $rep['stats'] as $i => $item ) {
		/* translators: %d: nummer van het cijfer */
		$prefix = sprintf( __( 'Cijfer %d', 'koomenit' ), $i );
		$add( "koomenit_stat{$i}_source", 'koomenit_stats', $prefix . ' – ' . __( 'bron', 'koomenit' ), 'select', 'koomenit_sanitize_select', array( 'choices' => koomenit_stat_sources(), 'default' => $item['source'] ) );
		$add( "koomenit_stat{$i}_number", 'koomenit_stats', $prefix . ' – ' . __( 'getal (bij "zelf invullen")', 'koomenit' ), 'number', 'absint', array( 'default' => $item['number'] ) );
		$add( "koomenit_stat{$i}_suffix", 'koomenit_stats', $prefix . ' – ' . __( 'achtervoegsel (+, %)', 'koomenit' ), 'text', 'sanitize_text_field', array( 'default' => $item['suffix'] ) );
		$add( "koomenit_stat{$i}_label", 'koomenit_stats', $prefix . ' – ' . __( 'omschrijving (leeg = verbergen)', 'koomenit' ), 'text', 'sanitize_text_field', array( 'default' => $item['label'] ) );
	}

	// Blog.
	$add( 'koomenit_news_show', 'koomenit_news', __( 'Sectie tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_news_title', 'koomenit_news', __( 'Titel', 'koomenit' ) );
	$add( 'koomenit_news_count', 'koomenit_news', __( 'Aantal berichten', 'koomenit' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1, 'max' => 9 ) ) );

	// Blogpagina.
	$add( 'koomenit_blog_intro', 'koomenit_blog', __( 'Introductie onder de titel', 'koomenit' ), 'textarea', 'sanitize_textarea_field', array( 'description' => __( 'Wordt gebruikt als de pagina Blog zelf geen samenvatting heeft.', 'koomenit' ) ) );
	$add(
		'koomenit_blog_layout',
		'koomenit_blog',
		__( 'Weergave van het overzicht', 'koomenit' ),
		'select',
		'koomenit_sanitize_select',
		array(
			'choices' => array(
				'grid' => __( 'Raster met kaarten', 'koomenit' ),
				'list' => __( 'Lijst met afbeelding links', 'koomenit' ),
			),
		)
	);
	$add( 'koomenit_blog_featured', 'koomenit_blog', __( 'Nieuwste bericht groot bovenaan', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_blog_filters', 'koomenit_blog', __( 'Filterknoppen per categorie', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_blog_reading_time', 'koomenit_blog', __( 'Leestijd tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_blog_tags', 'koomenit_blog', __( 'Tags op de kaarten tonen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_blog_sidebar', 'koomenit_blog', __( 'Zijbalk tonen (zoeken, categorieën, GitHub of eigen widgets)', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_blog_toc', 'koomenit_blog', __( 'Inhoudsopgave bij berichten met 3 of meer tussenkoppen', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );
	$add( 'koomenit_blog_related', 'koomenit_blog', __( '"Meer berichten" onder een bericht', 'koomenit' ), 'checkbox', 'koomenit_sanitize_checkbox' );

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
	$add( 'koomenit_social_github', 'koomenit_footer', 'GitHub URL', 'url', 'esc_url_raw' );
	$add( 'koomenit_social_linkedin', 'koomenit_footer', 'LinkedIn URL', 'url', 'esc_url_raw' );
	$add( 'koomenit_social_youtube', 'koomenit_footer', 'YouTube URL', 'url', 'esc_url_raw' );
	$add( 'koomenit_social_instagram', 'koomenit_footer', 'Instagram URL', 'url', 'esc_url_raw' );
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
		'--bl-node'      => koomenit_mod( 'koomenit_color_node' ),
		'--bl-dark'      => koomenit_mod( 'koomenit_color_dark' ),
		'--bl-ink'       => koomenit_mod( 'koomenit_color_ink' ),
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
