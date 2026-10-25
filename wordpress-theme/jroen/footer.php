<?php
/**
 * Footer-template: contactsectie en footer.
 *
 * @package Jroen
 */

$jroen_copy = jroen_mod( 'jroen_footer_copy' );
if ( ! $jroen_copy ) {
	/* translators: 1: jaartal, 2: sitenaam */
	$jroen_copy = sprintf( __( '© %1$s %2$s • Alle rechten voorbehouden.', 'jroen' ), gmdate( 'Y' ), get_bloginfo( 'name' ) );
}
$jroen_widgets = is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' );

if ( jroen_mod( 'jroen_contact_show' ) ) {
	get_template_part( 'template-parts/contact' );
}
?>

<footer id="colophon" class="site-footer">
	<?php if ( $jroen_widgets ) : ?>
		<div class="container site-footer__widgets">
			<?php for ( $jroen_i = 1; $jroen_i <= 3; $jroen_i++ ) : ?>
				<div class="site-footer__col"><?php dynamic_sidebar( 'footer-' . $jroen_i ); ?></div>
			<?php endfor; ?>
		</div>
	<?php endif; ?>
	<div class="container site-footer__bar">
		<div class="site-footer__brand">
			<?php jroen_site_title( true ); ?>
			<?php jroen_social_links(); ?>
		</div>
		<div class="site-footer__meta">
			<?php if ( jroen_mod( 'jroen_footer_note' ) ) : ?>
				<p><?php echo esc_html( jroen_mod( 'jroen_footer_note' ) ); ?></p>
			<?php endif; ?>
			<p><?php echo esc_html( $jroen_copy ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
