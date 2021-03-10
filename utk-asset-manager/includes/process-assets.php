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
function am_process_post_type_form_data() {

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

	if ( ! empty( $_GET ) && isset( $_GET['page'] ) && 'manage_post_types' !== $_GET['page'] ) {
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
		$notice = new Asset_Manager\Admin_Notice();
		$result = '';

		check_admin_referer( 'am_post_type_form_nonce_action', 'am_post_type_form_nonce_field' );

		if ( isset( $_POST['am_submit'] ) ) {
			$result = am_update_post_types( $_POST );
		}

		if ( isset( $_POST['am_delete'] ) ) {
			$result = am_delete_post_type( $_POST );
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

		if ( isset( $_POST['am_delete'] ) && isset( $_GET['edit'] ) ) {
			wp_safe_redirect(
				add_query_arg(
					[ 'page' => 'manage_post_types' ],
					admin_url( 'admin.php' )
				)
			);
		}
	}
}
add_action( 'init', 'am_process_post_type_form_data', 8 );

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
 * @return array
 */
function am_update_post_types( $data = [] ): array {

	/**
	 * Get all our registered post types to
	 * make it easy to add the new one we're
	 * about to create.
	 *
	 * @since 0.5.0
	 * @returns array $post_types
	 */
	$post_types = am_get_registered_post_types();

	/**
	 * Check the slug of the post type the user is
	 * attempting to register against existing slugs.
	 *
	 * If the slug does exist, stop our process and
	 * throw an error notification to the user.
	 *
	 * @since 0.5.1
	 *
	 * @todo  Change am_slug_exists to output where the existing slug is in the system
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
	 * If the user is updating the post name and has
	 * chosen to update the posts assigned to it, we
	 * take a side trip to am_convert_posts.
	 *
	 * @since 0.5.0
	 */
	if ( ! empty( $data['original'] ) && $data['original'] !== $data['name'] ) {
		if ( ! empty( $data['update_posts'] ) ) {
			add_filter( 'am_convert_posts', '__return_true' );
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

	if ( ! empty( $data['supports'] ) && is_array( $data['supports'] ) ) {
		$supports = $data['supports'];
	} else {
		$supports = [
			'title',
			'editor',
			'revisions',
			'author',
			'excerpt',
			'thumbnail',
			'post-formats',
		];
	}

	if ( empty( $data['taxonomies'] ) || ! is_array( $data['taxonomies'] ) ) {
		$taxonomies = [];
	} else {
		$taxonomies = $data['taxonomies'];
	}

	/**
	 * Create an array of values from our $_POST data to pass to the
	 * registration functions (keyed with the value from the 'name'
	 * field from $_POST data) in our $post_types array.
	 *
	 * sample $_POST array:
	 * [data_type] => asset
	 * [page_action] => new
	 * [singular_label] => Cup
	 * [label] => Cups
	 * [name] => cup
	 * [menu_icon] => dashicons-cloud
	 * [public] => 1
	 * [show_in_menu] => 0
	 * [show_in_admin_bar] => 1
	 * [hierarchical] => 0
	 * [description] => Cups are things
	 * [supports] => Array
	 * (
	 *     [0] => title
	 *     [1] => editor
	 *     [2] => revisions
	 *     [3] => author
	 *     [4] => excerpt
	 *     [5] => thumbnail
	 *     [6] => post-formats
	 * )
	 *
	 * [taxonomies] => Array
	 * (
	 *     [0] => color
	 * )
	 *
	 * @since 0.0.1
	 */
	$post_types[ $name ] = [
		'name'              => $name,
		'label'             => $label,
		'singular_label'    => $singular_label,
		'menu_icon'         => trim( $data['menu_icon'] ),
		'description'       => trim( sanitize_textarea_field( $data['description'] ) ),
		'public'            => ( ! empty( $data['public'] ) ) ? am_coerce_bool( $data['public'] ) : true,
		'show_in_admin_bar' => ( ! empty( $data['show_in_admin_bar'] ) ) ? am_coerce_bool( $data['show_in_admin_bar'] ) : true,
		'show_in_menu'      => ( ! empty( $data['show_in_menu'] ) ) ? am_coerce_bool( $data['show_in_menu'] ) : false,
		'show_in_nav_menus' => ( ! empty( $data['show_in_nav_menus'] ) ) ? am_coerce_bool( $data['show_in_nav_menus'] ) : true,
		'hierarchical'      => ( ! empty( $data['hierarchical'] ) ) ? am_coerce_bool( $data['hierarchical'] ) : false,
		'supports'          => $supports,
		'taxonomies'        => $taxonomies,
	];

	/**
	 * Filter the array data above before we save it
	 *
	 * @since 0.5.0
	 */
	$post_types = apply_filters( 'am_pre_save_post_type', $post_types, $name );

	/**
	 * If we've made it this far, it's safe to update
	 * our am_post_types option with the new data.
	 *
	 * @since 0.5.0
	 */
	if ( false === ( $success = apply_filters( 'am_post_type_update_save', false, $post_types, $data ) ) ) {
		$success = update_option( 'am_post_types', $post_types );
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
 * Perform a safe delete operation on
 * the selected custom post type
 *
 * @param array $data
 *
 * @since 0.5.0
 *
 * @return array
 */
function am_delete_post_type( $data = [] ): array {
	$post_types = am_get_registered_post_types();

	if ( is_string( $data ) && post_type_exists( $data ) ) {
		$data = [
			'name' => $data
		];
	}

	if ( array_key_exists( $data['name'], $post_types ) ) {

		unset( $post_types[ $data['name'] ] );

		if ( false === ( $success = apply_filters( 'am_post_type_delete_type', false, $post_types, $data ) ) ) {
			$success = update_option( 'am_post_types', $post_types );
		}
	}

	do_action( 'am_post_delete_post_type', $data );

	set_transient( 'am_flush_rewrite_rules', 'true', 5 * 60 );

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


function am_convert_posts( $original = '', $new = '' ) {
	$args = [
		'post_type'      => $original,
		'posts_per_page' => - 1,
	];

	$q = new WP_Query( $args );

	if ( $q->have_posts() ) {
		while ( $q - have_posts() ) {
			$q->the_post();

			set_post_type( get_the_ID(), $new );
		}
	}

	am_delete_post_type( $original );
}

function am_do_convert_posts() {

	if ( apply_filters( 'am_convert_posts', false ) ) {

		check_admin_referer( 'am_post_type_form_nonce_action', 'am_post_type_form_nonce_field' );

		am_convert_posts( sanitize_text_field( $_POST['original'] ), sanitize_text_field( $_POST['name'] ) );

	}

}
add_action( 'init', 'am_do_convert_posts' );