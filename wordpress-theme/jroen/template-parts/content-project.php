<?php
/**
 * Projectkaart (liggend): afbeelding links, logo, tekst en knoppen rechts.
 *
 * @package Jroen
 */

$jroen_id        = get_the_ID();
$jroen_platforms = jroen_project_terms( 'platform' );
$jroen_types     = jroen_project_terms( 'projecttype' );
$jroen_tech      = array_slice( jroen_project_terms( 'techniek' ), 0, 5 );
$jroen_status    = get_post_meta( $jroen_id, 'jroen_status', true );
$jroen_year      = get_post_meta( $jroen_id, 'jroen_year', true );
$jroen_github    = get_post_meta( $jroen_id, 'jroen_github', true );
$jroen_statuses  = jroen_project_statuses();
$jroen_icon_name = jroen_platform_icon( $jroen_platforms ? $jroen_platforms[0]->name : '' );
$jroen_fallback  = '<span class="item-card__placeholder">' . jroen_icon( $jroen_icon_name ) . '</span>';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'item-card item-card--project reveal' ); ?> data-terms="<?php echo esc_attr( jroen_term_slugs( 'platform' ) ); ?>">
	<div class="item-card__media" aria-hidden="true">
		<?php jroen_item_media( $jroen_id, 'jroen-card', $jroen_fallback ); ?>
		<?php if ( $jroen_platforms ) : ?>
			<span class="project-card__platforms">
				<?php foreach ( $jroen_platforms as $jroen_platform ) : ?>
					<span class="badge badge--platform"><?php echo esc_html( $jroen_platform->name ); ?></span>
				<?php endforeach; ?>
			</span>
		<?php endif; ?>
	</div>
	<div class="item-card__body">
		<div class="item-card__meta">
			<span class="item-card__type"><?php echo esc_html( implode( ' · ', wp_list_pluck( $jroen_types, 'name' ) ) ); ?></span>
			<?php if ( $jroen_status && isset( $jroen_statuses[ $jroen_status ] ) ) : ?>
				<span class="status status--<?php echo esc_attr( $jroen_status ); ?>"><?php echo esc_html( $jroen_statuses[ $jroen_status ] ); ?></span>
			<?php endif; ?>
		</div>
		<div class="item-card__heading">
			<?php jroen_logo_tile( $jroen_id, $jroen_icon_name ); ?>
			<h3 class="item-card__title">
				<a class="item-card__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<?php if ( $jroen_platforms || $jroen_year ) : ?>
					<span class="item-card__sub"><?php echo esc_html( implode( ' · ', array_filter( array( implode( ', ', wp_list_pluck( $jroen_platforms, 'name' ) ), $jroen_year ) ) ) ); ?></span>
				<?php endif; ?>
			</h3>
		</div>
		<p class="item-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
		<?php if ( $jroen_tech ) : ?>
			<ul class="chips">
				<?php foreach ( $jroen_tech as $jroen_t ) : ?>
					<li class="chip"><?php echo esc_html( $jroen_t->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="item-card__actions">
			<a class="btn btn--primary btn--sm" href="<?php the_permalink(); ?>" tabindex="-1"><?php esc_html_e( 'Lees meer', 'jroen' ); ?> <?php echo jroen_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php if ( $jroen_github ) : ?>
				<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $jroen_github ); ?>" target="_blank" rel="noopener"><?php echo jroen_icon( 'github' ); // phpcs:ignore ?> <?php esc_html_e( 'Code op GitHub', 'jroen' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</article>
