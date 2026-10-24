<?php
/**
 * Enkel bericht.
 *
 * @package Blueline
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<header class="page-hero">
		<div class="container container--narrow">
			<?php $blueline_cats = get_the_category(); ?>
			<?php if ( $blueline_cats ) : ?>
				<a class="eyebrow eyebrow--light" href="<?php echo esc_url( get_category_link( $blueline_cats[0] ) ); ?>"><?php echo esc_html( $blueline_cats[0]->name ); ?></a>
			<?php endif; ?>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
			<div class="page-hero__meta"><?php blueline_posted_on(); ?></div>
		</div>
	</header>

	<main id="primary" class="site-main section">
		<div class="container layout<?php echo is_active_sidebar( 'sidebar-1' ) ? ' layout--sidebar' : ''; ?>">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
				<?php the_tags( '<div class="entry-tags">', '', '</div>' ); ?>
				<?php
				the_post_navigation(
					array(
						'prev_text' => '<span class="nav-label">' . esc_html__( 'Vorige', 'blueline' ) . '</span>%title',
						'next_text' => '<span class="nav-label">' . esc_html__( 'Volgende', 'blueline' ) . '</span>%title',
					)
				);
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</article>
			<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
				<aside class="sidebar"><?php dynamic_sidebar( 'sidebar-1' ); ?></aside>
			<?php endif; ?>
		</div>
	</main>
	<?php
endwhile;

get_footer();
