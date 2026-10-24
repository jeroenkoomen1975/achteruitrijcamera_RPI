<?php
/**
 * Homepage: hero.
 *
 * @package KoomenIT
 */

$koomenit_image  = koomenit_mod( 'koomenit_hero_image' );
$koomenit_style  = $koomenit_image ? ' style="background-image:url(' . esc_url( $koomenit_image ) . ')"' : '';
$koomenit_visual = koomenit_mod( 'koomenit_hero_visual' );
$koomenit_lines  = array_filter( array_map( 'trim', explode( "\n", (string) koomenit_mod( 'koomenit_hero_terminal' ) ) ) );
?>
<section class="hero<?php echo $koomenit_image ? ' hero--image' : ''; ?>"<?php echo $koomenit_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="hero__overlay" aria-hidden="true"></div>
	<?php koomenit_network_svg( 'hero__network' ); ?>
	<div class="container hero__inner<?php echo 'none' === $koomenit_visual ? ' hero__inner--single' : ''; ?>">
		<div class="hero__content reveal">
			<?php if ( koomenit_mod( 'koomenit_hero_eyebrow' ) ) : ?>
				<span class="eyebrow eyebrow--light"><?php echo esc_html( koomenit_mod( 'koomenit_hero_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h1 class="hero__title"><?php echo esc_html( koomenit_mod( 'koomenit_hero_title' ) ); ?></h1>
			<p class="hero__text"><?php echo esc_html( koomenit_mod( 'koomenit_hero_text' ) ); ?></p>
			<div class="hero__actions">
				<?php if ( koomenit_mod( 'koomenit_hero_btn1_text' ) ) : ?>
					<a class="btn btn--primary" href="<?php echo esc_url( koomenit_mod( 'koomenit_hero_btn1_url' ) ); ?>"><?php echo esc_html( koomenit_mod( 'koomenit_hero_btn1_text' ) ); ?> <?php echo koomenit_icon( 'arrow' ); // phpcs:ignore ?></a>
				<?php endif; ?>
				<?php if ( koomenit_mod( 'koomenit_hero_btn2_text' ) ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( koomenit_mod( 'koomenit_hero_btn2_url' ) ); ?>"><?php echo esc_html( koomenit_mod( 'koomenit_hero_btn2_text' ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( 'terminal' === $koomenit_visual && $koomenit_lines ) : ?>
			<div class="hero__visual reveal" aria-hidden="true">
				<div class="terminal">
					<div class="terminal__bar">
						<span class="terminal__dots"><i></i><i></i><i></i></span>
						<span class="terminal__title"><?php echo esc_html( koomenit_mod( 'koomenit_hero_terminal_title' ) ); ?></span>
					</div>
					<div class="terminal__body">
						<?php foreach ( $koomenit_lines as $koomenit_i => $koomenit_line ) : ?>
							<div class="terminal__line" style="--i:<?php echo (int) $koomenit_i; ?>"><?php echo esc_html( $koomenit_line ); ?></div>
						<?php endforeach; ?>
						<div class="terminal__line terminal__cursor" style="--i:<?php echo count( $koomenit_lines ); ?>">&gt; <span></span></div>
					</div>
				</div>
			</div>
		<?php elseif ( 'logo' === $koomenit_visual ) : ?>
			<div class="hero__visual hero__visual--logo reveal" aria-hidden="true">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-mark-dark.svg' ); ?>" alt="" width="258" height="328">
			</div>
		<?php endif; ?>
	</div>
</section>
