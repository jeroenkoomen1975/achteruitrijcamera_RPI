<?php
/**
 * Homepage: over mij.
 *
 * @package Jroen
 */

$jroen_points = array_filter( array_map( 'trim', explode( "\n", (string) jroen_mod( 'jroen_about_points' ) ) ) );
$jroen_image  = jroen_mod( 'jroen_about_image' );
?>
<section id="over" class="section section--light">
	<div class="container split">
		<div class="split__media reveal">
			<?php if ( $jroen_image ) : ?>
				<img src="<?php echo esc_url( $jroen_image ); ?>" alt="<?php echo esc_attr( jroen_mod( 'jroen_about_title' ) ); ?>" loading="lazy">
			<?php else : ?>
				<div class="media-placeholder" aria-hidden="true">
					<?php jroen_network_svg( 'media-placeholder__network' ); ?>
					<img class="media-placeholder__mark" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-mark-dark.svg' ); ?>" alt="" width="258" height="328">
					<div class="media-placeholder__card">
						<?php echo jroen_icon( 'code' ); // phpcs:ignore ?>
						<strong>C++</strong>
					</div>
					<div class="media-placeholder__card media-placeholder__card--alt">
						<?php echo jroen_icon( 'terminal' ); // phpcs:ignore ?>
						<strong>Python</strong>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="split__content reveal">
			<?php if ( jroen_mod( 'jroen_about_eyebrow' ) ) : ?>
				<span class="eyebrow"><?php echo esc_html( jroen_mod( 'jroen_about_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h2 class="section-title"><?php echo esc_html( jroen_mod( 'jroen_about_title' ) ); ?></h2>
			<p><?php echo esc_html( jroen_mod( 'jroen_about_text' ) ); ?></p>
			<?php if ( $jroen_points ) : ?>
				<ul class="checklist">
					<?php foreach ( $jroen_points as $jroen_point ) : ?>
						<li><?php echo jroen_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( $jroen_point ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( jroen_mod( 'jroen_about_btn_text' ) ) : ?>
				<a class="btn btn--primary" href="<?php echo esc_url( jroen_mod( 'jroen_about_btn_url' ) ); ?>"><?php echo esc_html( jroen_mod( 'jroen_about_btn_text' ) ); ?> <?php echo jroen_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
