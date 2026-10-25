<?php
/**
 * Homepage met aanpasbare secties (Weergave → Customizer → jroen.nl thema-opties).
 *
 * @package Jroen
 */

get_header();

$jroen_sections = array( 'hero', 'telemetry', 'overview', 'services', 'projects', 'websites', 'about', 'stats', 'news', 'cta' );
?>

<main id="primary" class="site-main site-main--front">
	<?php
	foreach ( $jroen_sections as $jroen_section ) {
		if ( jroen_mod( 'jroen_' . $jroen_section . '_show' ) ) {
			get_template_part( 'template-parts/section', $jroen_section );
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
