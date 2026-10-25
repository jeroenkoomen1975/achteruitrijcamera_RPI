<?php
/**
 * Pagina.
 *
 * @package Jroen
 */

get_header();

while ( have_posts() ) :
	the_post();
	jroen_page_header( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<main id="primary" class="site-main section">
		<div class="container container--narrow">
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
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</article>
		</div>
	</main>
	<?php
endwhile;

get_footer();
