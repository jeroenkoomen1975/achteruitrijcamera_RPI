<?php
/**
 * Zoekformulier.
 *
 * @package KoomenIT
 */

$koomenit_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $koomenit_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Zoeken naar:', 'koomenit' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $koomenit_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Zoeken…', 'koomenit' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Zoeken', 'koomenit' ); ?></button>
</form>
