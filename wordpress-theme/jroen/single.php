<?php
/**
 * Enkel bericht met leestijd, inhoudsopgave en meer berichten.
 *
 * @package Jroen
 */

get_header();

while ( have_posts() ) :
	the_post();
	$jroen_cats = get_the_category();
	$jroen_body = jroen_content_with_toc();
	$jroen_toc  = jroen_mod( 'jroen_blog_toc' ) && count( $jroen_body['toc'] ) >= 3;
	?>
	<header class="page-hero page-hero--post">
		<?php jroen_network_svg( 'page-hero__network' ); ?>
		<div class="container container--narrow">
			<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Kruimelpad', 'jroen' ); ?>">
				<a href="<?php echo esc_url( jroen_blog_url() ); ?>"><?php esc_html_e( 'Blog', 'jroen' ); ?></a>
				<?php if ( $jroen_cats ) : ?>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_category_link( $jroen_cats[0] ) ); ?>"><?php echo esc_html( $jroen_cats[0]->name ); ?></a>
				<?php endif; ?>
			</nav>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
			<div class="page-hero__meta post-meta">
				<span><?php echo jroen_icon( 'calendar' ); // phpcs:ignore ?><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
				<span><?php echo jroen_icon( 'users' ); // phpcs:ignore ?><?php echo esc_html( get_the_author() ); ?></span>
				<?php if ( jroen_mod( 'jroen_blog_reading_time' ) ) : ?>
					<span><?php echo jroen_icon( 'clock' ); // phpcs:ignore ?><?php echo esc_html( jroen_reading_time_label() ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<main id="primary" class="site-main section">
		<div class="container container--narrow">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<?php
				if ( $jroen_toc ) {
					jroen_toc( $jroen_body['toc'] );
				}
				?>
				<div class="entry-content">
					<?php
					echo $jroen_body['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- uitvoer van the_content.
					wp_link_pages();
					?>
				</div>
				<?php the_tags( '<div class="entry-tags">', '', '</div>' ); ?>
				<?php
				the_post_navigation(
					array(
						'prev_text' => '<span class="nav-label">' . esc_html__( 'Vorige', 'jroen' ) . '</span>%title',
						'next_text' => '<span class="nav-label">' . esc_html__( 'Volgende', 'jroen' ) . '</span>%title',
					)
				);
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</article>
		</div>

		<?php
		if ( jroen_mod( 'jroen_blog_related' ) ) :
			$jroen_related = new WP_Query(
				array(
					'post_type'           => 'post',
					'posts_per_page'      => 3,
					'post__not_in'        => array( get_the_ID() ),
					'category__in'        => wp_list_pluck( $jroen_cats, 'term_id' ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			if ( ! $jroen_related->have_posts() ) {
				$jroen_related = new WP_Query(
					array(
						'post_type'           => 'post',
						'posts_per_page'      => 3,
						'post__not_in'        => array( get_the_ID() ),
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					)
				);
			}
			if ( $jroen_related->have_posts() ) :
				?>
				<div class="container related">
					<h2 class="related__title"><?php esc_html_e( 'Meer berichten', 'jroen' ); ?></h2>
					<div class="post-grid">
						<?php
						while ( $jroen_related->have_posts() ) {
							$jroen_related->the_post();
							get_template_part( 'template-parts/content', 'card' );
						}
						wp_reset_postdata();
						?>
					</div>
				</div>
				<?php
			endif;
		endif;
		?>
	</main>
	<?php
endwhile;

get_footer();
