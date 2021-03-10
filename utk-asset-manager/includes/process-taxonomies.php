<?php
/**
 * Process form data to prepare
 * it for registration.
 *
 * @since 0.5.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Given an array of $_POST data, we first
 * check the validity of the request, then
 * route the data to the appropriate function
 * for processing based on the action the user
 * performed.
 *
 * @since 0.5.0
 */
function am_process_metadata_form_data() {

	/**
	 * Make sure we're in the right place and
	 * nothing naughty is happening.
	 *
	 * @since 0.0.1
	 */
	if ( wp_doing_ajax() ) {
		return;
	}

	if ( ! is_admin() ) {
		return;
	}

	if ( ! empty( $_GET ) && isset( $_GET['page'] ) && 'manage_metadata' !== $_GET['page'] ) {
		return;
	}

	/**
	 * If there's no $_POST data, there's nothing to do.
	 * We have to have the post_type value to properly
	 * route the $_POST data to the appropriate function.
	 *
	 * @param array $_POST form data
	 *
	 * @since 0.5.0
	 *
	 */
	if ( ! empty( $_POST ) && isset( $_POST['data_type'] ) ) {
		$_POST  = stripslashes_deep( $_POST );
		$result = '';
		$notice = new Asset_Manager\Admin_Notice();

		check_admin_referer( 'am_metadata_form_nonce_action', 'am_metadata_form_nonce_field' );

		if ( isset( $_POST['am_submit'] ) ) {
			$result = am_update_taxonomies( $_POST );
		}

		if ( isset( $_POST['am_delete'] ) ) {
			$result = am_delete_taxonomy( $_POST );
		}

		if ( $result ) {
			$notice->the_notice(
				$result['type'],
				sprintf(
					__( $result['message'], AM_TEXT ),
					ucwords( $result['type'] ),
					$result['label'],
					$result['outcome']
				)
			);
		}

		wp_safe_redirect(
			add_query_arg(
				[ 'page' => 'manage_metadata' ],
				admin_url( 'admin.php' )
			)
		);
	}
}
add_action( 'init', 'am_process_metadata_form_data', 8 );

function am_delete_taxonomy( $data = [] ) {
	$taxonomies = am_get_registered_taxes();
	$terms      = get_terms( [
		'taxonomy'   => $data['name'],
		'hide_empty' => false,
		'fields'     => 'ids',
	] );

	if ( is_string( $data ) && taxonomy_exists( $data ) ) {
		$data = [
			'name' => $data
		];
	}

	if ( array_key_exists( $data['name'], $taxonomies ) ) {
		unset( $taxonomies[ $data['name'] ] );

		if ( false == ( $success = apply_filters( 'am_delete_taxonomy', false, $taxonomies, $data ) ) ) {
			$success = update_option( 'am_taxonomies', $taxonomies );
		}

		/**
		 * On taxonomy deletion, we also
		 * delete the terms assigned to that taxonomy.
		 *
		 * @since 0.5.1
		 */
		if ( ! empty( $terms ) && is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				wp_delete_term( $term, $data['name'] );
			}
		}

	}

	delete_option( "default_term_{$data['name']}" );

	// Help flush the rewrite rules on init
	set_transient( 'am_flush_rewrite_rules', true, 5 * 60 );

	/**
	 * On completion, we return an array of values
	 * we can use to generate the admin notification.
	 *
	 * @since 1.0.0
	 */
	if ( isset( $success ) ) {
		$message = '<strong>%s</strong><br><br><strong>%s</strong> has been %s.';
	} else {
		$message = '<strong>%s</strong><br><br><strong>%s</strong> could not be %s.';
	}

	return [
		'type'    => ( isset( $success ) ) ? 'success' : 'error',
		'label'   => ( isset( $data['label'] ) ) ? $data['label'] : $data['name'],
		'message' => $message,
		'outcome' => 'deleted'
	];
}

/**
 * Given an array of form data from $_POST,
 * either create a new post type or update
 * an existing one, and update the am_post_types
 * global option with the new data
 *
 * @param array $data form data
 *
 * @since 0.5.0
 *
 */
