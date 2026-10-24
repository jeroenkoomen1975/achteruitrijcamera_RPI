<?php
/**
 * Template Name: Volledige breedte (zonder paginakop)
 * Template Post Type: page
 *
 * Ideaal om met blokken een eigen landingspagina op te bouwen.
 *
 * @package Blueline
 */

get_header();
?>
<main id="primary" class="site-main site-main--full">
	<?php
	while ( have_posts() ) {
		the_post();
		echo '<div class="entry-content entry-content--full">';
		the_content();
		echo '</div>';
	}
	?>
</main>
<?php
get_footer();
