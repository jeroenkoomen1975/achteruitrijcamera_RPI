<?php
/**
 * Customizer-instellingen: kleuren, typografie en alle homepage-secties.
 *
 * @package Jroen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Standaardwaarden voor alle instellingen.
 *
 * @return array
 */
function jroen_defaults() {
	return array(
		// Kleuren (uit de merkkit van jroen.nl).
		'jroen_color_primary'   => '#f56b1f',
		'jroen_color_secondary' => '#ff8a47',
		'jroen_color_node'      => '#8b95a1',
		'jroen_color_dark'      => '#0d1d34',
		'jroen_color_ink'       => '#2b333a',
		'jroen_color_light'     => '#f4f3ee',
		'jroen_color_text'      => '#3b434b',
		'jroen_radius'          => 12,

		// Header.
		'jroen_header_cta_text' => __( 'Bekijk websites →', 'jroen' ),
		'jroen_header_cta_url'  => '/websites/',
		'jroen_topbar_show'     => true,
		'jroen_topbar_phone'    => '',
		'jroen_topbar_email'    => 'info@jroen.nl',
		'jroen_topbar_text'     => __( 'Hardware • Software • Onderwijs', 'jroen' ),
		'jroen_topbar_right'    => 'ESP32 • Arduino • Raspberry Pi • Web',

		// Hero.
		'jroen_hero_show'       => true,
		'jroen_hero_eyebrow'    => __( 'Hardware • Software • Onderwijs', 'jroen' ),
		'jroen_hero_title'      => __( 'De brug tussen *fysieke hardware* en het web.', 'jroen' ),
		'jroen_hero_text'       => __( 'Mijn specialiteit is het combineren van microcontrollers zoals de **ESP32**, **Arduino** en **Raspberry Pi** met webinterfaces en dashboards. Van sensoren uitlezen tot webapplicaties, met software in C++ en Python.', 'jroen' ),
		'jroen_hero_btn1_text'  => __( 'Bekijk projecten', 'jroen' ),
		'jroen_hero_btn1_url'   => '/projecten/',
		'jroen_hero_btn2_text'  => __( 'Bekijk mijn websites', 'jroen' ),
		'jroen_hero_btn2_url'   => '/websites/',
		'jroen_hero_tech_label' => __( 'Actieve technologieën & omgevingen', 'jroen' ),
		'jroen_hero_tech'       => 'ESP32, Arduino C++, Raspberry Pi, Python, WordPress',
		'jroen_hero_image'      => '',
		'jroen_hero_overlay'    => 85,
		'jroen_hero_visual'     => 'code',
		'jroen_hero_code_file'  => 'parkeersensor/logica.py',
		'jroen_hero_code_lang'  => 'python',
		'jroen_hero_code_start' => 15,
		'jroen_hero_code'       => "def bepaal_zone(afstand_cm, oranje_grens_cm, rood_grens_cm):\n    \"\"\"Geef de zone terug voor een gemeten afstand.\"\"\"\n    if afstand_cm is None or afstand_cm > oranje_grens_cm:\n        return Zone.GROEN\n    if afstand_cm > rood_grens_cm:\n        return Zone.ORANJE\n    return Zone.ROOD",
		'jroen_hero_code_env'   => 'Raspberry Pi · Python 3',
		'jroen_hero_code_status' => __( '13 tests geslaagd', 'jroen' ),
		'jroen_hero_code_link_text' => 'Achteruitrij-parkeersensor',
		'jroen_hero_code_link_url'  => '',
		'jroen_hero_code_animate'   => true,
		'jroen_hero_terminal_title' => 'esp32-weerstation · 115200 baud',
		'jroen_hero_terminal'   => "> wifi verbonden: 192.168.1.42\n> BME280 gevonden op 0x76\n[12:04:01] temp 21.4 °C  vocht 58 %\n[12:04:11] temp 21.5 °C  vocht 57 %\n> MQTT publish: huis/woonkamer ✓",

		// Platforms.
		'jroen_services_show'   => false,
		'jroen_services_title'  => __( 'Platforms', 'jroen' ),
		'jroen_services_intro'  => __( 'Microcontrollers, de Raspberry Pi en de websites en software eromheen.', 'jroen' ),

		// Projecten.
		'jroen_projects_show'   => false,
		'jroen_projects_eyebrow' => __( 'Projecten', 'jroen' ),
		'jroen_projects_title'  => __( 'Recente projecten', 'jroen' ),
		'jroen_projects_intro'  => __( 'Documentatie van mijn projecten, met de code op GitHub. Filter op platform.', 'jroen' ),
		'jroen_projects_count'  => 3,
		'jroen_projects_filter' => true,

		// Websites.
		'jroen_websites_show'    => false,
		'jroen_websites_eyebrow' => __( 'Websites', 'jroen' ),
		'jroen_websites_title'   => __( 'Al mijn websites', 'jroen' ),
		'jroen_websites_intro'   => __( 'Van bedrijfssites tot webshops en webinterfaces voor hardware. Filter op type.', 'jroen' ),
		'jroen_websites_count'   => 3,
		'jroen_websites_filter'  => true,

		// Telemetrie.
		'jroen_telemetry_show'    => true,
		'jroen_telemetry_eyebrow' => __( 'Hardware stream', 'jroen' ),
		'jroen_telemetry_title'   => __( 'ESP32 / Raspberry Pi telemetrie', 'jroen' ),
		'jroen_telemetry_url'     => '',
		'jroen_telemetry_interval' => 2,
		'jroen_telemetry_m1_label' => __( 'Temperatuur (BME280)', 'jroen' ),
		'jroen_telemetry_m1_unit'  => '°C',
		'jroen_telemetry_m1_key'   => 'temp',
		'jroen_telemetry_m2_label' => __( 'Luchtvochtigheid (BME280)', 'jroen' ),
		'jroen_telemetry_m2_unit'  => '% RH',
		'jroen_telemetry_m2_key'   => 'hum',
		'jroen_telemetry_m3_label' => __( 'CPU-belasting Pi', 'jroen' ),
		'jroen_telemetry_m3_unit'  => '%',
		'jroen_telemetry_m3_key'   => 'cpu',
		'jroen_telemetry_relay_label' => __( 'Relais GPIO 2', 'jroen' ),
		'jroen_telemetry_relay_key'   => 'relay',

		// Overzichtskaarten.
		'jroen_overview_show'        => true,
		'jroen_overview_p_badge'     => __( 'Projectencatalogus', 'jroen' ),
		'jroen_overview_p_title'     => __( 'Hardware- en makerprojecten', 'jroen' ),
		'jroen_overview_p_text'      => __( 'Concrete toepassingen met microcontrollers, sensoren en de Raspberry Pi, met code en documentatie.', 'jroen' ),
		'jroen_overview_w_badge'     => __( 'Websites', 'jroen' ),
		'jroen_overview_w_title'     => __( 'Al mijn websites en portalen', 'jroen' ),
		'jroen_overview_w_text'      => __( 'Browservoorbeelden van de websites en webapplicaties die ik bouw en beheer.', 'jroen' ),

		// Contact.
		'jroen_contact_show'      => true,
		'jroen_contact_eyebrow'   => __( 'Contact & samenwerking', 'jroen' ),
		'jroen_contact_title'     => __( 'Heb je een vraag, een idee of een project op maat?', 'jroen' ),
		'jroen_contact_text'      => __( 'Of het nu gaat om een sensorkoppeling met de ESP32, een webdashboard, een webapplicatie op maat of een educatief traject: ik denk graag met je mee.', 'jroen' ),
		'jroen_contact_sites'     => 'jroen.nl • roen.nl',
		'jroen_contact_region'    => __( 'Zaanstreek / Noord-Holland / op afstand', 'jroen' ),
		'jroen_contact_to'        => '',
		'jroen_contact_subjects'  => __( "Hardware / microcontrollers (ESP32, Arduino)\nWebontwikkeling / website op maat\nIT-workshops / onderwijs\nVrijblijvende vraag", 'jroen' ),

		// Donkere modus.
		'jroen_dark_toggle'       => true,
		'jroen_dark_default'      => 'auto',

		// Over.
		'jroen_about_show'      => true,
		'jroen_about_eyebrow'   => __( 'Over jroen.nl', 'jroen' ),
		'jroen_about_title'     => __( 'Persoonlijke website en het thuis van mijn bedrijf', 'jroen' ),
		'jroen_about_text'      => __( 'Ik werk met microcontrollers zoals de ESP32 en Arduino, en met de Raspberry Pi, en koppel die aan webinterfaces en dashboards. Denk aan sensoren uitlezen, data verwerken en de resultaten overzichtelijk tonen op een display of in je browser. Daarnaast bouw ik websites en webapplicaties, van bedrijfssites tot webshops en webinterfaces voor hardware.', 'jroen' ),
		'jroen_about_points'    => __( "C++ voor ESP32 en Arduino\nPython op de Raspberry Pi\nWebsites, webshops en webapplicaties\nWebinterfaces en dashboards voor hardware", 'jroen' ),
		'jroen_about_image'     => '',
		'jroen_about_btn_text'  => __( 'Code op GitHub', 'jroen' ),
		'jroen_about_btn_url'   => 'https://github.com/jeroenkoomen1975',

		// Cijfers.
		'jroen_stats_show'      => false,

		// Nieuws.
		'jroen_news_show'       => true,
		'jroen_news_title'      => __( 'Blog', 'jroen' ),
		'jroen_news_count'      => 3,

		// Blogpagina.
		'jroen_blog_intro'        => __( 'Blogs en documentatie van mijn projecten. Zo kun je zien hoe ik werk en kun je er misschien zelf iets van leren.', 'jroen' ),
		'jroen_blog_layout'       => 'grid',
		'jroen_blog_featured'     => true,
		'jroen_blog_filters'      => true,
		'jroen_blog_reading_time' => true,
		'jroen_blog_tags'         => true,
		'jroen_blog_sidebar'      => true,
		'jroen_blog_toc'          => true,
		'jroen_blog_related'      => true,

		// CTA.
		'jroen_cta_show'        => false,
		'jroen_cta_title'       => __( 'Een vraag, een idee of een project op maat?', 'jroen' ),
		'jroen_cta_text'        => __( 'Neem gerust contact op. Ik denk graag met je mee.', 'jroen' ),
		'jroen_cta_btn_text'    => __( 'Neem contact op', 'jroen' ),
		'jroen_cta_btn_url'     => 'mailto:info@jroen.nl',

		// Footer.
		'jroen_footer_about'    => __( 'Persoonlijke website en het thuis van mijn bedrijf. Hardware, software en web.', 'jroen' ),
		'jroen_footer_address'  => '',
		'jroen_footer_note'     => __( 'Bereikbaar via jroen.nl en roen.nl', 'jroen' ),
		'jroen_footer_copy'     => '',
		'jroen_social_github'   => 'https://github.com/jeroenkoomen1975',
		'jroen_social_linkedin' => '',
		'jroen_social_youtube'  => '',
		'jroen_social_instagram' => '',

		// Typografie.
		'jroen_font_heading'    => 'Inter',
		'jroen_font_body'       => 'Inter',
	);
}

