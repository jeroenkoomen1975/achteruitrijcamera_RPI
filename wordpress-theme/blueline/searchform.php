<?php
/**
 * Zoekformulier.
 *
 * @package Blueline
 */

$blueline_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $blueline_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Zoeken naar:', 'blueline' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $blueline_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Zoeken…', 'blueline' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Zoeken', 'blueline' ); ?></button>
</form>
