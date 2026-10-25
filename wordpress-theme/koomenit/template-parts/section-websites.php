<?php
/**
 * Homepage: recente websites met filter per websitetype.
 *
 * @package KoomenIT
 */

$koomenit_sites = new WP_Query(
	array(
		'post_type'           => 'website',
		'posts_per_page'      => max( 1, absint( koomenit_mod( 'koomenit_websites_count' ) ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section id="websites" class="section">
	<div class="container">
		<div class="section-head section-head--row reveal">
			<div>
				<?php if ( koomenit_mod( 'koomenit_websites_eyebrow' ) ) : ?>
					<span class="eyebrow"><?php echo esc_html( koomenit_mod( 'koomenit_websites_eyebrow' ) ); ?></span>
				<?php endif; ?>
				<h2 class="section-title"><?php echo esc_html( koomenit_mod( 'koomenit_websites_title' ) ); ?></h2>
				<?php if ( koomenit_mod( 'koomenit_websites_intro' ) ) : ?>
					<p class="section-intro"><?php echo esc_html( koomenit_mod( 'koomenit_websites_intro' ) ); ?></p>
				<?php endif; ?>
			</div>
			<a class="btn btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'website' ) ); ?>"><?php esc_html_e( 'Alle websites', 'koomenit' ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>

		<?php if ( $koomenit_sites->have_posts() ) : ?>
			<?php
			if ( koomenit_mod( 'koomenit_websites_filter' ) ) {
				koomenit_website_filters();
			}
			?>
			<div class="item-list" data-filter-grid>
				<?php
				while ( $koomenit_sites->have_posts() ) {
					$koomenit_sites->the_post();
					get_template_part( 'template-parts/content', 'website' );
				}
				wp_reset_postdata();
				?>
			</div>
			<p class="filter-empty" hidden><?php esc_html_e( 'Nog geen websites van dit type.', 'koomenit' ); ?></p>
		<?php else : ?>
			<div class="empty-state">
				<?php echo koomenit_icon( 'globe' ); // phpcs:ignore ?>
				<p><?php esc_html_e( 'Hier verschijnen je websites. Voeg de eerste toe via Websites → Nieuwe website in het dashboard.', 'koomenit' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
