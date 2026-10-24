<?php
/**
 * Homepage: aanbod.
 *
 * @package KoomenIT
 */

?>
<section id="aanbod" class="section section--services">
	<div class="container">
		<div class="section-head reveal">
			<h2 class="section-title"><?php echo esc_html( koomenit_mod( 'koomenit_services_title' ) ); ?></h2>
			<?php if ( koomenit_mod( 'koomenit_services_intro' ) ) : ?>
				<p class="section-intro"><?php echo esc_html( koomenit_mod( 'koomenit_services_intro' ) ); ?></p>
			<?php endif; ?>
		</div>
		<div class="cards">
			<?php foreach ( koomenit_get_services() as $koomenit_service ) : ?>
				<article class="card reveal">
					<div class="card__icon"><?php echo koomenit_icon( $koomenit_service['icon'] ); // phpcs:ignore ?></div>
					<h3 class="card__title"><?php echo esc_html( $koomenit_service['title'] ); ?></h3>
					<p class="card__text"><?php echo esc_html( $koomenit_service['text'] ); ?></p>
					<?php if ( $koomenit_service['url'] ) : ?>
						<a class="card__link" href="<?php echo esc_url( $koomenit_service['url'] ); ?>"><?php esc_html_e( 'Lees meer', 'koomenit' ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></a>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
