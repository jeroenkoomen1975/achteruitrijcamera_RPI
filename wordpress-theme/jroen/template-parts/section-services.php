<?php
/**
 * Homepage: platforms.
 *
 * @package Jroen
 */

?>
<section id="platforms" class="section section--services">
	<div class="container">
		<div class="section-head reveal">
			<h2 class="section-title"><?php echo esc_html( jroen_mod( 'jroen_services_title' ) ); ?></h2>
			<?php if ( jroen_mod( 'jroen_services_intro' ) ) : ?>
				<p class="section-intro"><?php echo esc_html( jroen_mod( 'jroen_services_intro' ) ); ?></p>
			<?php endif; ?>
		</div>
		<div class="cards">
			<?php foreach ( jroen_get_services() as $jroen_service ) : ?>
				<?php $jroen_tag = $jroen_service['url'] ? 'a' : 'article'; ?>
				<<?php echo $jroen_tag; // phpcs:ignore ?> class="card reveal"<?php echo $jroen_service['url'] ? ' href="' . esc_url( $jroen_service['url'] ) . '"' : ''; // phpcs:ignore ?>>
					<div class="card__top">
						<div class="card__icon"><?php echo jroen_icon( $jroen_service['icon'] ); // phpcs:ignore ?></div>
						<?php if ( $jroen_service['count'] ) : ?>
							<span class="card__count">
								<?php
								/* translators: %d: aantal projecten */
								echo esc_html( sprintf( _n( '%d project', '%d projecten', $jroen_service['count'], 'jroen' ), $jroen_service['count'] ) );
								?>
							</span>
						<?php endif; ?>
					</div>
					<h3 class="card__title"><?php echo esc_html( $jroen_service['title'] ); ?></h3>
					<p class="card__text"><?php echo esc_html( $jroen_service['text'] ); ?></p>
					<?php if ( $jroen_service['url'] ) : ?>
						<span class="card__link"><?php esc_html_e( 'Bekijk projecten', 'jroen' ); ?> <?php echo jroen_icon( 'arrow' ); // phpcs:ignore ?></span>
					<?php endif; ?>
				</<?php echo $jroen_tag; // phpcs:ignore ?>>
			<?php endforeach; ?>
		</div>
	</div>
</section>
