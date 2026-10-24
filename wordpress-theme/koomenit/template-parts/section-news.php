<?php
/**
 * Homepage: laatste berichten.
 *
 * @package KoomenIT
 */

$koomenit_news = new WP_Query(
	array(
		'posts_per_page'      => max( 1, absint( koomenit_mod( 'koomenit_news_count' ) ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $koomenit_news->have_posts() ) {
	return;
}

$koomenit_posts_page = get_option( 'page_for_posts' );
?>
<section id="blog" class="section">
	<div class="container">
		<div class="section-head section-head--row reveal">
			<h2 class="section-title"><?php echo esc_html( koomenit_mod( 'koomenit_news_title' ) ); ?></h2>
			<?php if ( $koomenit_posts_page ) : ?>
				<a class="btn btn--outline" href="<?php echo esc_url( get_permalink( $koomenit_posts_page ) ); ?>"><?php esc_html_e( 'Alle berichten', 'koomenit' ); ?></a>
			<?php endif; ?>
		</div>
		<div class="post-grid">
			<?php
			while ( $koomenit_news->have_posts() ) {
				$koomenit_news->the_post();
				get_template_part( 'template-parts/content', 'card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
