<?php
/**
 * Eén project met projectgegevens en gerelateerde projecten.
 *
 * @package KoomenIT
 */

get_header();

while ( have_posts() ) :
	the_post();
	$koomenit_platforms = koomenit_project_terms( 'platform' );
	$koomenit_types     = koomenit_project_terms( 'projecttype' );
	$koomenit_tech      = koomenit_project_terms( 'techniek' );
	$koomenit_github    = get_post_meta( get_the_ID(), 'koomenit_github', true );
	$koomenit_demo      = get_post_meta( get_the_ID(), 'koomenit_demo', true );
	$koomenit_year      = get_post_meta( get_the_ID(), 'koomenit_year', true );
	$koomenit_status    = get_post_meta( get_the_ID(), 'koomenit_status', true );
	$koomenit_statuses  = koomenit_project_statuses();
	$koomenit_hardware  = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( get_the_ID(), 'koomenit_hardware', true ) ) ) );
	?>
	<header class="page-hero page-hero--project">
		<?php koomenit_network_svg( 'page-hero__network' ); ?>
		<div class="container">
			<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Kruimelpad', 'koomenit' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>"><?php esc_html_e( 'Projecten', 'koomenit' ); ?></a>
				<?php foreach ( $koomenit_platforms as $koomenit_platform ) : ?>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_term_link( $koomenit_platform ) ); ?>"><?php echo esc_html( $koomenit_platform->name ); ?></a>
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
				<?php if ( $koomenit_github ) : ?>
					<a class="btn btn--primary" href="<?php echo esc_url( $koomenit_github ); ?>" target="_blank" rel="noopener"><?php echo koomenit_icon( 'github' ); // phpcs:ignore ?> <?php esc_html_e( 'Code op GitHub', 'koomenit' ); ?></a>
				<?php endif; ?>
				<?php if ( $koomenit_demo ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( $koomenit_demo ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Bekijk demo', 'koomenit' ); ?> <?php echo koomenit_icon( 'external' ); // phpcs:ignore ?></a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<main id="primary" class="site-main section">
		<div class="container project-layout">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<?php if ( has_post_thumbnail() && ! koomenit_is_logo_image( get_post_thumbnail_id() ) ) : ?>
					<figure class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
			</article>

			<aside class="project-info" aria-label="<?php esc_attr_e( 'Projectgegevens', 'koomenit' ); ?>">
				<h2 class="project-info__title"><?php esc_html_e( 'Projectgegevens', 'koomenit' ); ?></h2>
				<dl>
					<?php if ( $koomenit_platforms ) : ?>
						<dt><?php esc_html_e( 'Platform', 'koomenit' ); ?></dt>
						<dd class="badges">
							<?php foreach ( $koomenit_platforms as $koomenit_platform ) : ?>
								<a class="badge badge--platform" href="<?php echo esc_url( get_term_link( $koomenit_platform ) ); ?>"><?php echo koomenit_icon( koomenit_platform_icon( $koomenit_platform->name ) ); // phpcs:ignore ?><?php echo esc_html( $koomenit_platform->name ); ?></a>
							<?php endforeach; ?>
						</dd>
					<?php endif; ?>
					<?php if ( $koomenit_types ) : ?>
						<dt><?php esc_html_e( 'Type', 'koomenit' ); ?></dt>
						<dd><?php echo esc_html( implode( ', ', wp_list_pluck( $koomenit_types, 'name' ) ) ); ?></dd>
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
					<?php if ( $koomenit_hardware ) : ?>
						<dt><?php esc_html_e( 'Hardware', 'koomenit' ); ?></dt>
						<dd>
							<ul class="hardware">
								<?php foreach ( $koomenit_hardware as $koomenit_part ) : ?>
									<li><?php echo esc_html( $koomenit_part ); ?></li>
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
				'post_type'           => 'project',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'tax_query'           => $koomenit_platforms ? array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'platform',
						'field'    => 'term_id',
						'terms'    => wp_list_pluck( $koomenit_platforms, 'term_id' ),
					),
				) : array(),
			)
		);
		if ( $koomenit_related->have_posts() ) :
			?>
			<div class="container related">
				<h2 class="related__title"><?php esc_html_e( 'Meer projecten', 'koomenit' ); ?></h2>
				<div class="item-list">
					<?php
					while ( $koomenit_related->have_posts() ) {
						$koomenit_related->the_post();
						get_template_part( 'template-parts/content', 'project' );
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
