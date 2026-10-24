<?php
/**
 * Hulpfuncties voor templates.
 *
 * @package KoomenIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inline SVG-icoon teruggeven.
 *
 * @param string $name Iconnaam.
 * @return string
 */
function koomenit_icon( $name ) {
	$paths = array(
		'users'      => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
		'lightbulb'  => '<path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.1V18h6v-1.2c0-.8.4-1.6 1-2.1A7 7 0 0 0 12 2z"/>',
		'graduation' => '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/>',
		'cloud'      => '<path d="M17.5 19H7a5 5 0 1 1 .9-9.9A6 6 0 0 1 19 11a4 4 0 0 1-1.5 8z"/>',
		'shield'     => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
		'code'       => '<path d="m16 18 6-6-6-6"/><path d="m8 6-6 6 6 6"/>',
		'server'     => '<rect x="2" y="3" width="20" height="8" rx="2"/><rect x="2" y="13" width="20" height="8" rx="2"/><path d="M6 7h.01M6 17h.01"/>',
		'chart'      => '<path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 6-6"/>',
		'network'    => '<circle cx="5" cy="6" r="2"/><circle cx="19" cy="5" r="2"/><circle cx="12" cy="19" r="2"/><circle cx="20" cy="15" r="2"/><path d="M7 6.2l10-1M6 7.8l5 9.4M18.6 7l-5.8 10.2M19.2 7l.6 6M18.2 16.1 13.8 18"/>',
		'check'      => '<path d="M20 6 9 17l-5-5"/>',
		'arrow'      => '<path d="M5 12h14"/><path d="m13 5 7 7-7 7"/>',
		'phone'      => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
		'mail'       => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/>',
		'pin'        => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
		'linkedin'   => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
		'facebook'   => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
		'instagram'  => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
		'x'          => '<path d="M4 4l16 16M20 4 4 20"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return '<svg class="bl-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Dienstenitems uit de Customizer.
 *
 * @return array
 */
function koomenit_get_services() {
	$items = array();
	foreach ( koomenit_repeat_defaults()['services'] as $i => $default ) {
		$title = get_theme_mod( "koomenit_service{$i}_title", $default['title'] );
		if ( '' === trim( $title ) ) {
			continue;
		}
		$items[] = array(
			'icon'  => get_theme_mod( "koomenit_service{$i}_icon", $default['icon'] ),
			'title' => $title,
			'text'  => get_theme_mod( "koomenit_service{$i}_text", $default['text'] ),
			'url'   => get_theme_mod( "koomenit_service{$i}_url", '' ),
		);
	}
	return $items;
}

/**
 * Cijfers uit de Customizer.
 *
 * @return array
 */
function koomenit_get_stats() {
	$items = array();
	foreach ( koomenit_repeat_defaults()['stats'] as $i => $default ) {
		$label = get_theme_mod( "koomenit_stat{$i}_label", $default['label'] );
		if ( '' === trim( $label ) ) {
			continue;
		}
		$items[] = array(
			'number' => get_theme_mod( "koomenit_stat{$i}_number", $default['number'] ),
			'suffix' => get_theme_mod( "koomenit_stat{$i}_suffix", $default['suffix'] ),
			'label'  => $label,
		);
	}
	return $items;
}

/**
 * Social-media links tonen.
 */
function koomenit_social_links() {
	$networks = array( 'linkedin', 'facebook', 'instagram', 'x' );
	$output   = '';
	foreach ( $networks as $network ) {
		$url = koomenit_mod( 'koomenit_social_' . $network );
		if ( $url ) {
			$output .= sprintf(
				'<a href="%1$s" class="social-link" target="_blank" rel="noopener" aria-label="%2$s">%3$s</a>',
				esc_url( $url ),
				esc_attr( ucfirst( $network ) ),
				koomenit_icon( $network )
			);
		}
	}
	if ( $output ) {
		echo '<div class="social-links">' . $output . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- opgebouwd uit geëscapete delen.
	}
}

/**
 * Datum en auteur van een bericht.
 */
function koomenit_posted_on() {
	printf(
		'<span class="posted-on"><time datetime="%1$s">%2$s</time></span><span class="byline"> · %3$s</span>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		esc_html( get_the_author() )
	);
}

/**
 * Pagina-header voor binnenpagina's.
 *
 * @param string $title    Titel.
 * @param string $subtitle Ondertitel.
 */
function koomenit_page_header( $title, $subtitle = '' ) {
	?>
	<header class="page-hero">
		<div class="container">
			<h1 class="page-hero__title"><?php echo wp_kses_post( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="page-hero__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</header>
	<?php
}

/**
 * Fallback voor het hoofdmenu als er nog geen menu is ingesteld.
 */
function koomenit_menu_fallback() {
	echo '<ul class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'koomenit' ) . '</a></li>';
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
		)
	);
	echo '</ul>';
}
