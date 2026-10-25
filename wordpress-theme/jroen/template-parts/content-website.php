<?php
/**
 * Websitekaart (liggend): afbeelding in een browservenster links, logo, tekst en knoppen rechts.
 *
 * @package Jroen
 */

$jroen_id       = get_the_ID();
$jroen_types    = jroen_project_terms( 'websitetype' );
$jroen_tech     = array_slice( jroen_project_terms( 'techniek' ), 0, 5 );
$jroen_url      = get_post_meta( $jroen_id, 'jroen_url', true );
$jroen_status   = get_post_meta( $jroen_id, 'jroen_status', true );
$jroen_role     = get_post_meta( $jroen_id, 'jroen_role', true );
$jroen_year     = get_post_meta( $jroen_id, 'jroen_year', true );
$jroen_statuses = jroen_website_statuses();
$jroen_domain   = jroen_website_domain( $jroen_url );
ob_start();
jroen_wireframe();
$jroen_fallback = ob_get_clean();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'item-card reveal' ); ?> data-terms="<?php echo esc_attr( jroen_term_slugs( 'websitetype' ) ); ?>">
	<div class="item-card__media" aria-hidden="true">
		<?php jroen_browser_bar( $jroen_domain ); ?>
		<?php jroen_item_media( $jroen_id, 'jroen-card', $jroen_fallback ); ?>
	</div>
	<div class="item-card__body">
		<div class="item-card__meta">
			<span class="item-card__type"><?php echo esc_html( implode( ' · ', wp_list_pluck( $jroen_types, 'name' ) ) ); ?></span>
			<?php if ( $jroen_status && isset( $jroen_statuses[ $jroen_status ] ) ) : ?>
				<span class="status status--<?php echo esc_attr( $jroen_status ); ?>"><?php echo esc_html( $jroen_statuses[ $jroen_status ] ); ?></span>
			<?php endif; ?>
		</div>
		<div class="item-card__heading">
			<?php jroen_logo_tile( $jroen_id, 'globe' ); ?>
			<h3 class="item-card__title">
				<a class="item-card__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<?php if ( $jroen_domain ) : ?>
					<span class="item-card__sub"><?php echo esc_html( $jroen_domain ); ?></span>
				<?php endif; ?>
			</h3>
		</div>
		<p class="item-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
		<?php if ( $jroen_role || $jroen_year ) : ?>
			<p class="item-card__facts">
				<?php if ( $jroen_role ) : ?>
					<span><?php esc_html_e( 'Rol:', 'jroen' ); ?> <strong><?php echo esc_html( $jroen_role ); ?></strong></span>
				<?php endif; ?>
				<?php if ( $jroen_year ) : ?>
					<span><?php esc_html_e( 'Jaar:', 'jroen' ); ?> <strong><?php echo esc_html( $jroen_year ); ?></strong></span>
				<?php endif; ?>
			</p>
		<?php endif; ?>
		<?php if ( $jroen_tech ) : ?>
			<ul class="chips">
				<?php foreach ( $jroen_tech as $jroen_t ) : ?>
					<li class="chip"><?php echo esc_html( $jroen_t->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="item-card__actions">
			<a class="btn btn--primary btn--sm" href="<?php the_permalink(); ?>" tabindex="-1"><?php esc_html_e( 'Lees meer', 'jroen' ); ?> <?php echo jroen_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php if ( $jroen_url && 'archief' !== $jroen_status ) : ?>
				<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $jroen_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Bezoek website', 'jroen' ); ?> <?php echo jroen_icon( 'external' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</article>
