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

		<?php if ( 'code' === $koomenit_visual && '' !== trim( (string) koomenit_mod( 'koomenit_hero_code' ) ) ) : ?>
			<?php
			$koomenit_code   = rtrim( (string) koomenit_mod( 'koomenit_hero_code' ) );
			$koomenit_start  = max( 1, absint( koomenit_mod( 'koomenit_hero_code_start' ) ) );
			$koomenit_count  = substr_count( $koomenit_code, "\n" ) + 1;
			$koomenit_file   = (string) koomenit_mod( 'koomenit_hero_code_file' );
			$koomenit_slash  = strrpos( $koomenit_file, '/' );
			$koomenit_lang   = koomenit_mod( 'koomenit_hero_code_lang' );
			$koomenit_status = koomenit_mod( 'koomenit_hero_code_status' );
			$koomenit_ltext  = koomenit_mod( 'koomenit_hero_code_link_text' );
			$koomenit_lurl   = koomenit_mod( 'koomenit_hero_code_link_url' );
			if ( $koomenit_ltext && ! $koomenit_lurl ) {
				$koomenit_match = get_posts(
					array(
						'post_type'   => 'project',
						'title'       => $koomenit_ltext,
						'numberposts' => 1,
						'fields'      => 'ids',
					)
				);
				$koomenit_lurl  = $koomenit_match ? get_permalink( $koomenit_match[0] ) : '';
			}
			?>
			<div class="hero__visual reveal">
				<figure class="editor<?php echo koomenit_mod( 'koomenit_hero_code_animate' ) ? ' editor--animate' : ''; ?>" style="--lines:<?php echo (int) $koomenit_count; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: bestandsnaam */ __( 'Codevoorbeeld: %s', 'koomenit' ), $koomenit_file ) ); ?>">
					<div class="editor__bar">
						<span class="editor__dots" aria-hidden="true"><i></i><i></i><i></i></span>
						<span class="editor__tab">
							<?php if ( false !== $koomenit_slash ) : ?>
								<small><?php echo esc_html( substr( $koomenit_file, 0, $koomenit_slash + 1 ) ); ?></small><?php echo esc_html( substr( $koomenit_file, $koomenit_slash + 1 ) ); ?>
							<?php else : ?>
								<?php echo esc_html( $koomenit_file ); ?>
							<?php endif; ?>
						</span>
						<span class="editor__lang"><?php echo 'cpp' === $koomenit_lang ? 'C++' : 'Python'; ?></span>
					</div>
					<div class="editor__body">
						<div class="editor__gutter" aria-hidden="true"><?php echo implode( '<br>', range( $koomenit_start, $koomenit_start + $koomenit_count - 1 ) ); // phpcs:ignore ?></div>
						<pre class="editor__code"><code data-lang="<?php echo esc_attr( $koomenit_lang ); ?>"><?php echo esc_html( $koomenit_code ); ?></code></pre>
					</div>
					<?php if ( koomenit_mod( 'koomenit_hero_code_env' ) || $koomenit_status ) : ?>
						<div class="editor__status">
							<span><?php echo esc_html( koomenit_mod( 'koomenit_hero_code_env' ) ); ?></span>
							<?php if ( $koomenit_status ) : ?>
								<span class="editor__ok" data-final="<?php echo esc_attr( $koomenit_status ); ?>"><?php echo esc_html( $koomenit_status ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</figure>
				<?php if ( $koomenit_ltext ) : ?>
					<p class="editor__source">
						<?php esc_html_e( 'uit het project', 'koomenit' ); ?>
						<?php if ( $koomenit_lurl ) : ?>
							<a href="<?php echo esc_url( $koomenit_lurl ); ?>"><?php echo esc_html( $koomenit_ltext ); ?></a>
						<?php else : ?>
							<span><?php echo esc_html( $koomenit_ltext ); ?></span>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
		<?php elseif ( 'terminal' === $koomenit_visual && $koomenit_lines ) : ?>
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
