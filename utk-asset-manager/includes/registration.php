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

function am_prepare_post_type_data() {
	$post_types = am_get_registered_post_types();

	if ( empty( $post_types ) ) {
		return;
	}

	do_action( 'am_pre_register_post_type', $post_types );

	if ( is_array( $post_types ) ) {
		foreach ( $post_types as $post_type ) {
			/**
			 * Filters whether or not to skip registration of the current iterated post type.
			 *
			 * Dynamic part of the filter name is the chosen post type slug.
			 *
			 * @param bool  $value     Whether or not to skip the post type.
			 * @param array $post_type Current post type being registered.
			 *
			 * @since 1.7.0
			 *
			 */
			if ( (bool) apply_filters( "am_disable_{$post_type['name']}_cpt", false, $post_type ) ) {
				continue;
			}

			/**
			 * Filters whether or not to skip registration of the current iterated post type.
			 *
			 * @param bool  $value     Whether or not to skip the post type.
			 * @param array $post_type Current post type being registered.
			 *
			 * @since 1.7.0
			 *
			 */
			if ( (bool) apply_filters( 'am_disable_cpt', false, $post_type ) ) {
				continue;
			}

			am_register_single_post_type( $post_type );
		}
	}

	do_action( 'am_post_register_cpts', $post_types );
}
add_action( 'init', 'am_prepare_post_type_data', 10 );

function am_prepare_tax_data() {
	$taxes = am_get_registered_taxes();

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
			 * @param bool  $value Whether or not to skip the post type.
			 * @param array $cpt   Current post type being registered.
			 *
			 * @since 1.7.0
			 *
			 */
			if ( (bool) apply_filters( "cptui_disable_{$tax['name']}_tax", false, $tax ) ) {
				continue;
			}

			/**
			 * Filters whether or not to skip registration of the current iterated post type.
			 *
			 * @param bool  $value Whether or not to skip the post type.
			 * @param array $cpt   Current post type being registered.
			 *
			 * @since 1.7.0
			 *
			 */
			if ( (bool) apply_filters( 'cptui_disable_tax', false, $tax ) ) {
				continue;
			}

			am_register_single_tax( $tax );
		}
	}

	do_action( 'am_post_register_taxes', $taxes );
}
add_action( 'init', 'am_prepare_tax_data', 9 );

function am_register_single_post_type( $cpt = [] ) {

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

	$public     = ( ! empty( $cpt['public'] ) ) ? am_coerce_bool( $cpt['public'] ) : true;
	$taxonomies = ( ! empty( $cpt['taxonomies'] ) && is_array( $cpt['taxonomies'] ) ) ? $cpt['taxonomies'] : [];

	/**
	 * Select the chosen menu icon from the $svg array
	 * and format it to work with WordPress.
	 *
	 * @param array $cpt
	 *
	 * @since 0.5.0
	 *
	 * @uses  \am_get_svg_codes()
	 */
	$menu_icon = '';
	if ( ! empty( $cpt['menu_icon'] ) ) {
		$menu_icon = trim( sanitize_key( $cpt['menu_icon'] ) );
		$ex        = explode( '-', $menu_icon, 2 );
		$svg       = am_get_svg_codes();
		$search    = [
			'<svg',
			'<path',
		];
		$destroy   = [
			'<svg height="20" width="20"',
			'<path fill="black"',
		];

		if ( false === strpos( $menu_icon, 'dashicons' ) ) {
			$menu_icon = 'data:image/svg+xml;base64,' . base64_encode( str_replace( $search, $destroy, $svg[ $ex[0] ][ $ex[1] ] ) );
		} else {
			$menu_icon = $cpt['menu_icon'];
		}
	}

	$args = [
		'labels'              => $labels,
		'label'               => $label,
		'description'         => ( ! empty( $cpt['description'] ) ) ? $cpt['description'] : '',
		'public'              => $public,
		'menu_icon'           => $menu_icon,
		'publicly_queryable'  => ( ! empty( $cpt['publicly_queryable'] ) ) ? am_coerce_bool( $cpt['publicly_queryable'] ) : $public,
		'exclude_from_search' => ( ! empty( $cpt['exclude_from_search'] ) ) ? am_coerce_bool( $cpt['exclude_from_search'] ) : false,
		'hierarchical'        => ( ! empty( $cpt['hierarchical'] ) ) ? am_coerce_bool( $cpt['hierarchical'] ) : false,
		'show_ui'             => ( ! empty( $cpt['show_ui'] ) ) ? am_coerce_bool( $cpt['show_ui'] ) : $public,
		'show_in_menu'        => ( ! empty( $cpt['show_in_menu'] ) ) ? am_coerce_bool( $cpt['show_in_menu'] ) : false,
		'show_in_nav_menus'   => ( ! empty( $cpt['show_in_nav_menus'] ) ) ? am_coerce_bool( $cpt['show_in_nav_menus'] ) : $public,
		'show_in_rest'        => true,
		'menu_position'       => ( ! empty( $cpt['menu_position'] ) ) ? sanitize_text_field( $cpt['menu_position'] ) : 1.5,
		'capability_type'     => 'post',
		'supports'            => ( ! empty( $cpt['supports'] ) ) ? $cpt['supports'] : false,
		'taxonomies'          => $taxonomies,
		'has_archive'         => ( ! empty( $cpt['has_archive'] ) ) ? am_coerce_bool( $cpt['has_archive'] ) : true,
		'can_export'          => ( ! empty( $cpt['can_export'] ) ) ? am_coerce_bool( $cpt['can_export'] ) : true,
		'delete_with_user'    => ( ! empty( $cpt['delete_with_user'] ) ) ? am_coerce_bool( $cpt['delete_with_user'] ) : false,
	];

	$args = apply_filters( 'am_pre_register_cpt', $args, $cpt['name'], $cpt );

	return register_post_type( $cpt['name'], $args );

}

