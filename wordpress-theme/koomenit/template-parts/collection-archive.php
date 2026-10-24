<?php
/**
 * Overzicht van projecten of websites (alles, of per platform/type/techniek) met filter.
 *
 * @package KoomenIT
 */

$koomenit_term      = is_tax() ? get_queried_object() : null;
$koomenit_is_site   = is_post_type_archive( 'website' ) || ( $koomenit_term && 'websitetype' === $koomenit_term->taxonomy );
$koomenit_filter_by = $koomenit_is_site ? 'websitetype' : 'platform';
$koomenit_active    = ( $koomenit_term && $koomenit_filter_by === $koomenit_term->taxonomy ) ? $koomenit_term->slug : '';
$koomenit_filters   = ! $koomenit_term || $koomenit_filter_by === $koomenit_term->taxonomy;

if ( $koomenit_term ) {
	$koomenit_tax = get_taxonomy( $koomenit_term->taxonomy );
	koomenit_page_header( $koomenit_term->name, term_description( $koomenit_term ), $koomenit_tax ? $koomenit_tax->labels->singular_name : '' );
} elseif ( $koomenit_is_site ) {
	koomenit_page_header(
		post_type_archive_title( '', false ),
		esc_html__( 'Bedrijfswebsites, webshops, webapplicaties en webinterfaces voor hardware.', 'koomenit' ),
		esc_html__( 'Websites', 'koomenit' )
	);
} else {
	koomenit_page_header(
		post_type_archive_title( '', false ),
		esc_html__( 'Embedded projecten en software met ESP32, Arduino en Raspberry Pi.', 'koomenit' ),
		esc_html__( 'Portfolio', 'koomenit' )
	);
}
?>

<main id="primary" class="site-main section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php
			if ( $koomenit_filters ) {
				koomenit_term_filters( $koomenit_filter_by, get_post_type_archive_link( $koomenit_is_site ? 'website' : 'project' ), $koomenit_active );
			}
			?>
			<div class="project-grid" data-filter-grid>
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'website' === get_post_type() ? 'website' : 'project' );
				}
				?>
			</div>
			<p class="filter-empty" hidden><?php esc_html_e( 'Hier staat nog niets in deze categorie.', 'koomenit' ); ?></p>
			<?php
			the_posts_pagination(
				array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
				)
			);
			?>
		<?php else : ?>
			<div class="empty-state">
				<?php echo koomenit_icon( $koomenit_is_site ? 'globe' : 'chip' ); // phpcs:ignore ?>
				<p><?php echo $koomenit_is_site ? esc_html__( 'Er staan hier nog geen websites.', 'koomenit' ) : esc_html__( 'Er staan hier nog geen projecten.', 'koomenit' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</main>
