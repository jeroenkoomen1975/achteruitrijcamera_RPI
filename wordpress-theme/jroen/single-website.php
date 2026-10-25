<?php
/**
 * Eén website met websitegegevens en meer websites.
 *
 * @package Jroen
 */

get_header();

while ( have_posts() ) :
	the_post();
	$jroen_types     = jroen_project_terms( 'websitetype' );
	$jroen_tech      = jroen_project_terms( 'techniek' );
	$jroen_url       = get_post_meta( get_the_ID(), 'jroen_url', true );
	$jroen_github    = get_post_meta( get_the_ID(), 'jroen_github', true );
	$jroen_client    = get_post_meta( get_the_ID(), 'jroen_client', true );
	$jroen_role      = get_post_meta( get_the_ID(), 'jroen_role', true );
	$jroen_year      = get_post_meta( get_the_ID(), 'jroen_year', true );
	$jroen_status    = get_post_meta( get_the_ID(), 'jroen_status', true );
	$jroen_statuses  = jroen_website_statuses();
	$jroen_domain    = jroen_website_domain( $jroen_url );
	$jroen_reachable = $jroen_url && 'archief' !== $jroen_status;
	?>
	<header class="page-hero page-hero--project">
		<?php jroen_network_svg( 'page-hero__network' ); ?>
		<div class="container">
			<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Kruimelpad', 'jroen' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'website' ) ); ?>"><?php esc_html_e( 'Websites', 'jroen' ); ?></a>
				<?php foreach ( $jroen_types as $jroen_type ) : ?>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_term_link( $jroen_type ) ); ?>"><?php echo esc_html( $jroen_type->name ); ?></a>
				<?php endforeach; ?>
			</nav>
			<div class="page-hero__heading">
				<?php if ( jroen_item_logo_id( get_the_ID() ) ) : ?>
					<?php jroen_logo_tile( get_the_ID(), 'globe' ); ?>
				<?php endif; ?>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
			</div>
			<?php if ( has_excerpt() ) : ?>
				<p class="page-hero__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<div class="page-hero__actions">
				<?php if ( $jroen_reachable ) : ?>
					<a class="btn btn--primary" href="<?php echo esc_url( $jroen_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Bezoek website', 'jroen' ); ?> <?php echo jroen_icon( 'external' ); // phpcs:ignore ?></a>
				<?php endif; ?>
				<?php if ( $jroen_github ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( $jroen_github ); ?>" target="_blank" rel="noopener"><?php echo jroen_icon( 'github' ); // phpcs:ignore ?> <?php esc_html_e( 'Code op GitHub', 'jroen' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<main id="primary" class="site-main section">
		<div class="container project-layout">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<div class="site-frame">
					<?php jroen_browser_bar( $jroen_domain ); ?>
					<div class="site-frame__shot">
						<?php
						ob_start();
						jroen_wireframe();
						jroen_item_media( get_the_ID(), 'large', ob_get_clean() );
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

			<aside class="project-info" aria-label="<?php esc_attr_e( 'Websitegegevens', 'jroen' ); ?>">
				<h2 class="project-info__title"><?php esc_html_e( 'Websitegegevens', 'jroen' ); ?></h2>
				<dl>
					<?php if ( $jroen_domain ) : ?>
						<dt><?php esc_html_e( 'Adres', 'jroen' ); ?></dt>
						<dd>
							<?php if ( $jroen_reachable ) : ?>
								<a class="project-info__link" href="<?php echo esc_url( $jroen_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $jroen_domain ); ?> <?php echo jroen_icon( 'external' ); // phpcs:ignore ?></a>
							<?php else : ?>
								<?php echo esc_html( $jroen_domain ); ?>
							<?php endif; ?>
						</dd>
					<?php endif; ?>
					<?php if ( $jroen_types ) : ?>
						<dt><?php esc_html_e( 'Type', 'jroen' ); ?></dt>
						<dd><?php echo esc_html( implode( ', ', wp_list_pluck( $jroen_types, 'name' ) ) ); ?></dd>
					<?php endif; ?>
					<?php if ( $jroen_client ) : ?>
						<dt><?php esc_html_e( 'Opdrachtgever', 'jroen' ); ?></dt>
						<dd><?php echo esc_html( $jroen_client ); ?></dd>
					<?php endif; ?>
					<?php if ( $jroen_role ) : ?>
						<dt><?php esc_html_e( 'Rol', 'jroen' ); ?></dt>
						<dd><?php echo esc_html( $jroen_role ); ?></dd>
					<?php endif; ?>
					<?php if ( $jroen_status && isset( $jroen_statuses[ $jroen_status ] ) ) : ?>
						<dt><?php esc_html_e( 'Status', 'jroen' ); ?></dt>
						<dd><span class="status status--<?php echo esc_attr( $jroen_status ); ?>"><?php echo esc_html( $jroen_statuses[ $jroen_status ] ); ?></span></dd>
					<?php endif; ?>
					<?php if ( $jroen_year ) : ?>
						<dt><?php esc_html_e( 'Jaar', 'jroen' ); ?></dt>
						<dd><?php echo esc_html( $jroen_year ); ?></dd>
					<?php endif; ?>
					<?php if ( $jroen_tech ) : ?>
						<dt><?php esc_html_e( 'Technieken', 'jroen' ); ?></dt>
						<dd>
							<ul class="chips">
								<?php foreach ( $jroen_tech as $jroen_t ) : ?>
									<li><a class="chip" href="<?php echo esc_url( get_term_link( $jroen_t ) ); ?>"><?php echo esc_html( $jroen_t->name ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</dd>
					<?php endif; ?>
				</dl>
			</aside>
		</div>

		<?php
		$jroen_related = new WP_Query(
			array(
				'post_type'           => 'website',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		if ( $jroen_related->have_posts() ) :
			?>
			<div class="container related">
				<h2 class="related__title"><?php esc_html_e( 'Meer websites', 'jroen' ); ?></h2>
				<div class="item-list">
					<?php
					while ( $jroen_related->have_posts() ) {
						$jroen_related->the_post();
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
