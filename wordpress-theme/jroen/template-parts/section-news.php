<?php
/**
 * Homepage: laatste berichten.
 *
 * @package Jroen
 */

$jroen_news = new WP_Query(
	array(
		'posts_per_page'      => max( 1, absint( jroen_mod( 'jroen_news_count' ) ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $jroen_news->have_posts() ) {
	return;
}

$jroen_posts_page = get_option( 'page_for_posts' );
?>
<section id="blog" class="section">
	<div class="container">
		<div class="section-head section-head--row reveal">
			<h2 class="section-title"><?php echo esc_html( jroen_mod( 'jroen_news_title' ) ); ?></h2>
			<?php if ( $jroen_posts_page ) : ?>
				<a class="btn btn--outline" href="<?php echo esc_url( get_permalink( $jroen_posts_page ) ); ?>"><?php esc_html_e( 'Alle berichten', 'jroen' ); ?></a>
			<?php endif; ?>
		</div>
		<div class="post-grid">
			<?php
			while ( $jroen_news->have_posts() ) {
				$jroen_news->the_post();
				get_template_part( 'template-parts/content', 'card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
