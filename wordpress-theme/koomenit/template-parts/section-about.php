<?php
/**
 * Homepage: over ons.
 *
 * @package KoomenIT
 */

$koomenit_points = array_filter( array_map( 'trim', explode( "\n", (string) koomenit_mod( 'koomenit_about_points' ) ) ) );
$koomenit_image  = koomenit_mod( 'koomenit_about_image' );
?>
<section id="over-ons" class="section section--light">
	<div class="container split">
		<div class="split__media reveal">
			<?php if ( $koomenit_image ) : ?>
				<img src="<?php echo esc_url( $koomenit_image ); ?>" alt="<?php echo esc_attr( koomenit_mod( 'koomenit_about_title' ) ); ?>" loading="lazy">
			<?php else : ?>
				<div class="media-placeholder" aria-hidden="true">
					<div class="media-placeholder__card">
						<?php echo koomenit_icon( 'shield' ); // phpcs:ignore ?>
						<strong><?php esc_html_e( 'Betrouwbaar', 'koomenit' ); ?></strong>
					</div>
					<div class="media-placeholder__card media-placeholder__card--alt">
						<?php echo koomenit_icon( 'chart' ); // phpcs:ignore ?>
						<strong><?php esc_html_e( 'Groei', 'koomenit' ); ?></strong>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="split__content reveal">
			<?php if ( koomenit_mod( 'koomenit_about_eyebrow' ) ) : ?>
				<span class="eyebrow"><?php echo esc_html( koomenit_mod( 'koomenit_about_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h2 class="section-title"><?php echo esc_html( koomenit_mod( 'koomenit_about_title' ) ); ?></h2>
			<p><?php echo esc_html( koomenit_mod( 'koomenit_about_text' ) ); ?></p>
			<?php if ( $koomenit_points ) : ?>
				<ul class="checklist">
					<?php foreach ( $koomenit_points as $koomenit_point ) : ?>
						<li><?php echo koomenit_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( $koomenit_point ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( koomenit_mod( 'koomenit_about_btn_text' ) ) : ?>
				<a class="btn btn--primary" href="<?php echo esc_url( koomenit_mod( 'koomenit_about_btn_url' ) ); ?>"><?php echo esc_html( koomenit_mod( 'koomenit_about_btn_text' ) ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
