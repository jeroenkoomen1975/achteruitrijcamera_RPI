<?php
/**
 * Projectkaart voor overzichten.
 *
 * @package KoomenIT
 */

$koomenit_platforms = koomenit_project_terms( 'platform' );
$koomenit_types     = koomenit_project_terms( 'projecttype' );
$koomenit_tech      = array_slice( koomenit_project_terms( 'techniek' ), 0, 4 );
$koomenit_status    = get_post_meta( get_the_ID(), 'koomenit_status', true );
$koomenit_statuses  = koomenit_project_statuses();
$koomenit_first     = $koomenit_platforms ? $koomenit_platforms[0]->name : '';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'project-card reveal' ); ?> data-platforms="<?php echo esc_attr( koomenit_project_filter_slugs() ); ?>">
	<a class="project-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'koomenit-card' ); ?>
		<?php else : ?>
			<span class="project-card__placeholder">
				<?php echo koomenit_icon( koomenit_platform_icon( $koomenit_first ) ); // phpcs:ignore ?>
			</span>
		<?php endif; ?>
		<?php if ( $koomenit_platforms ) : ?>
			<span class="project-card__platforms">
				<?php foreach ( $koomenit_platforms as $koomenit_platform ) : ?>
					<span class="badge badge--platform"><?php echo esc_html( $koomenit_platform->name ); ?></span>
				<?php endforeach; ?>
			</span>
		<?php endif; ?>
	</a>
	<div class="project-card__body">
		<div class="project-card__meta">
			<?php if ( $koomenit_types ) : ?>
				<span class="project-card__type"><?php echo esc_html( implode( ' · ', wp_list_pluck( $koomenit_types, 'name' ) ) ); ?></span>
			<?php endif; ?>
			<?php if ( $koomenit_status && isset( $koomenit_statuses[ $koomenit_status ] ) ) : ?>
				<span class="status status--<?php echo esc_attr( $koomenit_status ); ?>"><?php echo esc_html( $koomenit_statuses[ $koomenit_status ] ); ?></span>
			<?php endif; ?>
		</div>
		<h3 class="project-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="project-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
		<?php if ( $koomenit_tech ) : ?>
			<ul class="chips">
				<?php foreach ( $koomenit_tech as $koomenit_t ) : ?>
					<li class="chip"><?php echo esc_html( $koomenit_t->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</article>