function am_register_single_tax( $tax = [] ) {

	$label          = $tax['label'];
	$singular_label = $tax['singular_label'];

	/**
	 * Nicole wants any custom taxonomy created
	 * by this plugin to automatically be assigned
	 * to every post type. That is ill-advised, but
	 * I can't get a word in edgewise to explain why,
	 * so we'll just hope for the best that it
	 * doesn't screw anything up.
	 *
	 * @since 0.5.0
	 */
	$built_in_post_types = [
		'page',
		'post',
	];
	$allowed_post_types  = array_merge( array_keys( am_get_registered_post_types() ), $built_in_post_types );

	$labels = [
		'name'                       => sprintf( _x( '%s', 'taxonomy general name', AM_TEXT ), $label ),
		'singular_name'              => sprintf( _x( '%s', 'taxonomy singular name', AM_TEXT ), $singular_label ),
		'menu_name'                  => $label,
		'all_items'                  => sprintf( __( 'All %s', AM_TEXT ), $label ),
		'edit_item'                  => sprintf( __( 'Edit %s', AM_TEXT ), $singular_label ),
		'view_item'                  => sprintf( __( 'View %s', AM_TEXT ), $singular_label ),
		'update_item'                => sprintf( __( 'Update %s', AM_TEXT ), $singular_label ),
		'add_new_item'               => sprintf( __( 'Add New %s', AM_TEXT ), $singular_label ),
		'new_item_name'              => sprintf( __( 'New %s Name', AM_TEXT ), $singular_label ),
		'parent_item'                => sprintf( __( 'Parent %s', AM_TEXT ), $singular_label ),
		'parent_item_colon'          => sprintf( __( 'Parent %s:', AM_TEXT ), $singular_label ),
		'search_items'               => sprintf( __( 'Search %s', AM_TEXT ), $singular_label ),
		'popular_items'              => sprintf( __( 'Popular %s', AM_TEXT ), $singular_label ),
		'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', AM_TEXT ), strtolower( $label ) ),
		'add_or_remove_items'        => sprintf( __( 'Add or remove %s', AM_TEXT ), strtolower( $label ) ),
		'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', AM_TEXT ), strtolower( $label ) ),
		'not_found'                  => sprintf( __( 'No %s found.', AM_TEXT ), strtolower( $label ) ),
		'back_to_items'              => sprintf( __( '← Back to %s', AM_TEXT ), strtolower( $label ) ),
	];
	$args   = array(
		'labels'             => $labels,
		'description'        => ( ! empty( $tax['description'] ) ) ? $tax['description'] : '',
		'public'             => ( ! empty( $tax['public'] ) ) ? $tax['public'] : true,
		'publicly_queryable' => ( ! empty( $tax['publicly_queryable'] ) ) ? $tax['publicly_queryable'] : true,
		'hierarchical'       => ( ! empty( $tax['hierarchical'] ) ) ? $tax['hierarchical'] : false,
		'show_ui'            => ( ! empty( $tax['show_ui'] ) ) ? $tax['show_ui'] : true,
		'show_in_menu'       => ( ! empty( $tax['show_in_menu'] ) ) ? $tax['show_in_menu'] : false,
		'show_in_nav_menus'  => ( ! empty( $tax['show_in_nav_menus'] ) ) ? $tax['show_in_nav_menus'] : false,
		'show_in_rest'       => ( ! empty( $tax['show_in_rest'] ) ) ? $tax['show_in_rest'] : true,
		'show_tagcloud'      => ( ! empty( $tax['show_tagcloud'] ) ) ? $tax['show_tagcloud'] : true,
		'show_in_quick_edit' => ( ! empty( $tax['show_in_quick_edit'] ) ) ? $tax['show_in_quick_edit'] : true,
		'show_admin_column'  => ( ! empty( $tax['show_admin_column'] ) ) ? $tax['show_admin_column'] : true,
		'rewrite'            => [
			'slug'         => $tax['name'],
			'with_front'   => ( ! empty( $tax['rewrite']['with_front'] ) ) ? $tax['rewrite']['with_front'] : true,
			'hierarchical' => ( ! empty( $tax['rewrite']['hierarchical'] ) ) ? $tax['rewrite']['hierarchical'] : false,
		],
		'query_var'          => ( ! empty( $tax['query_var'] ) ) ? $tax['query_var'] : $tax['name'],
	);

	$args = apply_filters( 'am_pre_register_tax', $args, $tax['name'], $tax );

	return register_taxonomy( $tax['name'], $allowed_post_types, $args );
}