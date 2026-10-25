<?php
/**
 * Projectkaart (liggend): afbeelding links, logo, tekst en knoppen rechts.
 *
 * @package KoomenIT
 */

$koomenit_id        = get_the_ID();
$koomenit_platforms = koomenit_project_terms( 'platform' );
$koomenit_types     = koomenit_project_terms( 'projecttype' );
$koomenit_tech      = array_slice( koomenit_project_terms( 'techniek' ), 0, 5 );
$koomenit_status    = get_post_meta( $koomenit_id, 'koomenit_status', true );
$koomenit_year      = get_post_meta( $koomenit_id, 'koomenit_year', true );
$koomenit_github    = get_post_meta( $koomenit_id, 'koomenit_github', true );
$koomenit_statuses  = koomenit_project_statuses();
$koomenit_icon_name = koomenit_platform_icon( $koomenit_platforms ? $koomenit_platforms[0]->name : '' );
$koomenit_fallback  = '<span class="item-card__placeholder">' . koomenit_icon( $koomenit_icon_name ) . '</span>';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'item-card item-card--project reveal' ); ?> data-terms="<?php echo esc_attr( koomenit_term_slugs( 'platform' ) ); ?>">
	<div class="item-card__media" aria-hidden="true">
		<?php koomenit_item_media( $koomenit_id, 'koomenit-card', $koomenit_fallback ); ?>
		<?php if ( $koomenit_platforms ) : ?>
			<span class="project-card__platforms">
				<?php foreach ( $koomenit_platforms as $koomenit_platform ) : ?>
					<span class="badge badge--platform"><?php echo esc_html( $koomenit_platform->name ); ?></span>
				<?php endforeach; ?>
			</span>
		<?php endif; ?>
	</div>
	<div class="item-card__body">
		<div class="item-card__meta">
			<span class="item-card__type"><?php echo esc_html( implode( ' · ', wp_list_pluck( $koomenit_types, 'name' ) ) ); ?></span>
			<?php if ( $koomenit_status && isset( $koomenit_statuses[ $koomenit_status ] ) ) : ?>
				<span class="status status--<?php echo esc_attr( $koomenit_status ); ?>"><?php echo esc_html( $koomenit_statuses[ $koomenit_status ] ); ?></span>
			<?php endif; ?>
		</div>
		<div class="item-card__heading">
			<?php koomenit_logo_tile( $koomenit_id, $koomenit_icon_name ); ?>
			<h3 class="item-card__title">
				<a class="item-card__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<?php if ( $koomenit_platforms || $koomenit_year ) : ?>
					<span class="item-card__sub"><?php echo esc_html( implode( ' · ', array_filter( array( implode( ', ', wp_list_pluck( $koomenit_platforms, 'name' ) ), $koomenit_year ) ) ) ); ?></span>
				<?php endif; ?>
			</h3>
		</div>
		<p class="item-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
		<?php if ( $koomenit_tech ) : ?>
			<ul class="chips">
				<?php foreach ( $koomenit_tech as $koomenit_t ) : ?>
					<li class="chip"><?php echo esc_html( $koomenit_t->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="item-card__actions">
			<a class="btn btn--primary btn--sm" href="<?php the_permalink(); ?>" tabindex="-1"><?php esc_html_e( 'Lees meer', 'koomenit' ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php if ( $koomenit_github ) : ?>
				<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $koomenit_github ); ?>" target="_blank" rel="noopener"><?php echo koomenit_icon( 'github' ); // phpcs:ignore ?> <?php esc_html_e( 'Code op GitHub', 'koomenit' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</article>
