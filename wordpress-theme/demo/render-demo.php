<?php
/**
 * Rendert de Koomen.IT-thema-templates zonder WordPress naar een statische demo.
 *
 * Gebruik: php wordpress-theme/demo/render-demo.php
 * Schrijft index.html, projecten.html en project-parkeersensor.html in deze map.
 *
 * Dit script definieert minimale stubs van de gebruikte WordPress-functies,
 * vult ze met demo-inhoud en laat daarna de echte thema-templates hun HTML uitvoeren.
 */

$pages = array(
	'home'      => 'index.html',
	'projecten' => 'projecten.html',
	'project'   => 'project-parkeersensor.html',
);

// Zonder argument: elke pagina in een eigen PHP-proces renderen.
if ( ! isset( $argv[1] ) ) {
	foreach ( $pages as $page => $file ) {
		$cmd = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' ' . $page . ' > ' . escapeshellarg( __DIR__ . '/' . $file );
		passthru( $cmd, $code );
		if ( 0 !== $code ) {
			fwrite( STDERR, "Renderen van $page mislukt\n" );
			exit( 1 );
		}
	}
	exit( 0 );
}

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['page']  = $argv[1];
$GLOBALS['theme'] = dirname( __DIR__ ) . '/koomenit';

// --- Demo-inhoud --------------------------------------------------------------
$GLOBALS['terms'] = array(
	'platform'    => array(
		'esp32'        => 'ESP32',
		'arduino'      => 'Arduino',
		'raspberry-pi' => 'Raspberry Pi',
	),
	'projecttype' => array(
		'website'  => 'Website / webinterface',
		'sensor'   => 'Sensorproject',
		'software' => 'Programma / software',
	),
);

$parkeersensor_content = <<<'HTML'
<p>Een parkeersensor zoals in een auto, gebouwd met een Raspberry Pi. Een HC-SR04 ultrasone sensor meet de afstand tot een obstakel. Drie LEDs, een buzzer en een 2.0" kleurendisplay laten zien hoe dichtbij je bent.</p>
<figure><img src="img/parkeersensor-scherm.png" alt="Het display in vier standen: VRIJ, 35 cm, LET OP 15 cm en STOP! 6 cm"></figure>
<h2>Hoe het werkt</h2>
<div class="table-wrap"><table>
<thead><tr><th>Afstand</th><th>LED</th><th>Buzzer</th><th>Display</th></tr></thead>
<tbody>
<tr><td>meer dan 50 cm</td><td>groen</td><td>stil</td><td><code>VRIJ</code></td></tr>
<tr><td>20 – 50 cm</td><td>groen</td><td>langzaam piepen</td><td><code>VRIJ</code></td></tr>
<tr><td>10 – 20 cm</td><td>oranje</td><td>snel piepen</td><td><code>LET OP</code></td></tr>
<tr><td>10 cm of minder</td><td>rood</td><td>continu piepen</td><td><code>STOP!</code></td></tr>
</tbody></table></div>
<p>Het display toont de afstand in grote cijfers, met daaronder een balk die voller wordt naarmate je dichterbij komt. Alle grenzen staan in <code>config.py</code> en zijn eenvoudig aan te passen.</p>
<h2>Techniek</h2>
<p>De software is geschreven in Python met <code>gpiozero</code>. Het GMT020-02-7P-display (ST7789, 240x320) wordt via SPI aangestuurd door een eigen driver. Omdat dit display geen CS-pin heeft, draait SPI in mode 3. Met een systemd-service start de sensor automatisch op bij het opstarten van de Pi.</p>
<blockquote><p>De ECHO-pin van de HC-SR04 geeft 5V, terwijl de GPIO-pinnen van de Pi maar 3,3V aankunnen. Een spanningsdeler van 1 kΩ en 2 kΩ lost dat op.</p></blockquote>
<pre><code>sudo apt install -y python3-gpiozero python3-lgpio python3-spidev python3-pil
python3 -m venv --system-site-packages .venv
.venv/bin/python -m parkeersensor</code></pre>
HTML;

$voorbeeld = '<p>Dit is een voorbeeldproject om te laten zien hoe een projectpagina eruitziet. Vervang het door een echt project.</p>';

