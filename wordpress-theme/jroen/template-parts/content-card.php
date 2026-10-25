<?php
/**
 * Berichtkaart voor overzichten. Met $args['variant'] = 'row' wordt het een lijstregel.
 *
 * @package Jroen
 */

$jroen_variant = ( isset( $args['variant'] ) && 'row' === $args['variant'] ) ? ' post-card--row' : '';
$jroen_cats    = get_the_category();
$jroen_tags    = jroen_mod( 'jroen_blog_tags' ) ? get_the_tags() : array();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card reveal' . $jroen_variant ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'jroen-card' ); ?>
		<?php else : ?>
			<span class="post-card__placeholder"><?php echo jroen_icon( 'code' ); // phpcs:ignore ?></span>
		<?php endif; ?>
	</a>
	<div class="post-card__body">
		<?php if ( $jroen_cats ) : ?>
			<a class="post-card__cat" href="<?php echo esc_url( get_category_link( $jroen_cats[0] ) ); ?>"><?php echo esc_html( $jroen_cats[0]->name ); ?></a>
		<?php endif; ?>
		<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $jroen_variant ? 32 : 20 ) ); ?></p>
		<?php if ( $jroen_tags ) : ?>
			<ul class="chips post-card__tags">
				<?php foreach ( array_slice( $jroen_tags, 0, 3 ) as $jroen_tag ) : ?>
					<li class="chip">#<?php echo esc_html( $jroen_tag->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="post-card__meta">
			<?php jroen_posted_on(); ?>
			<?php if ( jroen_mod( 'jroen_blog_reading_time' ) ) : ?>
				<span class="post-card__read"> · <?php echo esc_html( jroen_reading_time_label() ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</article>
