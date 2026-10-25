<?php
/**
 * Portfolio: berichttype "Projecten" met platforms, projecttypes, technieken en projectgegevens.
 *
 * @package KoomenIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Berichttype en indelingen registreren.
 */
function koomenit_register_projects() {
	register_post_type(
		'project',
		array(
			'labels'        => array(
				'name'               => __( 'Projecten', 'koomenit' ),
				'singular_name'      => __( 'Project', 'koomenit' ),
				'add_new'            => __( 'Nieuw project', 'koomenit' ),
				'add_new_item'       => __( 'Nieuw project toevoegen', 'koomenit' ),
				'edit_item'          => __( 'Project bewerken', 'koomenit' ),
				'view_item'          => __( 'Project bekijken', 'koomenit' ),
				'all_items'          => __( 'Alle projecten', 'koomenit' ),
				'search_items'       => __( 'Projecten zoeken', 'koomenit' ),
				'not_found'          => __( 'Geen projecten gevonden', 'koomenit' ),
				'featured_image'     => __( 'Projectafbeelding', 'koomenit' ),
				'set_featured_image' => __( 'Projectafbeelding instellen', 'koomenit' ),
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
			'name'         => __( 'Platforms', 'koomenit' ),
			'singular'     => __( 'Platform', 'koomenit' ),
			'slug'         => 'platform',
			'hierarchical' => true,
		),
		'projecttype' => array(
			'name'         => __( 'Projecttypes', 'koomenit' ),
			'singular'     => __( 'Projecttype', 'koomenit' ),
			'slug'         => 'type',
			'hierarchical' => true,
		),
		'techniek'    => array(
			'name'         => __( 'Technieken', 'koomenit' ),
			'singular'     => __( 'Techniek', 'koomenit' ),
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
					'add_new_item'  => sprintf( __( 'Nieuw(e) %s toevoegen', 'koomenit' ), strtolower( $t['singular'] ) ),
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
		'koomenit_github'   => 'esc_url_raw',
		'koomenit_demo'     => 'esc_url_raw',
		'koomenit_status'   => 'sanitize_key',
		'koomenit_year'     => 'sanitize_text_field',
		'koomenit_hardware' => 'sanitize_textarea_field',
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
		'koomenit_logo',
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
add_action( 'init', 'koomenit_register_projects' );

/**
 * Standaardtermen voor projecten en websites aanmaken (bestaande blijven staan).
 */
function koomenit_create_default_terms() {
	$terms = array(
		'platform'    => array( 'ESP32', 'Arduino', 'Raspberry Pi' ),
		'projecttype' => array( __( 'Website / webinterface', 'koomenit' ), __( 'Sensorproject', 'koomenit' ), __( 'Programma / software', 'koomenit' ) ),
		'websitetype' => array( __( 'Bedrijfswebsite', 'koomenit' ), __( 'Webshop', 'koomenit' ), __( 'Portfolio', 'koomenit' ), __( 'Webapplicatie', 'koomenit' ), __( 'Webinterface voor hardware', 'koomenit' ) ),
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
function koomenit_maybe_upgrade() {
	if ( get_option( 'koomenit_version' ) === KOOMENIT_VERSION ) {
		return;
	}
	koomenit_create_default_terms();
	flush_rewrite_rules();
	update_option( 'koomenit_version', KOOMENIT_VERSION );
}
add_action( 'init', 'koomenit_maybe_upgrade', 99 );

/**
 * Bij (opnieuw) activeren altijd opnieuw controleren.
 */
function koomenit_activate_projects() {
	delete_option( 'koomenit_version' );
}
add_action( 'after_switch_theme', 'koomenit_activate_projects' );

/**
 * Overzichten van projecten en websites tonen alles op één pagina, zodat het filteren werkt.
 *
 * @param WP_Query $query Query.
 */
function koomenit_project_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( array( 'project', 'website' ) ) || $query->is_tax( array( 'platform', 'projecttype', 'techniek', 'websitetype' ) ) ) {
		$query->set( 'posts_per_page', 60 );
	}
}
add_action( 'pre_get_posts', 'koomenit_project_archive_query' );

/**
 * Mogelijke projectstatussen.
 *
 * @return array
 */
function koomenit_project_statuses() {
	return array(
		''            => __( '— geen —', 'koomenit' ),
		'afgerond'    => __( 'Afgerond', 'koomenit' ),
		'in-gebruik'  => __( 'In gebruik', 'koomenit' ),
		'ontwikkeling' => __( 'In ontwikkeling', 'koomenit' ),
		'prototype'   => __( 'Prototype', 'koomenit' ),
	);
}

/**
 * Metabox met projectgegevens.
 */
function koomenit_project_metabox() {
	add_meta_box( 'koomenit_project_info', __( 'Projectgegevens', 'koomenit' ), 'koomenit_project_metabox_html', 'project', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'koomenit_project_metabox' );

/**
 * Inhoud van de metabox.
 *
 * @param WP_Post $post Bericht.
 */
function koomenit_project_metabox_html( $post ) {
	wp_nonce_field( 'koomenit_project_save', 'koomenit_project_nonce' );
	$status = get_post_meta( $post->ID, 'koomenit_status', true );
	koomenit_logo_field( $post );
	?>
	<p>
		<label for="koomenit_status"><strong><?php esc_html_e( 'Status', 'koomenit' ); ?></strong></label><br>
		<select id="koomenit_status" name="koomenit_status" style="width:100%">
			<?php foreach ( koomenit_project_statuses() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="koomenit_year"><strong><?php esc_html_e( 'Jaar', 'koomenit' ); ?></strong></label><br>
		<input type="text" id="koomenit_year" name="koomenit_year" value="<?php echo esc_attr( get_post_meta( $post->ID, 'koomenit_year', true ) ); ?>" placeholder="<?php echo esc_attr( gmdate( 'Y' ) ); ?>" style="width:100%">
	</p>
	<p>
		<label for="koomenit_github"><strong><?php esc_html_e( 'GitHub-link', 'koomenit' ); ?></strong></label><br>
		<input type="url" id="koomenit_github" name="koomenit_github" value="<?php echo esc_attr( get_post_meta( $post->ID, 'koomenit_github', true ) ); ?>" placeholder="https://github.com/..." style="width:100%">
	</p>
	<p>
		<label for="koomenit_demo"><strong><?php esc_html_e( 'Demo / website', 'koomenit' ); ?></strong></label><br>
		<input type="url" id="koomenit_demo" name="koomenit_demo" value="<?php echo esc_attr( get_post_meta( $post->ID, 'koomenit_demo', true ) ); ?>" placeholder="https://" style="width:100%">
	</p>
	<p>
		<label for="koomenit_hardware"><strong><?php esc_html_e( 'Hardware (één onderdeel per regel)', 'koomenit' ); ?></strong></label><br>
		<textarea id="koomenit_hardware" name="koomenit_hardware" rows="5" style="width:100%"><?php echo esc_textarea( get_post_meta( $post->ID, 'koomenit_hardware', true ) ); ?></textarea>
	</p>
	<p class="description"><?php esc_html_e( 'Kies rechts ook het platform, het projecttype en de technieken. De samenvatting komt uit het veld "Samenvatting".', 'koomenit' ); ?></p>
	<?php
}

/**
 * Projectgegevens opslaan.
 *
 * @param int $post_id Bericht-ID.
 */
function koomenit_project_save( $post_id ) {
	if ( ! isset( $_POST['koomenit_project_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['koomenit_project_nonce'] ) ), 'koomenit_project_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'koomenit_github'   => 'esc_url_raw',
		'koomenit_demo'     => 'esc_url_raw',
		'koomenit_year'     => 'sanitize_text_field',
		'koomenit_hardware' => 'sanitize_textarea_field',
	);
	foreach ( $fields as $key => $sanitize ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, call_user_func( $sanitize, wp_unslash( $_POST[ $key ] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- opgeschoond via $sanitize.
		}
	}

	koomenit_save_logo_field( $post_id );

	if ( isset( $_POST['koomenit_status'] ) ) {
		$status = sanitize_key( wp_unslash( $_POST['koomenit_status'] ) );
		if ( array_key_exists( $status, koomenit_project_statuses() ) ) {
			update_post_meta( $post_id, 'koomenit_status', $status );
		}
	}
}
add_action( 'save_post_project', 'koomenit_project_save' );

/**
 * Termen van een project in een indeling.
 *
 * @param string   $taxonomy Indeling.
 * @param int|null $post_id  Bericht-ID.
 * @return WP_Term[]
 */
function koomenit_project_terms( $taxonomy, $post_id = null ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), $taxonomy );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();
}

/**
 * Bijpassend icoon voor een platform.
 *
 * @param string $name Platformnaam.
 * @return string
 */
function koomenit_platform_icon( $name ) {
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
function koomenit_term_icon( $term ) {
	if ( 'websitetype' !== $term->taxonomy ) {
		return koomenit_platform_icon( $term->name );
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
function koomenit_term_slugs( $taxonomy ) {
	return implode( ' ', wp_list_pluck( koomenit_project_terms( $taxonomy ), 'slug' ) );
}

/**
 * Platform-slugs van een project als spatiegescheiden lijst (voor de filter).
 *
 * @return string
 */
function koomenit_project_filter_slugs() {
	return koomenit_term_slugs( 'platform' );
}

/**
 * Filterknoppen voor een indeling.
 *
 * @param string $taxonomy Indeling (platform of websitetype).
 * @param string $all_url  Link van de knop "Alles".
 * @param string $active   Actieve term-slug.
 */
function koomenit_term_filters( $taxonomy, $all_url, $active = '' ) {
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
	$label = sprintf( __( 'Filter op %s', 'koomenit' ), $tax ? strtolower( $tax->labels->singular_name ) : $taxonomy );
	?>
	<div class="filters" role="group" aria-label="<?php echo esc_attr( $label ); ?>">
		<a class="filter<?php echo $active ? '' : ' is-active'; ?>" href="<?php echo esc_url( $all_url ); ?>" data-filter="*" aria-pressed="<?php echo $active ? 'false' : 'true'; ?>"><?php esc_html_e( 'Alles', 'koomenit' ); ?></a>
		<?php foreach ( $terms as $term ) : ?>
			<a class="filter<?php echo $active === $term->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>" data-filter="<?php echo esc_attr( $term->slug ); ?>" aria-pressed="<?php echo $active === $term->slug ? 'true' : 'false'; ?>">
				<?php echo koomenit_icon( koomenit_term_icon( $term ) ); // phpcs:ignore ?>
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
function koomenit_project_filters( $active = '' ) {
	koomenit_term_filters( 'platform', get_post_type_archive_link( 'project' ), $active );
}

/**
 * Logoveld met mediakiezer, voor projecten en websites.
 *
 * @param WP_Post $post Bericht.
 */
function koomenit_logo_field( $post ) {
	$logo = (int) get_post_meta( $post->ID, 'koomenit_logo', true );
	?>
	<div class="koomenit-logo" style="margin:0 0 1em">
		<p style="margin:0 0 .4em"><strong><?php esc_html_e( 'Logo', 'koomenit' ); ?></strong></p>
		<div class="koomenit-logo__preview" style="max-width:120px;margin-bottom:.5em"><?php echo $logo ? wp_get_attachment_image( $logo, 'thumbnail', false, array( 'style' => 'max-width:100%;height:auto' ) ) : ''; ?></div>
		<input type="hidden" name="koomenit_logo" value="<?php echo esc_attr( $logo ? $logo : '' ); ?>">
		<button type="button" class="button koomenit-logo-select"><?php echo $logo ? esc_html__( 'Logo wijzigen', 'koomenit' ) : esc_html__( 'Logo kiezen', 'koomenit' ); ?></button>
		<button type="button" class="button-link koomenit-logo-remove" style="margin-left:.5em;color:#b32d2e<?php echo $logo ? '' : ';display:none'; ?>"><?php esc_html_e( 'Verwijderen', 'koomenit' ); ?></button>
		<p class="description"><?php esc_html_e( 'Verschijnt klein vóór de titel. Zonder uitgelichte afbeelding staat het logo ook groot op de kaart.', 'koomenit' ); ?></p>
		<?php $mode = get_post_meta( $post->ID, 'koomenit_image_mode', true ); ?>
		<p style="margin:1em 0 .3em"><label for="koomenit_image_mode"><strong><?php esc_html_e( 'Weergave uitgelichte afbeelding', 'koomenit' ); ?></strong></label></p>
		<select id="koomenit_image_mode" name="koomenit_image_mode" style="width:100%">
			<option value="" <?php selected( $mode, '' ); ?>><?php esc_html_e( 'Automatisch', 'koomenit' ); ?></option>
			<option value="logo" <?php selected( $mode, 'logo' ); ?>><?php esc_html_e( 'Logo: passend, niet bijsnijden', 'koomenit' ); ?></option>
			<option value="screenshot" <?php selected( $mode, 'screenshot' ); ?>><?php esc_html_e( 'Schermafbeelding: vlak vullen', 'koomenit' ); ?></option>
		</select>
	</div>
	<?php
}

/**
 * Logoveld opslaan (de nonce is al gecontroleerd door de aanroeper).
 *
 * @param int $post_id Bericht-ID.
 */
function koomenit_save_logo_field( $post_id ) {
	if ( isset( $_POST['koomenit_image_mode'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$mode = sanitize_key( wp_unslash( $_POST['koomenit_image_mode'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( in_array( $mode, array( 'logo', 'screenshot' ), true ) ) {
			update_post_meta( $post_id, 'koomenit_image_mode', $mode );
		} else {
			delete_post_meta( $post_id, 'koomenit_image_mode' );
		}
	}
	if ( ! isset( $_POST['koomenit_logo'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	$logo = absint( wp_unslash( $_POST['koomenit_logo'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( $logo && wp_attachment_is_image( $logo ) ) {
		update_post_meta( $post_id, 'koomenit_logo', $logo );
	} else {
		delete_post_meta( $post_id, 'koomenit_logo' );
	}
}

/**
 * Mediakiezer laden op het bewerkscherm van projecten en websites.
 *
 * @param string $hook Huidige beheerpagina.
 */
function koomenit_admin_logo_script( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, array( 'project', 'website' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'koomenit-admin-logo', get_template_directory_uri() . '/assets/js/admin-logo.js', array( 'jquery' ), KOOMENIT_VERSION, true );
	wp_localize_script(
		'koomenit-admin-logo',
		'koomenitLogo',
		array(
			'title'  => __( 'Logo kiezen', 'koomenit' ),
			'button' => __( 'Gebruik als logo', 'koomenit' ),
			'change' => __( 'Logo wijzigen', 'koomenit' ),
			'choose' => __( 'Logo kiezen', 'koomenit' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'koomenit_admin_logo_script' );
