<?php
/**
 * Header-template.
 *
 * @package Jroen
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<script>document.documentElement.classList.add('js');</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Naar de inhoud', 'jroen' ); ?></a>

<?php if ( jroen_mod( 'jroen_topbar_show' ) ) : ?>
	<div class="topbar">
		<div class="container topbar__inner">
			<span class="topbar__text"><?php echo esc_html( jroen_mod( 'jroen_topbar_text' ) ); ?></span>
			<div class="topbar__contact">
				<?php $phone = jroen_mod( 'jroen_topbar_phone' ); ?>
				<?php if ( $phone ) : ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo jroen_icon( 'phone' ); // phpcs:ignore ?> <?php echo esc_html( $phone ); ?></a>
				<?php endif; ?>
				<?php $email = jroen_mod( 'jroen_topbar_email' ); ?>
				<?php if ( $email ) : ?>
					<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo jroen_icon( 'mail' ); // phpcs:ignore ?> <?php echo esc_html( antispambot( $email ) ); ?></a>
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

		<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false">
			<span class="menu-toggle__bar"></span>
			<span class="menu-toggle__bar"></span>
			<span class="menu-toggle__bar"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'jroen' ); ?></span>
		</button>

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
			<?php $cta = jroen_mod( 'jroen_header_cta_text' ); ?>
			<?php if ( $cta ) : ?>
				<a class="btn btn--primary btn--sm header-cta" href="<?php echo esc_url( jroen_mod( 'jroen_header_cta_url' ) ); ?>"><?php echo esc_html( $cta ); ?></a>
			<?php endif; ?>
		</nav>
	</div>
</header>
