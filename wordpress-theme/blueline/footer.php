<?php
/**
 * Footer-template.
 *
 * @package Blueline
 */

$blueline_copy = blueline_mod( 'blueline_footer_copy' );
if ( ! $blueline_copy ) {
	/* translators: 1: jaartal, 2: sitenaam */
	$blueline_copy = sprintf( __( '© %1$s %2$s. Alle rechten voorbehouden.', 'blueline' ), gmdate( 'Y' ), get_bloginfo( 'name' ) );
}
?>

<footer id="colophon" class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<a class="site-title site-title--light" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="site-title__mark" aria-hidden="true"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span>
				<span class="site-title__text"><?php bloginfo( 'name' ); ?></span>
			</a>
			<p><?php echo esc_html( blueline_mod( 'blueline_footer_about' ) ); ?></p>
			<?php blueline_social_links(); ?>
		</div>

		<?php for ( $blueline_i = 1; $blueline_i <= 3; $blueline_i++ ) : ?>
			<div class="site-footer__col">
				<?php if ( is_active_sidebar( 'footer-' . $blueline_i ) ) : ?>
					<?php dynamic_sidebar( 'footer-' . $blueline_i ); ?>
				<?php elseif ( 1 === $blueline_i ) : ?>
					<h4 class="footer-title"><?php esc_html_e( 'Diensten', 'blueline' ); ?></h4>
					<ul>
						<?php foreach ( blueline_get_services() as $blueline_service ) : ?>
							<li><a href="<?php echo esc_url( $blueline_service['url'] ? $blueline_service['url'] : '#diensten' ); ?>"><?php echo esc_html( $blueline_service['title'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ( 2 === $blueline_i ) : ?>
					<h4 class="footer-title"><?php esc_html_e( 'Navigatie', 'blueline' ); ?></h4>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => 'blueline_menu_fallback',
						)
					);
					?>
				<?php else : ?>
					<h4 class="footer-title"><?php esc_html_e( 'Contact', 'blueline' ); ?></h4>
					<ul class="footer-contact">
						<?php if ( blueline_mod( 'blueline_footer_address' ) ) : ?>
							<li><?php echo blueline_icon( 'pin' ); // phpcs:ignore ?><span><?php echo nl2br( esc_html( blueline_mod( 'blueline_footer_address' ) ) ); ?></span></li>
						<?php endif; ?>
						<?php if ( blueline_mod( 'blueline_topbar_phone' ) ) : ?>
							<li><?php echo blueline_icon( 'phone' ); // phpcs:ignore ?><span><?php echo esc_html( blueline_mod( 'blueline_topbar_phone' ) ); ?></span></li>
						<?php endif; ?>
						<?php if ( blueline_mod( 'blueline_topbar_email' ) ) : ?>
							<li><?php echo blueline_icon( 'mail' ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( antispambot( blueline_mod( 'blueline_topbar_email' ) ) ); ?>"><?php echo esc_html( antispambot( blueline_mod( 'blueline_topbar_email' ) ) ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endfor; ?>
	</div>

	<div class="site-footer__bottom">
		<div class="container">
			<span><?php echo esc_html( $blueline_copy ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