/**
 * Standaarden voor de herhaalbare items (platforms en cijfers).
 * Een item met een lege titel/omschrijving wordt niet getoond.
 *
 * @return array
 */
function jroen_repeat_defaults() {
	return array(
		'services' => array(
			1 => array( 'icon' => 'wifi', 'title' => 'ESP32', 'text' => __( 'Wifi en Bluetooth aan boord: ideaal voor sensoren die hun metingen naar een webinterface of MQTT sturen.', 'jroen' ) ),
			2 => array( 'icon' => 'chip', 'title' => 'Arduino', 'text' => __( 'Snel prototypen met sensoren, motoren en relais. Eenvoudig, betrouwbaar en zuinig.', 'jroen' ) ),
			3 => array( 'icon' => 'cpu', 'title' => 'Raspberry Pi', 'text' => __( 'Een complete Linux-computer voor Python, displays, camera\'s en lokale webservers.', 'jroen' ) ),
			4 => array( 'icon' => 'monitor', 'title' => __( 'Web & software', 'jroen' ), 'text' => __( 'Webinterfaces, dashboards en programma\'s die de hardware aansturen en de data laten zien.', 'jroen' ) ),
		),
		'stats'    => array(
			1 => array( 'source' => 'projects', 'number' => '', 'suffix' => '', 'label' => __( 'Projecten', 'jroen' ) ),
			2 => array( 'source' => 'platforms', 'number' => '', 'suffix' => '', 'label' => __( 'Platforms', 'jroen' ) ),
			3 => array( 'source' => 'techniques', 'number' => '', 'suffix' => '', 'label' => __( 'Technieken & sensoren', 'jroen' ) ),
			4 => array( 'source' => 'websites', 'number' => '', 'suffix' => '', 'label' => __( 'Websites', 'jroen' ) ),
		),
	);
}

