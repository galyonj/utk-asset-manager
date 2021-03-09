<?php
/**
 * Functions to register assets and taxonomies
 * that are created by our plugin
 *
 * @since      0.0.5
 * @package    UTK_Asset_Manager
 * @subpackage includes/register-created-assets
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

function am_prepare_cpt_array() {
	$cpts = get_option( 'am_cpt' );

	if ( empty( $cpts ) ) {
		return;
	}

	do_action( 'am_pre_register_cpt', $cpts );

	if ( is_array( $cpts ) ) {
		foreach ( $cpts as $cpt ) {
			/**
			 * Filters whether or not to skip registration of the current iterated post type.
			 *
			 * Dynamic part of the filter name is the chosen post type slug.
			 *
			 * @since 1.7.0
			 *
			 * @param bool  $value Whether or not to skip the post type.
			 * @param array $cpt   Current post type being registered.
			 */
			if ( (bool) apply_filters( "cptui_disable_{$cpt['name']}_cpt", false, $cpt ) ) {
				continue;
			}

			/**
			 * Filters whether or not to skip registration of the current iterated post type.
			 *
			 * @since 1.7.0
			 *
			 * @param bool  $value Whether or not to skip the post type.
			 * @param array $cpt   Current post type being registered.
			 */
			if ( (bool) apply_filters( 'cptui_disable_cpt', false, $cpt ) ) {
				continue;
			}

			am_register_single_cpt( $cpt );
		}
	}

	do_action( 'am_post_register_cpts', $cpts );
}
//add_action( 'init', 'am_prepare_cpt_array', 10 );

function am_prepare_tax_array() {
	$taxes = get_option( 'am_cpt' );

	if ( empty( $taxes ) ) {
		return;
	}

	do_action( 'am_pre_register_tax', $taxes );

	if ( is_array( $taxes ) ) {
		foreach ( $taxes as $tax ) {
			/**
			 * Filters whether or not to skip registration of the current iterated post type.
			 *
			 * Dynamic part of the filter name is the chosen post type slug.
			 *
			 * @since 1.7.0
			 *
			 * @param bool  $value Whether or not to skip the post type.
			 * @param array $cpt   Current post type being registered.
			 */
			if ( (bool) apply_filters( "cptui_disable_{$tax['name']}_tax", false, $tax ) ) {
				continue;
			}

			/**
			 * Filters whether or not to skip registration of the current iterated post type.
			 *
			 * @since 1.7.0
			 *
			 * @param bool  $value Whether or not to skip the post type.
			 * @param array $cpt   Current post type being registered.
			 */
			if ( (bool) apply_filters( 'cptui_disable_tax', false, $tax ) ) {
				continue;
			}

			am_register_single_tax( $tax );
		}
	}

	do_action( 'am_post_register_taxes', $taxes );
}
//add_action( 'init', 'am_prepare_tax_array', 9 );

