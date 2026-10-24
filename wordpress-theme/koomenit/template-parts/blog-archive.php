<?php
/**
 * Blogoverzicht: berichtenpagina, categorie- en tagpagina's.
 *
 * @package KoomenIT
 */

$koomenit_page_id = (int) get_option( 'page_for_posts' );
$koomenit_layout  = koomenit_mod( 'koomenit_blog_layout' );
$koomenit_sidebar = koomenit_mod( 'koomenit_blog_sidebar' );
$koomenit_paged   = max( 1, (int) get_query_var( 'paged' ) );
$koomenit_feature = koomenit_mod( 'koomenit_blog_featured' ) && is_home() && 1 === $koomenit_paged;

if ( is_category() || is_tag() ) {
	koomenit_page_header( single_term_title( '', false ), term_description(), is_category() ? __( 'Categorie', 'koomenit' ) : __( 'Tag', 'koomenit' ) );
} else {
	$koomenit_intro = ( $koomenit_page_id && has_excerpt( $koomenit_page_id ) ) ? get_the_excerpt( $koomenit_page_id ) : koomenit_mod( 'koomenit_blog_intro' );
	koomenit_page_header( $koomenit_page_id ? get_the_title( $koomenit_page_id ) : __( 'Blog', 'koomenit' ), esc_html( $koomenit_intro ), __( 'Blog', 'koomenit' ) );
}
?>

<main id="primary" class="site-main section">
	<div class="container<?php echo $koomenit_sidebar ? ' layout layout--sidebar' : ''; ?>">
		<div class="blog-main">
			<?php
			if ( koomenit_mod( 'koomenit_blog_filters' ) ) {
				koomenit_blog_filters();
			}
			?>
			<?php if ( have_posts() ) : ?>
				<?php
				if ( $koomenit_feature ) {
					the_post();
					get_template_part( 'template-parts/content', 'featured' );
				}
				?>
				<div class="<?php echo 'list' === $koomenit_layout ? 'post-list' : 'post-grid' . ( $koomenit_sidebar ? ' post-grid--narrow' : '' ); ?>">
					<?php
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/content', 'card', array( 'variant' => 'list' === $koomenit_layout ? 'row' : 'card' ) );
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
		if ( $koomenit_sidebar ) {
			koomenit_blog_sidebar();
		}
		?>
	</div>
</main>