$GLOBALS['projects'] = array(
	array(
		'id' => 101, 'title' => 'Achteruitrij-parkeersensor', 'link' => 'project-parkeersensor.html',
		'excerpt' => 'Parkeersensor met een ultrasone sensor, LEDs, een buzzer en een 2.0" kleurendisplay dat de afstand toont. Gebouwd in Python op een Raspberry Pi.',
		'content' => $parkeersensor_content,
		'platform' => array( 'raspberry-pi' ), 'projecttype' => array( 'sensor', 'software' ),
		'techniek' => array( 'Python', 'HC-SR04', 'ST7789-display', 'gpiozero', 'SPI', 'systemd' ),
		'meta' => array(
			'koomenit_github'   => 'https://github.com/jeroenkoomen1975/achteruitrijcamera_RPI',
			'koomenit_status'   => 'afgerond',
			'koomenit_year'     => '2026',
			'koomenit_hardware' => "Raspberry Pi (3, 4, 5 of Zero 2 W)\nHC-SR04 ultrasone sensor\nGMT020-02-7P 2.0\" display (ST7789)\n3 LEDs met 220 Ω weerstanden\nActieve buzzer\nSpanningsdeler 1 kΩ + 2 kΩ",
		),
		'thumb' => 'img/parkeersensor-kaart.jpg', 'image' => 'img/parkeersensor-scherm.png',
	),
	array(
		'id' => 102, 'title' => 'Voorbeeld: weerstation met ESP32', 'link' => 'projecten.html',
		'excerpt' => 'Meet temperatuur, luchtvochtigheid en luchtdruk en stuurt de waarden via MQTT naar Home Assistant. Met een eigen webpagina op de ESP32.',
		'content' => $voorbeeld, 'platform' => array( 'esp32' ), 'projecttype' => array( 'sensor', 'website' ),
		'techniek' => array( 'C++', 'BME280', 'MQTT', 'Webserver' ), 'meta' => array( 'koomenit_status' => 'in-gebruik', 'koomenit_year' => '2025' ),
	),
	array(
		'id' => 103, 'title' => 'Voorbeeld: plantenbewatering met Arduino', 'link' => 'projecten.html',
		'excerpt' => 'Een bodemvochtsensor bepaalt wanneer een pompje via een relais water geeft. Werkt maanden op een powerbank.',
		'content' => $voorbeeld, 'platform' => array( 'arduino' ), 'projecttype' => array( 'sensor' ),
		'techniek' => array( 'C++', 'Bodemvochtsensor', 'Relais' ), 'meta' => array( 'koomenit_status' => 'afgerond', 'koomenit_year' => '2025' ),
	),
	array(
		'id' => 104, 'title' => 'Voorbeeld: webinterface voor ledstrips', 'link' => 'projecten.html',
		'excerpt' => 'Bedien WS2812B-ledstrips vanaf je telefoon. De ESP32 serveert een webapp en ontvangt kleuren live via WebSockets.',
		'content' => $voorbeeld, 'platform' => array( 'esp32' ), 'projecttype' => array( 'website' ),
		'techniek' => array( 'C++', 'HTML/CSS/JS', 'WebSocket', 'WS2812B' ), 'meta' => array( 'koomenit_status' => 'ontwikkeling', 'koomenit_year' => '2026' ),
	),
	array(
		'id' => 105, 'title' => 'Voorbeeld: dashboard voor sensordata', 'link' => 'projecten.html',
		'excerpt' => 'Een Flask-webapp op de Raspberry Pi die metingen van meerdere sensoren opslaat in SQLite en als grafieken toont.',
		'content' => $voorbeeld, 'platform' => array( 'raspberry-pi' ), 'projecttype' => array( 'website', 'software' ),
		'techniek' => array( 'Python', 'Flask', 'SQLite', 'Chart.js' ), 'meta' => array( 'koomenit_status' => 'prototype', 'koomenit_year' => '2026' ),
	),
	array(
		'id' => 106, 'title' => 'Voorbeeld: seriële datalogger', 'link' => 'projecten.html',
		'excerpt' => 'Een klein Python-programma dat metingen van een Arduino via USB uitleest en per dag opslaat als CSV-bestand.',
		'content' => $voorbeeld, 'platform' => array( 'arduino' ), 'projecttype' => array( 'software' ),
		'techniek' => array( 'Python', 'pySerial', 'CSV' ), 'meta' => array( 'koomenit_status' => 'afgerond', 'koomenit_year' => '2024' ),
	),
);

