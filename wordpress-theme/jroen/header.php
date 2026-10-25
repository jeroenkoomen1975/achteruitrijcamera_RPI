<?php
/**
 * Header-template.
 *
 * @package Jroen
 */

$jroen_dark_default = jroen_mod( 'jroen_dark_default' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<script>
		( function ( d, def ) {
			d.classList.add( 'js' );
			var saved = null;
			try { saved = localStorage.getItem( 'jroen-theme' ); } catch ( e ) {}
			var mode = saved || def;
			if ( 'auto' === mode ) {
				mode = window.matchMedia && window.matchMedia( '(prefers-color-scheme: dark)' ).matches ? 'dark' : 'light';
			}
			if ( 'dark' === mode ) { d.classList.add( 'dark' ); }
		} )( document.documentElement, <?php echo wp_json_encode( $jroen_dark_default ); ?> );
	</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Naar de inhoud', 'jroen' ); ?></a>

<?php if ( jroen_mod( 'jroen_topbar_show' ) ) : ?>
	<div class="topbar">
		<div class="container topbar__inner">
			<span class="topbar__text"><span class="topbar__dot" aria-hidden="true"></span><?php echo esc_html( jroen_mod( 'jroen_topbar_text' ) ); ?></span>
			<div class="topbar__contact">
				<?php $phone = jroen_mod( 'jroen_topbar_phone' ); ?>
				<?php if ( $phone ) : ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
				<?php endif; ?>
				<?php if ( jroen_mod( 'jroen_topbar_right' ) ) : ?>
					<span class="topbar__right"><?php echo esc_html( jroen_mod( 'jroen_topbar_right' ) ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<header id="masthead" class="site-header">
	<div class="container site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<?php jroen_site_title(); ?>
			<?php endif; ?>
		</div>

		<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Hoofdmenu', 'jroen' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'fallback_cb'    => 'jroen_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<?php if ( jroen_mod( 'jroen_dark_toggle' ) ) : ?>
				<button class="theme-toggle" type="button" aria-label="<?php esc_attr_e( 'Donkere modus aan of uit', 'jroen' ); ?>" aria-pressed="false">
					<svg class="theme-toggle__moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
					<svg class="theme-toggle__sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
				</button>
			<?php endif; ?>
			<?php $cta = jroen_mod( 'jroen_header_cta_text' ); ?>
			<?php if ( $cta ) : ?>
				<a class="btn btn--primary btn--sm header-cta" href="<?php echo esc_url( jroen_mod( 'jroen_header_cta_url' ) ); ?>"><?php echo esc_html( $cta ); ?></a>
			<?php endif; ?>
			<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false">
				<span class="menu-toggle__bar"></span>
				<span class="menu-toggle__bar"></span>
				<span class="menu-toggle__bar"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'jroen' ); ?></span>
			</button>
		</div>
	</div>
</header>
