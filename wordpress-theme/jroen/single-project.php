<?php
/**
 * Eén project met projectgegevens en gerelateerde projecten.
 *
 * @package Jroen
 */

get_header();

while ( have_posts() ) :
	the_post();
	$jroen_platforms = jroen_project_terms( 'platform' );
	$jroen_types     = jroen_project_terms( 'projecttype' );
	$jroen_tech      = jroen_project_terms( 'techniek' );
	$jroen_github    = get_post_meta( get_the_ID(), 'jroen_github', true );
	$jroen_demo      = get_post_meta( get_the_ID(), 'jroen_demo', true );
	$jroen_year      = get_post_meta( get_the_ID(), 'jroen_year', true );
	$jroen_status    = get_post_meta( get_the_ID(), 'jroen_status', true );
	$jroen_statuses  = jroen_project_statuses();
	$jroen_hardware  = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( get_the_ID(), 'jroen_hardware', true ) ) ) );
	?>
	<header class="page-hero page-hero--project">
		<?php jroen_network_svg( 'page-hero__network' ); ?>
		<div class="container">
			<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Kruimelpad', 'jroen' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>"><?php esc_html_e( 'Projecten', 'jroen' ); ?></a>
				<?php foreach ( $jroen_platforms as $jroen_platform ) : ?>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_term_link( $jroen_platform ) ); ?>"><?php echo esc_html( $jroen_platform->name ); ?></a>
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
				<?php if ( $jroen_github ) : ?>
					<a class="btn btn--primary" href="<?php echo esc_url( $jroen_github ); ?>" target="_blank" rel="noopener"><?php echo jroen_icon( 'github' ); // phpcs:ignore ?> <?php esc_html_e( 'Code op GitHub', 'jroen' ); ?></a>
				<?php endif; ?>
				<?php if ( $jroen_demo ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( $jroen_demo ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Bekijk demo', 'jroen' ); ?> <?php echo jroen_icon( 'external' ); // phpcs:ignore ?></a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<main id="primary" class="site-main section">
		<div class="container project-layout">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<?php if ( has_post_thumbnail() && ! jroen_is_logo_image( get_post_thumbnail_id(), get_the_ID() ) ) : ?>
					<figure class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
			</article>

			<aside class="project-info" aria-label="<?php esc_attr_e( 'Projectgegevens', 'jroen' ); ?>">
				<h2 class="project-info__title"><?php esc_html_e( 'Projectgegevens', 'jroen' ); ?></h2>
				<dl>
					<?php if ( $jroen_platforms ) : ?>
						<dt><?php esc_html_e( 'Platform', 'jroen' ); ?></dt>
						<dd class="badges">
							<?php foreach ( $jroen_platforms as $jroen_platform ) : ?>
								<a class="badge badge--platform" href="<?php echo esc_url( get_term_link( $jroen_platform ) ); ?>"><?php echo jroen_icon( jroen_platform_icon( $jroen_platform->name ) ); // phpcs:ignore ?><?php echo esc_html( $jroen_platform->name ); ?></a>
							<?php endforeach; ?>
						</dd>
					<?php endif; ?>
					<?php if ( $jroen_types ) : ?>
						<dt><?php esc_html_e( 'Type', 'jroen' ); ?></dt>
						<dd><?php echo esc_html( implode( ', ', wp_list_pluck( $jroen_types, 'name' ) ) ); ?></dd>
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
					<?php if ( $jroen_hardware ) : ?>
						<dt><?php esc_html_e( 'Hardware', 'jroen' ); ?></dt>
						<dd>
							<ul class="hardware">
								<?php foreach ( $jroen_hardware as $jroen_part ) : ?>
									<li><?php echo esc_html( $jroen_part ); ?></li>
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
				'post_type'           => 'project',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'tax_query'           => $jroen_platforms ? array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'platform',
						'field'    => 'term_id',
						'terms'    => wp_list_pluck( $jroen_platforms, 'term_id' ),
					),
				) : array(),
			)
		);
		if ( $jroen_related->have_posts() ) :
			?>
			<div class="container related">
				<h2 class="related__title"><?php esc_html_e( 'Meer projecten', 'jroen' ); ?></h2>
				<div class="item-list">
					<?php
					while ( $jroen_related->have_posts() ) {
						$jroen_related->the_post();
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
