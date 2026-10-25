<?php
/**
 * Footer-template.
 *
 * @package Jroen
 */

$jroen_copy = jroen_mod( 'jroen_footer_copy' );
if ( ! $jroen_copy ) {
	/* translators: 1: jaartal, 2: sitenaam */
	$jroen_copy = sprintf( __( '© %1$s %2$s. Alle rechten voorbehouden.', 'jroen' ), gmdate( 'Y' ), get_bloginfo( 'name' ) );
}
?>

<footer id="colophon" class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<?php jroen_site_title( true ); ?>
			<p><?php echo esc_html( jroen_mod( 'jroen_footer_about' ) ); ?></p>
			<?php jroen_social_links(); ?>
		</div>

		<?php for ( $jroen_i = 1; $jroen_i <= 3; $jroen_i++ ) : ?>
			<div class="site-footer__col">
				<?php if ( is_active_sidebar( 'footer-' . $jroen_i ) ) : ?>
					<?php dynamic_sidebar( 'footer-' . $jroen_i ); ?>
				<?php elseif ( 1 === $jroen_i ) : ?>
					<h4 class="footer-title"><?php esc_html_e( 'Platforms', 'jroen' ); ?></h4>
					<ul>
						<?php foreach ( jroen_get_services() as $jroen_service ) : ?>
							<li><a href="<?php echo esc_url( $jroen_service['url'] ? $jroen_service['url'] : '#platforms' ); ?>"><?php echo esc_html( $jroen_service['title'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ( 2 === $jroen_i ) : ?>
					<h4 class="footer-title"><?php esc_html_e( 'Navigatie', 'jroen' ); ?></h4>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => 'jroen_menu_fallback',
						)
					);
					?>
				<?php else : ?>
					<h4 class="footer-title"><?php esc_html_e( 'Contact', 'jroen' ); ?></h4>
					<ul class="footer-contact">
						<?php if ( jroen_mod( 'jroen_footer_address' ) ) : ?>
							<li><?php echo jroen_icon( 'pin' ); // phpcs:ignore ?><span><?php echo nl2br( esc_html( jroen_mod( 'jroen_footer_address' ) ) ); ?></span></li>
						<?php endif; ?>
						<?php if ( jroen_mod( 'jroen_topbar_phone' ) ) : ?>
							<li><?php echo jroen_icon( 'phone' ); // phpcs:ignore ?><span><?php echo esc_html( jroen_mod( 'jroen_topbar_phone' ) ); ?></span></li>
						<?php endif; ?>
						<?php if ( jroen_mod( 'jroen_topbar_email' ) ) : ?>
							<li><?php echo jroen_icon( 'mail' ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( antispambot( jroen_mod( 'jroen_topbar_email' ) ) ); ?>"><?php echo esc_html( antispambot( jroen_mod( 'jroen_topbar_email' ) ) ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endfor; ?>
	</div>

	<div class="site-footer__bottom">
		<div class="container">
			<span><?php echo esc_html( $jroen_copy ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