$GLOBALS['blog'] = array(
	array( 'id' => 201, 'title' => 'Voorbeeld: 5V-sensoren veilig aansluiten op een Raspberry Pi', 'cat' => 'Raspberry Pi', 'excerpt' => 'Waarom een spanningsdeler nodig is voor de ECHO-pin van een HC-SR04, en hoe je de weerstanden kiest. Dit is een voorbeeldbericht.', 'date' => '14 oktober 2026' ),
	array( 'id' => 202, 'title' => 'Voorbeeld: maanden op een batterij met ESP32 deep sleep', 'cat' => 'ESP32', 'excerpt' => 'Een sensor die elk kwartier meet en daarna slaapt: zo haal je het stroomverbruik omlaag. Dit is een voorbeeldbericht.', 'date' => '2 oktober 2026' ),
	array( 'id' => 203, 'title' => 'Voorbeeld: een ST7789-display aansturen zonder CS-pin', 'cat' => 'Displays', 'excerpt' => 'Sommige goedkope displays missen een CS-pin. Met SPI mode 3 werken ze toch. Dit is een voorbeeldbericht.', 'date' => '20 september 2026' ),
);

/**
 * Term-object maken.
 */
function demo_term( $taxonomy, $slug, $name = null ) {
	static $ids = array();
	$key = $taxonomy . ':' . $slug;
	if ( ! isset( $ids[ $key ] ) ) {
		$ids[ $key ] = count( $ids ) + 1;
	}
	$count = 0;
	foreach ( $GLOBALS['projects'] as $p ) {
		$list = 'techniek' === $taxonomy ? array_map( 'demo_slug', $p['techniek'] ) : $p[ $taxonomy ];
		$count += in_array( $slug, $list, true ) ? 1 : 0;
	}
	return (object) array( 'term_id' => $ids[ $key ], 'name' => $name ?: $GLOBALS['terms'][ $taxonomy ][ $slug ], 'slug' => $slug, 'taxonomy' => $taxonomy, 'count' => $count );
}
function demo_slug( $name ) { return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( $name ) ), '-' ); }
function demo_find( $id ) {
	foreach ( array_merge( $GLOBALS['projects'], $GLOBALS['blog'] ) as $p ) {
		if ( $p['id'] === $id ) {
			return $p;
		}
	}
	return null;
}

// --- Algemene stubs -------------------------------------------------------------
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
function esc_attr__( $t ) { return esc_attr( $t ); }
function wp_kses_post( $t ) { return $t; }
function wp_strip_all_tags( $t ) { return strip_tags( $t ); }
function absint( $n ) { return abs( (int) $n ); }
function antispambot( $e ) { return $e; }
function selected() {}
function is_wp_error( $x ) { return false; }
function get_theme_mod( $key, $default = false ) { return $default; }
function get_template_directory() { return $GLOBALS['theme']; }
function get_template_directory_uri() { return '../koomenit'; }
function home_url( $p = '' ) { return 'index.html'; }
function get_option( $k ) { return 'show_on_front' === $k ? 'posts' : 0; }
function get_bloginfo( $k = 'name' ) { return 'name' === $k ? 'koomen.it' : 'Embedded projecten, websites & software'; }
function bloginfo( $k ) { echo 'charset' === $k ? 'UTF-8' : esc_html( get_bloginfo( $k ) ); }
function language_attributes() { echo 'lang="nl-NL"'; }
function body_class() { echo 'class="' . esc_attr( $GLOBALS['page'] ) . '"'; }
function wp_body_open() {}
function has_custom_logo() { return false; }
function has_site_icon() { return false; }
function is_active_sidebar() { return false; }
function wp_unique_id( $p ) { static $i = 0; return $p . ( ++$i ); }
function wp_trim_words( $t, $n ) { $w = explode( ' ', $t ); return count( $w ) > $n ? implode( ' ', array_slice( $w, 0, $n ) ) . '…' : $t; }
function wp_list_pluck( $list, $field ) { return array_map( function ( $o ) use ( $field ) { return is_object( $o ) ? $o->$field : $o[ $field ]; }, $list ); }
function wp_list_pages() {}
function wp_link_pages() {}
function the_posts_pagination() {}
function is_tax() { return false; }
function get_queried_object() { return null; }
function is_post_type_archive( $t = '' ) { return 'projecten' === $GLOBALS['page']; }
function is_singular( $t = '' ) { return 'project' === $GLOBALS['page']; }
function post_type_archive_title() { return 'Projecten'; }
function get_post_type_archive_link() { return 'projecten.html'; }
function get_term_link( $term ) { return 'projecten.html'; }
function term_description() { return ''; }
function get_taxonomy() { return null; }
function get_posts( $args ) {
	foreach ( $GLOBALS['projects'] as $p ) {
		if ( isset( $args['title'] ) && $p['title'] === $args['title'] ) {
			return array( $p['id'] );
		}
	}
	return array();
}
function get_permalink( $id ) { $p = demo_find( $id ); return $p ? $p['link'] : '#'; }
function wp_count_posts() { return (object) array( 'publish' => count( $GLOBALS['projects'] ) ); }

