<?php
/**
 * Homepage: diensten.
 *
 * @package Blueline
 */

?>
<section id="diensten" class="section section--services">
	<div class="container">
		<div class="section-head reveal">
			<h2 class="section-title"><?php echo esc_html( blueline_mod( 'blueline_services_title' ) ); ?></h2>
			<?php if ( blueline_mod( 'blueline_services_intro' ) ) : ?>
				<p class="section-intro"><?php echo esc_html( blueline_mod( 'blueline_services_intro' ) ); ?></p>
			<?php endif; ?>
		</div>
		<div class="cards">
			<?php foreach ( blueline_get_services() as $blueline_service ) : ?>
				<article class="card reveal">
					<div class="card__icon"><?php echo blueline_icon( $blueline_service['icon'] ); // phpcs:ignore ?></div>
					<h3 class="card__title"><?php echo esc_html( $blueline_service['title'] ); ?></h3>
					<p class="card__text"><?php echo esc_html( $blueline_service['text'] ); ?></p>
					<?php if ( $blueline_service['url'] ) : ?>
						<a class="card__link" href="<?php echo esc_url( $blueline_service['url'] ); ?>"><?php esc_html_e( 'Lees meer', 'blueline' ); ?> <?php echo blueline_icon( 'arrow' ); // phpcs:ignore ?></a>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
