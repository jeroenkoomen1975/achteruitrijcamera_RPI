<?php
/**
 * Blog: leestijd, categoriefilters, inhoudsopgave en zijbalk.
 *
 * @package KoomenIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Geschatte leestijd in minuten (200 woorden per minuut, codeblokken tellen mee).
 *
 * @param int|null $post_id Bericht-ID.
 * @return int
 */
function koomenit_reading_time( $post_id = null ) {
	$text  = wp_strip_all_tags( get_post_field( 'post_content', $post_id ? $post_id : get_the_ID() ) );
	$words = count( preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Leestijd als label.
 *
 * @param int|null $post_id Bericht-ID.
 * @return string
 */
function koomenit_reading_time_label( $post_id = null ) {
	/* translators: %d: aantal minuten */
	return sprintf( __( '%d min lezen', 'koomenit' ), koomenit_reading_time( $post_id ) );
}

/**
 * Adres van het blogoverzicht (de berichtenpagina, of anders de homepage).
 *
 * @return string
 */
function koomenit_blog_url() {
	$page = (int) get_option( 'page_for_posts' );
	return $page ? get_permalink( $page ) : home_url( '/' );
}

/**
 * Filterknoppen per categorie. Dit zijn gewone links naar de categoriepagina,
 * zodat ze ook met meerdere pagina's berichten kloppen.
 */
function koomenit_blog_filters() {
	$cats = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
		)
	);
	if ( ! $cats || is_wp_error( $cats ) || count( $cats ) < 2 ) {
		return;
	}
	$current = is_category() ? get_queried_object() : null;
	?>
	<nav class="filters" aria-label="<?php esc_attr_e( 'Categorieën', 'koomenit' ); ?>">
		<a class="filter<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url( koomenit_blog_url() ); ?>"<?php echo $current ? '' : ' aria-current="page"'; ?>><?php esc_html_e( 'Alles', 'koomenit' ); ?></a>
		<?php foreach ( $cats as $cat ) : ?>
			<?php $koomenit_on = $current && $current->term_id === $cat->term_id; ?>
			<a class="filter<?php echo $koomenit_on ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"<?php echo $koomenit_on ? ' aria-current="page"' : ''; ?>>
				<?php echo esc_html( $cat->name ); ?>
				<span class="filter__count"><?php echo esc_html( $cat->count ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
}

/**
 * Inhoud van een bericht met ankers bij de tussenkoppen, plus de inhoudsopgave.
 *
 * @return array { content: string, toc: array<array{id:string,title:string,level:int}> }
 */
function koomenit_content_with_toc() {
	$content = apply_filters( 'the_content', get_the_content() );
	$toc     = array();
	$used    = array();

	$content = preg_replace_callback(
		'#<h([23])([^>]*)>(.*?)</h\1>#is',
		function ( $m ) use ( &$toc, &$used ) {
			$title = trim( wp_strip_all_tags( $m[3] ) );
			if ( '' === $title ) {
				return $m[0];
			}
			if ( preg_match( '/\sid=["\']([^"\']+)["\']/', $m[2], $existing ) ) {
				$id = $existing[1];
			} else {
				$base = sanitize_title( $title );
				$id   = $base ? $base : 'sectie';
				$n    = 2;
				while ( isset( $used[ $id ] ) ) {
					$id = $base . '-' . $n++;
				}
				$m[2] .= ' id="' . esc_attr( $id ) . '"';
			}
			$used[ $id ] = true;
			$toc[]       = array(
				'id'    => $id,
				'title' => $title,
				'level' => (int) $m[1],
			);
			return '<h' . $m[1] . $m[2] . '>' . $m[3] . '</h' . $m[1] . '>';
		},
		$content
	);

	return array(
		'content' => $content,
		'toc'     => $toc,
	);
}

/**
 * Inhoudsopgave tonen.
 *
 * @param array $toc Koppen uit koomenit_content_with_toc().
 */
function koomenit_toc( $toc ) {
	?>
	<nav class="toc" aria-label="<?php esc_attr_e( 'Inhoud', 'koomenit' ); ?>">
		<p class="toc__title"><?php esc_html_e( 'Inhoud', 'koomenit' ); ?></p>
		<ol class="toc__list">
			<?php foreach ( $toc as $item ) : ?>
				<li class="toc__item toc__item--h<?php echo (int) $item['level']; ?>"><a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a></li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/**
 * Zijbalk van de blog: eigen widgets, of anders zoeken, categorieën en GitHub.
 */
function koomenit_blog_sidebar() {
	echo '<aside class="sidebar blog-sidebar" aria-label="' . esc_attr__( 'Zijbalk', 'koomenit' ) . '">';
	if ( is_active_sidebar( 'sidebar-1' ) ) {
		dynamic_sidebar( 'sidebar-1' );
		echo '</aside>';
		return;
	}
	?>
	<section class="widget widget--search">
		<h3 class="widget-title"><?php esc_html_e( 'Zoeken', 'koomenit' ); ?></h3>
		<?php get_search_form(); ?>
	</section>
	<?php
	$cats = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
		)
	);
	if ( $cats && ! is_wp_error( $cats ) ) :
		?>
		<section class="widget">
			<h3 class="widget-title"><?php esc_html_e( 'Categorieën', 'koomenit' ); ?></h3>
			<ul class="widget-cats">
				<?php foreach ( $cats as $cat ) : ?>
					<li><a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a><span><?php echo esc_html( $cat->count ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>
	<?php $koomenit_github = koomenit_mod( 'koomenit_social_github' ); ?>
	<?php if ( $koomenit_github ) : ?>
		<section class="widget widget--github">
			<?php echo koomenit_icon( 'github' ); // phpcs:ignore ?>
			<h3 class="widget-title"><?php esc_html_e( 'Code van de projecten', 'koomenit' ); ?></h3>
			<p><?php esc_html_e( 'De broncode van de projecten staat op GitHub.', 'koomenit' ); ?></p>
			<a class="btn btn--primary btn--sm" href="<?php echo esc_url( $koomenit_github ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Naar GitHub', 'koomenit' ); ?></a>
		</section>
	<?php endif; ?>
	<?php
	echo '</aside>';
}
