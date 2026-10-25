<?php
/**
 * Homepage: call-to-action.
 *
 * @package Jroen
 */

?>
<section id="contact" class="section section--cta">
	<div class="container">
		<div class="cta reveal">
			<div class="cta__content">
				<h2 class="cta__title"><?php echo esc_html( jroen_mod( 'jroen_cta_title' ) ); ?></h2>
				<p class="cta__text"><?php echo esc_html( jroen_mod( 'jroen_cta_text' ) ); ?></p>
			</div>
			<?php if ( jroen_mod( 'jroen_cta_btn_text' ) ) : ?>
				<a class="btn btn--white" href="<?php echo esc_url( jroen_mod( 'jroen_cta_btn_url' ) ); ?>"><?php echo esc_html( jroen_mod( 'jroen_cta_btn_text' ) ); ?> <?php echo jroen_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
