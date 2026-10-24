<?php
/**
 * Homepage met aanpasbare secties (Weergave → Customizer → Koomen.IT thema-opties).
 *
 * @package KoomenIT
 */

get_header();

$koomenit_sections = array( 'hero', 'services', 'projects', 'about', 'stats', 'news', 'cta' );
?>

<main id="primary" class="site-main site-main--front">
	<?php
	foreach ( $koomenit_sections as $koomenit_section ) {
		if ( koomenit_mod( 'koomenit_' . $koomenit_section . '_show' ) ) {
			get_template_part( 'template-parts/section', $koomenit_section );
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
