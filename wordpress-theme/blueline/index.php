<?php
/**
 * Hoofdtemplate (berichtenoverzicht en fallback).
 *
 * @package Blueline
 */

get_header();

if ( is_home() && ! is_front_page() ) {
	blueline_page_header( single_post_title( '', false ) );
} elseif ( is_archive() ) {
	blueline_page_header( get_the_archive_title(), get_the_archive_description() );
} elseif ( is_search() ) {
	/* translators: %s: zoekterm */
	blueline_page_header( sprintf( esc_html__( 'Zoekresultaten voor: %s', 'blueline' ), '<span>' . esc_html( get_search_query() ) . '</span>' ) );
} else {
	blueline_page_header( get_bloginfo( 'name' ), get_bloginfo( 'description' ) );
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
