<?php

/**
 * @link              https://github.com/galyonj
 * @since             0.0.1
 * @package           UTK_Asset_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Asset Manager
 * Plugin URI:        https://github.com/galyonj/utk-asset-manager
 * Description:       Simplified digital asset management with custom post types and taxonomies.
 * Version:           0.0.1
 * Author:            John Galyon
 * Author URI:        https://github.com/galyonj
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       utk-asset-manager
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$file_data = get_file_data(
	__FILE__,
	[
		'name'    => 'Plugin Name',
		'version' => 'Version',
		'text'    => 'Text Domain'
	]
);
define( 'AM_NAME', $file_data['name'] );
define( 'AM_VERSION', $file_data['version'] );
define( 'AM_TEXT', $file_data['text'] );

/**
 * Glob through the classes directory
 * and require each file therein.
 *
 * @since 0.0.1
 */
function am_load_classes() {
	$classes = glob( plugin_dir_path( __FILE__ ) . '/classes/*.php' );

	foreach ( $classes as $class ) {
		require_once( $class );
	}
}

add_action( 'plugins_loaded', 'am_load_classes' );

/**
 * Glob through the includes directory
 * and require each file therein.
 *
 * @since  0.0.1
 */
function am_required_files() {
	$files = glob( plugin_dir_path( __FILE__ ) . '/includes/*.php' );

	foreach ( $files as $file ) {
		require_once( $file );
	}
}

add_action( 'plugins_loaded', 'am_required_files' );

/**
 * Load the plugin textdomain and show the path to the languages directory
 *
 * @since  0.0.1
 */
function am_load_textdomain() {
	load_plugin_textdomain( AM_TEXT, false, plugin_dir_path( __FILE__ ) . '/languages' );
}

add_action( 'plugins_loaded', 'am_load_textdomain' );

function am_create_plugin_menu() {
	$caps        = 'manage_options';
	$parent_slug = 'asset_manager';

	add_menu_page(
		__( AM_NAME, AM_TEXT ),
		__( AM_NAME, AM_TEXT ),
		$caps,
		$parent_slug,
		'am_display_settings',
		am_get_menu_icon()
	);

	add_submenu_page(
		$parent_slug,
		__( 'Manage Assets', AM_TEXT ),
		__( 'Manage Assets', AM_TEXT ),
		$caps,
		'manage_assets',
		'am_display_manage_assets'
	);

	add_submenu_page(
		$parent_slug,
		__( 'Manage Taxonomies', AM_TEXT ),
		__( 'Manage Taxonomies', AM_TEXT ),
		$caps,
		'manage_taxonomies',
		'am_display_manage_taxonomies'
	);

	remove_submenu_page( $parent_slug, 'asset_manager' );

	add_submenu_page(
		$parent_slug,
		__( AM_NAME, AM_TEXT ),
		__( 'System Information', AM_TEXT ),
		$caps,
		'about_asset_manager',
		'am_display_settings'
	);
}

add_action( 'admin_menu', 'am_create_plugin_menu' );
