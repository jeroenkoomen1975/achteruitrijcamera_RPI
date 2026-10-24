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
		'wifi'        => '<path d="M5 12.55a11 11 0 0 1 14 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><path d="M12 20h.01"/>',
		'chip'        => '<rect x="6" y="6" width="12" height="12" rx="1.5"/><path d="M9 2v4M15 2v4M9 18v4M15 18v4M2 9h4M2 15h4M18 9h4M18 15h4"/><circle cx="12" cy="12" r="2"/>',
		'cpu'         => '<rect x="3" y="5" width="18" height="14" rx="2"/><rect x="7" y="9" width="5" height="6" rx="0.5"/><path d="M15 9h3M15 12h3M15 15h3M6 2v3M10 2v3M14 2v3M18 2v3"/>',
		'monitor'     => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="m8 8-2 2 2 2M16 8l2 2-2 2"/>',
		'terminal'    => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m6 9 3 3-3 3M12 15h6"/>',
		'thermometer' => '<path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/>',
		'users'       => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
		'lightbulb'   => '<path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.1V18h6v-1.2c0-.8.4-1.6 1-2.1A7 7 0 0 0 12 2z"/>',
		'graduation'  => '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/>',
		'cloud'       => '<path d="M17.5 19H7a5 5 0 1 1 .9-9.9A6 6 0 0 1 19 11a4 4 0 0 1-1.5 8z"/>',
		'shield'      => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
		'code'        => '<path d="m16 18 6-6-6-6"/><path d="m8 6-6 6 6 6"/>',
		'server'      => '<rect x="2" y="3" width="20" height="8" rx="2"/><rect x="2" y="13" width="20" height="8" rx="2"/><path d="M6 7h.01M6 17h.01"/>',
		'chart'       => '<path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 6-6"/>',
		'network'     => '<circle cx="5" cy="6" r="2"/><circle cx="19" cy="5" r="2"/><circle cx="12" cy="19" r="2"/><circle cx="20" cy="15" r="2"/><path d="M7 6.2l10-1M6 7.8l5 9.4M18.6 7l-5.8 10.2M19.2 7l.6 6M18.2 16.1 13.8 18"/>',
		'check'       => '<path d="M20 6 9 17l-5-5"/>',
		'arrow'       => '<path d="M5 12h14"/><path d="m13 5 7 7-7 7"/>',
		'external'    => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
		'calendar'    => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'phone'       => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
		'mail'        => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/>',
		'pin'         => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
		'github'      => '<path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>',
		'linkedin'    => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
		'youtube'     => '<path d="M22.5 6.4a2.8 2.8 0 0 0-2-2C18.9 4 12 4 12 4s-6.9 0-8.5.4a2.8 2.8 0 0 0-2 2A29 29 0 0 0 1 12a29 29 0 0 0 .5 5.6 2.8 2.8 0 0 0 2 2c1.6.4 8.5.4 8.5.4s6.9 0 8.5-.4a2.8 2.8 0 0 0 2-2A29 29 0 0 0 23 12a29 29 0 0 0-.5-5.6z"/><path d="m9.75 15.02 5.75-3.02-5.75-3.02z"/>',
		'instagram'   => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return '<svg class="bl-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Sitenaam als logo-tekst: het deel na de laatste punt (".it") krijgt een eigen kleur.
 *
 * @return string
 */
function koomenit_wordmark() {
	$name = get_bloginfo( 'name' );
	$dot  = strrpos( $name, '.' );
	if ( false === $dot || 0 === $dot ) {
		return esc_html( $name );
	}
	return esc_html( substr( $name, 0, $dot ) ) . '<span class="site-title__tld">' . esc_html( substr( $name, $dot ) ) . '</span>';
}

/**
 * Logo met beeldmerk en sitenaam.
 *
 * @param bool $dark Variant voor een donkere ondergrond.
 */
function koomenit_site_title( $dark = false ) {
	$mark = get_template_directory_uri() . '/assets/img/' . ( $dark ? 'logo-mark-dark.svg' : 'logo-mark.svg' );
	?>
	<a class="site-title<?php echo $dark ? ' site-title--light' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<img class="site-title__icon" src="<?php echo esc_url( $mark ); ?>" alt="" width="258" height="328">
		<span class="site-title__words">
			<span class="site-title__text"><?php echo koomenit_wordmark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- geëscapet in koomenit_wordmark(). ?></span>
			<?php if ( get_bloginfo( 'description' ) ) : ?>
				<span class="site-title__tagline"><?php bloginfo( 'description' ); ?></span>
			<?php endif; ?>
		</span>
	</a>
	<?php
}

/**
 * Favicon van het thema als er in de Customizer geen site-icoon is gekozen.
 */
function koomenit_fallback_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	$base = get_template_directory_uri() . '/assets/img/';
	echo '<link rel="icon" href="' . esc_url( $base . 'favicon-32.png' ) . '" sizes="32x32">' . "\n";
	echo '<link rel="icon" href="' . esc_url( $base . 'app-icon.png' ) . '" sizes="512x512">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $base . 'apple-touch-icon.png' ) . '">' . "\n";
}
add_action( 'wp_head', 'koomenit_fallback_favicon' );

