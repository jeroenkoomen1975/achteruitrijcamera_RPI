<?php
/**
 * Homepage: hero.
 *
 * @package Jroen
 */

$jroen_image  = jroen_mod( 'jroen_hero_image' );
$jroen_style  = $jroen_image ? ' style="background-image:url(' . esc_url( $jroen_image ) . ')"' : '';
$jroen_visual = jroen_mod( 'jroen_hero_visual' );
$jroen_lines  = array_filter( array_map( 'trim', explode( "\n", (string) jroen_mod( 'jroen_hero_terminal' ) ) ) );
?>
<section class="hero<?php echo $jroen_image ? ' hero--image' : ''; ?>"<?php echo $jroen_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="hero__overlay" aria-hidden="true"></div>
	<div class="container hero__inner<?php echo 'none' === $jroen_visual ? ' hero__inner--single' : ''; ?>">
		<div class="hero__content reveal">
			<?php if ( jroen_mod( 'jroen_hero_eyebrow' ) ) : ?>
				<span class="pill"><span class="pill__dot" aria-hidden="true"></span><?php echo esc_html( jroen_mod( 'jroen_hero_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h1 class="hero__title"><?php echo jroen_inline_format( jroen_mod( 'jroen_hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- geëscapet in jroen_inline_format(). ?></h1>
			<p class="hero__text"><?php echo jroen_inline_format( jroen_mod( 'jroen_hero_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<div class="hero__actions">
				<?php if ( jroen_mod( 'jroen_hero_btn1_text' ) ) : ?>
					<a class="btn btn--primary" href="<?php echo esc_url( jroen_mod( 'jroen_hero_btn1_url' ) ); ?>"><?php echo jroen_icon( 'chip' ); // phpcs:ignore ?> <?php echo esc_html( jroen_mod( 'jroen_hero_btn1_text' ) ); ?></a>
				<?php endif; ?>
				<?php if ( jroen_mod( 'jroen_hero_btn2_text' ) ) : ?>
					<a class="btn btn--outline" href="<?php echo esc_url( jroen_mod( 'jroen_hero_btn2_url' ) ); ?>"><span class="btn__icon-accent"><?php echo jroen_icon( 'globe' ); // phpcs:ignore ?></span> <?php echo esc_html( jroen_mod( 'jroen_hero_btn2_text' ) ); ?></a>
				<?php endif; ?>
			</div>
			<?php $jroen_tech = array_filter( array_map( 'trim', explode( ',', (string) jroen_mod( 'jroen_hero_tech' ) ) ) ); ?>
			<?php if ( $jroen_tech ) : ?>
				<div class="hero__tech">
					<?php if ( jroen_mod( 'jroen_hero_tech_label' ) ) : ?>
						<span class="hero__tech-label"><?php echo esc_html( jroen_mod( 'jroen_hero_tech_label' ) ); ?></span>
					<?php endif; ?>
					<ul class="chips">
						<?php foreach ( $jroen_tech as $jroen_t ) : ?>
							<li class="chip"><?php echo esc_html( $jroen_t ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( 'code' === $jroen_visual && '' !== trim( (string) jroen_mod( 'jroen_hero_code' ) ) ) : ?>
			<?php
			$jroen_code   = rtrim( (string) jroen_mod( 'jroen_hero_code' ) );
			$jroen_start  = max( 1, absint( jroen_mod( 'jroen_hero_code_start' ) ) );
			$jroen_count  = substr_count( $jroen_code, "\n" ) + 1;
			$jroen_file   = (string) jroen_mod( 'jroen_hero_code_file' );
			$jroen_slash  = strrpos( $jroen_file, '/' );
			$jroen_lang   = jroen_mod( 'jroen_hero_code_lang' );
			$jroen_status = jroen_mod( 'jroen_hero_code_status' );
			$jroen_ltext  = jroen_mod( 'jroen_hero_code_link_text' );
			$jroen_lurl   = jroen_mod( 'jroen_hero_code_link_url' );
			if ( $jroen_ltext && ! $jroen_lurl ) {
				$jroen_match = get_posts(
					array(
						'post_type'   => 'project',
						'title'       => $jroen_ltext,
						'numberposts' => 1,
						'fields'      => 'ids',
					)
				);
				$jroen_lurl  = $jroen_match ? get_permalink( $jroen_match[0] ) : '';
			}
			?>
			<div class="hero__visual reveal">
				<figure class="editor editor--card<?php echo jroen_mod( 'jroen_hero_code_animate' ) ? ' editor--animate' : ''; ?>" style="--lines:<?php echo (int) $jroen_count; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: bestandsnaam */ __( 'Codevoorbeeld: %s', 'jroen' ), $jroen_file ) ); ?>">
					<div class="editor__bar">
						<span class="editor__dots" aria-hidden="true"><i></i><i></i><i></i></span>
						<span class="editor__tab">
							<?php if ( false !== $jroen_slash ) : ?>
								<small><?php echo esc_html( substr( $jroen_file, 0, $jroen_slash + 1 ) ); ?></small><?php echo esc_html( substr( $jroen_file, $jroen_slash + 1 ) ); ?>
							<?php else : ?>
								<?php echo esc_html( $jroen_file ); ?>
							<?php endif; ?>
						</span>
						<span class="editor__lang"><?php echo 'cpp' === $jroen_lang ? 'C++' : 'Python'; ?></span>
					</div>
					<div class="editor__body">
						<div class="editor__gutter" aria-hidden="true"><?php echo implode( '<br>', range( $jroen_start, $jroen_start + $jroen_count - 1 ) ); // phpcs:ignore ?></div>
						<pre class="editor__code"><code data-lang="<?php echo esc_attr( $jroen_lang ); ?>"><?php echo esc_html( $jroen_code ); ?></code></pre>
					</div>
					<?php if ( jroen_mod( 'jroen_hero_code_env' ) || $jroen_status ) : ?>
						<div class="editor__daemon">
							<span class="pulse" aria-hidden="true"></span>
							<span class="editor__daemon-text">
								<strong class="editor__ok" data-final="<?php echo esc_attr( $jroen_status ); ?>"><?php echo esc_html( $jroen_status ); ?></strong>
								<small><?php echo esc_html( jroen_mod( 'jroen_hero_code_env' ) ); ?></small>
							</span>
							<span class="editor__badge" data-final="OK">OK</span>
						</div>
					<?php endif; ?>
				</figure>
				<?php if ( $jroen_ltext ) : ?>
					<p class="editor__source">
						<?php esc_html_e( 'uit het project', 'jroen' ); ?>
						<?php if ( $jroen_lurl ) : ?>
							<a href="<?php echo esc_url( $jroen_lurl ); ?>"><?php echo esc_html( $jroen_ltext ); ?></a>
						<?php else : ?>
							<span><?php echo esc_html( $jroen_ltext ); ?></span>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
		<?php elseif ( 'terminal' === $jroen_visual && $jroen_lines ) : ?>
			<div class="hero__visual reveal" aria-hidden="true">
				<div class="terminal">
					<div class="terminal__bar">
						<span class="terminal__dots"><i></i><i></i><i></i></span>
						<span class="terminal__title"><?php echo esc_html( jroen_mod( 'jroen_hero_terminal_title' ) ); ?></span>
					</div>
					<div class="terminal__body">
						<?php foreach ( $jroen_lines as $jroen_i => $jroen_line ) : ?>
							<div class="terminal__line" style="--i:<?php echo (int) $jroen_i; ?>"><?php echo esc_html( $jroen_line ); ?></div>
						<?php endforeach; ?>
						<div class="terminal__line terminal__cursor" style="--i:<?php echo count( $jroen_lines ); ?>">&gt; <span></span></div>
					</div>
				</div>
			</div>
		<?php elseif ( 'logo' === $jroen_visual ) : ?>
			<div class="hero__visual hero__visual--logo reveal" aria-hidden="true">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-mark-dark.svg' ); ?>" alt="" width="258" height="328">
			</div>
		<?php endif; ?>
	</div>
</section>
