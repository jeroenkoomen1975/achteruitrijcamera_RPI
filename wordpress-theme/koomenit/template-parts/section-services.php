<?php
/**
 * Homepage: platforms.
 *
 * @package KoomenIT
 */

?>
<section id="platforms" class="section section--services">
	<div class="container">
		<div class="section-head reveal">
			<h2 class="section-title"><?php echo esc_html( koomenit_mod( 'koomenit_services_title' ) ); ?></h2>
			<?php if ( koomenit_mod( 'koomenit_services_intro' ) ) : ?>
				<p class="section-intro"><?php echo esc_html( koomenit_mod( 'koomenit_services_intro' ) ); ?></p>
			<?php endif; ?>
		</div>
		<div class="cards">
			<?php foreach ( koomenit_get_services() as $koomenit_service ) : ?>
				<?php $koomenit_tag = $koomenit_service['url'] ? 'a' : 'article'; ?>
				<<?php echo $koomenit_tag; // phpcs:ignore ?> class="card reveal"<?php echo $koomenit_service['url'] ? ' href="' . esc_url( $koomenit_service['url'] ) . '"' : ''; // phpcs:ignore ?>>
					<div class="card__top">
						<div class="card__icon"><?php echo koomenit_icon( $koomenit_service['icon'] ); // phpcs:ignore ?></div>
						<?php if ( $koomenit_service['count'] ) : ?>
							<span class="card__count">
								<?php
								/* translators: %d: aantal projecten */
								echo esc_html( sprintf( _n( '%d project', '%d projecten', $koomenit_service['count'], 'koomenit' ), $koomenit_service['count'] ) );
								?>
							</span>
						<?php endif; ?>
					</div>
					<h3 class="card__title"><?php echo esc_html( $koomenit_service['title'] ); ?></h3>
					<p class="card__text"><?php echo esc_html( $koomenit_service['text'] ); ?></p>
					<?php if ( $koomenit_service['url'] ) : ?>
						<span class="card__link"><?php esc_html_e( 'Bekijk projecten', 'koomenit' ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></span>
					<?php endif; ?>
				</<?php echo $koomenit_tag; // phpcs:ignore ?>>
			<?php endforeach; ?>
		</div>
	</div>
</section>
