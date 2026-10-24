<?php
/**
 * Homepage: hero.
 *
 * @package KoomenIT
 */

$koomenit_image = koomenit_mod( 'koomenit_hero_image' );
$koomenit_style = $koomenit_image ? ' style="background-image:url(' . esc_url( $koomenit_image ) . ')"' : '';
?>
<section class="hero<?php echo $koomenit_image ? ' hero--image' : ''; ?>"<?php echo $koomenit_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="hero__overlay" aria-hidden="true"></div>
	<svg class="hero__network" viewBox="0 0 1440 640" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
		<g class="hero__lines">
			<path d="M80 120 340 60 520 210 300 330 80 120zM520 210 760 90 980 170 860 360 520 210zM300 330 140 520 470 600 860 360M980 170 1240 80 1380 300 1160 470 860 360M1160 470 1300 600M470 600 760 520 1160 470"/>
		</g>
		<g class="hero__nodes">
			<circle cx="80" cy="120" r="7"/><circle cx="340" cy="60" r="6"/><circle cx="520" cy="210" r="8"/><circle cx="300" cy="330" r="6"/>
			<circle cx="760" cy="90" r="6"/><circle cx="980" cy="170" r="8"/><circle cx="860" cy="360" r="7"/><circle cx="140" cy="520" r="6"/>
			<circle cx="470" cy="600" r="7"/><circle cx="1240" cy="80" r="6"/><circle cx="1380" cy="300" r="8"/><circle cx="1160" cy="470" r="7"/>
			<circle cx="1300" cy="600" r="6"/><circle cx="760" cy="520" r="5"/>
		</g>
	</svg>
	<div class="container hero__inner">
		<div class="hero__content reveal">
			<?php if ( koomenit_mod( 'koomenit_hero_eyebrow' ) ) : ?>
				<span class="eyebrow eyebrow--light"><?php echo esc_html( koomenit_mod( 'koomenit_hero_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h1 class="hero__title"><?php echo esc_html( koomenit_mod( 'koomenit_hero_title' ) ); ?></h1>
			<p class="hero__text"><?php echo esc_html( koomenit_mod( 'koomenit_hero_text' ) ); ?></p>
			<div class="hero__actions">
				<?php if ( koomenit_mod( 'koomenit_hero_btn1_text' ) ) : ?>
					<a class="btn btn--white" href="<?php echo esc_url( koomenit_mod( 'koomenit_hero_btn1_url' ) ); ?>"><?php echo esc_html( koomenit_mod( 'koomenit_hero_btn1_text' ) ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></a>
				<?php endif; ?>
				<?php if ( koomenit_mod( 'koomenit_hero_btn2_text' ) ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( koomenit_mod( 'koomenit_hero_btn2_url' ) ); ?>"><?php echo esc_html( koomenit_mod( 'koomenit_hero_btn2_text' ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( koomenit_mod( 'koomenit_hero_logo' ) ) : ?>
			<div class="hero__visual" aria-hidden="true">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-icon-light.png' ); ?>" alt="" width="258" height="320">
			</div>
		<?php endif; ?>
	</div>
	<svg class="hero__wave" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true"><path d="M0 80V40c240 30 480 40 720 20S1200 0 1440 30v50z"/></svg>
</section>
