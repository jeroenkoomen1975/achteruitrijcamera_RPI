<?php
/**
 * Websites: berichttype "Websites" met websitetypes, technieken en websitegegevens.
 * Werkt op dezelfde manier als de projecten (zie inc/projects.php).
 *
 * @package KoomenIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Berichttype en indeling registreren.
 */
function koomenit_register_websites() {
	register_post_type(
		'website',
		array(
			'labels'        => array(
				'name'               => __( 'Websites', 'koomenit' ),
				'singular_name'      => __( 'Website', 'koomenit' ),
				'add_new'            => __( 'Nieuwe website', 'koomenit' ),
				'add_new_item'       => __( 'Nieuwe website toevoegen', 'koomenit' ),
				'edit_item'          => __( 'Website bewerken', 'koomenit' ),
				'view_item'          => __( 'Website bekijken', 'koomenit' ),
				'all_items'          => __( 'Alle websites', 'koomenit' ),
				'search_items'       => __( 'Websites zoeken', 'koomenit' ),
				'not_found'          => __( 'Geen websites gevonden', 'koomenit' ),
				'featured_image'     => __( 'Screenshot', 'koomenit' ),
				'set_featured_image' => __( 'Screenshot instellen', 'koomenit' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'websites' ),
			'menu_icon'     => 'dashicons-admin-site-alt3',
			'menu_position' => 6,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
		)
	);

	register_taxonomy(
		'websitetype',
		'website',
		array(
			'labels'            => array(
				'name'          => __( 'Websitetypes', 'koomenit' ),
				'singular_name' => __( 'Websitetype', 'koomenit' ),
				'add_new_item'  => __( 'Nieuw websitetype toevoegen', 'koomenit' ),
				'all_items'     => __( 'Websitetypes', 'koomenit' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'websitetype' ),
		)
	);

	$meta = array(
		'koomenit_url'    => 'esc_url_raw',
		'koomenit_client' => 'sanitize_text_field',
		'koomenit_role'   => 'sanitize_text_field',
		'koomenit_status' => 'sanitize_key',
		'koomenit_year'   => 'sanitize_text_field',
		'koomenit_github' => 'esc_url_raw',
	);
	foreach ( $meta as $key => $sanitize ) {
		register_post_meta(
			'website',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => $sanitize,
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'koomenit_register_websites' );

/**
 * Mogelijke statussen van een website.
 *
 * @return array
 */
function koomenit_website_statuses() {
	return array(
		''             => __( '— geen —', 'koomenit' ),
		'live'         => __( 'Live', 'koomenit' ),
		'ontwikkeling' => __( 'In ontwikkeling', 'koomenit' ),
		'onderhoud'    => __( 'In beheer', 'koomenit' ),
		'archief'      => __( 'Archief / offline', 'koomenit' ),
	);
}

/**
 * Domeinnaam zonder protocol en "www." voor in de adresbalk.
 *
 * @param string $url Webadres.
 * @return string
 */
function koomenit_website_domain( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	return $host ? preg_replace( '/^www\./', '', $host ) : '';
}

/**
 * Metabox met websitegegevens.
 */
function koomenit_website_metabox() {
	add_meta_box( 'koomenit_website_info', __( 'Websitegegevens', 'koomenit' ), 'koomenit_website_metabox_html', 'website', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'koomenit_website_metabox' );

/**
 * Inhoud van de metabox.
 *
 * @param WP_Post $post Bericht.
 */
function koomenit_website_metabox_html( $post ) {
	wp_nonce_field( 'koomenit_website_save', 'koomenit_website_nonce' );
	$status = get_post_meta( $post->ID, 'koomenit_status', true );
	$fields = array(
		'koomenit_url'    => array( __( 'Webadres', 'koomenit' ), 'url', 'https://' ),
		'koomenit_client' => array( __( 'Opdrachtgever', 'koomenit' ), 'text', __( 'bijv. eigen project', 'koomenit' ) ),
		'koomenit_role'   => array( __( 'Rol', 'koomenit' ), 'text', __( 'bijv. ontwerp en ontwikkeling', 'koomenit' ) ),
		'koomenit_year'   => array( __( 'Jaar', 'koomenit' ), 'text', gmdate( 'Y' ) ),
		'koomenit_github' => array( __( 'GitHub-link (optioneel)', 'koomenit' ), 'url', 'https://github.com/...' ),
	);
	?>
	<p>
		<label for="koomenit_status"><strong><?php esc_html_e( 'Status', 'koomenit' ); ?></strong></label><br>
		<select id="koomenit_status" name="koomenit_status" style="width:100%">
			<?php foreach ( koomenit_website_statuses() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php foreach ( $fields as $key => $field ) : ?>
		<p>
			<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $field[0] ); ?></strong></label><br>
			<input type="<?php echo esc_attr( $field[1] ); ?>" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>" placeholder="<?php echo esc_attr( $field[2] ); ?>" style="width:100%">
		</p>
	<?php endforeach; ?>
	<p class="description"><?php esc_html_e( 'Kies rechts ook het websitetype en de technieken. Stel een screenshot in als uitgelichte afbeelding.', 'koomenit' ); ?></p>
	<?php
}

/**
 * Websitegegevens opslaan.
 *
 * @param int $post_id Bericht-ID.
 */
function koomenit_website_save( $post_id ) {
	if ( ! isset( $_POST['koomenit_website_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['koomenit_website_nonce'] ) ), 'koomenit_website_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'koomenit_url'    => 'esc_url_raw',
		'koomenit_client' => 'sanitize_text_field',
		'koomenit_role'   => 'sanitize_text_field',
		'koomenit_year'   => 'sanitize_text_field',
		'koomenit_github' => 'esc_url_raw',
	);
	foreach ( $fields as $key => $sanitize ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, call_user_func( $sanitize, wp_unslash( $_POST[ $key ] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- opgeschoond via $sanitize.
		}
	}

	if ( isset( $_POST['koomenit_status'] ) ) {
		$status = sanitize_key( wp_unslash( $_POST['koomenit_status'] ) );
		if ( array_key_exists( $status, koomenit_website_statuses() ) ) {
			update_post_meta( $post_id, 'koomenit_status', $status );
		}
	}
}
add_action( 'save_post_website', 'koomenit_website_save' );

/**
 * Filterknoppen per websitetype.
 *
 * @param string $active Actieve websitetype-slug.
 */
function koomenit_website_filters( $active = '' ) {
	koomenit_term_filters( 'websitetype', get_post_type_archive_link( 'website' ), $active );
}
