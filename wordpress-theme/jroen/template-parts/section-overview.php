<?php
/**
 * Homepage: twee overzichtskaarten naar Projecten en Websites.
 *
 * @package Jroen
 */

$jroen_cards = array(
	'p' => array( 'project', 'chip', 'blue' ),
	'w' => array( 'website', 'globe', 'orange' ),
);
?>
<section class="section overview">
	<div class="container overview__grid">
		<?php foreach ( $jroen_cards as $jroen_k => $jroen_card ) : ?>
			<?php
			$jroen_latest = get_posts(
				array(
					'post_type'   => $jroen_card[0],
					'numberposts' => 2,
				)
			);
			$jroen_link   = get_post_type_archive_link( $jroen_card[0] );
			?>
			<a class="overview__card reveal" href="<?php echo esc_url( $jroen_link ); ?>">
				<span class="overview__badge overview__badge--<?php echo esc_attr( $jroen_card[2] ); ?>"><?php echo jroen_icon( $jroen_card[1] ); // phpcs:ignore ?><?php echo esc_html( jroen_mod( "jroen_overview_{$jroen_k}_badge" ) ); ?></span>
				<h3 class="overview__title"><?php echo esc_html( jroen_mod( "jroen_overview_{$jroen_k}_title" ) ); ?></h3>
				<p class="overview__text"><?php echo esc_html( jroen_mod( "jroen_overview_{$jroen_k}_text" ) ); ?></p>
				<?php if ( $jroen_latest ) : ?>
					<p class="overview__latest">
						<?php esc_html_e( 'Nieuwste:', 'jroen' ); ?>
						<?php echo esc_html( implode( ', ', wp_list_pluck( $jroen_latest, 'post_title' ) ) ); ?>
					</p>
				<?php endif; ?>
				<span class="overview__more">
					<?php echo 'p' === $jroen_k ? esc_html__( 'Open projecten', 'jroen' ) : esc_html__( 'Open websites', 'jroen' ); ?>
					<?php echo jroen_icon( 'arrow' ); // phpcs:ignore ?>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
