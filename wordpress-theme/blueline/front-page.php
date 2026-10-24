<?php
/**
 * Homepage met aanpasbare secties (Weergave → Customizer → Blueline thema-opties).
 *
 * @package Blueline
 */

get_header();

$blueline_sections = array( 'hero', 'services', 'about', 'stats', 'news', 'cta' );
?>

<main id="primary" class="site-main site-main--front">
	<?php
	foreach ( $blueline_sections as $blueline_section ) {
		if ( blueline_mod( 'blueline_' . $blueline_section . '_show' ) ) {
			get_template_part( 'template-parts/section', $blueline_section );
		}
	}

	// Inhoud van de ingestelde statische homepage (indien aanwezig) onder de secties.
	if ( 'page' === get_option( 'show_on_front' ) ) {
		while ( have_posts() ) {
			the_post();
			if ( '' !== trim( get_the_content() ) ) {
				echo '<section class="section"><div class="container container--narrow entry-content">';
				the_content();
				echo '</div></section>';
			}
		}
	}
	?>
</main>

<?php
get_footer();
