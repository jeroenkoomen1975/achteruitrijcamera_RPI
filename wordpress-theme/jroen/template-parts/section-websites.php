<?php
/**
 * Homepage: recente websites met filter per websitetype.
 *
 * @package Jroen
 */

$jroen_sites = new WP_Query(
	array(
		'post_type'           => 'website',
		'posts_per_page'      => max( 1, absint( jroen_mod( 'jroen_websites_count' ) ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section id="websites" class="section">
	<div class="container">
		<div class="section-head section-head--row reveal">
			<div>
				<?php if ( jroen_mod( 'jroen_websites_eyebrow' ) ) : ?>
					<span class="eyebrow"><?php echo esc_html( jroen_mod( 'jroen_websites_eyebrow' ) ); ?></span>
				<?php endif; ?>
				<h2 class="section-title"><?php echo esc_html( jroen_mod( 'jroen_websites_title' ) ); ?></h2>
				<?php if ( jroen_mod( 'jroen_websites_intro' ) ) : ?>
					<p class="section-intro"><?php echo esc_html( jroen_mod( 'jroen_websites_intro' ) ); ?></p>
				<?php endif; ?>
			</div>
			<a class="btn btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'website' ) ); ?>"><?php esc_html_e( 'Alle websites', 'jroen' ); ?> <?php echo jroen_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>

		<?php if ( $jroen_sites->have_posts() ) : ?>
			<?php
			if ( jroen_mod( 'jroen_websites_filter' ) ) {
				jroen_website_filters();
			}
			?>
			<div class="item-list" data-filter-grid>
				<?php
				while ( $jroen_sites->have_posts() ) {
					$jroen_sites->the_post();
					get_template_part( 'template-parts/content', 'website' );
				}
				wp_reset_postdata();
				?>
			</div>
			<p class="filter-empty" hidden><?php esc_html_e( 'Nog geen websites van dit type.', 'jroen' ); ?></p>
		<?php else : ?>
			<div class="empty-state">
				<?php echo jroen_icon( 'globe' ); // phpcs:ignore ?>
				<p><?php esc_html_e( 'Hier verschijnen je websites. Voeg de eerste toe via Websites → Nieuwe website in het dashboard.', 'jroen' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
