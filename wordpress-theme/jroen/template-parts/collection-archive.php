<?php
/**
 * Overzicht van projecten of websites (alles, of per platform/type/techniek) met filter.
 *
 * @package Jroen
 */

$jroen_term      = is_tax() ? get_queried_object() : null;
$jroen_is_site   = is_post_type_archive( 'website' ) || ( $jroen_term && 'websitetype' === $jroen_term->taxonomy );
$jroen_filter_by = $jroen_is_site ? 'websitetype' : 'platform';
$jroen_active    = ( $jroen_term && $jroen_filter_by === $jroen_term->taxonomy ) ? $jroen_term->slug : '';
$jroen_filters   = ! $jroen_term || $jroen_filter_by === $jroen_term->taxonomy;

if ( $jroen_term ) {
	$jroen_tax = get_taxonomy( $jroen_term->taxonomy );
	jroen_page_header( $jroen_term->name, term_description( $jroen_term ), $jroen_tax ? $jroen_tax->labels->singular_name : '' );
} elseif ( $jroen_is_site ) {
	jroen_page_header(
		post_type_archive_title( '', false ),
		esc_html__( 'Bedrijfswebsites, webshops, webapplicaties en webinterfaces voor hardware.', 'jroen' ),
		esc_html__( 'Websites', 'jroen' )
	);
} else {
	jroen_page_header(
		post_type_archive_title( '', false ),
		esc_html__( 'Embedded projecten en software met ESP32, Arduino en Raspberry Pi.', 'jroen' ),
		esc_html__( 'Portfolio', 'jroen' )
	);
}
?>

<main id="primary" class="site-main section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php
			if ( $jroen_filters ) {
				jroen_term_filters( $jroen_filter_by, get_post_type_archive_link( $jroen_is_site ? 'website' : 'project' ), $jroen_active );
			}
			?>
			<div class="item-list" data-filter-grid>
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'website' === get_post_type() ? 'website' : 'project' );
				}
				?>
			</div>
			<p class="filter-empty" hidden><?php esc_html_e( 'Hier staat nog niets in deze categorie.', 'jroen' ); ?></p>
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
				<?php echo jroen_icon( $jroen_is_site ? 'globe' : 'chip' ); // phpcs:ignore ?>
				<p><?php echo $jroen_is_site ? esc_html__( 'Er staan hier nog geen websites.', 'jroen' ) : esc_html__( 'Er staan hier nog geen projecten.', 'jroen' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</main>
