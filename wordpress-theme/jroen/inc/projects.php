<?php
/**
 * Portfolio: berichttype "Projecten" met platforms, projecttypes, technieken en projectgegevens.
 *
 * @package Jroen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Berichttype en indelingen registreren.
 */
function jroen_register_projects() {
	register_post_type(
		'project',
		array(
			'labels'        => array(
				'name'               => __( 'Projecten', 'jroen' ),
				'singular_name'      => __( 'Project', 'jroen' ),
				'add_new'            => __( 'Nieuw project', 'jroen' ),
				'add_new_item'       => __( 'Nieuw project toevoegen', 'jroen' ),
				'edit_item'          => __( 'Project bewerken', 'jroen' ),
				'view_item'          => __( 'Project bekijken', 'jroen' ),
				'all_items'          => __( 'Alle projecten', 'jroen' ),
				'search_items'       => __( 'Projecten zoeken', 'jroen' ),
				'not_found'          => __( 'Geen projecten gevonden', 'jroen' ),
				'featured_image'     => __( 'Projectafbeelding', 'jroen' ),
				'set_featured_image' => __( 'Projectafbeelding instellen', 'jroen' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'projecten' ),
			'menu_icon'     => 'dashicons-portfolio',
			'menu_position' => 5,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
		)
	);

	$taxonomies = array(
		'platform'    => array(
			'name'         => __( 'Platforms', 'jroen' ),
			'singular'     => __( 'Platform', 'jroen' ),
			'slug'         => 'platform',
			'hierarchical' => true,
		),
		'projecttype' => array(
			'name'         => __( 'Projecttypes', 'jroen' ),
			'singular'     => __( 'Projecttype', 'jroen' ),
			'slug'         => 'type',
			'hierarchical' => true,
		),
		'techniek'    => array(
			'name'         => __( 'Technieken', 'jroen' ),
			'singular'     => __( 'Techniek', 'jroen' ),
			'slug'         => 'techniek',
			'hierarchical' => false,
		),
	);

	foreach ( $taxonomies as $taxonomy => $t ) {
		register_taxonomy(
			$taxonomy,
			'techniek' === $taxonomy ? array( 'project', 'website' ) : 'project',
			array(
				'labels'            => array(
					'name'          => $t['name'],
					'singular_name' => $t['singular'],
					/* translators: %s: naam van de indeling */
					'add_new_item'  => sprintf( __( 'Nieuw(e) %s toevoegen', 'jroen' ), strtolower( $t['singular'] ) ),
					'all_items'     => $t['name'],
				),
				'hierarchical'      => $t['hierarchical'],
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => $t['slug'] ),
			)
		);
	}

	$meta = array(
		'jroen_github'   => 'esc_url_raw',
		'jroen_demo'     => 'esc_url_raw',
		'jroen_status'   => 'sanitize_key',
		'jroen_year'     => 'sanitize_text_field',
		'jroen_hardware' => 'sanitize_textarea_field',
	);
	foreach ( $meta as $key => $sanitize ) {
		register_post_meta(
			'project',
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
		'project',
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
add_action( 'init', 'jroen_register_projects' );

/**
 * Standaardtermen voor projecten en websites aanmaken (bestaande blijven staan).
 */
function jroen_create_default_terms() {
	$terms = array(
		'platform'    => array( 'ESP32', 'Arduino', 'Raspberry Pi' ),
		'projecttype' => array( __( 'Website / webinterface', 'jroen' ), __( 'Sensorproject', 'jroen' ), __( 'Programma / software', 'jroen' ) ),
		'websitetype' => array( __( 'Bedrijfswebsite', 'jroen' ), __( 'Webshop', 'jroen' ), __( 'Portfolio', 'jroen' ), __( 'Webapplicatie', 'jroen' ), __( 'Webinterface voor hardware', 'jroen' ) ),
	);
	foreach ( $terms as $taxonomy => $names ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}
		foreach ( $names as $name ) {
			if ( ! term_exists( $name, $taxonomy ) ) {
				wp_insert_term( $name, $taxonomy );
			}
		}
	}
}

/**
 * Bij activeren én na een update van het thema: standaardtermen aanmaken en
 * permalinks verversen, zodat nieuwe overzichten (zoals /websites/) direct werken.
 */
function jroen_maybe_upgrade() {
	if ( get_option( 'jroen_version' ) === JROEN_VERSION ) {
		return;
	}
	jroen_create_default_terms();
	flush_rewrite_rules();
	update_option( 'jroen_version', JROEN_VERSION );
}
add_action( 'init', 'jroen_maybe_upgrade', 99 );

/**
 * Bij (opnieuw) activeren altijd opnieuw controleren.
 */
function jroen_activate_projects() {
	delete_option( 'jroen_version' );
}
add_action( 'after_switch_theme', 'jroen_activate_projects' );

/**
 * Overzichten van projecten en websites tonen alles op één pagina, zodat het filteren werkt.
 *
 * @param WP_Query $query Query.
 */
function jroen_project_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( array( 'project', 'website' ) ) || $query->is_tax( array( 'platform', 'projecttype', 'techniek', 'websitetype' ) ) ) {
		$query->set( 'posts_per_page', 60 );
	}
}
add_action( 'pre_get_posts', 'jroen_project_archive_query' );

