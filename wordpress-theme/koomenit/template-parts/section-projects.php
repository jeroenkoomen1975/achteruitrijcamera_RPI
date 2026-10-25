<?php
/**
 * Homepage: recente projecten met filter per platform.
 *
 * @package KoomenIT
 */

$koomenit_projects = new WP_Query(
	array(
		'post_type'           => 'project',
		'posts_per_page'      => max( 1, absint( koomenit_mod( 'koomenit_projects_count' ) ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section id="projecten" class="section section--light">
	<div class="container">
		<div class="section-head section-head--row reveal">
			<div>
				<?php if ( koomenit_mod( 'koomenit_projects_eyebrow' ) ) : ?>
					<span class="eyebrow"><?php echo esc_html( koomenit_mod( 'koomenit_projects_eyebrow' ) ); ?></span>
				<?php endif; ?>
				<h2 class="section-title"><?php echo esc_html( koomenit_mod( 'koomenit_projects_title' ) ); ?></h2>
				<?php if ( koomenit_mod( 'koomenit_projects_intro' ) ) : ?>
					<p class="section-intro"><?php echo esc_html( koomenit_mod( 'koomenit_projects_intro' ) ); ?></p>
				<?php endif; ?>
			</div>
			<a class="btn btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>"><?php esc_html_e( 'Alle projecten', 'koomenit' ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>

		<?php if ( $koomenit_projects->have_posts() ) : ?>
			<?php
			if ( koomenit_mod( 'koomenit_projects_filter' ) ) {
				koomenit_project_filters();
			}
			?>
			<div class="item-list" data-filter-grid>
				<?php
				while ( $koomenit_projects->have_posts() ) {
					$koomenit_projects->the_post();
					get_template_part( 'template-parts/content', 'project' );
				}
				wp_reset_postdata();
				?>
			</div>
			<p class="filter-empty" hidden><?php esc_html_e( 'Nog geen projecten op dit platform.', 'koomenit' ); ?></p>
		<?php else : ?>
			<div class="empty-state">
				<?php echo koomenit_icon( 'chip' ); // phpcs:ignore ?>
				<p><?php esc_html_e( 'Hier verschijnen je projecten. Voeg je eerste project toe via Projecten → Nieuw project in het dashboard.', 'koomenit' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
