<?php
/**
 * 404-pagina.
 *
 * @package Blueline
 */

get_header();
blueline_page_header( esc_html__( 'Pagina niet gevonden', 'blueline' ) );
?>
<main id="primary" class="site-main section">
	<div class="container container--narrow error-404">
		<p class="error-404__code">404</p>
		<p><?php esc_html_e( 'De pagina die je zoekt bestaat niet (meer). Probeer te zoeken of ga terug naar de homepage.', 'blueline' ); ?></p>
		<?php get_search_form(); ?>
		<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Naar de homepage', 'blueline' ); ?></a></p>
	</div>
</main>
<?php
get_footer();
