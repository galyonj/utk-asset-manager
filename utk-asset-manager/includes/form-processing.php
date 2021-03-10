<?php
///**
// * Process form data to prepare
// * it for registration.
// *
// * @since 0.5.0
// */
//
//if ( ! defined( 'WPINC' ) ) {
//	die;
//}
//
///**
// * Given an array of $_POST data, we first
// * check the vaility of the request, then
// * route the data to the appropriate function
// * for processing based on the action the user
// * performed.
// *
// * @since 0.5.0
// */
//function am_process_form_data() {
//
//	/**
//	 * Check the nonce and that we're in the right place
//	 *
//	 * @since 0.5.1
//	 */
//	if ( ! isset( $_POST['am_form_nonce_field'] ) || ! wp_verify_nonce( $_POST['am_form_nonce_field'], 'am_form_nonce_action' ) ) {
//		if ( ! is_admin() ) {
//			return;
//		}
//	}
//
//	/**
//	 * If there's no $_POST data, there's nothing to do.
//	 * We have to have the post_type value to properly
//	 * route the $_POST data to the appropriate function.
//	 *
//	 * @param array $_POST form data
//	 *
//	 * @since 0.5.0
//	 *
//	 */
//	if ( ! empty( $_POST ) && isset( $_POST['data_type'] ) ) {
//
//		if ( 'asset' === $_POST['data_type'] ) {
//			if ( isset( $_POST['am_submit'] ) ) {
//				am_update_post_types( $_POST );
//			}
//		} else {
//			if ( isset( $_POST['am_submit'] ) ) {
//				am_update_taxonomies( $_POST );
//			}
//		}
//	}
//}
////add_action( 'init', 'am_process_form_data', 8 );
//
///**
// * Given an array of form data from $_POST,
// * either create a new post type or update
// * an existing one, and update the am_post_types
// * global option with the new data
// *
// * @param array $data form data
// *
// * @since 0.5.0
// *
// */
//function am_update_post_types( $data = [] ) {
//
//	/**
//	 * Get all our registered post types to
//	 * make it easy to add the new one we're
//	 * about to create.
//	 *
//	 * @since 0.5.0
//	 * @returns array $post_types
//	 */
//	$post_types = am_get_registered_post_types();
//
//	/**
//	 * Check the slug of the post type the user is
//	 * attempting to register against existing slugs.
//	 *
//	 * If the slug does exist, stop our process and
//	 * throw an error notification to the user.
//	 *
//	 * @since 0.5.1
//	 */
//	if ( ! empty( $data['name'] ) && am_slug_exists( $data['name'] ) ) {
//		add_action( 'admin_notices', 'am_error_slug_exists' );
//
//		return;
//	}
//
//	/**
//	 * If the user is updating the post name and has
//	 * chosen to update the posts assigned to it, we
//	 * take a side trip to am_convert_posts.
//	 *
//	 * @since 0.5.0
//	 */
//	if ( ! empty( $data['original'] ) && $data['original'] !== $data['name'] ) {
//		if ( ! empty( $data['update_posts'] ) ) {
//			add_filter( 'am_convert_posts', '__return_true' );
//		}
//	}
//
//	/**
//	 * Sanitize strings and create fallback values for arrays expected
//	 * in the $_POST data
//	 *
//	 * @since 0.0.1
//	 */
//	$name           = trim( sanitize_key( $data['name'] ) );
//	$label          = trim( sanitize_text_field( str_replace( [ '\'', '"' ], '', $data['label'] ) ) );
//	$singular_label = trim( sanitize_text_field( str_replace( [ '\'', '"' ], '', $data['singular_label'] ) ) );
//
//	if ( empty( $data['supports'] ) && ! is_array( $data['supports'] ) ) {
//		$supports = [
//			'title',
//			'editor',
//			'revisions',
//			'author',
//			'excerpt',
//			'thumbnail',
//			'post-formats'
//		];
//	} else {
//		$supports = $data['supports'];
//	}
//
//	if ( empty( $data['taxonomies'] ) && ! is_array( $data['taxonomies'] ) ) {
//		$taxonomies = [];
//	} else {
//		$taxonomies = $data['taxonomies'];
//	}
//
//	/**
//	 * Create an array of values from our $_POST data to pass to the
//	 * registration functions (keyed with the value from the 'name'
//	 * field from $_POST data) in our $post_types array.
//	 *
//	 * sample $_POST array:
//	 * [data_type] => asset
//	 * [page_action] => new
//	 * [singular_label] => Cup
//	 * [label] => Cups
//	 * [name] => cup
//	 * [menu_icon] => dashicons-cloud
//	 * [public] => 1
//	 * [show_in_menu] => 0
//	 * [show_in_admin_bar] => 1
//	 * [hierarchical] => 0
//	 * [description] => Cups are things
//	 * [supports] => Array
//	 * (
//	 *     [0] => title
//	 *     [1] => editor
//	 *     [2] => revisions
//	 *     [3] => author
//	 *     [4] => excerpt
//	 *     [5] => thumbnail
//	 *     [6] => post-formats
//	 * )
//	 *
//	 * [taxonomies] => Array
//	 * (
//	 *     [0] => color
//	 * )
//	 *
//	 * @since 0.0.1
//	 */
//	$post_types[ $name ] = [
//		'name'              => $name,
//		'label'             => $label,
//		'singular_label'    => $singular_label,
//		'menu_icon'         => trim( $data['menu_icon'] ),
//		'description'       => trim( sanitize_textarea_field( $data['description'] ) ),
//		'public'            => ( ! empty( $data['public'] ) ) ? am_coerce_bool( $data['public'] ) : true,
//		'show_in_admin_bar' => ( ! empty( $data['show_in_admin_bar'] ) ) ? am_coerce_bool( $data['show_in_admin_bar'] ) : true,
//		'show_in_menu'      => ( ! empty( $data['show_in_menu'] ) ) ? am_coerce_bool( $data['show_in_menu'] ) : false,
//		'show_in_nav_menus' => ( ! empty( $data['show_in_nav_menus'] ) ) ? am_coerce_bool( $data['show_in_nav_menus'] ) : true,
//		'hierarchical'      => ( ! empty( $data['hierarchical'] ) ) ? am_coerce_bool( $data['hierarchical'] ) : false,
//		'supports'          => $supports,
//		'taxonomies'        => $taxonomies,
//	];
//
//	/**
//	 * Filter the array data above before we save it
//	 *
//	 * @since 0.5.0
//	 */
//	$post_types = apply_filters( 'am_pre_save_post_type', $post_types, $name );
//
//	/**
//	 * If we've made it this far, it's safe to update
//	 * our am_post_types option with the new data.
//	 *
//	 * @since 0.5.0
//	 */
//	update_option( 'am_post_types', $post_types );
//
//	/**
//	 * Set our transient so the new rewrite rules
//	 * will take effect on init
//	 *
//	 * @since 0.5.0
//	 */
//	set_transient( 'am_flush_rewrite_rules', 'true', 5 * 60 );
//
//
//	// TODO make the notifications work
////	if( 'new' === $data['page_action']) {
////		return add_action('')
////	}
//}
//
///**
// * Given an array of form data from $_POST,
// * either create a new taxonomy type or update
// * an existing one, and update the am_taxonomies
// * global option with the new data
// *
// * @param array $data form data
// *
// * @since 0.5.0
// *
// */
//function am_update_taxonomies( $data = [] ) {
//
//	/**
//	 * Get all our registered post types to
//	 * make it easy to add the new one we're
//	 * about to create.
//	 *
//	 * @since 0.5.0
//	 * @returns array $post_types
//	 */
//	$taxonomies = am_get_registered_taxes();
//
//	/**
//	 * Check the slug of the post type the user is
//	 * attempting to register against existing slugs.
//	 *
//	 * If the slug does exist, stop our process and
//	 * throw an error notification to the user.
//	 *
//	 * @since 0.5.1
//	 */
//	if ( ! empty( $data['name'] ) && am_slug_exists( $data['name'] ) ) {
//		add_action( 'admin_notices', 'am_error_slug_exists' );
//
//		return;
//	}
//
//	/**
//	 * If the user is updating the post name and has
//	 * chosen to update the posts assigned to it, we
//	 * take a side trip to am_convert_posts.
//	 *
//	 * @since 0.5.0
//	 */
//	if ( ! empty( $data['original'] ) && $data['original'] !== $data['name'] ) {
//		if ( ! empty( $data['update_terms'] ) ) {
//			add_filter( 'am_convert_terms', '__return_true' );
//		}
//	}
//
//	/**
//	 * Sanitize strings and create fallback values for arrays expected
//	 * in the $_POST data
//	 *
//	 * @since 0.0.1
//	 */
//	$name           = trim( sanitize_key( $data['name'] ) );
//	$label          = trim( sanitize_text_field( str_replace( [ '\'', '"' ], '', $data['label'] ) ) );
//	$singular_label = trim( sanitize_text_field( str_replace( [ '\'', '"' ], '', $data['singular_label'] ) ) );
//
//	if ( ! empty( $data['post_types'] ) && is_array( $data['post_types'] ) ) {
//		$post_types = $data['post_types'];
//	} else {
//		$post_types = [];
//	}
//
//	/**
//	 * Create an array of values from our $_POST data to pass to the
//	 * registration functions (keyed with the value from the 'name'
//	 * field from $_POST data) in our $post_types array.
//	 *
//	 * sample $_POST array:
//	 * [singular_label] => Term
//	 * [label] => Terms
//	 * [name] => term
//	 * [public] => 1
//	 * [show_in_menu] => 1
//	 * [show_tagcloud] => 1
//	 * [show_in_quick_edit] => 1
//	 * [show_admin_column] => 1
//	 * [hierarchical] => 1
//	 * [description] => This is a description
//	 * [post_types] => Array
//	 * (
//	 *     [0] => asset
//	 *     [1] => test
//	 * )
//	 *
//	 * @since 0.0.1
//	 */
//	$post_types[ $name ] = [
//		'name'               => $name,
//		'label'              => $label,
//		'singular_label'     => $singular_label,
//		'description'        => trim( sanitize_textarea_field( $data['description'] ) ),
//		'public'             => ( ! empty( $data['public'] ) ) ? am_coerce_bool( $data['public'] ) : true,
//		'show_in_admin_bar'  => ( ! empty( $data['show_in_admin_bar'] ) ) ? am_coerce_bool( $data['show_in_admin_bar'] ) : true,
//		'show_in_menu'       => ( ! empty( $data['show_in_menu'] ) ) ? am_coerce_bool( $data['show_in_menu'] ) : false,
//		'show_in_nav_menus'  => ( ! empty( $data['show_in_nav_menus'] ) ) ? am_coerce_bool( $data['show_in_nav_menus'] ) : true,
//		'show_tagcloud'      => ( ! empty( $data['show_tagcloud'] ) ) ? am_coerce_bool( $data['show_tagcloud'] ) : true,
//		'show_in_quick_edit' => ( ! empty( $data['show_in_quick_edit'] ) ) ? am_coerce_bool( $data['show_in_quick_edit'] ) : true,
//		'show_admin_column'  => ( ! empty( $data['show_admin_column'] ) ) ? am_coerce_bool( $data['show_admin_column'] ) : false,
//		'hierarchical'       => ( ! empty( $data['hierarchical'] ) ) ? am_coerce_bool( $data['hierarchical'] ) : false,
//		'object_types'       => $post_types,
//	];
//
//	/**
//	 * Filter the array data above before we save it
//	 *
//	 * @since 0.5.0
//	 */
//	$post_types = apply_filters( 'am_pre_save_taxonomy', $taxonomies, $name );
//
//	/**
//	 * If we've made it this far, it's safe to update
//	 * our am_post_types option with the new data.
//	 *
//	 * @since 0.5.0
//	 */
//	update_option( 'am_taxonomies', $taxonomies );
//
//	/**
//	 * Set our transient so the new rewrite rules
//	 * will take effect on init
//	 *
//	 * @since 0.5.0
//	 */
//	set_transient( 'am_flush_rewrite_rules', 'true', 5 * 60 );
//
//
//	// TODO make the notifications work
////	if( 'new' === $data['page_action']) {
////		return add_action('')
////	}
//}
//
///**
// * Convert posts of a given $original
// * post_type to the $new one.
// *
// * @param string $original original post_type name
// * @param string $new      new post_type name
// *
// * @since 0.0.1
// *
// *        TODO: Make both the posts and taxonomies conversions work
// */
//function am_convert_posts( $original = '', $new = '' ) {
//	$posts = get_posts( [
//		'numberposts' => - 1,
//		'post_type'   => $original,
//	] );
//
//	foreach ( $posts as $post ) {
//		set_post_type( $post->ID, $new );
//	}
//}
//
//function am_convert_taxonomy_terms( $post_types = [], $original = '', $new = '' ) {
//	$posts = get_posts( [
//		'post_type'      => $post_types,
//		'posts_per_page' => - 1,
//		'tax_query'      => [
//			[
//				'taxonomy' => $original,
//				'operator' => 'EXISTS'
//			]
//		]
//	] );
//	$terms = get_terms( [
//		'taxonomy'   => $original,
//		'hide_empty' => false,
//		'fields'     => 'ids',
//	] );
//
//
//}
