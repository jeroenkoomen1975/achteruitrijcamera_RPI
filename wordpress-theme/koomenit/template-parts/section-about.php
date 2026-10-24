<?php
/**
 * Homepage: over mij.
 *
 * @package KoomenIT
 */

$koomenit_points = array_filter( array_map( 'trim', explode( "\n", (string) koomenit_mod( 'koomenit_about_points' ) ) ) );
$koomenit_image  = koomenit_mod( 'koomenit_about_image' );
?>
<section id="over" class="section">
	<div class="container split">
		<div class="split__media reveal">
			<?php if ( $koomenit_image ) : ?>
				<img src="<?php echo esc_url( $koomenit_image ); ?>" alt="<?php echo esc_attr( koomenit_mod( 'koomenit_about_title' ) ); ?>" loading="lazy">
			<?php else : ?>
				<div class="media-placeholder" aria-hidden="true">
					<?php koomenit_network_svg( 'media-placeholder__network' ); ?>
					<img class="media-placeholder__mark" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-mark-dark.svg' ); ?>" alt="" width="258" height="328">
					<div class="media-placeholder__card">
						<?php echo koomenit_icon( 'code' ); // phpcs:ignore ?>
						<strong>C++</strong>
					</div>
					<div class="media-placeholder__card media-placeholder__card--alt">
						<?php echo koomenit_icon( 'terminal' ); // phpcs:ignore ?>
						<strong>Python</strong>
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