/**
 * Platformkaarten uit de Customizer. Zonder eigen link wordt automatisch
 * naar het platform met dezelfde naam gelinkt, met het aantal projecten erbij.
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
		$url   = get_theme_mod( "koomenit_service{$i}_url", '' );
		$count = null;
		$term  = get_term_by( 'name', $title, 'platform' );
		if ( $term && ! is_wp_error( $term ) ) {
			$count = (int) $term->count;
			if ( ! $url && $count ) {
				$url = get_term_link( $term );
			}
		}
		$items[] = array(
			'icon'  => get_theme_mod( "koomenit_service{$i}_icon", $default['icon'] ),
			'title' => $title,
			'text'  => get_theme_mod( "koomenit_service{$i}_text", $default['text'] ),
			'url'   => is_wp_error( $url ) ? '' : $url,
			'count' => $count,
		);
	}
	return $items;
}

/**
 * Aantal termen met minstens één project.
 *
 * @param string $taxonomy Indeling.
 * @return int
 */
function koomenit_count_used_terms( $taxonomy ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'fields'     => 'ids',
		)
	);
	return is_wp_error( $terms ) ? 0 : count( $terms );
}

/**
 * Cijfers uit de Customizer, eventueel automatisch geteld.
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
		switch ( get_theme_mod( "koomenit_stat{$i}_source", $default['source'] ) ) {
			case 'projects':
				$counts = wp_count_posts( 'project' );
				$number = isset( $counts->publish ) ? (int) $counts->publish : 0;
				break;
			case 'platforms':
				$number = koomenit_count_used_terms( 'platform' );
				break;
			case 'techniques':
				$number = koomenit_count_used_terms( 'techniek' );
				break;
			default:
				$number = absint( get_theme_mod( "koomenit_stat{$i}_number", $default['number'] ) );
		}
		$items[] = array(
			'number' => $number,
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
	$networks = array(
		'github'    => 'GitHub',
		'linkedin'  => 'LinkedIn',
		'youtube'   => 'YouTube',
		'instagram' => 'Instagram',
	);
	$output   = '';
	foreach ( $networks as $network => $label ) {
		$url = koomenit_mod( 'koomenit_social_' . $network );
		if ( $url ) {
			$output .= sprintf(
				'<a href="%1$s" class="social-link" target="_blank" rel="noopener" aria-label="%2$s">%3$s</a>',
				esc_url( $url ),
				esc_attr( $label ),
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
 * @param string $eyebrow  Label boven de titel.
 */
function koomenit_page_header( $title, $subtitle = '', $eyebrow = '' ) {
	?>
	<header class="page-hero">
		<?php koomenit_network_svg( 'page-hero__network' ); ?>
		<div class="container">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow eyebrow--light"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h1 class="page-hero__title"><?php echo wp_kses_post( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<div class="page-hero__subtitle"><?php echo wp_kses_post( $subtitle ); ?></div>
			<?php endif; ?>
		</div>
	</header>
	<?php
}

/**
 * Decoratief netwerk van lijnen en knooppunten, zoals in het logo.
 *
 * @param string $class CSS-klasse.
 */
function koomenit_network_svg( $class ) {
	?>
	<svg class="<?php echo esc_attr( $class ); ?> network" viewBox="0 0 1440 640" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
		<g class="network__lines">
			<path d="M80 120 340 60 520 210 300 330 80 120zM520 210 760 90 980 170 860 360 520 210zM300 330 140 520 470 600 860 360M980 170 1240 80 1380 300 1160 470 860 360M1160 470 1300 600M470 600 760 520 1160 470"/>
		</g>
		<g class="network__nodes">
			<circle cx="80" cy="120" r="7"/><circle cx="340" cy="60" r="6"/><circle cx="300" cy="330" r="6"/>
			<circle cx="760" cy="90" r="6"/><circle cx="860" cy="360" r="7"/><circle cx="140" cy="520" r="6"/>
			<circle cx="470" cy="600" r="7"/><circle cx="520" cy="210" r="7"/><circle cx="1380" cy="300" r="8"/>
			<circle cx="1300" cy="600" r="6"/><circle cx="760" cy="520" r="5"/>
		</g>
		<circle class="network__light" cx="980" cy="170" r="16"/>
		<circle class="network__blue" cx="1240" cy="80" r="12"/>
		<circle class="network__blue" cx="1160" cy="470" r="11"/>
	</svg>
	<?php
}

/**
 * Fallback voor het hoofdmenu als er nog geen menu is ingesteld.
 */
function koomenit_menu_fallback() {
	echo '<ul class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'koomenit' ) . '</a></li>';
	$archive = get_post_type_archive_link( 'project' );
	if ( $archive ) {
		echo '<li' . ( is_post_type_archive( 'project' ) || is_singular( 'project' ) ? ' class="current-menu-item"' : '' ) . '><a href="' . esc_url( $archive ) . '">' . esc_html__( 'Projecten', 'koomenit' ) . '</a></li>';
	}
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
		)
	);
	echo '</ul>';
}