function am_update_taxonomies( $data = [] ) {

	/**
	 * Get all our registered post types to
	 * make it easy to add the new one we're
	 * about to create.
	 *
	 * @since 0.5.0
	 * @returns array $post_types
	 */
	$taxonomies = am_get_registered_taxes();

	/**
	 * Check the slug of the post type the user is
	 * attempting to register against existing slugs.
	 *
	 * If the slug does exist, stop our process and
	 * throw an error notification to the user.
	 *
	 * @since 0.5.1
	 */
	if ( ! empty( $data['name'] ) && am_slug_exists( $data['name'] ) ) {
		/**
		 * On completion, we return an array of values
		 * we can use to generate the admin notification.
		 *
		 * @since 1.0.0
		 */
		return [
			'type'    => 'error',
			'label'   => $data['name'],
			'message' => '<strong>%s</strong><br><br>Please choose a different post type name. <strong>%s</strong> already exists.',
			'outcome' => ( isset( $data['original'] ) ) ? 'updated' : 'added',
		];
	}

	/**
	 * If the user is updating the taxonomy name and has
	 * chosen to update the terms assigned to it, we
	 * take a side trip to am_convert_taxonomy_terms.
	 *
	 * @since 0.5.0
	 */
	if ( ! empty( $data['original'] ) && $data['original'] !== $data['name'] ) {
		if ( ! empty( $data['update_terms'] ) ) {
			add_filter( 'am_convert_taxonomy_terms', '__return_true' );
		}
	}

	/**
	 * Sanitize strings and create fallback values for arrays expected
	 * in the $_POST data
	 *
	 * @since 0.0.1
	 */
	$name           = trim( sanitize_key( $data['name'] ) );
	$label          = trim( sanitize_text_field( str_replace( [ '\'', '"' ], '', $data['label'] ) ) );
	$singular_label = trim( sanitize_text_field( str_replace( [ '\'', '"' ], '', $data['singular_label'] ) ) );

	if ( ! empty( $data['post_types'] ) && is_array( $data['post_types'] ) ) {
		$post_types = $data['post_types'];
	} else {
		$post_types = [];
	}

	/**
	 * Create an array of values from our $_POST data to pass to the
	 * registration functions (keyed with the value from the 'name'
	 * field from $_POST data) in our $post_types array.
	 *
	 * sample $_POST array:
	 * [singular_label] => Term
	 * [label] => Terms
	 * [name] => term
	 * [public] => 1
	 * [show_in_menu] => 1
	 * [show_tagcloud] => 1
	 * [show_in_quick_edit] => 1
	 * [show_admin_column] => 1
	 * [hierarchical] => 1
	 * [description] => This is a description
	 * [post_types] => Array
	 * (
	 *     [0] => asset
	 *     [1] => test
	 * )
	 *
	 * @since 0.0.1
	 */
	$taxonomies[ $name ] = [
		'name'               => $name,
		'label'              => $label,
		'singular_label'     => $singular_label,
		'description'        => trim( sanitize_textarea_field( $data['description'] ) ),
		'public'             => ( ! empty( $data['public'] ) ) ? am_coerce_bool( $data['public'] ) : true,
		'show_in_admin_bar'  => ( ! empty( $data['show_in_admin_bar'] ) ) ? am_coerce_bool( $data['show_in_admin_bar'] ) : true,
		'show_in_menu'       => ( ! empty( $data['show_in_menu'] ) ) ? am_coerce_bool( $data['show_in_menu'] ) : false,
		'show_in_nav_menus'  => ( ! empty( $data['show_in_nav_menus'] ) ) ? am_coerce_bool( $data['show_in_nav_menus'] ) : true,
		'show_tagcloud'      => ( ! empty( $data['show_tagcloud'] ) ) ? am_coerce_bool( $data['show_tagcloud'] ) : true,
		'show_in_quick_edit' => ( ! empty( $data['show_in_quick_edit'] ) ) ? am_coerce_bool( $data['show_in_quick_edit'] ) : true,
		'show_admin_column'  => ( ! empty( $data['show_admin_column'] ) ) ? am_coerce_bool( $data['show_admin_column'] ) : false,
		'hierarchical'       => ( ! empty( $data['hierarchical'] ) ) ? am_coerce_bool( $data['hierarchical'] ) : false,
		'object_types'       => $post_types,
	];

	/**
	 * Filter the array data above before we save it
	 *
	 * @since 0.5.0
	 */
	$taxonomies = apply_filters( 'am_pre_save_taxonomy', $taxonomies, $name );

	/**
	 * If we've made it this far, it's safe to update
	 * our am_post_types option with the new data.
	 *
	 * @since 0.5.0
	 */
	if ( false === ( $success = apply_filters( 'am_taxonomy_update_save', false, $taxonomies, $data ) ) ) {
		$success = update_option( 'am_taxonomies', $taxonomies );
	}

	/**
	 * Set our transient so the new rewrite rules
	 * will take effect on init
	 *
	 * @since 0.5.0
	 */
	set_transient( 'am_flush_rewrite_rules', 'true', 5 * 60 );

	/**
	 * On completion, we return an array of values
	 * we can use to generate the admin notification.
	 *
	 * @since 1.0.0
	 */
	if ( isset( $success ) ) {
		$message = '<strong>%s</strong><br><br>Post type <strong>%s</strong> has been %s.';
	} else {
		$message = '<strong>%s</strong><br><br>Post type <strong>%s</strong> could not be %s.';
	}

	return [
		'type'    => ( isset( $success ) ) ? 'success' : 'error',
		'label'   => $label,
		'message' => $message,
		'outcome' => ( isset( $data['original'] ) ) ? 'updated' : 'added',
	];

}

/**
 * Move terms existing in $original taxonomy
 * to $new taxonomy, Then delete $original taxonomy.
 *
 * @param string $original original taxonomy name
 * @param string $new      new taxonomy name
 *
 * @since 0.4.0
 *
 */
function am_convert_taxonomy_terms( $original = '', $new = '' ) {
	global $wpdb;
	$terms = get_terms( [
		'taxonomy'   => $original,
		'hide_empty' => false,
		'fields'     => 'ids',
	] );

	if ( is_int( $terms ) ) {
		$terms = (array) $terms;
	}

	// TODO clean up the SQL statement
	// See https://wordpress.stackexchange.com/a/243693
	if ( is_array( $terms ) && ! empty( $terms ) ) {
		$terms = implode( ',', $terms );

		$q = "update `{$wpdb->term_taxonomy}` set `taxonomy` = %s where `taxonomy` = %s and `term_id` in ({$terms})";

		$wpdb->query(
			$wpdb->prepare( $q, $new, $original )
		);
	}

	am_delete_taxonomy( $original );

}

/**
 * When updating a taxonomy type and/or moving terms
 * from one taxonomy to another, we have to wait to
 * to fire until the new term exists and has been
 * properly registered.
 *
 * @since 0.4.0
 */
function am_do_convert_taxonomy_terms() {
	if ( apply_filters( 'am_convert_taxonomy_terms', false ) ) {
		check_admin_referer( 'am_metadata_form_nonce_action', 'am_metadata_form_nonce_field' );

		am_convert_taxonomy_terms( sanitize_text_field( $_POST['original'] ), sanitize_text_field( $_POST['name'] ) );
	}
}
add_action( 'init', 'am_do_convert_taxonomy_terms' );