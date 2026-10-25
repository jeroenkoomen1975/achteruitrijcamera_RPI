<?php
/**
 * Websitekaart (liggend): afbeelding in een browservenster links, logo, tekst en knoppen rechts.
 *
 * @package KoomenIT
 */

$koomenit_id       = get_the_ID();
$koomenit_types    = koomenit_project_terms( 'websitetype' );
$koomenit_tech     = array_slice( koomenit_project_terms( 'techniek' ), 0, 5 );
$koomenit_url      = get_post_meta( $koomenit_id, 'koomenit_url', true );
$koomenit_status   = get_post_meta( $koomenit_id, 'koomenit_status', true );
$koomenit_role     = get_post_meta( $koomenit_id, 'koomenit_role', true );
$koomenit_year     = get_post_meta( $koomenit_id, 'koomenit_year', true );
$koomenit_statuses = koomenit_website_statuses();
$koomenit_domain   = koomenit_website_domain( $koomenit_url );
ob_start();
koomenit_wireframe();
$koomenit_fallback = ob_get_clean();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'item-card reveal' ); ?> data-terms="<?php echo esc_attr( koomenit_term_slugs( 'websitetype' ) ); ?>">
	<div class="item-card__media" aria-hidden="true">
		<?php koomenit_browser_bar( $koomenit_domain ); ?>
		<?php koomenit_item_media( $koomenit_id, 'koomenit-card', $koomenit_fallback ); ?>
	</div>
	<div class="item-card__body">
		<div class="item-card__meta">
			<span class="item-card__type"><?php echo esc_html( implode( ' · ', wp_list_pluck( $koomenit_types, 'name' ) ) ); ?></span>
			<?php if ( $koomenit_status && isset( $koomenit_statuses[ $koomenit_status ] ) ) : ?>
				<span class="status status--<?php echo esc_attr( $koomenit_status ); ?>"><?php echo esc_html( $koomenit_statuses[ $koomenit_status ] ); ?></span>
			<?php endif; ?>
		</div>
		<div class="item-card__heading">
			<?php koomenit_logo_tile( $koomenit_id, 'globe' ); ?>
			<h3 class="item-card__title">
				<a class="item-card__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<?php if ( $koomenit_domain ) : ?>
					<span class="item-card__sub"><?php echo esc_html( $koomenit_domain ); ?></span>
				<?php endif; ?>
			</h3>
		</div>
		<p class="item-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
		<?php if ( $koomenit_role || $koomenit_year ) : ?>
			<p class="item-card__facts">
				<?php if ( $koomenit_role ) : ?>
					<span><?php esc_html_e( 'Rol:', 'koomenit' ); ?> <strong><?php echo esc_html( $koomenit_role ); ?></strong></span>
				<?php endif; ?>
				<?php if ( $koomenit_year ) : ?>
					<span><?php esc_html_e( 'Jaar:', 'koomenit' ); ?> <strong><?php echo esc_html( $koomenit_year ); ?></strong></span>
				<?php endif; ?>
			</p>
		<?php endif; ?>
		<?php if ( $koomenit_tech ) : ?>
			<ul class="chips">
				<?php foreach ( $koomenit_tech as $koomenit_t ) : ?>
					<li class="chip"><?php echo esc_html( $koomenit_t->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="item-card__actions">
			<a class="btn btn--primary btn--sm" href="<?php the_permalink(); ?>" tabindex="-1"><?php esc_html_e( 'Lees meer', 'koomenit' ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php if ( $koomenit_url && 'archief' !== $koomenit_status ) : ?>
				<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $koomenit_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Bezoek website', 'koomenit' ); ?> <?php echo koomenit_icon( 'external' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</article>
