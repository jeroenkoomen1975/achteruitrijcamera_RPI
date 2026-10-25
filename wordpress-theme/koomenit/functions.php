<?php
/**
 * Koomen.IT functies en definities.
 *
 * @package KoomenIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KOOMENIT_VERSION', '2.4.3' );

/**
 * Thema-ondersteuning registreren.
 */
function koomenit_setup() {
	load_theme_textdomain( 'koomenit', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'koomenit-card', 720, 460, true );
	add_post_type_support( 'page', 'excerpt' );

	register_nav_menus(
		array(
			'primary' => __( 'Hoofdmenu', 'koomenit' ),
			'footer'  => __( 'Footermenu', 'koomenit' ),
		)
	);

	add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'koomenit_setup' );

/**
 * Widgetgebieden.
 */
function koomenit_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Zijbalk', 'koomenit' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Wordt getoond naast berichten.', 'koomenit' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: kolomnummer */
				'name'          => sprintf( __( 'Footer kolom %d', 'koomenit' ), $i ),
				'id'            => 'footer-' . $i,
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h4 class="footer-title">',
				'after_title'   => '</h4>',
			)
		);
	}
}
add_action( 'widgets_init', 'koomenit_widgets_init' );

/**
 * Stijlen en scripts.
 */
function koomenit_scripts() {
	wp_enqueue_style( 'koomenit-fonts', koomenit_fonts_url(), array(), null );
	wp_enqueue_style( 'koomenit-style', get_stylesheet_uri(), array(), KOOMENIT_VERSION );
	wp_add_inline_style( 'koomenit-style', koomenit_customizer_css() );

	wp_enqueue_script( 'koomenit-main', get_template_directory_uri() . '/assets/js/main.js', array(), KOOMENIT_VERSION, true );
	wp_enqueue_script( 'koomenit-code', get_template_directory_uri() . '/assets/js/code.js', array(), KOOMENIT_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'koomenit_scripts' );

/**
 * Google Fonts URL op basis van het gekozen lettertype.
 *
 * @return string
 */
function koomenit_fonts_url() {
	$heading = get_theme_mod( 'koomenit_font_heading', 'Plus Jakarta Sans' );
	$body    = get_theme_mod( 'koomenit_font_body', 'Inter' );
	$fonts   = array_unique( array( $heading, $body ) );

	$families = array_map(
		function ( $font ) {
			return 'family=' . str_replace( ' ', '+', $font ) . ':wght@400;500;600;700;800';
		},
		$fonts
	);

	$families[] = 'family=JetBrains+Mono:wght@400;600';

	return 'https://fonts.googleapis.com/css2?' . implode( '&', $families ) . '&display=swap';
}

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/projects.php';
require get_template_directory() . '/inc/websites.php';
require get_template_directory() . '/inc/blog.php';
require get_template_directory() . '/inc/template-tags.php';
