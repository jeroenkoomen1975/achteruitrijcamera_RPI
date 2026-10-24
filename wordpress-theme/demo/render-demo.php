<?php
/**
 * Rendert de Koomen.IT-homepage zonder WordPress naar een statische demo.
 *
 * Gebruik: php wordpress-theme/demo/render-demo.php > wordpress-theme/demo/index.html
 *
 * Dit script definieert minimale stubs van de gebruikte WordPress-functies
 * en laat daarna de echte thema-templates hun HTML uitvoeren.
 */

define( 'ABSPATH', __DIR__ . '/' );
$theme = dirname( __DIR__ ) . '/koomenit';

// --- Algemene stubs ---------------------------------------------------------
function add_action() {}
function add_filter() {}
function __( $t ) { return $t; }
function _e( $t ) { echo $t; }
function _n( $s, $p, $n ) { return 1 === (int) $n ? $s : $p; }
function esc_html( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $t ) { return esc_html( $t ); }
function esc_url( $t ) { return esc_html( $t ); }
function esc_url_raw( $t ) { return $t; }
function esc_html__( $t ) { return esc_html( $t ); }
function esc_html_e( $t ) { echo esc_html( $t ); }
function esc_attr_e( $t ) { echo esc_attr( $t ); }
function wp_kses_post( $t ) { return $t; }
function wp_strip_all_tags( $t ) { return strip_tags( $t ); }
function absint( $n ) { return abs( (int) $n ); }
function antispambot( $e ) { return $e; }
function get_theme_mod( $key, $default = false ) { return $default; }
function get_template_directory() { return $GLOBALS['theme']; }
function get_template_directory_uri() { return '../koomenit'; }
function home_url( $p = '' ) { return '#'; }
function get_option( $k ) { return 'show_on_front' === $k ? 'posts' : 0; }
function get_bloginfo( $k = 'name' ) { return 'name' === $k ? 'koomen.it' : 'Training, Onderwijs & Advies'; }
function bloginfo( $k ) { echo 'charset' === $k ? 'UTF-8' : esc_html( get_bloginfo( $k ) ); }
function language_attributes() { echo 'lang="nl-NL"'; }
function body_class() { echo 'class="home"'; }
function wp_body_open() {}
function has_custom_logo() { return false; }
function is_active_sidebar() { return false; }
function wp_unique_id( $p ) { static $i = 0; return $p . ( ++$i ); }
function wp_trim_words( $t, $n ) { $w = explode( ' ', $t ); return count( $w ) > $n ? implode( ' ', array_slice( $w, 0, $n ) ) . '…' : $t; }

function get_header() { require $GLOBALS['theme'] . '/header.php'; }
function get_footer() { require $GLOBALS['theme'] . '/footer.php'; }
function get_template_part( $slug, $name ) { require $GLOBALS['theme'] . "/{$slug}-{$name}.php"; }

function wp_head() {
	echo '<link rel="stylesheet" href="' . esc_attr( koomenit_fonts_url() ) . '">' . "\n";
	echo '<title>Koomen.IT – demo</title>' . "\n";
	echo "<style>\n" . file_get_contents( $GLOBALS['theme'] . '/style.css' ) . "\n" . koomenit_customizer_css() . "\n</style>\n";
}
function wp_footer() {
	echo "<script>\n" . file_get_contents( $GLOBALS['theme'] . '/assets/js/main.js' ) . "\n</script>\n";
}

function wp_nav_menu( $args ) {
	$items = 'footer' === $args['theme_location']
		? array( 'Over Koomen.IT', 'Nieuws', 'Kennisbank', 'Privacyverklaring', 'Contact' )
		: array( 'Home' => '#', 'Training' => '#aanbod', 'Onderwijs' => '#aanbod', 'Advies' => '#aanbod', 'Over' => '#over-ons', 'Contact' => '#contact' );
	echo '<ul class="' . esc_attr( isset( $args['menu_class'] ) ? $args['menu_class'] : 'menu' ) . '">';
	$first = true;
	foreach ( $items as $label => $url ) {
		if ( is_int( $label ) ) {
			$label = $url;
			$url   = '#';
		}
		echo '<li class="' . ( $first && 'primary' === $args['theme_location'] ? 'current-menu-item' : '' ) . '"><a href="' . esc_attr( $url ) . '">' . esc_html( $label ) . '</a></li>';
		$first = false;
	}
	echo '</ul>';
}

// --- Loop-stubs met demoberichten -------------------------------------------
$GLOBALS['demo_posts'] = array(
	array( 'Voorbeeld: workshop Git voor teams', 'Training', 'In één dag van losse bestanden naar een vaste werkwijze met branches, pull requests en code reviews. Dit is een voorbeeldbericht.', '14 oktober 2026' ),
	array( 'Voorbeeld: Python leren in het mbo', 'Onderwijs', 'Hoe je programmeerlessen opbouwt rond kleine, echte projecten waar studenten direct resultaat zien. Dit is een voorbeeldbericht.', '2 oktober 2026' ),
	array( 'Voorbeeld: vijf vragen voor je cloudkeuze', 'Advies', 'Voordat je kiest voor een cloudplatform: deze vragen helpen je om kosten, beheer en kennis in balans te houden. Dit is een voorbeeldbericht.', '20 september 2026' ),
);
$GLOBALS['demo_i'] = -1;

class WP_Query {
	public function __construct( $args ) { $GLOBALS['demo_i'] = -1; }
	public function have_posts() { return $GLOBALS['demo_i'] + 1 < count( $GLOBALS['demo_posts'] ); }
	public function the_post() { $GLOBALS['demo_i']++; }
}
function demo_post( $k ) { return $GLOBALS['demo_posts'][ $GLOBALS['demo_i'] ][ $k ]; }
function have_posts() { return false; }
function wp_reset_postdata() {}
function the_ID() { echo (int) $GLOBALS['demo_i'] + 1; }
function post_class( $c ) { echo 'class="' . esc_attr( $c ) . '"'; }
function the_permalink() { echo '#'; }
function has_post_thumbnail() { return false; }
function get_the_category() { return array( (object) array( 'name' => demo_post( 1 ) ) ); }
function the_title() { echo esc_html( demo_post( 0 ) ); }
function get_the_excerpt() { return demo_post( 2 ); }
function get_the_date( $f = '' ) { return $f ? '2026-10-01' : demo_post( 3 ); }
function get_the_author() { return 'Redactie'; }

// --- Renderen ---------------------------------------------------------------
require $theme . '/functions.php';
require $theme . '/front-page.php';
