<?php
/**
 * Eén website met websitegegevens en meer websites.
 *
 * @package KoomenIT
 */

get_header();

while ( have_posts() ) :
	the_post();
	$koomenit_types     = koomenit_project_terms( 'websitetype' );
	$koomenit_tech      = koomenit_project_terms( 'techniek' );
	$koomenit_url       = get_post_meta( get_the_ID(), 'koomenit_url', true );
	$koomenit_github    = get_post_meta( get_the_ID(), 'koomenit_github', true );
	$koomenit_client    = get_post_meta( get_the_ID(), 'koomenit_client', true );
	$koomenit_role      = get_post_meta( get_the_ID(), 'koomenit_role', true );
	$koomenit_year      = get_post_meta( get_the_ID(), 'koomenit_year', true );
	$koomenit_status    = get_post_meta( get_the_ID(), 'koomenit_status', true );
	$koomenit_statuses  = koomenit_website_statuses();
	$koomenit_domain    = koomenit_website_domain( $koomenit_url );
	$koomenit_reachable = $koomenit_url && 'archief' !== $koomenit_status;
	?>
	<header class="page-hero page-hero--project">
		<?php koomenit_network_svg( 'page-hero__network' ); ?>
		<div class="container">
			<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Kruimelpad', 'koomenit' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'website' ) ); ?>"><?php esc_html_e( 'Websites', 'koomenit' ); ?></a>
				<?php foreach ( $koomenit_types as $koomenit_type ) : ?>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_term_link( $koomenit_type ) ); ?>"><?php echo esc_html( $koomenit_type->name ); ?></a>
				<?php endforeach; ?>
			</nav>
			<div class="page-hero__heading">
				<?php if ( koomenit_item_logo_id( get_the_ID() ) ) : ?>
					<?php koomenit_logo_tile( get_the_ID(), 'globe' ); ?>
				<?php endif; ?>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
			</div>
			<?php if ( has_excerpt() ) : ?>
				<p class="page-hero__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<div class="page-hero__actions">
				<?php if ( $koomenit_reachable ) : ?>
					<a class="btn btn--primary" href="<?php echo esc_url( $koomenit_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Bezoek website', 'koomenit' ); ?> <?php echo koomenit_icon( 'external' ); // phpcs:ignore ?></a>
				<?php endif; ?>
				<?php if ( $koomenit_github ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( $koomenit_github ); ?>" target="_blank" rel="noopener"><?php echo koomenit_icon( 'github' ); // phpcs:ignore ?> <?php esc_html_e( 'Code op GitHub', 'koomenit' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<main id="primary" class="site-main section">
		<div class="container project-layout">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<div class="site-frame">
					<?php koomenit_browser_bar( $koomenit_domain ); ?>
					<div class="site-frame__shot">
						<?php
						ob_start();
						koomenit_wireframe();
						koomenit_item_media( get_the_ID(), 'large', ob_get_clean() );
						?>
					</div>
				</div>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
			</article>

			<aside class="project-info" aria-label="<?php esc_attr_e( 'Websitegegevens', 'koomenit' ); ?>">
				<h2 class="project-info__title"><?php esc_html_e( 'Websitegegevens', 'koomenit' ); ?></h2>
				<dl>
					<?php if ( $koomenit_domain ) : ?>
						<dt><?php esc_html_e( 'Adres', 'koomenit' ); ?></dt>
						<dd>
							<?php if ( $koomenit_reachable ) : ?>
								<a class="project-info__link" href="<?php echo esc_url( $koomenit_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $koomenit_domain ); ?> <?php echo koomenit_icon( 'external' ); // phpcs:ignore ?></a>
							<?php else : ?>
								<?php echo esc_html( $koomenit_domain ); ?>
							<?php endif; ?>
						</dd>
					<?php endif; ?>
					<?php if ( $koomenit_types ) : ?>
						<dt><?php esc_html_e( 'Type', 'koomenit' ); ?></dt>
						<dd><?php echo esc_html( implode( ', ', wp_list_pluck( $koomenit_types, 'name' ) ) ); ?></dd>
					<?php endif; ?>
					<?php if ( $koomenit_client ) : ?>
						<dt><?php esc_html_e( 'Opdrachtgever', 'koomenit' ); ?></dt>
						<dd><?php echo esc_html( $koomenit_client ); ?></dd>
					<?php endif; ?>
					<?php if ( $koomenit_role ) : ?>
						<dt><?php esc_html_e( 'Rol', 'koomenit' ); ?></dt>
						<dd><?php echo esc_html( $koomenit_role ); ?></dd>
					<?php endif; ?>
					<?php if ( $koomenit_status && isset( $koomenit_statuses[ $koomenit_status ] ) ) : ?>
						<dt><?php esc_html_e( 'Status', 'koomenit' ); ?></dt>
						<dd><span class="status status--<?php echo esc_attr( $koomenit_status ); ?>"><?php echo esc_html( $koomenit_statuses[ $koomenit_status ] ); ?></span></dd>
					<?php endif; ?>
					<?php if ( $koomenit_year ) : ?>
						<dt><?php esc_html_e( 'Jaar', 'koomenit' ); ?></dt>
						<dd><?php echo esc_html( $koomenit_year ); ?></dd>
					<?php endif; ?>
					<?php if ( $koomenit_tech ) : ?>
						<dt><?php esc_html_e( 'Technieken', 'koomenit' ); ?></dt>
						<dd>
							<ul class="chips">
								<?php foreach ( $koomenit_tech as $koomenit_t ) : ?>
									<li><a class="chip" href="<?php echo esc_url( get_term_link( $koomenit_t ) ); ?>"><?php echo esc_html( $koomenit_t->name ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</dd>
					<?php endif; ?>
				</dl>
			</aside>
		</div>

		<?php
		$koomenit_related = new WP_Query(
			array(
				'post_type'           => 'website',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		if ( $koomenit_related->have_posts() ) :
			?>
			<div class="container related">
				<h2 class="related__title"><?php esc_html_e( 'Meer websites', 'koomenit' ); ?></h2>
				<div class="item-list">
					<?php
					while ( $koomenit_related->have_posts() ) {
						$koomenit_related->the_post();
						get_template_part( 'template-parts/content', 'website' );
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		<?php endif; ?>
	</main>
	<?php
endwhile;

get_footer();