/**
 * Mogelijke projectstatussen.
 *
 * @return array
 */
function jroen_project_statuses() {
	return array(
		''            => __( '— geen —', 'jroen' ),
		'afgerond'    => __( 'Afgerond', 'jroen' ),
		'in-gebruik'  => __( 'In gebruik', 'jroen' ),
		'ontwikkeling' => __( 'In ontwikkeling', 'jroen' ),
		'prototype'   => __( 'Prototype', 'jroen' ),
	);
}

/**
 * Metabox met projectgegevens.
 */
function jroen_project_metabox() {
	add_meta_box( 'jroen_project_info', __( 'Projectgegevens', 'jroen' ), 'jroen_project_metabox_html', 'project', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'jroen_project_metabox' );

/**
 * Inhoud van de metabox.
 *
 * @param WP_Post $post Bericht.
 */
function jroen_project_metabox_html( $post ) {
	wp_nonce_field( 'jroen_project_save', 'jroen_project_nonce' );
	$status = get_post_meta( $post->ID, 'jroen_status', true );
	jroen_logo_field( $post );
	?>
	<p>
		<label for="jroen_status"><strong><?php esc_html_e( 'Status', 'jroen' ); ?></strong></label><br>
		<select id="jroen_status" name="jroen_status" style="width:100%">
			<?php foreach ( jroen_project_statuses() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="jroen_year"><strong><?php esc_html_e( 'Jaar', 'jroen' ); ?></strong></label><br>
		<input type="text" id="jroen_year" name="jroen_year" value="<?php echo esc_attr( get_post_meta( $post->ID, 'jroen_year', true ) ); ?>" placeholder="<?php echo esc_attr( gmdate( 'Y' ) ); ?>" style="width:100%">
	</p>
	<p>
		<label for="jroen_github"><strong><?php esc_html_e( 'GitHub-link', 'jroen' ); ?></strong></label><br>
		<input type="url" id="jroen_github" name="jroen_github" value="<?php echo esc_attr( get_post_meta( $post->ID, 'jroen_github', true ) ); ?>" placeholder="https://github.com/..." style="width:100%">
	</p>
	<p>
		<label for="jroen_demo"><strong><?php esc_html_e( 'Demo / website', 'jroen' ); ?></strong></label><br>
		<input type="url" id="jroen_demo" name="jroen_demo" value="<?php echo esc_attr( get_post_meta( $post->ID, 'jroen_demo', true ) ); ?>" placeholder="https://" style="width:100%">
	</p>
	<p>
		<label for="jroen_hardware"><strong><?php esc_html_e( 'Hardware (één onderdeel per regel)', 'jroen' ); ?></strong></label><br>
		<textarea id="jroen_hardware" name="jroen_hardware" rows="5" style="width:100%"><?php echo esc_textarea( get_post_meta( $post->ID, 'jroen_hardware', true ) ); ?></textarea>
	</p>
	<p class="description"><?php esc_html_e( 'Kies rechts ook het platform, het projecttype en de technieken. De samenvatting komt uit het veld "Samenvatting".', 'jroen' ); ?></p>
	<?php
}

/**
 * Projectgegevens opslaan.
 *
 * @param int $post_id Bericht-ID.
 */
function jroen_project_save( $post_id ) {
	if ( ! isset( $_POST['jroen_project_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jroen_project_nonce'] ) ), 'jroen_project_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'jroen_github'   => 'esc_url_raw',
		'jroen_demo'     => 'esc_url_raw',
		'jroen_year'     => 'sanitize_text_field',
		'jroen_hardware' => 'sanitize_textarea_field',
	);
	foreach ( $fields as $key => $sanitize ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, call_user_func( $sanitize, wp_unslash( $_POST[ $key ] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- opgeschoond via $sanitize.
		}
	}

	jroen_save_logo_field( $post_id );

	if ( isset( $_POST['jroen_status'] ) ) {
		$status = sanitize_key( wp_unslash( $_POST['jroen_status'] ) );
		if ( array_key_exists( $status, jroen_project_statuses() ) ) {
			update_post_meta( $post_id, 'jroen_status', $status );
		}
	}
}
add_action( 'save_post_project', 'jroen_project_save' );

/**
 * Termen van een project in een indeling.
 *
 * @param string   $taxonomy Indeling.
 * @param int|null $post_id  Bericht-ID.
 * @return WP_Term[]
 */
function jroen_project_terms( $taxonomy, $post_id = null ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), $taxonomy );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();
}