function am_register_single_cpt( $cpt = [] ) {
	$cpt['map_meta_cap'] = apply_filters( 'am_map_meta_cap', true, $cpt['name'], $cpt );

	if ( empty( $cpt['supports'] ) ) {
		$cpt['supports'] = [
			'title',
			'editor',
			'revisions',
			'author',
			'excerpt',
			'page-attributes',
			'thumbnail',
			'post-formats'
		];
	}

	$label             = $cpt['label'];
	$label_lc          = strtolower( $cpt['label'] );
	$singular_label    = $cpt['singular_label'];
	$singular_label_lc = strtolower( $cpt['singular_label'] );

	$labels = [
		'name'                     => _x( "{$label}", 'Post Type General Name', AM_TEXT ),
		'singular_name'            => _x( "{$singular_label}", 'Post Type Singular Name', AM_TEXT ),
		'add_new'                  => __( 'Add New', AM_TEXT ),
		'add_new_item'             => __( "Add New {$singular_label}", AM_TEXT ),
		'edit_item'                => __( "Edit {$singular_label}", AM_TEXT ),
		'new_item'                 => __( "New {$singular_label}", AM_TEXT ),
		'view_item'                => __( "View {$singular_label}", AM_TEXT ),
		'view_items'               => __( "View {$label}", AM_TEXT ),
		'search_items'             => __( "Search {$singular_label}", AM_TEXT ),
		'not_found'                => __( "No {$label_lc} found.", AM_TEXT ),
		'not_found_in_trash'       => __( "Not {$label_lc} in Trash.", AM_TEXT ),
		'parent_item_colon'        => __( "Parent {$singular_label}:", AM_TEXT ),
		'all_items'                => __( "All ${label}", AM_TEXT ),
		'archives'                 => __( "${singular_label} Archives", AM_TEXT ),
		'attributes'               => __( "{$singular_label} Attributes", AM_TEXT ),
		'insert_into_item'         => __( "Insert into {$singular_label_lc}", AM_TEXT ),
		'uploaded_to_this_item'    => __( "Uploaded to this {$singular_label_lc}", AM_TEXT ),
		'featured_image'           => __( 'Featured Image', AM_TEXT ),
		'set_featured_image'       => __( 'Set featured image', AM_TEXT ),
		'remove_featured_image'    => __( 'Remove featured image', AM_TEXT ),
		'use_featured_image'       => __( 'Use as featured image', AM_TEXT ),
		'filter_items_list'        => __( "Filter {$label_lc} list", AM_TEXT ),
		'items_list_navigation'    => __( "{$label} list navigation", AM_TEXT ),
		'items_list'               => __( "{$label} list", AM_TEXT ),
		'item_published'           => __( "{$label} published.", AM_TEXT ),
		'item_published privately' => __( "{$label} published privately.", AM_TEXT ),
		'item_reverted_to_draft'   => __( "{$label} reverted to draft.", AM_TEXT ),
		'item_scheduled'           => __( "{$label} scheduled.", AM_TEXT ),
		'item_updated'             => __( "{$label} updated.", AM_TEXT ),
		'update_item'              => __( "Update {$singular_label}", AM_TEXT ),
		'menu_name'                => __( "{$label}", AM_TEXT ),
	];

	$public          = ( ! empty( $cpt['public'] ) ) ? am_coerce_bool( $cpt['public'] ) : true;
	$show_ui         = ( ! empty( $cpt['show_ui'] ) ) ? am_coerce_bool( $cpt['show_ui'] ) : $public;
	$has_archive     = ( ! empty( $cpt['has_archive'] ) ) ? am_coerce_bool( $cpt['has_archive'] ) : false;
	$capability_type = 'post';
	if ( ! empty( $cpt['capability_type'] ) ) {
		$capability_type = $cpt['capability_type'];

		if ( false !== strpos( $cpt['capability_type'], ',' ) ) {
			$caps = array_map( 'trim', explode( ', ', $cpt['capability_type'] ) );

			if ( count( $caps ) > 2 ) {
				$caps = array_slice( $caps, 0, 2 );
			}

			$capability_type = $caps;
		}
	}

	$taxonomies = [];
	if ( ! empty( $cpt['taxonomies'] ) && is_array( $cpt['taxonomies'] ) ) {
		$taxonomies = $cpt['taxonomies'];
	}

	$args = [
		'labels'              => $labels,
		'label'               => $label,
		'description'         => ( ! empty( $cpt['description'] ) ) ? $cpt['description'] : '',
		'public'              => $public,
		'publicly_queryable'  => ( ! empty( $cpt['publicly_queryable'] ) ) ? am_coerce_bool( $cpt['publicly_queryable'] ) : $public,
		'exclude_from_search' => ( ! empty( $cpt['exclude_from_search'] ) ) ? am_coerce_bool( $cpt['exclude_from_search'] ) : false,
		'hierarchical'        => ( ! empty( $cpt['hierarchical'] ) ) ? am_coerce_bool( $cpt['hierarchical'] ) : false,
		'show_ui'             => ( ! empty( $cpt['show_ui'] ) ) ? am_coerce_bool( $cpt['show_ui'] ) : true,
		'show_in_menu'        => ( ! empty( $cpt['show_in_menu'] ) ) ? am_coerce_bool( $cpt['show_in_menu'] ) : $show_ui,
		'show_in_nav_menus'   => ( ! empty( $cpt['show_in_nav_menus'] ) ) ? am_coerce_bool( $cpt['show_in_nav_menus'] ) : $public,
		'show_in_rest'        => true,
		'menu_position'       => ( ! empty( $cpt['menu_position'] ) ) ? sanitize_text_field( $cpt['menu_position'] ) : 1,
		'capability_type'     => 'post',
		'supports'            => ( ! empty( $cpt['supports'] ) ) ? $cpt['supports'] : false,
		'taxonomies'          => $taxonomies,
		'has_archive'         => $has_archive,
		'can_export'          => ( ! empty( $cpt['can_export'] ) ) ? am_coerce_bool( $cpt['can_export'] ) : true,
		'delete_with_user'    => ( ! empty( $cpt['delete_with_user'] ) ) ? am_coerce_bool( $cpt['delete_with_user'] ) : false,
	];

	$args = apply_filters( 'am_pre_register_cpt', $args, $cpt['name'], $cpt );

	return register_post_type( $cpt['name'], $args );

}

