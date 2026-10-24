<?php
/**
 * Homepage: call-to-action.
 *
 * @package Blueline
 */

?>
<section id="contact" class="section section--cta">
	<div class="container">
		<div class="cta reveal">
			<div class="cta__content">
				<h2 class="cta__title"><?php echo esc_html( blueline_mod( 'blueline_cta_title' ) ); ?></h2>
				<p class="cta__text"><?php echo esc_html( blueline_mod( 'blueline_cta_text' ) ); ?></p>
			</div>
			<?php if ( blueline_mod( 'blueline_cta_btn_text' ) ) : ?>
				<a class="btn btn--white" href="<?php echo esc_url( blueline_mod( 'blueline_cta_btn_url' ) ); ?>"><?php echo esc_html( blueline_mod( 'blueline_cta_btn_text' ) ); ?> <?php echo blueline_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
