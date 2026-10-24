<?php
/**
 * Footer-template.
 *
 * @package KoomenIT
 */

$koomenit_copy = koomenit_mod( 'koomenit_footer_copy' );
if ( ! $koomenit_copy ) {
	/* translators: 1: jaartal, 2: sitenaam */
	$koomenit_copy = sprintf( __( '© %1$s %2$s. Alle rechten voorbehouden.', 'koomenit' ), gmdate( 'Y' ), get_bloginfo( 'name' ) );
}
?>

<footer id="colophon" class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<a class="site-title site-title--light" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img class="site-title__icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-icon-light.png' ); ?>" alt="" width="258" height="320">
				<span class="site-title__words">
					<span class="site-title__text"><?php bloginfo( 'name' ); ?></span>
					<?php if ( get_bloginfo( 'description' ) ) : ?>
						<span class="site-title__tagline"><?php bloginfo( 'description' ); ?></span>
					<?php endif; ?>
				</span>
			</a>
			<p><?php echo esc_html( koomenit_mod( 'koomenit_footer_about' ) ); ?></p>
			<?php koomenit_social_links(); ?>
		</div>

		<?php for ( $koomenit_i = 1; $koomenit_i <= 3; $koomenit_i++ ) : ?>
			<div class="site-footer__col">
				<?php if ( is_active_sidebar( 'footer-' . $koomenit_i ) ) : ?>
					<?php dynamic_sidebar( 'footer-' . $koomenit_i ); ?>
				<?php elseif ( 1 === $koomenit_i ) : ?>
					<h4 class="footer-title"><?php esc_html_e( 'Aanbod', 'koomenit' ); ?></h4>
					<ul>
						<?php foreach ( koomenit_get_services() as $koomenit_service ) : ?>
							<li><a href="<?php echo esc_url( $koomenit_service['url'] ? $koomenit_service['url'] : '#aanbod' ); ?>"><?php echo esc_html( $koomenit_service['title'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ( 2 === $koomenit_i ) : ?>
					<h4 class="footer-title"><?php esc_html_e( 'Navigatie', 'koomenit' ); ?></h4>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => 'koomenit_menu_fallback',
						)
					);
					?>
				<?php else : ?>
					<h4 class="footer-title"><?php esc_html_e( 'Contact', 'koomenit' ); ?></h4>
					<ul class="footer-contact">
						<?php if ( koomenit_mod( 'koomenit_footer_address' ) ) : ?>
							<li><?php echo koomenit_icon( 'pin' ); // phpcs:ignore ?><span><?php echo nl2br( esc_html( koomenit_mod( 'koomenit_footer_address' ) ) ); ?></span></li>
						<?php endif; ?>
						<?php if ( koomenit_mod( 'koomenit_topbar_phone' ) ) : ?>
							<li><?php echo koomenit_icon( 'phone' ); // phpcs:ignore ?><span><?php echo esc_html( koomenit_mod( 'koomenit_topbar_phone' ) ); ?></span></li>
						<?php endif; ?>
						<?php if ( koomenit_mod( 'koomenit_topbar_email' ) ) : ?>
							<li><?php echo koomenit_icon( 'mail' ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( antispambot( koomenit_mod( 'koomenit_topbar_email' ) ) ); ?>"><?php echo esc_html( antispambot( koomenit_mod( 'koomenit_topbar_email' ) ) ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endfor; ?>
	</div>

	<div class="site-footer__bottom">
		<div class="container">
			<span><?php echo esc_html( $koomenit_copy ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
