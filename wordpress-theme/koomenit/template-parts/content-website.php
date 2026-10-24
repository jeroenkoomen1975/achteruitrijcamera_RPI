<?php
/**
 * Websitekaart voor overzichten: screenshot in een browservenster met adresbalk.
 *
 * @package KoomenIT
 */

$koomenit_types    = koomenit_project_terms( 'websitetype' );
$koomenit_tech     = array_slice( koomenit_project_terms( 'techniek' ), 0, 4 );
$koomenit_url      = get_post_meta( get_the_ID(), 'koomenit_url', true );
$koomenit_status   = get_post_meta( get_the_ID(), 'koomenit_status', true );
$koomenit_statuses = koomenit_website_statuses();
$koomenit_domain   = koomenit_website_domain( $koomenit_url );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'project-card site-card reveal' ); ?> data-terms="<?php echo esc_attr( koomenit_term_slugs( 'websitetype' ) ); ?>">
	<a class="site-card__frame" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php koomenit_browser_bar( $koomenit_domain ); ?>
		<span class="site-card__shot">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'koomenit-card' ); ?>
			<?php else : ?>
				<?php koomenit_wireframe(); ?>
			<?php endif; ?>
		</span>
	</a>
	<div class="project-card__body">
		<div class="project-card__meta">
			<?php if ( $koomenit_types ) : ?>
				<span class="project-card__type"><?php echo esc_html( implode( ' · ', wp_list_pluck( $koomenit_types, 'name' ) ) ); ?></span>
			<?php endif; ?>
			<?php if ( $koomenit_status && isset( $koomenit_statuses[ $koomenit_status ] ) ) : ?>
				<span class="status status--<?php echo esc_attr( $koomenit_status ); ?>"><?php echo esc_html( $koomenit_statuses[ $koomenit_status ] ); ?></span>
			<?php endif; ?>
		</div>
		<h3 class="project-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="project-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
		<?php if ( $koomenit_tech ) : ?>
			<ul class="chips">
				<?php foreach ( $koomenit_tech as $koomenit_t ) : ?>
					<li class="chip"><?php echo esc_html( $koomenit_t->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $koomenit_url && 'archief' !== $koomenit_status ) : ?>
			<a class="site-card__visit" href="<?php echo esc_url( $koomenit_url ); ?>" target="_blank" rel="noopener">
				<?php
				/* translators: %s: domeinnaam */
				echo esc_html( sprintf( __( 'Bezoek %s', 'koomenit' ), $koomenit_domain ) );
				?>
				<?php echo koomenit_icon( 'external' ); // phpcs:ignore ?>
			</a>
		<?php endif; ?>
	</div>
</article>