/**
 * Een thema-instelling ophalen met de juiste standaardwaarde.
 *
 * @param string $key Instellingsnaam.
 * @return mixed
 */
function jroen_mod( $key ) {
	$defaults = jroen_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Beschikbare iconen voor de platformkaarten.
 *
 * @return array
 */
function jroen_icon_choices() {
	return array(
		'wifi'        => __( 'Wifi (ESP32)', 'jroen' ),
		'chip'        => __( 'Chip (Arduino)', 'jroen' ),
		'cpu'         => __( 'Processor (Raspberry Pi)', 'jroen' ),
		'monitor'     => __( 'Scherm (web)', 'jroen' ),
		'terminal'    => __( 'Terminal (software)', 'jroen' ),
		'thermometer' => __( 'Thermometer (sensor)', 'jroen' ),
		'code'        => __( 'Code', 'jroen' ),
		'network'     => __( 'Netwerk', 'jroen' ),
		'server'      => __( 'Server', 'jroen' ),
		'shield'      => __( 'Beveiliging', 'jroen' ),
		'lightbulb'   => __( 'Lamp', 'jroen' ),
		'graduation'  => __( 'Opleiding', 'jroen' ),
	);
}

/**
 * Bronnen voor de cijfers.
 *
 * @return array
 */
function jroen_stat_sources() {
	return array(
		'projects'   => __( 'Automatisch: aantal projecten', 'jroen' ),
		'platforms'  => __( 'Automatisch: aantal platforms', 'jroen' ),
		'techniques' => __( 'Automatisch: aantal technieken', 'jroen' ),
		'websites'   => __( 'Automatisch: aantal websites', 'jroen' ),
		'manual'     => __( 'Zelf invullen', 'jroen' ),
	);
}

/**
 * Checkbox opschonen.
 *
 * @param mixed $value Waarde.
 * @return bool
 */
function jroen_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Code uit het codevenster opschonen: regels en inspringing blijven staan,
 * de tekst wordt bij het tonen geëscapet.
 *
 * @param string $value Waarde.
 * @return string
 */
function jroen_sanitize_code( $value ) {
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
function jroen_sanitize_select( $value, $setting ) {
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

/**
 * Customizer registreren.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function jroen_customize_register( $wp_customize ) {
	$d   = jroen_defaults();
	$rep = jroen_repeat_defaults();

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
		'jroen_panel',
		array(
			'title'    => __( 'jroen.nl thema-opties', 'jroen' ),
			'priority' => 20,
		)
	);

	$sections = array(
		'jroen_colors'   => __( 'Kleuren & stijl', 'jroen' ),
		'jroen_type'     => __( 'Typografie', 'jroen' ),
		'jroen_header'   => __( 'Header & topbalk', 'jroen' ),
		'jroen_hero'     => __( 'Homepage: Hero', 'jroen' ),
		'jroen_telemetry' => __( 'Homepage: Telemetrie', 'jroen' ),
		'jroen_overview' => __( 'Homepage: Overzichtskaarten', 'jroen' ),
		'jroen_services' => __( 'Homepage: Platforms', 'jroen' ),
		'jroen_projects' => __( 'Homepage: Projecten', 'jroen' ),
		'jroen_websites' => __( 'Homepage: Websites', 'jroen' ),
		'jroen_about'    => __( 'Homepage: Over', 'jroen' ),
		'jroen_stats'    => __( 'Homepage: Cijfers', 'jroen' ),
		'jroen_news'     => __( 'Homepage: Blog', 'jroen' ),
		'jroen_cta'      => __( 'Homepage: Call-to-action', 'jroen' ),
		'jroen_blog'     => __( 'Blogpagina en berichten', 'jroen' ),
		'jroen_contact'  => __( 'Contactsectie en formulier', 'jroen' ),
		'jroen_footer'   => __( 'Footer & social media', 'jroen' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'jroen_panel' ) );
	}

	// Kleuren.
	$colors = array(
		'jroen_color_primary'   => __( 'Primair blauw', 'jroen' ),
		'jroen_color_secondary' => __( 'Lichtblauw accent', 'jroen' ),
		'jroen_color_node'      => __( 'Netwerk-knooppunten', 'jroen' ),
		'jroen_color_dark'      => __( 'Donkere achtergrond (hero, footer)', 'jroen' ),
		'jroen_color_ink'       => __( 'Koppen', 'jroen' ),
		'jroen_color_light'     => __( 'Lichte achtergrond', 'jroen' ),
		'jroen_color_text'      => __( 'Tekstkleur', 'jroen' ),
	);
	foreach ( $colors as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'default' => $d[ $id ], 'sanitize_callback' => 'sanitize_hex_color' ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array( 'label' => $label, 'section' => 'jroen_colors' ) ) );
	}
	$add( 'jroen_radius', 'jroen_colors', __( 'Afronding hoeken (px)', 'jroen' ), 'range', 'absint', array( 'input_attrs' => array( 'min' => 0, 'max' => 32, 'step' => 1 ) ) );

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
	$add( 'jroen_font_heading', 'jroen_type', __( 'Lettertype koppen', 'jroen' ), 'select', 'jroen_sanitize_select', array( 'choices' => $fonts ) );
	$add( 'jroen_font_body', 'jroen_type', __( 'Lettertype tekst', 'jroen' ), 'select', 'jroen_sanitize_select', array( 'choices' => $fonts ) );

	// Header.
	$add( 'jroen_topbar_show', 'jroen_header', __( 'Topbalk tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_topbar_text', 'jroen_header', __( 'Topbalk tekst', 'jroen' ) );
	$add( 'jroen_topbar_phone', 'jroen_header', __( 'Telefoonnummer', 'jroen' ) );
	$add( 'jroen_topbar_email', 'jroen_header', __( 'E-mailadres', 'jroen' ), 'email', 'sanitize_email' );
	$add( 'jroen_topbar_right', 'jroen_header', __( 'Topbalk rechts', 'jroen' ) );
	$add( 'jroen_header_cta_text', 'jroen_header', __( 'Knoptekst header', 'jroen' ) );
	$add( 'jroen_header_cta_url', 'jroen_header', __( 'Knoplink header', 'jroen' ), 'url', 'esc_url_raw' );

	// Hero.
	$add( 'jroen_hero_show', 'jroen_hero', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_hero_eyebrow', 'jroen_hero', __( 'Label boven titel', 'jroen' ) );
	$add( 'jroen_hero_title', 'jroen_hero', __( 'Titel', 'jroen' ), 'textarea', 'sanitize_textarea_field', array( 'description' => __( 'Zet *sterretjes* om een deel van de titel oranje te markeren.', 'jroen' ) ) );
	$add( 'jroen_hero_text', 'jroen_hero', __( 'Tekst', 'jroen' ), 'textarea', 'sanitize_textarea_field', array( 'description' => __( 'Zet **dubbele sterretjes** om een woord vet te maken.', 'jroen' ) ) );
	$add( 'jroen_hero_tech_label', 'jroen_hero', __( 'Technologieën: kopje', 'jroen' ) );
	$add( 'jroen_hero_tech', 'jroen_hero', __( 'Technologieën (gescheiden door komma\'s)', 'jroen' ) );
	$add( 'jroen_hero_btn1_text', 'jroen_hero', __( 'Knop 1 tekst', 'jroen' ) );
	$add( 'jroen_hero_btn1_url', 'jroen_hero', __( 'Knop 1 link', 'jroen' ), 'url', 'esc_url_raw' );
	$add( 'jroen_hero_btn2_text', 'jroen_hero', __( 'Knop 2 tekst', 'jroen' ) );
	$add( 'jroen_hero_btn2_url', 'jroen_hero', __( 'Knop 2 link', 'jroen' ), 'url', 'esc_url_raw' );
	$add(
		'jroen_hero_visual',
		'jroen_hero',
		__( 'Illustratie rechts', 'jroen' ),
		'select',
		'jroen_sanitize_select',
		array(
			'choices' => array(
				'code'     => __( 'Codevenster', 'jroen' ),
				'terminal' => __( 'Seriële monitor', 'jroen' ),
				'logo'     => __( 'Logo', 'jroen' ),
				'none'     => __( 'Geen', 'jroen' ),
			),
		)
	);
	$add( 'jroen_hero_code_file', 'jroen_hero', __( 'Codevenster: bestandsnaam', 'jroen' ) );
	$add(
		'jroen_hero_code_lang',
		'jroen_hero',
		__( 'Codevenster: taal', 'jroen' ),
		'select',
		'jroen_sanitize_select',
		array(
			'choices' => array(
				'python' => 'Python',
				'cpp'    => 'C++ / Arduino',
			),
		)
	);
	$add( 'jroen_hero_code', 'jroen_hero', __( 'Codevenster: code', 'jroen' ), 'textarea', 'jroen_sanitize_code', array( 'input_attrs' => array( 'style' => 'font-family:monospace;min-height:180px' ) ) );
	$add( 'jroen_hero_code_start', 'jroen_hero', __( 'Codevenster: eerste regelnummer', 'jroen' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1 ) ) );
	$add( 'jroen_hero_code_env', 'jroen_hero', __( 'Codevenster: statusbalk links', 'jroen' ) );
	$add( 'jroen_hero_code_status', 'jroen_hero', __( 'Codevenster: statusbalk rechts (groen)', 'jroen' ) );
	$add( 'jroen_hero_code_link_text', 'jroen_hero', __( 'Codevenster: project waar de code uit komt', 'jroen' ) );
	$add(
		'jroen_hero_code_link_url',
		'jroen_hero',
		__( 'Codevenster: link naar dat project', 'jroen' ),
		'url',
		'esc_url_raw',
		array( 'description' => __( 'Leeg = automatisch naar het project met deze titel (als dat bestaat).', 'jroen' ) )
	);
	$add( 'jroen_hero_code_animate', 'jroen_hero', __( 'Codevenster: code laten intypen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_hero_terminal_title', 'jroen_hero', __( 'Seriële monitor: titel', 'jroen' ) );
	$add( 'jroen_hero_terminal', 'jroen_hero', __( 'Seriële monitor: regels (één per regel)', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	$wp_customize->add_setting( 'jroen_hero_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'jroen_hero_image', array( 'label' => __( 'Achtergrondafbeelding', 'jroen' ), 'section' => 'jroen_hero' ) ) );
	$add( 'jroen_hero_overlay', 'jroen_hero', __( 'Dekking donkere overlay (%)', 'jroen' ), 'range', 'absint', array( 'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ) ) );

	// Platforms.
	$add( 'jroen_services_show', 'jroen_services', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_services_title', 'jroen_services', __( 'Titel', 'jroen' ) );
	$add( 'jroen_services_intro', 'jroen_services', __( 'Introductie', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	foreach ( $rep['services'] as $i => $item ) {
		/* translators: %d: nummer van de kaart */
		$prefix = sprintf( __( 'Kaart %d', 'jroen' ), $i );
		$add( "jroen_service{$i}_icon", 'jroen_services', $prefix . ' – ' . __( 'icoon', 'jroen' ), 'select', 'jroen_sanitize_select', array( 'choices' => jroen_icon_choices(), 'default' => $item['icon'] ) );
		$add( "jroen_service{$i}_title", 'jroen_services', $prefix . ' – ' . __( 'titel', 'jroen' ), 'text', 'sanitize_text_field', array( 'default' => $item['title'] ) );
		$add( "jroen_service{$i}_text", 'jroen_services', $prefix . ' – ' . __( 'tekst', 'jroen' ), 'textarea', 'sanitize_textarea_field', array( 'default' => $item['text'] ) );
		$add(
			"jroen_service{$i}_url",
			'jroen_services',
			$prefix . ' – ' . __( 'link', 'jroen' ),
			'url',
			'esc_url_raw',
			array(
				'default'     => '',
				'description' => __( 'Leeg = automatisch naar het platform met dezelfde naam (als dat bestaat).', 'jroen' ),
			)
		);
	}

	// Projecten.
	$add( 'jroen_projects_show', 'jroen_projects', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_projects_eyebrow', 'jroen_projects', __( 'Label boven titel', 'jroen' ) );
	$add( 'jroen_projects_title', 'jroen_projects', __( 'Titel', 'jroen' ) );
	$add( 'jroen_projects_intro', 'jroen_projects', __( 'Introductie', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'jroen_projects_count', 'jroen_projects', __( 'Aantal projecten', 'jroen' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1, 'max' => 12 ) ) );
	$add( 'jroen_projects_filter', 'jroen_projects', __( 'Filterknoppen per platform tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );

	// Websites.
	$add( 'jroen_websites_show', 'jroen_websites', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_websites_eyebrow', 'jroen_websites', __( 'Label boven titel', 'jroen' ) );
	$add( 'jroen_websites_title', 'jroen_websites', __( 'Titel', 'jroen' ) );
	$add( 'jroen_websites_intro', 'jroen_websites', __( 'Introductie', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'jroen_websites_count', 'jroen_websites', __( 'Aantal websites', 'jroen' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1, 'max' => 12 ) ) );
	$add( 'jroen_websites_filter', 'jroen_websites', __( 'Filterknoppen per websitetype tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );

	// Telemetrie.
	$add( 'jroen_telemetry_show', 'jroen_telemetry', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_telemetry_eyebrow', 'jroen_telemetry', __( 'Label boven titel', 'jroen' ) );
	$add( 'jroen_telemetry_title', 'jroen_telemetry', __( 'Titel', 'jroen' ) );
	$add(
		'jroen_telemetry_url',
		'jroen_telemetry',
		__( 'Adres van de live data (JSON)', 'jroen' ),
		'url',
		'esc_url_raw',
		array( 'description' => __( 'Bijvoorbeeld een endpoint op je ESP32 of Raspberry Pi dat {"temp":21.4,"hum":48,"cpu":12,"relay":false} teruggeeft (met CORS toegestaan). Leeg = voorbeelddata, met het label "Demo-data".', 'jroen' ) )
	);
	$add( 'jroen_telemetry_interval', 'jroen_telemetry', __( 'Verversen elke … seconden', 'jroen' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1, 'max' => 60 ) ) );
	foreach ( array( 1, 2, 3 ) as $i ) {
		/* translators: %d: nummer van de meetwaarde */
		$prefix = sprintf( __( 'Meetwaarde %d', 'jroen' ), $i );
		$add( "jroen_telemetry_m{$i}_label", 'jroen_telemetry', $prefix . ' – ' . __( 'naam', 'jroen' ) );
		$add( "jroen_telemetry_m{$i}_unit", 'jroen_telemetry', $prefix . ' – ' . __( 'eenheid', 'jroen' ) );
		$add( "jroen_telemetry_m{$i}_key", 'jroen_telemetry', $prefix . ' – ' . __( 'veld in de JSON', 'jroen' ) );
	}
	$add( 'jroen_telemetry_relay_label', 'jroen_telemetry', __( 'Schakelaar – naam (leeg = verbergen)', 'jroen' ) );
	$add( 'jroen_telemetry_relay_key', 'jroen_telemetry', __( 'Schakelaar – veld in de JSON', 'jroen' ) );

	// Overzichtskaarten.
	$add( 'jroen_overview_show', 'jroen_overview', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	foreach ( array( 'p' => __( 'Projecten', 'jroen' ), 'w' => __( 'Websites', 'jroen' ) ) as $k => $name ) {
		$add( "jroen_overview_{$k}_badge", 'jroen_overview', $name . ' – ' . __( 'label', 'jroen' ) );
		$add( "jroen_overview_{$k}_title", 'jroen_overview', $name . ' – ' . __( 'titel', 'jroen' ) );
		$add( "jroen_overview_{$k}_text", 'jroen_overview', $name . ' – ' . __( 'tekst', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	}

	// Contact.
	$add( 'jroen_contact_show', 'jroen_contact', __( 'Contactsectie onderaan elke pagina tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_contact_eyebrow', 'jroen_contact', __( 'Label boven titel', 'jroen' ) );
	$add( 'jroen_contact_title', 'jroen_contact', __( 'Titel', 'jroen' ) );
	$add( 'jroen_contact_text', 'jroen_contact', __( 'Tekst', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'jroen_contact_sites', 'jroen_contact', __( 'Regel "websites"', 'jroen' ) );
	$add( 'jroen_contact_region', 'jroen_contact', __( 'Regel "regio" (leeg = verbergen)', 'jroen' ) );
	$add( 'jroen_contact_to', 'jroen_contact', __( 'Berichten sturen naar (leeg = e-mailadres van de beheerder)', 'jroen' ), 'email', 'sanitize_email' );
	$add( 'jroen_contact_subjects', 'jroen_contact', __( 'Onderwerpen (één per regel)', 'jroen' ), 'textarea', 'sanitize_textarea_field' );

	// Donkere modus.
	$add( 'jroen_dark_toggle', 'jroen_colors', __( 'Knop voor donkere modus in de header', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add(
		'jroen_dark_default',
		'jroen_colors',
		__( 'Standaardweergave', 'jroen' ),
		'select',
		'jroen_sanitize_select',
		array(
			'choices' => array(
				'auto'  => __( 'Volgt het apparaat (licht of donker)', 'jroen' ),
				'light' => __( 'Altijd licht', 'jroen' ),
				'dark'  => __( 'Altijd donker', 'jroen' ),
			),
		)
	);

	// Over.
	$add( 'jroen_about_show', 'jroen_about', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_about_eyebrow', 'jroen_about', __( 'Label boven titel', 'jroen' ) );
	$add( 'jroen_about_title', 'jroen_about', __( 'Titel', 'jroen' ) );
	$add( 'jroen_about_text', 'jroen_about', __( 'Tekst', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'jroen_about_points', 'jroen_about', __( 'Expertise (één per regel)', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	$wp_customize->add_setting( 'jroen_about_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'jroen_about_image', array( 'label' => __( 'Foto', 'jroen' ), 'section' => 'jroen_about' ) ) );
	$add( 'jroen_about_btn_text', 'jroen_about', __( 'Knoptekst', 'jroen' ) );
	$add( 'jroen_about_btn_url', 'jroen_about', __( 'Knoplink', 'jroen' ), 'url', 'esc_url_raw' );

	// Cijfers.
	$add( 'jroen_stats_show', 'jroen_stats', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	foreach ( $rep['stats'] as $i => $item ) {
		/* translators: %d: nummer van het cijfer */
		$prefix = sprintf( __( 'Cijfer %d', 'jroen' ), $i );
		$add( "jroen_stat{$i}_source", 'jroen_stats', $prefix . ' – ' . __( 'bron', 'jroen' ), 'select', 'jroen_sanitize_select', array( 'choices' => jroen_stat_sources(), 'default' => $item['source'] ) );
		$add( "jroen_stat{$i}_number", 'jroen_stats', $prefix . ' – ' . __( 'getal (bij "zelf invullen")', 'jroen' ), 'number', 'absint', array( 'default' => $item['number'] ) );
		$add( "jroen_stat{$i}_suffix", 'jroen_stats', $prefix . ' – ' . __( 'achtervoegsel (+, %)', 'jroen' ), 'text', 'sanitize_text_field', array( 'default' => $item['suffix'] ) );
		$add( "jroen_stat{$i}_label", 'jroen_stats', $prefix . ' – ' . __( 'omschrijving (leeg = verbergen)', 'jroen' ), 'text', 'sanitize_text_field', array( 'default' => $item['label'] ) );
	}

	// Blog.
	$add( 'jroen_news_show', 'jroen_news', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_news_title', 'jroen_news', __( 'Titel', 'jroen' ) );
	$add( 'jroen_news_count', 'jroen_news', __( 'Aantal berichten', 'jroen' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 1, 'max' => 9 ) ) );

	// Blogpagina.
	$add( 'jroen_blog_intro', 'jroen_blog', __( 'Introductie onder de titel', 'jroen' ), 'textarea', 'sanitize_textarea_field', array( 'description' => __( 'Wordt gebruikt als de pagina Blog zelf geen samenvatting heeft.', 'jroen' ) ) );
	$add(
		'jroen_blog_layout',
		'jroen_blog',
		__( 'Weergave van het overzicht', 'jroen' ),
		'select',
		'jroen_sanitize_select',
		array(
			'choices' => array(
				'grid' => __( 'Raster met kaarten', 'jroen' ),
				'list' => __( 'Lijst met afbeelding links', 'jroen' ),
			),
		)
	);
	$add( 'jroen_blog_featured', 'jroen_blog', __( 'Nieuwste bericht groot bovenaan', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_blog_filters', 'jroen_blog', __( 'Filterknoppen per categorie', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_blog_reading_time', 'jroen_blog', __( 'Leestijd tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_blog_tags', 'jroen_blog', __( 'Tags op de kaarten tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_blog_sidebar', 'jroen_blog', __( 'Zijbalk tonen (zoeken, categorieën, GitHub of eigen widgets)', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_blog_toc', 'jroen_blog', __( 'Inhoudsopgave bij berichten met 3 of meer tussenkoppen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_blog_related', 'jroen_blog', __( '"Meer berichten" onder een bericht', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );

	// CTA.
	$add( 'jroen_cta_show', 'jroen_cta', __( 'Sectie tonen', 'jroen' ), 'checkbox', 'jroen_sanitize_checkbox' );
	$add( 'jroen_cta_title', 'jroen_cta', __( 'Titel', 'jroen' ) );
	$add( 'jroen_cta_text', 'jroen_cta', __( 'Tekst', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'jroen_cta_btn_text', 'jroen_cta', __( 'Knoptekst', 'jroen' ) );
	$add( 'jroen_cta_btn_url', 'jroen_cta', __( 'Knoplink', 'jroen' ), 'url', 'esc_url_raw' );

	// Footer.
	$add( 'jroen_footer_about', 'jroen_footer', __( 'Korte omschrijving', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'jroen_footer_address', 'jroen_footer', __( 'Adres', 'jroen' ), 'textarea', 'sanitize_textarea_field' );
	$add( 'jroen_footer_note', 'jroen_footer', __( 'Regel in de footer', 'jroen' ) );
	$add( 'jroen_footer_copy', 'jroen_footer', __( 'Copyrighttekst (leeg = automatisch)', 'jroen' ) );
	$add( 'jroen_social_github', 'jroen_footer', 'GitHub URL', 'url', 'esc_url_raw' );
	$add( 'jroen_social_linkedin', 'jroen_footer', 'LinkedIn URL', 'url', 'esc_url_raw' );
	$add( 'jroen_social_youtube', 'jroen_footer', 'YouTube URL', 'url', 'esc_url_raw' );
	$add( 'jroen_social_instagram', 'jroen_footer', 'Instagram URL', 'url', 'esc_url_raw' );
}
add_action( 'customize_register', 'jroen_customize_register' );

/**
 * CSS-variabelen uit de Customizer.
 *
 * @return string
 */
function jroen_customizer_css() {
	$vars = array(
		'--bl-primary'   => jroen_mod( 'jroen_color_primary' ),
		'--bl-secondary' => jroen_mod( 'jroen_color_secondary' ),
		'--bl-node'      => jroen_mod( 'jroen_color_node' ),
		'--bl-dark'      => jroen_mod( 'jroen_color_dark' ),
		'--bl-ink'       => jroen_mod( 'jroen_color_ink' ),
		'--bl-light'     => jroen_mod( 'jroen_color_light' ),
		'--bl-text'      => jroen_mod( 'jroen_color_text' ),
		'--bl-radius'    => absint( jroen_mod( 'jroen_radius' ) ) . 'px',
		'--bl-overlay'   => min( 100, absint( jroen_mod( 'jroen_hero_overlay' ) ) ) / 100,
		'--bl-font-head' => '"' . jroen_mod( 'jroen_font_heading' ) . '", system-ui, sans-serif',
		'--bl-font-body' => '"' . jroen_mod( 'jroen_font_body' ) . '", system-ui, sans-serif',
	);

	$css = ':root{';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . wp_strip_all_tags( (string) $value ) . ';';
	}
	return $css . '}';
}
