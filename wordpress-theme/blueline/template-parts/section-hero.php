<?php
/**
 * Homepage: hero.
 *
 * @package Blueline
 */

$blueline_image = blueline_mod( 'blueline_hero_image' );
$blueline_style = $blueline_image ? ' style="background-image:url(' . esc_url( $blueline_image ) . ')"' : '';
?>
<section class="hero<?php echo $blueline_image ? ' hero--image' : ''; ?>"<?php echo $blueline_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="hero__overlay" aria-hidden="true"></div>
	<div class="hero__shapes" aria-hidden="true"><span></span><span></span><span></span></div>
	<div class="container hero__inner">
		<div class="hero__content reveal">
			<?php if ( blueline_mod( 'blueline_hero_eyebrow' ) ) : ?>
				<span class="eyebrow eyebrow--light"><?php echo esc_html( blueline_mod( 'blueline_hero_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h1 class="hero__title"><?php echo esc_html( blueline_mod( 'blueline_hero_title' ) ); ?></h1>
			<p class="hero__text"><?php echo esc_html( blueline_mod( 'blueline_hero_text' ) ); ?></p>
			<div class="hero__actions">
				<?php if ( blueline_mod( 'blueline_hero_btn1_text' ) ) : ?>
					<a class="btn btn--white" href="<?php echo esc_url( blueline_mod( 'blueline_hero_btn1_url' ) ); ?>"><?php echo esc_html( blueline_mod( 'blueline_hero_btn1_text' ) ); ?> <?php echo blueline_icon( 'arrow' ); // phpcs:ignore ?></a>
				<?php endif; ?>
				<?php if ( blueline_mod( 'blueline_hero_btn2_text' ) ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( blueline_mod( 'blueline_hero_btn2_url' ) ); ?>"><?php echo esc_html( blueline_mod( 'blueline_hero_btn2_text' ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<svg class="hero__wave" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true"><path d="M0 80V40c240 30 480 40 720 20S1200 0 1440 30v50z"/></svg>
</section>
