<?php
/**
 * Homepage: over ons.
 *
 * @package Blueline
 */

$blueline_points = array_filter( array_map( 'trim', explode( "\n", (string) blueline_mod( 'blueline_about_points' ) ) ) );
$blueline_image  = blueline_mod( 'blueline_about_image' );
?>
<section id="over-ons" class="section section--light">
	<div class="container split">
		<div class="split__media reveal">
			<?php if ( $blueline_image ) : ?>
				<img src="<?php echo esc_url( $blueline_image ); ?>" alt="<?php echo esc_attr( blueline_mod( 'blueline_about_title' ) ); ?>" loading="lazy">
			<?php else : ?>
				<div class="media-placeholder" aria-hidden="true">
					<div class="media-placeholder__card">
						<?php echo blueline_icon( 'shield' ); // phpcs:ignore ?>
						<strong><?php esc_html_e( 'Betrouwbaar', 'blueline' ); ?></strong>
					</div>
					<div class="media-placeholder__card media-placeholder__card--alt">
						<?php echo blueline_icon( 'chart' ); // phpcs:ignore ?>
						<strong><?php esc_html_e( 'Groei', 'blueline' ); ?></strong>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="split__content reveal">
			<?php if ( blueline_mod( 'blueline_about_eyebrow' ) ) : ?>
				<span class="eyebrow"><?php echo esc_html( blueline_mod( 'blueline_about_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h2 class="section-title"><?php echo esc_html( blueline_mod( 'blueline_about_title' ) ); ?></h2>
			<p><?php echo esc_html( blueline_mod( 'blueline_about_text' ) ); ?></p>
			<?php if ( $blueline_points ) : ?>
				<ul class="checklist">
					<?php foreach ( $blueline_points as $blueline_point ) : ?>
						<li><?php echo blueline_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( $blueline_point ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( blueline_mod( 'blueline_about_btn_text' ) ) : ?>
				<a class="btn btn--primary" href="<?php echo esc_url( blueline_mod( 'blueline_about_btn_url' ) ); ?>"><?php echo esc_html( blueline_mod( 'blueline_about_btn_text' ) ); ?> <?php echo blueline_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