/**
 * Bijpassend icoon voor een platform.
 *
 * @param string $name Platformnaam.
 * @return string
 */
function jroen_platform_icon( $name ) {
	$name = strtolower( $name );
	if ( false !== strpos( $name, 'esp' ) ) {
		return 'wifi';
	}
	if ( false !== strpos( $name, 'arduino' ) ) {
		return 'chip';
	}
	if ( false !== strpos( $name, 'pi' ) ) {
		return 'cpu';
	}
	return 'code';
}

/**
 * Bijpassend icoon voor een term (platform of websitetype).
 *
 * @param WP_Term $term Term.
 * @return string
 */
function jroen_term_icon( $term ) {
	if ( 'websitetype' !== $term->taxonomy ) {
		return jroen_platform_icon( $term->name );
	}
	$name = strtolower( $term->name );
	if ( false !== strpos( $name, 'shop' ) ) {
		return 'cart';
	}
	if ( false !== strpos( $name, 'hardware' ) || false !== strpos( $name, 'interface' ) ) {
		return 'chip';
	}
	if ( false !== strpos( $name, 'applicatie' ) || false !== strpos( $name, 'app' ) ) {
		return 'code';
	}
	if ( false !== strpos( $name, 'portfolio' ) ) {
		return 'layout';
	}
	return 'globe';
}

/**
 * Term-slugs van het huidige bericht als spatiegescheiden lijst (voor de filter).
 *
 * @param string $taxonomy Indeling.
 * @return string
 */
function jroen_term_slugs( $taxonomy ) {
	return implode( ' ', wp_list_pluck( jroen_project_terms( $taxonomy ), 'slug' ) );
}

/**
 * Platform-slugs van een project als spatiegescheiden lijst (voor de filter).
 *
 * @return string
 */
function jroen_project_filter_slugs() {
	return jroen_term_slugs( 'platform' );
}

/**
 * Filterknoppen voor een indeling.
 *
 * @param string $taxonomy Indeling (platform of websitetype).
 * @param string $all_url  Link van de knop "Alles".
 * @param string $active   Actieve term-slug.
 */
