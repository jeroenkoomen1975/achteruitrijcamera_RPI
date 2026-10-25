<?php
/**
 * Rendert de Koomen.IT-thema-templates zonder WordPress naar een statische demo.
 *
 * Gebruik: php wordpress-theme/demo/render-demo.php
 * Schrijft index.html, projecten.html, project-parkeersensor.html, websites.html
 * website-koomen-it.html, blog.html, blog-lijst.html en bericht-5v-sensoren.html in deze map.
 *
 * Dit script definieert minimale stubs van de gebruikte WordPress-functies,
 * vult ze met demo-inhoud en laat daarna de echte thema-templates hun HTML uitvoeren.
 */

$pages = array(
	'home'      => 'index.html',
	'projecten' => 'projecten.html',
	'project'   => 'project-parkeersensor.html',
	'websites'  => 'websites.html',
	'website'   => 'website-koomen-it.html',
	'blog'      => 'blog.html',
	'bloglijst' => 'blog-lijst.html',
	'bericht'   => 'bericht-5v-sensoren.html',
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
	'websitetype' => array(
		'bedrijfswebsite' => 'Bedrijfswebsite',
		'webshop'         => 'Webshop',
		'portfolio'       => 'Portfolio',
		'webapplicatie'   => 'Webapplicatie',
		'webinterface'    => 'Webinterface voor hardware',
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

$koomenit_content = <<<'HTML'
<p>De website waar je nu naar kijkt. Een eigen WordPress-thema in de huisstijl van koomen.it, gemaakt om embedded projecten en websites overzichtelijk te laten zien.</p>
<h2>Wat erin zit</h2>
<ul>
<li>Twee eigen berichttypes, <strong>Projecten</strong> en <strong>Websites</strong>, met filters per platform en per websitetype.</li>
<li>Per project en website gegevens zoals status, jaar, technieken, hardware en links naar GitHub of de live site.</li>
<li>Een hero met een codevenster dat echte code uit een project intypt, met syntax-kleuring voor Python en C++.</li>
<li>Alle teksten, kleuren en secties van de homepage zijn aan te passen in de Customizer.</li>
</ul>
<p>Het thema werkt zonder page builder of extra plugins, laadt alleen een klein script en is volledig responsive.</p>
HTML;

$GLOBALS['websites'] = array(
	array(
		'id' => 301, 'title' => 'koomen.it', 'link' => 'website-koomen-it.html',
		'excerpt' => 'Portfolio voor embedded projecten en websites, gebouwd als eigen WordPress-thema met filters, projectgegevens en een geanimeerd codevenster.',
		'content' => $koomenit_content, 'websitetype' => array( 'portfolio' ),
		'techniek' => array( 'WordPress', 'PHP', 'JavaScript', 'CSS' ),
		'meta' => array( 'koomenit_url' => 'https://koomen.it', 'koomenit_client' => 'Eigen website', 'koomenit_role' => 'Ontwerp en ontwikkeling', 'koomenit_status' => 'ontwikkeling', 'koomenit_year' => '2026' ),
		'thumb' => 'img/koomen-it.jpg', 'image' => 'img/koomen-it-groot.jpg', 'logo' => array( '../koomenit/assets/img/logo-mark.svg', 258, 328 ),
	),
	array(
		'id' => 302, 'title' => 'Voorbeeld: website voor een fysiotherapiepraktijk', 'link' => 'websites.html',
		'excerpt' => 'Overzichtelijke website met behandelingen, team en online afspraken maken. Beheerd door de praktijk zelf.',
		'content' => $voorbeeld, 'websitetype' => array( 'bedrijfswebsite' ),
		'techniek' => array( 'WordPress', 'PHP', 'CSS' ),
		'meta' => array( 'koomenit_url' => 'https://example.com', 'koomenit_client' => 'Voorbeeldpraktijk', 'koomenit_role' => 'Ontwerp, bouw en beheer', 'koomenit_status' => 'live', 'koomenit_year' => '2025' ),
		'logo' => array( 'img/voorbeeld-logo.svg', 200, 200 ),
	),
	array(
		'id' => 303, 'title' => 'Voorbeeld: webshop voor 3D-geprinte onderdelen', 'link' => 'websites.html',
		'excerpt' => 'Webshop met productvarianten, voorraadbeheer en betalen via iDEAL.',
		'content' => $voorbeeld, 'websitetype' => array( 'webshop' ),
		'techniek' => array( 'WooCommerce', 'WordPress', 'Mollie' ),
		'meta' => array( 'koomenit_url' => 'https://example.com', 'koomenit_client' => 'Voorbeeldbedrijf', 'koomenit_role' => 'Bouw en koppelingen', 'koomenit_status' => 'onderhoud', 'koomenit_year' => '2025' ),
	),
	array(
		'id' => 304, 'title' => 'Voorbeeld: webinterface voor het ESP32-weerstation', 'link' => 'websites.html',
		'excerpt' => 'Een webpagina die rechtstreeks door de ESP32 wordt geserveerd, met actuele metingen en een grafiek van de afgelopen 24 uur.',
		'content' => $voorbeeld, 'websitetype' => array( 'webinterface' ),
		'techniek' => array( 'HTML/CSS/JS', 'ESPAsyncWebServer', 'Chart.js' ),
		'meta' => array( 'koomenit_client' => 'Eigen project', 'koomenit_role' => 'Ontwerp en ontwikkeling', 'koomenit_status' => 'live', 'koomenit_year' => '2025' ),
	),
	array(
		'id' => 305, 'title' => 'Voorbeeld: reserveringssysteem voor een sportclub', 'link' => 'websites.html',
		'excerpt' => 'Leden reserveren een baan, beheerders zien de bezetting per dag. Vervangen door een nieuw systeem.',
		'content' => $voorbeeld, 'websitetype' => array( 'webapplicatie' ),
		'techniek' => array( 'Laravel', 'PHP', 'MySQL' ),
		'meta' => array( 'koomenit_url' => 'https://example.com', 'koomenit_client' => 'Voorbeeldclub', 'koomenit_role' => 'Ontwikkeling', 'koomenit_status' => 'archief', 'koomenit_year' => '2023' ),
	),
);

$bericht_content = <<<'HTML'
<p>Veel sensoren voor Arduino werken op 5V. Op een Raspberry Pi kun je ze ook gebruiken, maar niet zomaar: de GPIO-pinnen van de Pi verdragen maximaal 3,3V. In dit bericht zie je hoe je een HC-SR04 afstandssensor veilig aansluit, zoals in de achteruitrij-parkeersensor.</p>
<h2>Waarom 5V een probleem is</h2>
<p>De HC-SR04 krijgt 5V voeding en geeft op de ECHO-pin dus ook een signaal van 5V terug. De Raspberry Pi heeft geen bescherming tegen te hoge spanning op de GPIO-pinnen. Een paar keer 5V op een ingang kan al genoeg zijn om de pin, of de hele Pi, te beschadigen. De TRIG-pin is geen probleem: de Pi stuurt daar 3,3V naartoe, en dat herkent de sensor prima als hoog signaal.</p>
<h2>De spanningsdeler</h2>
<p>Met twee weerstanden verlaag je het ECHO-signaal naar een veilige spanning. Zet 1 kΩ tussen ECHO en de GPIO-pin, en 2 kΩ van de GPIO-pin naar GND.</p>
<pre><code>HC-SR04 ECHO ──[ 1 kΩ ]──┬──── GPIO 24 (pin 18)
                         │
                      [ 2 kΩ ]
                         │
                        GND</code></pre>
<p>De spanning op de GPIO-pin wordt dan 5V × 2k / (1k + 2k) ≈ 3,3V. Precies genoeg om als hoog gelezen te worden, en niet te veel.</p>
<h3>Geen weerstand van 2 kΩ?</h3>
<p>Twee weerstanden van 1 kΩ in serie werken ook. Een combinatie van 10 kΩ en 20 kΩ geeft dezelfde verhouding, maar maakt het signaal iets gevoeliger voor storing door lange draden.</p>
<h2>Aansluiten en testen</h2>
<p>Met <code>gpiozero</code> lees je de afstand in een paar regels uit:</p>
<pre><code>from gpiozero import DistanceSensor

sensor = DistanceSensor(echo=24, trigger=23, max_distance=2)
print(f"{sensor.distance * 100:.1f} cm")</code></pre>
<p>Meet je steeds dezelfde waarde, of altijd de maximale afstand? Controleer dan of ECHO en TRIG niet zijn verwisseld en of de sensor echt 5V krijgt.</p>
<h2>Samengevat</h2>
<ul>
<li>Sluit nooit een 5V-signaal rechtstreeks aan op een GPIO-pin van de Pi.</li>
<li>Een spanningsdeler van 1 kΩ en 2 kΩ maakt van 5V ongeveer 3,3V.</li>
<li>De TRIG-pin kan direct op de Pi, omdat die alleen een signaal ontvangt.</li>
</ul>
HTML;

$kort = '<p>Dit is een voorbeeldbericht om te laten zien hoe de blog eruitziet. Vervang het door een echt artikel over je eigen project, met uitleg, codevoorbeelden en foto\'s van de opbouw.</p>';

$GLOBALS['blog'] = array(
	array( 'id' => 201, 'title' => 'Voorbeeld: 5V-sensoren veilig aansluiten op een Raspberry Pi', 'link' => 'bericht-5v-sensoren.html', 'cat' => 'Raspberry Pi', 'tags' => array( 'HC-SR04', 'GPIO', 'elektronica' ), 'excerpt' => 'Waarom een spanningsdeler nodig is voor de ECHO-pin van een HC-SR04, en hoe je de weerstanden kiest.', 'content' => $bericht_content, 'date' => '14 oktober 2026', 'thumb' => 'img/parkeersensor-kaart.jpg' ),
	array( 'id' => 202, 'title' => 'Voorbeeld: maanden op een batterij met ESP32 deep sleep', 'link' => 'bericht-5v-sensoren.html', 'cat' => 'ESP32', 'tags' => array( 'deep sleep', 'energie' ), 'excerpt' => 'Een sensor die elk kwartier meet en daarna slaapt: zo haal je het stroomverbruik omlaag.', 'content' => $kort, 'date' => '2 oktober 2026' ),
	array( 'id' => 203, 'title' => 'Voorbeeld: een ST7789-display aansturen zonder CS-pin', 'link' => 'bericht-5v-sensoren.html', 'cat' => 'Displays', 'tags' => array( 'SPI', 'ST7789' ), 'excerpt' => 'Sommige goedkope displays missen een CS-pin. Met SPI mode 3 werken ze toch.', 'content' => $kort, 'date' => '20 september 2026' ),
	array( 'id' => 204, 'title' => 'Voorbeeld: sensorwaarden middelen met een mediaanfilter', 'link' => 'bericht-5v-sensoren.html', 'cat' => 'Software', 'tags' => array( 'Python', 'sensoren' ), 'excerpt' => 'Een enkele uitschieter maakt een gemiddelde onbruikbaar. De mediaan van een paar metingen is veel stabieler.', 'content' => $kort, 'date' => '5 september 2026' ),
	array( 'id' => 205, 'title' => 'Voorbeeld: een WordPress-thema bouwen zonder page builder', 'link' => 'bericht-5v-sensoren.html', 'cat' => 'Webontwikkeling', 'tags' => array( 'WordPress', 'PHP' ), 'excerpt' => 'Eigen berichttypes, de Customizer en een paar templates: meer heb je niet nodig voor een snel portfolio.', 'content' => $kort, 'date' => '22 augustus 2026' ),
	array( 'id' => 206, 'title' => 'Voorbeeld: MQTT-berichten bekijken met een paar regels Python', 'link' => 'bericht-5v-sensoren.html', 'cat' => 'Software', 'tags' => array( 'MQTT', 'Python' ), 'excerpt' => 'Handig bij het debuggen van sensoren: een klein script dat alle berichten op een topic laat zien.', 'content' => $kort, 'date' => '8 augustus 2026' ),
	array( 'id' => 207, 'title' => 'Voorbeeld: Arduino of ESP32, wat kies je?', 'link' => 'bericht-5v-sensoren.html', 'cat' => 'ESP32', 'tags' => array( 'Arduino', 'microcontrollers' ), 'excerpt' => 'Een overzicht van de verschillen in snelheid, verbinding en stroomverbruik, met voorbeelden uit eigen projecten.', 'content' => $kort, 'date' => '25 juli 2026' ),
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
	foreach ( array_merge( $GLOBALS['projects'], $GLOBALS['websites'] ) as $p ) {
		if ( ! isset( $p[ $taxonomy ] ) ) {
			continue;
		}
		$list = 'techniek' === $taxonomy ? array_map( 'demo_slug', $p['techniek'] ) : $p[ $taxonomy ];
		$count += in_array( $slug, $list, true ) ? 1 : 0;
	}
	return (object) array( 'term_id' => $ids[ $key ], 'name' => $name ?: $GLOBALS['terms'][ $taxonomy ][ $slug ], 'slug' => $slug, 'taxonomy' => $taxonomy, 'count' => $count );
}
function demo_slug( $name ) { return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( $name ) ), '-' ); }
function demo_find( $id ) {
	foreach ( array_merge( $GLOBALS['projects'], $GLOBALS['websites'], $GLOBALS['blog'] ) as $p ) {
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
function get_theme_mod( $key, $default = false ) { return ( 'bloglijst' === $GLOBALS['page'] && 'koomenit_blog_layout' === $key ) ? 'list' : $default; }
function get_template_directory() { return $GLOBALS['theme']; }
function get_template_directory_uri() { return '../koomenit'; }
function home_url( $p = '' ) { return 'index.html'; }
function get_option( $k ) { return array( 'show_on_front' => 'page', 'page_for_posts' => 900 )[ $k ] ?? 0; }
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
function is_post_type_archive( $t = '' ) {
	$map = array( 'projecten' => 'project', 'websites' => 'website' );
	return isset( $map[ $GLOBALS['page'] ] ) && ( '' === $t || in_array( $map[ $GLOBALS['page'] ], (array) $t, true ) );
}
function is_singular( $t = '' ) { return in_array( $GLOBALS['page'], array( 'project', 'website' ), true ) && ( '' === $t || $t === $GLOBALS['page'] ); }
function post_type_archive_title() { return 'websites' === $GLOBALS['page'] ? 'Websites' : 'Projecten'; }
function get_post_type_archive_link( $type = 'project' ) { return 'website' === $type ? 'websites.html' : 'projecten.html'; }
function get_term_link( $term ) { return array( 'websitetype' => 'websites.html', 'category' => 'blog.html', 'post_tag' => 'blog.html' )[ $term->taxonomy ] ?? 'projecten.html'; }
function get_category_link( $term ) { return 'blog.html'; }
function is_home() { return in_array( $GLOBALS['page'], array( 'blog', 'bloglijst' ), true ); }
function is_category() { return false; }
function is_tag() { return false; }
function get_query_var( $v ) { return 0; }
function apply_filters( $tag, $value ) { return $value; }
function sanitize_title( $t ) { return demo_slug( $t ); }
function get_post_field( $field, $id ) { $p = demo_find( $id ); return $p['content'] ?? ''; }
function get_the_content() { return demo_cur( 'content' ); }
function get_search_query() { return ''; }
function get_search_form() { require $GLOBALS['theme'] . '/searchform.php'; }
function the_tags( $before, $sep, $after ) {
	$tags = demo_cur( 'tags' );
	if ( $tags ) {
		echo $before . implode( $sep, array_map( function ( $t ) { return '<a href="blog.html">' . esc_html( $t ) . '</a>'; }, $tags ) ) . $after;
	}
}
function the_post_navigation() {}
function comments_open() { return false; }
function get_comments_number() { return 0; }
function demo_cat( $name ) {
	$count = count( array_filter( $GLOBALS['blog'], function ( $p ) use ( $name ) { return $p['cat'] === $name; } ) );
	return (object) array( 'term_id' => 500 + crc32( $name ) % 400, 'name' => $name, 'slug' => demo_slug( $name ), 'taxonomy' => 'category', 'count' => $count );
}
function term_description() { return ''; }
function get_taxonomy( $tax ) {
	$names = array( 'platform' => 'Platform', 'projecttype' => 'Projecttype', 'techniek' => 'Techniek', 'websitetype' => 'Websitetype' );
	return (object) array( 'labels' => (object) array( 'singular_name' => $names[ $tax ] ) );
}
function wp_parse_url( $url, $part = -1 ) { return parse_url( $url, $part ); }
function get_post_type() { return isset( $GLOBALS['cur']['websitetype'] ) ? 'website' : ( isset( $GLOBALS['cur']['platform'] ) ? 'project' : 'post' ); }
function get_posts( $args ) {
	foreach ( $GLOBALS['projects'] as $p ) {
		if ( isset( $args['title'] ) && $p['title'] === $args['title'] ) {
			return array( $p['id'] );
		}
	}
	return array();
}
function get_permalink( $id ) { if ( 900 === $id ) { return 'blog.html'; } $p = demo_find( $id ); return $p ? $p['link'] : '#'; }
function wp_count_posts( $type = 'project' ) { return (object) array( 'publish' => count( 'website' === $type ? $GLOBALS['websites'] : $GLOBALS['projects'] ) ); }

function get_terms( $args ) {
	$tax = $args['taxonomy'];
	if ( 'category' === $tax ) {
		$names = array_values( array_unique( wp_list_pluck( $GLOBALS['blog'], 'cat' ) ) );
		return array_map( 'demo_cat', $names );
	}
	if ( 'techniek' === $tax ) {
		$names = array();
		foreach ( $GLOBALS['projects'] as $p ) { foreach ( $p['techniek'] as $t ) { $names[ demo_slug( $t ) ] = $t; } }
		foreach ( $GLOBALS['websites'] as $p ) { foreach ( $p['techniek'] as $t ) { $names[ demo_slug( $t ) ] = $t; } }
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
function get_post_thumbnail_id( $id = null ) {
	$p = $id ? demo_find( $id ) : $GLOBALS['cur'];
	return ! empty( $p['thumb'] ) ? $p['id'] * 10 + 1 : 0;
}
function demo_attachment( $att ) {
	$p = demo_find( intdiv( $att, 10 ) );
	if ( ! $p ) {
		return null;
	}
	if ( 1 === $att % 10 ) {
		$size = getimagesize( __DIR__ . '/' . $p['thumb'] );
		return array( $p['thumb'], $size[0], $size[1], $p['image'] ?? $p['thumb'] );
	}
	return isset( $p['logo'] ) ? array( $p['logo'][0], $p['logo'][1], $p['logo'][2], $p['logo'][0] ) : null;
}
function wp_get_attachment_image_src( $att, $size = 'full' ) { $a = demo_attachment( $att ); return $a ? array( $a[0], $a[1], $a[2] ) : false; }
function wp_get_attachment_image( $att, $size = 'full', $icon = false, $attr = array() ) {
	$a = demo_attachment( $att );
	return $a ? '<img src="' . esc_attr( 'large' === $size ? $a[3] : $a[0] ) . '" alt="' . esc_attr( $attr['alt'] ?? '' ) . '" loading="lazy">' : '';
}
function get_post_meta( $id, $key, $single = false ) {
	if ( 'koomenit_logo' === $key ) {
		$p = demo_find( $id );
		return isset( $p['logo'] ) ? $id * 10 + 2 : '';
	}
	$p = demo_find( $id );
	return isset( $p['meta'][ $key ] ) ? $p['meta'][ $key ] : '';
}

function get_header() { require $GLOBALS['theme'] . '/header.php'; }
function get_footer() { require $GLOBALS['theme'] . '/footer.php'; }
function get_template_part( $slug, $name, $args = array() ) { require $GLOBALS['theme'] . "/{$slug}-{$name}.php"; }

function wp_head() {
	$titles = array( 'home' => 'koomen.it', 'projecten' => 'Projecten – koomen.it', 'project' => 'Achteruitrij-parkeersensor – koomen.it', 'websites' => 'Websites – koomen.it', 'website' => 'koomen.it – Websites – koomen.it', 'blog' => 'Blog – koomen.it', 'bloglijst' => 'Blog – koomen.it', 'bericht' => '5V-sensoren veilig aansluiten – koomen.it' );
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
		$items   = array( 'Projecten' => 'projecten.html', 'Websites' => 'websites.html', 'Over' => 'index.html#over', 'Blog' => 'blog.html', 'Contact' => 'index.html#contact' );
		$current = '';
	} else {
		$items   = array( 'Home' => 'index.html', 'Projecten' => 'projecten.html', 'Websites' => 'websites.html', 'Over' => 'index.html#over', 'Blog' => 'blog.html' );
		$current = array( 'home' => 'Home', 'projecten' => 'Projecten', 'project' => 'Projecten', 'websites' => 'Websites', 'website' => 'Websites', 'blog' => 'Blog', 'bloglijst' => 'Blog', 'bericht' => 'Blog' )[ $GLOBALS['page'] ];
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
		$items      = array( 'project' => $GLOBALS['projects'], 'website' => $GLOBALS['websites'] )[ $type ] ?? $GLOBALS['blog'];
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
	'blog'      => $GLOBALS['blog'],
	'bloglijst' => $GLOBALS['blog'],
	'bericht'   => array( $GLOBALS['blog'][0] ),
	'projecten' => $GLOBALS['projects'],
	'project'   => array( $GLOBALS['projects'][0] ),
	'websites'  => $GLOBALS['websites'],
	'website'   => array( $GLOBALS['websites'][0] ),
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
function get_the_title( $id = null ) { return 900 === $id ? 'Blog' : demo_cur( 'title' ); }
function get_the_excerpt() { return demo_cur( 'excerpt' ); }
function has_excerpt( $id = null ) { return 900 === $id ? false : '' !== demo_cur( 'excerpt' ); }
function the_content() { echo demo_cur( 'content' ); }
function has_post_thumbnail() { return 'project' === $GLOBALS['page'] && $GLOBALS['cur'] === $GLOBALS['main'][0] ? false : (bool) demo_cur( 'thumb' ); }
function the_post_thumbnail( $size = '' ) {
	$src = ( 'large' === $size && demo_cur( 'image' ) ) ? demo_cur( 'image' ) : demo_cur( 'thumb' );
	echo '<img src="' . esc_attr( $src ) . '" alt="" loading="lazy">';
}
function get_the_category() { return demo_cur( 'cat' ) ? array( demo_cat( demo_cur( 'cat' ) ) ) : array(); }
function get_the_tags() { return array_map( function ( $t ) { return (object) array( 'name' => $t, 'slug' => demo_slug( $t ), 'taxonomy' => 'post_tag' ); }, demo_cur( 'tags' ) ?: array() ); }
function get_the_date( $f = '' ) { return $f ? '2026-10-01' : demo_cur( 'date' ); }
function get_the_author() { return 'Jeroen Koomen'; }

// --- Renderen -------------------------------------------------------------------
require $GLOBALS['theme'] . '/functions.php';
$templates = array( 'home' => 'front-page.php', 'projecten' => 'archive-project.php', 'project' => 'single-project.php', 'websites' => 'archive-website.php', 'website' => 'single-website.php', 'blog' => 'home.php', 'bloglijst' => 'home.php', 'bericht' => 'single.php' );
require $GLOBALS['theme'] . '/' . $templates[ $GLOBALS['page'] ];
