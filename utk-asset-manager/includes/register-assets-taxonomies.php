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
add_action( 'init', 'am_prepare_cpt_array', 10 );

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
add_action( 'init', 'am_prepare_tax_array', 9 );

function am_register_single_cpt( $cpt = [] ) {
	$cpt['map_meta_cap'] = apply_filters( 'am_map_meta_cap', true, $cpt['name'], $cpt );

	if ( empty( $cpt['supports'] ) ) {
		$cpt['supports'] = [];
	}

	$support_params = apply_filters( 'am_support_params', [], $cpt['name'], $cpt );

	if ( is_array( $support_params ) && ! empty( $support_params ) ) {
		if ( is_array( $cpt['supports'] ) ) {
			$cpt['supports'] = array_merge( $cpt['supports'], $support_params );
		} else {
			$cpt['supports'] = [ $support_params ];
		}
	}

	if ( isset( $cpt['supports'] ) && is_array( $cpt['supports'] ) && in_array( 'none', $cpt['supports'], true ) ) {
		$cpt['supports'] = false;
	}

	$label             = $cpt['label'];
	$label_lc          = strtolower( $cpt['label'] );
	$singular_label    = $cpt['singular_label'];
	$singular_label_lc = strtolower( $cpt['singular_label'] );

	$labels = [
		'name'                  => _x( "{$label}", 'Post Type General Name', AM_TEXT ),
		'singular_name'         => _x( "{$singular_label}", 'Post Type Singular Name', AM_TEXT ),
		'menu_name'             => __( "{$label}", AM_TEXT ),
		'name_admin_bar'        => __( "{$singular_label}", AM_TEXT ),
		'archives'              => __( "${singular_label} Archives", AM_TEXT ),
		'attributes'            => __( "{$singular_label} Attributes", AM_TEXT ),
		'parent_item_colon'     => __( "Parent {$singular_label}:", AM_TEXT ),
		'all_items'             => __( "All ${label}", AM_TEXT ),
		'add_new_item'          => __( "Add New {$singular_label}", AM_TEXT ),
		'add_new'               => __( 'Add New', AM_TEXT ),
		'new_item'              => __( "New {$singular_label}", AM_TEXT ),
		'edit_item'             => __( "Edit {$singular_label}", AM_TEXT ),
		'update_item'           => __( "Update {$singular_label}", AM_TEXT ),
		'view_item'             => __( "View {$singular_label}", AM_TEXT ),
		'view_items'            => __( "View {$label}", AM_TEXT ),
		'search_items'          => __( "Search {$singular_label}", AM_TEXT ),
		'not_found'             => __( 'Not found', AM_TEXT ),
		'not_found_in_trash'    => __( 'Not found in Trash', AM_TEXT ),
		'featured_image'        => __( 'Featured Image', AM_TEXT ),
		'set_featured_image'    => __( 'Set featured image', AM_TEXT ),
		'remove_featured_image' => __( 'Remove featured image', AM_TEXT ),
		'use_featured_image'    => __( 'Use as featured image', AM_TEXT ),
		'insert_into_item'      => __( "Insert into {$singular_label_lc}", AM_TEXT ),
		'uploaded_to_this_item' => __( "Uploaded to this {$singular_label_lc}", AM_TEXT ),
		'items_list'            => __( "{$label} list", AM_TEXT ),
		'items_list_navigation' => __( "{$label} list navigation", AM_TEXT ),
		'filter_items_list'     => __( "Filter {$label_lc} list", AM_TEXT ),
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
	if ( ! empty( $cpt['taxonomies'] ) ) {
		if ( is_array( $cpt['taxonomies'] ) ) {
			$taxonomies = $cpt['taxonomies'];
		}
	}

	$rewrite = null;
	if ( ! empty( $cpt['rewrite'] ) ) {
		$rewrite = [
			'slug'       => ( ! empty( $cpt['rewrite']['slug'] ) ) ? $cpt['rewrite']['slug'] : $cpt['name'],
			'with_front' => ( ! empty( $cpt['rewrite']['with_front'] ) ) ? $cpt['rewrite']['with_front'] : false,
			'pages'      => ( ! empty( $cpt['rewrite']['pages'] ) ) ? am_coerce_bool( $cpt['rewrite']['pages'] ) : true,
			'feeds'      => ( ! empty( $cpt['rewrite']['feeds'] ) ) ? am_coerce_bool( $cpt['rewrite']['feeds'] ) : $has_archive,
			'ep_mask'    => ( ! empty( $cpt['rewrite']['ep_mask'] ) ) ? $cpt['rewrite']['ep_mask'] : null,
		];
	}

	$args = [
		'labels'                => $labels,
		'label'                 => $label,
		'description'           => ( ! empty( $cpt['description'] ) ) ? $cpt['description'] : '',
		'public'                => $public,
		'publicly_queryable'    => ( ! empty( $cpt['publicly_queryable'] ) ) ? am_coerce_bool( $cpt['publicly_queryable'] ) : $public,
		'exclude_from_search'   => ( ! empty( $cpt['exclude_from_search'] ) ) ? am_coerce_bool( $cpt['exclude_from_search'] ) : false,
		'hierarchical'          => ( ! empty( $cpt['hierarchical'] ) ) ? am_coerce_bool( $cpt['hierarchical'] ) : false,
		'show_ui'               => ( ! empty( $cpt['show_ui'] ) ) ? am_coerce_bool( $cpt['show_ui'] ) : true,
		'show_in_menu'          => ( ! empty( $cpt['show_in_menu'] ) ) ? am_coerce_bool( $cpt['show_in_menu'] ) : $show_ui,
		'show_in_nav_menus'     => ( ! empty( $cpt['show_in_nav_menus'] ) ) ? am_coerce_bool( $cpt['show_in_nav_menus'] ) : $public,
		'show_in_rest'          => ( ! empty( $cpt['show_in_rest'] ) ) ? am_coerce_bool( $cpt['show_in_rest'] ) : true,
		'rest_base'             => ( ! empty( $cpt['rest_base'] ) ) ? $cpt['rest_base'] : true,
		'rest_controller_class' => ( ! empty( $cpt['rest_controller_class'] ) ) ? $cpt['rest_controller_class'] : 'WP_REST_Posts_Controller',
		'menu_position'         => ( ! empty( $cpt['menu_position'] ) ) ? sanitize_text_field( $cpt['menu_position'] ) : 2,
		'capability_type'       => $capability_type,
		'supports'              => ( ! empty( $cpt['supports'] ) ) ? $cpt['supports'] : false,
		'taxonomies'            => $taxonomies,
		'has_archive'           => $has_archive,
		'rewrite'               => $rewrite,
		'query_var'             => ( ! empty( $cpt['query_var'] ) ) ? $cpt['query_var'] : $cpt['name'],
		'can_export'            => ( ! empty( $cpt['can_export'] ) ) ? am_coerce_bool( $cpt['can_export'] ) : true,
		'delete_with_user'      => ( ! empty( $cpt['delete_with_user'] ) ) ? am_coerce_bool( $cpt['delete_with_user'] ) : null,
		'template'              => ( ! empty( $cpt['template'] ) ) ? $cpt['template'] : null,
		'template_lock'         => ( ! empty( $cpt['template_lock'] ) ) ? $cpt['template_lock'] : false,
	];

	$args = apply_filters( 'am_pre_register_cpt', $args, $cpt['name'], $cpt );

	return register_post_type( $cpt['name'], $args );

}

function am_register_single_tax( $tax = [] ) {

}