function jroen_term_filters( $taxonomy, $all_url, $active = '' ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
		)
	);
	if ( ! $terms || is_wp_error( $terms ) ) {
		return;
	}
	$tax = get_taxonomy( $taxonomy );
	/* translators: %s: naam van de indeling, bijvoorbeeld platform */
	$label = sprintf( __( 'Filter op %s', 'jroen' ), $tax ? strtolower( $tax->labels->singular_name ) : $taxonomy );
	?>
	<div class="filters" role="group" aria-label="<?php echo esc_attr( $label ); ?>">
		<a class="filter<?php echo $active ? '' : ' is-active'; ?>" href="<?php echo esc_url( $all_url ); ?>" data-filter="*" aria-pressed="<?php echo $active ? 'false' : 'true'; ?>"><?php esc_html_e( 'Alles', 'jroen' ); ?></a>
		<?php foreach ( $terms as $term ) : ?>
			<a class="filter<?php echo $active === $term->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>" data-filter="<?php echo esc_attr( $term->slug ); ?>" aria-pressed="<?php echo $active === $term->slug ? 'true' : 'false'; ?>">
				<?php echo jroen_icon( jroen_term_icon( $term ) ); // phpcs:ignore ?>
				<?php echo esc_html( $term->name ); ?>
				<span class="filter__count"><?php echo esc_html( $term->count ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Filterknoppen per platform.
 *
 * @param string $active Actieve platform-slug.
 */
function jroen_project_filters( $active = '' ) {
	jroen_term_filters( 'platform', get_post_type_archive_link( 'project' ), $active );
}

/**
 * Logoveld met mediakiezer, voor projecten en websites.
 *
 * @param WP_Post $post Bericht.
 */
function jroen_logo_field( $post ) {
	$logo = (int) get_post_meta( $post->ID, 'jroen_logo', true );
	?>
	<div class="jroen-logo" style="margin:0 0 1em">
		<p style="margin:0 0 .4em"><strong><?php esc_html_e( 'Logo', 'jroen' ); ?></strong></p>
		<div class="jroen-logo__preview" style="max-width:120px;margin-bottom:.5em"><?php echo $logo ? wp_get_attachment_image( $logo, 'thumbnail', false, array( 'style' => 'max-width:100%;height:auto' ) ) : ''; ?></div>
		<input type="hidden" name="jroen_logo" value="<?php echo esc_attr( $logo ? $logo : '' ); ?>">
		<button type="button" class="button jroen-logo-select"><?php echo $logo ? esc_html__( 'Logo wijzigen', 'jroen' ) : esc_html__( 'Logo kiezen', 'jroen' ); ?></button>
		<button type="button" class="button-link jroen-logo-remove" style="margin-left:.5em;color:#b32d2e<?php echo $logo ? '' : ';display:none'; ?>"><?php esc_html_e( 'Verwijderen', 'jroen' ); ?></button>
		<p class="description"><?php esc_html_e( 'Verschijnt klein vóór de titel. Zonder uitgelichte afbeelding staat het logo ook groot op de kaart.', 'jroen' ); ?></p>
		<?php $mode = get_post_meta( $post->ID, 'jroen_image_mode', true ); ?>
		<p style="margin:1em 0 .3em"><label for="jroen_image_mode"><strong><?php esc_html_e( 'Weergave uitgelichte afbeelding', 'jroen' ); ?></strong></label></p>
		<select id="jroen_image_mode" name="jroen_image_mode" style="width:100%">
			<option value="" <?php selected( in_array( $mode, array( '', 'logo' ), true ) ); ?>><?php esc_html_e( 'Passend: hele afbeelding tonen (standaard)', 'jroen' ); ?></option>
			<option value="screenshot" <?php selected( $mode, 'screenshot' ); ?>><?php esc_html_e( 'Vullend: vlak vullen, randen worden bijgesneden', 'jroen' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Tip: een logo met transparante achtergrond (PNG of SVG) staat het mooist.', 'jroen' ); ?></p>
	</div>
	<?php
}

/**
 * Logoveld opslaan (de nonce is al gecontroleerd door de aanroeper).
 *
 * @param int $post_id Bericht-ID.
 */
function jroen_save_logo_field( $post_id ) {
	if ( isset( $_POST['jroen_image_mode'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$mode = sanitize_key( wp_unslash( $_POST['jroen_image_mode'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( in_array( $mode, array( 'logo', 'screenshot' ), true ) ) {
			update_post_meta( $post_id, 'jroen_image_mode', $mode );
		} else {
			delete_post_meta( $post_id, 'jroen_image_mode' );
		}
	}
	if ( ! isset( $_POST['jroen_logo'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	$logo = absint( wp_unslash( $_POST['jroen_logo'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( $logo && wp_attachment_is_image( $logo ) ) {
		update_post_meta( $post_id, 'jroen_logo', $logo );
	} else {
		delete_post_meta( $post_id, 'jroen_logo' );
	}
}

/**
 * Mediakiezer laden op het bewerkscherm van projecten en websites.
 *
 * @param string $hook Huidige beheerpagina.
 */
function jroen_admin_logo_script( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, array( 'project', 'website' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'jroen-admin-logo', get_template_directory_uri() . '/assets/js/admin-logo.js', array( 'jquery' ), JROEN_VERSION, true );
	wp_localize_script(
		'jroen-admin-logo',
		'jroenLogo',
		array(
			'title'  => __( 'Logo kiezen', 'jroen' ),
			'button' => __( 'Gebruik als logo', 'jroen' ),
			'change' => __( 'Logo wijzigen', 'jroen' ),
			'choose' => __( 'Logo kiezen', 'jroen' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'jroen_admin_logo_script' );
