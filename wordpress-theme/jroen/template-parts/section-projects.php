<?php
/**
 * Homepage: recente projecten met filter per platform.
 *
 * @package Jroen
 */

$jroen_projects = new WP_Query(
	array(
		'post_type'           => 'project',
		'posts_per_page'      => max( 1, absint( jroen_mod( 'jroen_projects_count' ) ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section id="projecten" class="section section--light">
	<div class="container">
		<div class="section-head section-head--row reveal">
			<div>
				<?php if ( jroen_mod( 'jroen_projects_eyebrow' ) ) : ?>
					<span class="eyebrow"><?php echo esc_html( jroen_mod( 'jroen_projects_eyebrow' ) ); ?></span>
				<?php endif; ?>
				<h2 class="section-title"><?php echo esc_html( jroen_mod( 'jroen_projects_title' ) ); ?></h2>
				<?php if ( jroen_mod( 'jroen_projects_intro' ) ) : ?>
					<p class="section-intro"><?php echo esc_html( jroen_mod( 'jroen_projects_intro' ) ); ?></p>
				<?php endif; ?>
			</div>
			<a class="btn btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>"><?php esc_html_e( 'Alle projecten', 'jroen' ); ?> <?php echo jroen_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>

		<?php if ( $jroen_projects->have_posts() ) : ?>
			<?php
			if ( jroen_mod( 'jroen_projects_filter' ) ) {
				jroen_project_filters();
			}
			?>
			<div class="item-list" data-filter-grid>
				<?php
				while ( $jroen_projects->have_posts() ) {
					$jroen_projects->the_post();
					get_template_part( 'template-parts/content', 'project' );
				}
				wp_reset_postdata();
				?>
			</div>
			<p class="filter-empty" hidden><?php esc_html_e( 'Nog geen projecten op dit platform.', 'jroen' ); ?></p>
		<?php else : ?>
			<div class="empty-state">
				<?php echo jroen_icon( 'chip' ); // phpcs:ignore ?>
				<p><?php esc_html_e( 'Hier verschijnen je projecten. Voeg je eerste project toe via Projecten → Nieuw project in het dashboard.', 'jroen' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
