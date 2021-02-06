<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Enqueue scripts and styles and all that good stuff that we need
 * for the plugin to work and look pretty-ish.
 *
 * @since  0.0.1
 * @author John Galyon
 */
function am_admin_enqueue() {
	global $hook_suffix;

	$allowed_slugs = [
		'asset-manager_page_about_asset_manager',
		'asset-manager_page_manage_assets',
		'asset-manager_page_manage_taxonomies'
	];

	if ( is_admin() && in_array( $hook_suffix, $allowed_slugs ) ) {

		wp_enqueue_style(
			AM_TEXT,
			dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/' . AM_TEXT . '.min.css',
			'',
			AM_VERSION,
		);

		wp_enqueue_script(
			AM_TEXT,
			dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/' . AM_TEXT . '.min.js',
			[ 'jquery' ],
			AM_VERSION,
			true
		);
	}
}

add_action( 'admin_enqueue_scripts', 'am_admin_enqueue' );
