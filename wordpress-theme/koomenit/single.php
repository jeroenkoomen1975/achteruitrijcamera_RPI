<?php
/**
 * Enkel bericht met leestijd, inhoudsopgave en meer berichten.
 *
 * @package KoomenIT
 */

get_header();

while ( have_posts() ) :
	the_post();
	$koomenit_cats = get_the_category();
	$koomenit_body = koomenit_content_with_toc();
	$koomenit_toc  = koomenit_mod( 'koomenit_blog_toc' ) && count( $koomenit_body['toc'] ) >= 3;
	?>
	<header class="page-hero page-hero--post">
		<?php koomenit_network_svg( 'page-hero__network' ); ?>
		<div class="container container--narrow">
			<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Kruimelpad', 'koomenit' ); ?>">
				<a href="<?php echo esc_url( koomenit_blog_url() ); ?>"><?php esc_html_e( 'Blog', 'koomenit' ); ?></a>
				<?php if ( $koomenit_cats ) : ?>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_category_link( $koomenit_cats[0] ) ); ?>"><?php echo esc_html( $koomenit_cats[0]->name ); ?></a>
				<?php endif; ?>
			</nav>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
			<div class="page-hero__meta post-meta">
				<span><?php echo koomenit_icon( 'calendar' ); // phpcs:ignore ?><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
				<span><?php echo koomenit_icon( 'users' ); // phpcs:ignore ?><?php echo esc_html( get_the_author() ); ?></span>
				<?php if ( koomenit_mod( 'koomenit_blog_reading_time' ) ) : ?>
					<span><?php echo koomenit_icon( 'clock' ); // phpcs:ignore ?><?php echo esc_html( koomenit_reading_time_label() ); ?></span>
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
				if ( $koomenit_toc ) {
					koomenit_toc( $koomenit_body['toc'] );
				}
				?>
				<div class="entry-content">
					<?php
					echo $koomenit_body['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- uitvoer van the_content.
					wp_link_pages();
					?>
				</div>
				<?php the_tags( '<div class="entry-tags">', '', '</div>' ); ?>
				<?php
				the_post_navigation(
					array(
						'prev_text' => '<span class="nav-label">' . esc_html__( 'Vorige', 'koomenit' ) . '</span>%title',
						'next_text' => '<span class="nav-label">' . esc_html__( 'Volgende', 'koomenit' ) . '</span>%title',
					)
				);
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</article>
		</div>

		<?php
		if ( koomenit_mod( 'koomenit_blog_related' ) ) :
			$koomenit_related = new WP_Query(
				array(
					'post_type'           => 'post',
					'posts_per_page'      => 3,
					'post__not_in'        => array( get_the_ID() ),
					'category__in'        => wp_list_pluck( $koomenit_cats, 'term_id' ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			if ( ! $koomenit_related->have_posts() ) {
				$koomenit_related = new WP_Query(
					array(
						'post_type'           => 'post',
						'posts_per_page'      => 3,
						'post__not_in'        => array( get_the_ID() ),
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					)
				);
			}
			if ( $koomenit_related->have_posts() ) :
				?>
				<div class="container related">
					<h2 class="related__title"><?php esc_html_e( 'Meer berichten', 'koomenit' ); ?></h2>
					<div class="post-grid">
						<?php
						while ( $koomenit_related->have_posts() ) {
							$koomenit_related->the_post();
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