function get_terms( $args ) {
	$tax = $args['taxonomy'];
	if ( 'techniek' === $tax ) {
		$names = array();
		foreach ( $GLOBALS['projects'] as $p ) { foreach ( $p['techniek'] as $t ) { $names[ demo_slug( $t ) ] = $t; } }
		$terms = array_map( function ( $slug ) use ( $names ) { return demo_term( 'techniek', $slug, $names[ $slug ] ); }, array_keys( $names ) );
	} else {
		$terms = array_map( function ( $slug ) use ( $tax ) { return demo_term( $tax, $slug ); }, array_keys( $GLOBALS['terms'][ $tax ] ) );
	}
	if ( ! empty( $args['hide_empty'] ) ) {
		$terms = array_values( array_filter( $terms, function ( $t ) { return $t->count > 0; } ) );
	}
	return ( isset( $args['fields'] ) && 'ids' === $args['fields'] ) ? wp_list_pluck( $terms, 'term_id' ) : $terms;
}
function get_term_by( $field, $value, $tax ) {
	foreach ( get_terms( array( 'taxonomy' => $tax ) ) as $t ) {
		if ( $t->name === $value ) {
			return $t;
		}
	}
	return false;
}
function get_the_terms( $id, $tax ) {
	$p = demo_find( $id );
	if ( ! $p || ! isset( $p[ $tax ] ) ) {
		return false;
	}
	if ( 'techniek' === $tax ) {
		return array_map( function ( $n ) { return demo_term( 'techniek', demo_slug( $n ), $n ); }, $p['techniek'] );
	}
	return array_map( function ( $slug ) use ( $tax ) { return demo_term( $tax, $slug ); }, $p[ $tax ] );
}
function get_post_meta( $id, $key, $single = false ) {
	$p = demo_find( $id );
	return isset( $p['meta'][ $key ] ) ? $p['meta'][ $key ] : '';
}

function get_header() { require $GLOBALS['theme'] . '/header.php'; }
function get_footer() { require $GLOBALS['theme'] . '/footer.php'; }
function get_template_part( $slug, $name ) { require $GLOBALS['theme'] . "/{$slug}-{$name}.php"; }

function wp_head() {
	$titles = array( 'home' => 'koomen.it', 'projecten' => 'Projecten – koomen.it', 'project' => 'Achteruitrij-parkeersensor – koomen.it' );
	echo '<title>' . esc_html( $titles[ $GLOBALS['page'] ] ) . "</title>\n";
	echo '<link rel="stylesheet" href="' . esc_attr( koomenit_fonts_url() ) . '">' . "\n";
	koomenit_fallback_favicon();
	echo "<style>\n" . file_get_contents( $GLOBALS['theme'] . '/style.css' ) . "\n" . koomenit_customizer_css() . "\n</style>\n";
}
function wp_footer() {
	foreach ( array( 'main.js', 'code.js' ) as $js ) {
		echo "<script>\n" . file_get_contents( $GLOBALS['theme'] . '/assets/js/' . $js ) . "\n</script>\n";
	}
}

