<?php
/**
 * Blogoverzicht: berichtenpagina, categorie- en tagpagina's.
 *
 * @package Jroen
 */

$jroen_page_id = (int) get_option( 'page_for_posts' );
$jroen_layout  = jroen_mod( 'jroen_blog_layout' );
$jroen_sidebar = jroen_mod( 'jroen_blog_sidebar' );
$jroen_paged   = max( 1, (int) get_query_var( 'paged' ) );
$jroen_feature = jroen_mod( 'jroen_blog_featured' ) && is_home() && 1 === $jroen_paged;

if ( is_category() || is_tag() ) {
	jroen_page_header( single_term_title( '', false ), term_description(), is_category() ? __( 'Categorie', 'jroen' ) : __( 'Tag', 'jroen' ) );
} else {
	$jroen_intro = ( $jroen_page_id && has_excerpt( $jroen_page_id ) ) ? get_the_excerpt( $jroen_page_id ) : jroen_mod( 'jroen_blog_intro' );
	jroen_page_header( $jroen_page_id ? get_the_title( $jroen_page_id ) : __( 'Blog', 'jroen' ), esc_html( $jroen_intro ), __( 'Blog', 'jroen' ) );
}
?>

<main id="primary" class="site-main section">
	<div class="container<?php echo $jroen_sidebar ? ' layout layout--sidebar' : ''; ?>">
		<div class="blog-main">
			<?php
			if ( jroen_mod( 'jroen_blog_filters' ) ) {
				jroen_blog_filters();
			}
			?>
			<?php if ( have_posts() ) : ?>
				<?php
				if ( $jroen_feature ) {
					the_post();
					get_template_part( 'template-parts/content', 'featured' );
				}
				?>
				<div class="<?php echo 'list' === $jroen_layout ? 'post-list' : 'post-grid' . ( $jroen_sidebar ? ' post-grid--narrow' : '' ); ?>">
					<?php
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/content', 'card', array( 'variant' => 'list' === $jroen_layout ? 'row' : 'card' ) );
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
		<?php
		if ( $jroen_sidebar ) {
			jroen_blog_sidebar();
		}
		?>
	</div>
</main>
