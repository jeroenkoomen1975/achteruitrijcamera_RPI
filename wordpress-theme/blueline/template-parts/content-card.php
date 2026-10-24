<?php
/**
 * Berichtkaart voor overzichten.
 *
 * @package Blueline
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card reveal' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'blueline-card' ); ?>
		<?php else : ?>
			<span class="post-card__placeholder"><?php echo blueline_icon( 'code' ); // phpcs:ignore ?></span>
		<?php endif; ?>
	</a>
	<div class="post-card__body">
		<?php $blueline_cats = get_the_category(); ?>
		<?php if ( $blueline_cats ) : ?>
			<span class="post-card__cat"><?php echo esc_html( $blueline_cats[0]->name ); ?></span>
		<?php endif; ?>
		<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
		<div class="post-card__meta"><?php blueline_posted_on(); ?></div>
	</div>
</article>