function am_register_single_tax( $tax = [] ) {

	$label          = $cpt['label'];
	$singular_label = $cpt['singular_label'];
	$name           = sanitize_key( str_replace( ' ', '_', $label ) );

	$labels = [
		'name'                       => $label,
		'singular_name'              => $singular_label,
		// translators: string %s is singular name.
		'search_items'               => sprintf( __( 'Search %s', AM_TEXT ), $plural ),
		// translators: string %s is plural name.
		'popular_items'              => sprintf( __( 'Popular %s', AM_TEXT ), $plural ),
		// translators: string %s is plural name.
		'all_items'                  => sprintf( __( 'All %s', AM_TEXT ), $plural ),
		// translators: string %s is plural name.
		'parent_item'                => sprintf( __( 'Parent %s', AM_TEXT ), $singular_label ),
		// translators: string %s is plural name.
		'parent_item_colon'          => sprintf( __( 'Parent %s:', AM_TEXT ), $singular_label ),
		// translators: string %s is plural name.
		'edit_item'                  => sprintf( __( 'Edit %s', AM_TEXT ), $singular_label ),
		// translators: string %s is plural name.
		'view_item'                  => sprintf( __( 'View %s', AM_TEXT ), $singular_label ),
		// translators: string %s is plural name.
		'update_item'                => sprintf( __( 'Update %s', AM_TEXT ), $singular_label ),
		// translators: string %s is plural name.
		'add_new_item'               => sprintf( __( 'Add New %s', AM_TEXT ), $singular_label ),
		// translators: string %s is plural name.
		'new_item_name'              => sprintf( __( 'New %s Name', AM_TEXT ), $singular_label ),
		// translators: string %s is plural name.
		'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', AM_TEXT ), strtolower( $plural ) ),
		// translators: string %s is plural name.
		'add_or_remove_items'        => sprintf( __( 'Add or remove %s', AM_TEXT ), strtolower( $plural ) ),
		// translators: string %s is plural name.
		'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', AM_TEXT ), strtolower( $plural ) ),
		// translators: string %s is plural name.
		'not_found'                  => sprintf( __( 'No %s found', AM_TEXT ), strtolower( $plural ) ),
		// translators: string %s is plural name.
		'no_terms'                   => sprintf( __( 'No %s', AM_TEXT ), strtolower( $plural ) ),
		// translators: string %s is plural name.
		'items_list_navigation'      => sprintf( __( '%s list navigation', AM_TEXT ), $plural ),
		// translators: string %s is plural name.
		'items_list'                 => sprintf( __( '%s list', AM_TEXT ), $plural ),
		// translators: string %s is plural name.
		'most_used'                  => sprintf( __( 'Most Used %s', AM_TEXT ), $plural ),
		// translators: string %s is plural name.
		'back_to_items'              => sprintf( __( '← Back to %s', AM_TEXT ), $plural ),
		'menu_name'                  => $plural,
		// translators: string %s is plural name.
		'new_item'                   => sprintf( __( 'New %s', AM_TEXT ), $singular_label ),
		// translators: string %s is plural name.
		'view_items'                 => sprintf( __( 'View %s', AM_TEXT ), $plural ),
		// translators: string %s is plural name.
		'not_found_in_trash'         => sprintf( __( 'No %s found in trash', AM_TEXT ), strtolower( $plural ) ),
		// translators: string %s is single name.
		'archives'                   => sprintf( __( '%s Archives', AM_TEXT ), $singular_label ),
		// translators: string %s is single name.
		'attributes'                 => sprintf( __( 'New %s', AM_TEXT ), $singular_label ),
		// translators: string %s is single name.
		'insert_into_item'           => sprintf( __( '%s Attributes', AM_TEXT ), $singular_label ),
		// translators: string %s is single name.
		'uploaded_to_this_item'      => sprintf( __( 'Uploaded to this %s', AM_TEXT ), strtolower( $singular_label ) ),
		'archive_title'              => $plural,
		'name_admin_bar'             => $singular_label,
	];

	$args = array(
		'name'                => $name,
		'capability_type'     => ( isset( $tax['capability_type'] ) ) ? $tax['capability_type'] : 'post',
		'description'         => ( isset( $tax['description'] ) ) ? $tax['description'] : '',
		'exclude_from_search' => ( isset( $tax['exclude_from_search'] ) ) ? $tax['exclude_from_search'] : false,
		'has_archive'         => ( isset( $tax['has_archive'] ) ) ? $tax['has_archive'] : true,
		'hierarchical'        => ( isset( $tax['hierarchical'] ) ) ? $tax['hierarchical'] : false,
		'labels'              => $labels,
		'menu_icon'           => ( isset( $tax['menu_icon'] ) ) ? $tax['menu_icon'] : 'dashicons-admin-generic',
		'menu_position'       => ( isset( $tax['menu_position'] ) ) ? $tax['menu_position'] : 21,
		'meta_box_cb'         => ( isset( $tax['meta_box_cb'] ) ) ? $tax['meta_box_cb'] : '',
		'public'              => ( isset( $tax['public'] ) ) ? $tax['public'] : true,
		'publicly_queryable'  => ( isset( $tax['publicly_queryable'] ) ) ? $tax['publicly_queryable'] : true,
		'query_var'           => ( isset( $tax['query_var'] ) ) ? $tax['query_var'] : true,
		'rewrite'             => array(
			'slug'         => ( isset( $tax['rewrite_slug'] ) ) ? $tax['rewrite_slug'] : strtolower( $plural ),
			'with_front'   => ( isset( $tax['with_front'] ) ) ? $tax['with_front'] : true,
			'hierarchical' => ( isset( $tax['rewrite_hierarchical'] ) ) ? $tax['rewrite_hierarchical'] : false,
		),
		'show_admin_column'   => ( isset( $tax['show_admin_column'] ) ) ? $tax['show_admin_column'] : true,
		'show_in_admin_bar'   => ( isset( $tax['show_in_admin_bar'] ) ) ? $tax['show_in_admin_bar'] : false,
		'show_in_menu'        => ( isset( $tax['show_in_menu'] ) ) ? $tax['show_in_menu'] : true,
		'show_in_nav_menus'   => ( isset( $tax['show_in_nav_menus'] ) ) ? $tax['show_in_nav_menus'] : true,
		'show_in_rest'        => ( isset( $tax['show_in_rest'] ) ) ? $tax['show_in_rest'] : true,
		'rest_base'           => ( isset( $tax['rest_base'] ) ) ? $tax['rest_base'] : strtolower( $plural ),
		'show_ui'             => ( isset( $tax['show_ui'] ) ) ? $tax['show_ui'] : true,
	);

	$post_type = ! empty( $tax['post_tyes'] ) ? $tax['post_types'] : null;

	return register_taxonomy( $tax['name'], $post_type, $args );
}