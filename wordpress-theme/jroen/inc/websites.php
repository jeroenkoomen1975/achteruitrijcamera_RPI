<?php
/**
 * Websites: berichttype "Websites" met websitetypes, technieken en websitegegevens.
 * Werkt op dezelfde manier als de projecten (zie inc/projects.php).
 *
 * @package Jroen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Berichttype en indeling registreren.
 */
function jroen_register_websites() {
	register_post_type(
		'website',
		array(
			'labels'        => array(
				'name'               => __( 'Websites', 'jroen' ),
				'singular_name'      => __( 'Website', 'jroen' ),
				'add_new'            => __( 'Nieuwe website', 'jroen' ),
				'add_new_item'       => __( 'Nieuwe website toevoegen', 'jroen' ),
				'edit_item'          => __( 'Website bewerken', 'jroen' ),
				'view_item'          => __( 'Website bekijken', 'jroen' ),
				'all_items'          => __( 'Alle websites', 'jroen' ),
				'search_items'       => __( 'Websites zoeken', 'jroen' ),
				'not_found'          => __( 'Geen websites gevonden', 'jroen' ),
				'featured_image'     => __( 'Screenshot', 'jroen' ),
				'set_featured_image' => __( 'Screenshot instellen', 'jroen' ),
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
				'name'          => __( 'Websitetypes', 'jroen' ),
				'singular_name' => __( 'Websitetype', 'jroen' ),
				'add_new_item'  => __( 'Nieuw websitetype toevoegen', 'jroen' ),
				'all_items'     => __( 'Websitetypes', 'jroen' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'websitetype' ),
		)
	);

	$meta = array(
		'jroen_url'    => 'esc_url_raw',
		'jroen_client' => 'sanitize_text_field',
		'jroen_role'   => 'sanitize_text_field',
		'jroen_status' => 'sanitize_key',
		'jroen_year'   => 'sanitize_text_field',
		'jroen_github' => 'esc_url_raw',
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

	register_post_meta(
		'website',
		'jroen_logo',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'jroen_register_websites' );

/**
 * Mogelijke statussen van een website.
 *
 * @return array
 */
function jroen_website_statuses() {
	return array(
		''             => __( '— geen —', 'jroen' ),
		'live'         => __( 'Live', 'jroen' ),
		'ontwikkeling' => __( 'In ontwikkeling', 'jroen' ),
		'onderhoud'    => __( 'In beheer', 'jroen' ),
		'archief'      => __( 'Archief / offline', 'jroen' ),
	);
}

/**
 * Domeinnaam zonder protocol en "www." voor in de adresbalk.
 *
 * @param string $url Webadres.
 * @return string
 */
function jroen_website_domain( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	return $host ? preg_replace( '/^www\./', '', $host ) : '';
}

/**
 * Metabox met websitegegevens.
 */
function jroen_website_metabox() {
	add_meta_box( 'jroen_website_info', __( 'Websitegegevens', 'jroen' ), 'jroen_website_metabox_html', 'website', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'jroen_website_metabox' );

/**
 * Inhoud van de metabox.
 *
 * @param WP_Post $post Bericht.
 */
function jroen_website_metabox_html( $post ) {
	wp_nonce_field( 'jroen_website_save', 'jroen_website_nonce' );
	$status = get_post_meta( $post->ID, 'jroen_status', true );
	jroen_logo_field( $post );
	$fields = array(
		'jroen_url'    => array( __( 'Webadres', 'jroen' ), 'url', 'https://' ),
		'jroen_client' => array( __( 'Opdrachtgever', 'jroen' ), 'text', __( 'bijv. eigen project', 'jroen' ) ),
		'jroen_role'   => array( __( 'Rol', 'jroen' ), 'text', __( 'bijv. ontwerp en ontwikkeling', 'jroen' ) ),
		'jroen_year'   => array( __( 'Jaar', 'jroen' ), 'text', gmdate( 'Y' ) ),
		'jroen_github' => array( __( 'GitHub-link (optioneel)', 'jroen' ), 'url', 'https://github.com/...' ),
	);
	?>
	<p>
		<label for="jroen_status"><strong><?php esc_html_e( 'Status', 'jroen' ); ?></strong></label><br>
		<select id="jroen_status" name="jroen_status" style="width:100%">
			<?php foreach ( jroen_website_statuses() as $value => $label ) : ?>
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
	<p class="description"><?php esc_html_e( 'Kies rechts ook het websitetype en de technieken. Stel een schermafbeelding in als uitgelichte afbeelding en het logo hierboven.', 'jroen' ); ?></p>
	<?php
}

/**
 * Websitegegevens opslaan.
 *
 * @param int $post_id Bericht-ID.
 */
function jroen_website_save( $post_id ) {
	if ( ! isset( $_POST['jroen_website_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jroen_website_nonce'] ) ), 'jroen_website_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'jroen_url'    => 'esc_url_raw',
		'jroen_client' => 'sanitize_text_field',
		'jroen_role'   => 'sanitize_text_field',
		'jroen_year'   => 'sanitize_text_field',
		'jroen_github' => 'esc_url_raw',
	);
	foreach ( $fields as $key => $sanitize ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, call_user_func( $sanitize, wp_unslash( $_POST[ $key ] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- opgeschoond via $sanitize.
		}
	}

	jroen_save_logo_field( $post_id );

	if ( isset( $_POST['jroen_status'] ) ) {
		$status = sanitize_key( wp_unslash( $_POST['jroen_status'] ) );
		if ( array_key_exists( $status, jroen_website_statuses() ) ) {
			update_post_meta( $post_id, 'jroen_status', $status );
		}
	}
}
add_action( 'save_post_website', 'jroen_website_save' );

/**
 * Filterknoppen per websitetype.
 *
 * @param string $active Actieve websitetype-slug.
 */
function jroen_website_filters( $active = '' ) {
	jroen_term_filters( 'websitetype', get_post_type_archive_link( 'website' ), $active );
}