function wp_nav_menu( $args ) {
	if ( 'footer' === $args['theme_location'] ) {
		$items   = array( 'Projecten' => 'projecten.html', 'Over' => 'index.html#over', 'Blog' => 'index.html#blog', 'Contact' => 'index.html#contact' );
		$current = '';
	} else {
		$items   = array( 'Home' => 'index.html', 'Projecten' => 'projecten.html', 'Over' => 'index.html#over', 'Blog' => 'index.html#blog' );
		$current = 'home' === $GLOBALS['page'] ? 'Home' : 'Projecten';
	}
	echo '<ul class="' . esc_attr( isset( $args['menu_class'] ) ? $args['menu_class'] : 'menu' ) . '">';
	foreach ( $items as $label => $url ) {
		echo '<li class="' . ( $label === $current ? 'current-menu-item' : '' ) . '"><a href="' . esc_attr( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

// --- Loop-stubs ---------------------------------------------------------------
$GLOBALS['cur'] = null;

class WP_Query {
	private $items;
	private $i = -1;
	private $prev;
	public function __construct( $args ) {
		$this->prev = $GLOBALS['cur'];
		$type       = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
		$items      = 'project' === $type ? $GLOBALS['projects'] : $GLOBALS['blog'];
		if ( ! empty( $args['post__not_in'] ) ) {
			$items = array_filter( $items, function ( $p ) use ( $args ) { return ! in_array( $p['id'], $args['post__not_in'], true ); } );
		}
		if ( ! empty( $args['tax_query'] ) ) {
			$ids   = $args['tax_query'][0]['terms'];
			$items = array_filter(
				$items,
				function ( $p ) use ( $ids ) {
					foreach ( $p['platform'] as $slug ) {
						if ( in_array( demo_term( 'platform', $slug )->term_id, $ids, true ) ) {
							return true;
						}
					}
					return false;
				}
			);
		}
		$this->items = array_slice( array_values( $items ), 0, $args['posts_per_page'] );
	}
	public function have_posts() { return $this->i + 1 < count( $this->items ); }
	public function the_post() { $GLOBALS['cur'] = $this->items[ ++$this->i ]; }
	public function reset() { $GLOBALS['cur'] = $this->prev; }
}
function wp_reset_postdata() { $GLOBALS['cur'] = $GLOBALS['main'][0] ?? null; }

$GLOBALS['main'] = array(
	'home'      => array(),
	'projecten' => $GLOBALS['projects'],
	'project'   => array( $GLOBALS['projects'][0] ),
)[ $GLOBALS['page'] ];
$GLOBALS['main_i'] = -1;
function have_posts() { return $GLOBALS['main_i'] + 1 < count( $GLOBALS['main'] ); }
function the_post() { $GLOBALS['cur'] = $GLOBALS['main'][ ++$GLOBALS['main_i'] ]; }

function demo_cur( $k ) { return isset( $GLOBALS['cur'][ $k ] ) ? $GLOBALS['cur'][ $k ] : ''; }
function get_the_ID() { return demo_cur( 'id' ); }
function the_ID() { echo (int) get_the_ID(); }
function post_class( $c = '' ) { echo 'class="' . esc_attr( $c ) . '"'; }
function the_permalink() { echo esc_attr( demo_cur( 'link' ) ?: '#' ); }
function the_title() { echo esc_html( demo_cur( 'title' ) ); }
function get_the_excerpt() { return demo_cur( 'excerpt' ); }
function has_excerpt() { return '' !== demo_cur( 'excerpt' ); }
function the_content() { echo demo_cur( 'content' ); }
function has_post_thumbnail() { return 'project' === $GLOBALS['page'] && $GLOBALS['cur'] === $GLOBALS['main'][0] ? false : (bool) demo_cur( 'thumb' ); }
function the_post_thumbnail( $size = '' ) { echo '<img src="' . esc_attr( demo_cur( 'thumb' ) ) . '" alt="" loading="lazy">'; }
function get_the_category() { return demo_cur( 'cat' ) ? array( (object) array( 'name' => demo_cur( 'cat' ) ) ) : array(); }
function get_the_date( $f = '' ) { return $f ? '2026-10-01' : demo_cur( 'date' ); }
function get_the_author() { return 'Jeroen Koomen'; }

// --- Renderen -------------------------------------------------------------------
require $GLOBALS['theme'] . '/functions.php';
$templates = array( 'home' => 'front-page.php', 'projecten' => 'archive-project.php', 'project' => 'single-project.php' );
require $GLOBALS['theme'] . '/' . $templates[ $GLOBALS['page'] ];
