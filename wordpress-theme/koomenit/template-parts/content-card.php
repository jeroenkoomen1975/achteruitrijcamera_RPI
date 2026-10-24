<?php
/**
 * Berichtkaart voor overzichten. Met $args['variant'] = 'row' wordt het een lijstregel.
 *
 * @package KoomenIT
 */

$koomenit_variant = ( isset( $args['variant'] ) && 'row' === $args['variant'] ) ? ' post-card--row' : '';
$koomenit_cats    = get_the_category();
$koomenit_tags    = koomenit_mod( 'koomenit_blog_tags' ) ? get_the_tags() : array();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card reveal' . $koomenit_variant ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'koomenit-card' ); ?>
		<?php else : ?>
			<span class="post-card__placeholder"><?php echo koomenit_icon( 'code' ); // phpcs:ignore ?></span>
		<?php endif; ?>
	</a>
	<div class="post-card__body">
		<?php if ( $koomenit_cats ) : ?>
			<a class="post-card__cat" href="<?php echo esc_url( get_category_link( $koomenit_cats[0] ) ); ?>"><?php echo esc_html( $koomenit_cats[0]->name ); ?></a>
		<?php endif; ?>
		<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $koomenit_variant ? 32 : 20 ) ); ?></p>
		<?php if ( $koomenit_tags ) : ?>
			<ul class="chips post-card__tags">
				<?php foreach ( array_slice( $koomenit_tags, 0, 3 ) as $koomenit_tag ) : ?>
					<li class="chip">#<?php echo esc_html( $koomenit_tag->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="post-card__meta">
			<?php koomenit_posted_on(); ?>
			<?php if ( koomenit_mod( 'koomenit_blog_reading_time' ) ) : ?>
				<span class="post-card__read"> · <?php echo esc_html( koomenit_reading_time_label() ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</article>
