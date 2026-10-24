<?php
/**
 * Uitgelicht bericht: groot, bovenaan het blogoverzicht.
 *
 * @package KoomenIT
 */

$koomenit_cats = get_the_category();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-featured reveal' ); ?>>
	<a class="post-featured__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'large' ); ?>
		<?php else : ?>
			<span class="post-featured__placeholder">
				<?php koomenit_network_svg( 'post-featured__network' ); ?>
				<?php echo koomenit_icon( 'code' ); // phpcs:ignore ?>
			</span>
		<?php endif; ?>
	</a>
	<div class="post-featured__body">
		<span class="post-featured__label">
			<?php esc_html_e( 'Nieuwste bericht', 'koomenit' ); ?>
			<?php if ( $koomenit_cats ) : ?>
				· <a href="<?php echo esc_url( get_category_link( $koomenit_cats[0] ) ); ?>"><?php echo esc_html( $koomenit_cats[0]->name ); ?></a>
			<?php endif; ?>
		</span>
		<h2 class="post-featured__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="post-featured__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 40 ) ); ?></p>
		<div class="post-card__meta">
			<?php koomenit_posted_on(); ?>
			<?php if ( koomenit_mod( 'koomenit_blog_reading_time' ) ) : ?>
				<span class="post-card__read"> · <?php echo esc_html( koomenit_reading_time_label() ); ?></span>
			<?php endif; ?>
		</div>
		<a class="btn btn--primary btn--sm" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Lees verder', 'koomenit' ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></a>
	</div>
</article>
