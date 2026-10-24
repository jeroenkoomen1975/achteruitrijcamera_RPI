<?php
/**
 * Homepage: laatste berichten.
 *
 * @package Blueline
 */

$blueline_news = new WP_Query(
	array(
		'posts_per_page'      => max( 1, absint( blueline_mod( 'blueline_news_count' ) ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $blueline_news->have_posts() ) {
	return;
}

$blueline_posts_page = get_option( 'page_for_posts' );
?>
<section id="nieuws" class="section">
	<div class="container">
		<div class="section-head section-head--row reveal">
			<h2 class="section-title"><?php echo esc_html( blueline_mod( 'blueline_news_title' ) ); ?></h2>
			<?php if ( $blueline_posts_page ) : ?>
				<a class="btn btn--outline" href="<?php echo esc_url( get_permalink( $blueline_posts_page ) ); ?>"><?php esc_html_e( 'Alle berichten', 'blueline' ); ?></a>
			<?php endif; ?>
		</div>
		<div class="post-grid">
			<?php
			while ( $blueline_news->have_posts() ) {
				$blueline_news->the_post();
				get_template_part( 'template-parts/content', 'card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
