<?php
/**
 * Hoofdtemplate (berichtenoverzicht en fallback).
 *
 * @package KoomenIT
 */

get_header();

if ( is_home() && ! is_front_page() ) {
	koomenit_page_header( single_post_title( '', false ) );
} elseif ( is_archive() ) {
	koomenit_page_header( get_the_archive_title(), get_the_archive_description() );
} elseif ( is_search() ) {
	/* translators: %s: zoekterm */
	koomenit_page_header( sprintf( esc_html__( 'Zoekresultaten voor: %s', 'koomenit' ), '<span>' . esc_html( get_search_query() ) . '</span>' ) );
} else {
	koomenit_page_header( get_bloginfo( 'name' ), get_bloginfo( 'description' ) );
}
?>

<main id="primary" class="site-main section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'card' );
				}
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
				)
			);
			?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
