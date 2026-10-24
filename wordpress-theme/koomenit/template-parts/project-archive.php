<?php
/**
 * Projectoverzicht (alle projecten, of per platform/type/techniek) met filter.
 *
 * @package KoomenIT
 */

$koomenit_term   = is_tax() ? get_queried_object() : null;
$koomenit_active = ( $koomenit_term && 'platform' === $koomenit_term->taxonomy ) ? $koomenit_term->slug : '';

if ( $koomenit_term ) {
	$koomenit_tax = get_taxonomy( $koomenit_term->taxonomy );
	koomenit_page_header( $koomenit_term->name, term_description( $koomenit_term ), $koomenit_tax ? $koomenit_tax->labels->singular_name : '' );
} else {
	koomenit_page_header(
		post_type_archive_title( '', false ),
		esc_html__( 'Programma\'s, websites en hardwareprojecten met ESP32, Arduino en Raspberry Pi.', 'koomenit' ),
		esc_html__( 'Portfolio', 'koomenit' )
	);
}
?>

<main id="primary" class="site-main section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php koomenit_project_filters( $koomenit_active ); ?>
			<div class="project-grid" data-filter-grid>
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'project' );
				}
				?>
			</div>
			<p class="filter-empty" hidden><?php esc_html_e( 'Nog geen projecten op dit platform.', 'koomenit' ); ?></p>
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
				<?php echo koomenit_icon( 'chip' ); // phpcs:ignore ?>
				<p><?php esc_html_e( 'Er staan hier nog geen projecten.', 'koomenit' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</main>
