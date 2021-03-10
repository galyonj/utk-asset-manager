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
 * Version:           1.1.0
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

/**
 * Create plugin constants
 *
 * @since 0.0.1
 */
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

function john_screwed_up() {
	delete_option( 'am_post_types' );
	delete_option( 'am_taxonomies' );
}

//add_action( 'init', 'john_screwed_up' );

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
	$post_types  = get_option( 'am_post_types' );

	add_menu_page(
		__( AM_NAME, AM_TEXT ),
		__( AM_NAME, AM_TEXT ),
		$caps,
		$parent_slug,
		'am_display_settings',
		am_get_menu_icon(),
		2
	);

	add_submenu_page(
		$parent_slug, // Parent Slug
		__( 'Manage Post Types', AM_TEXT ), // Page Title
		__( 'Manage Post Types', AM_TEXT ), // Menu Title
		$caps, // Capabilities required to see the menu link
		'manage_post_types', // Menu slug
		'am_display_html' // Callback function for displaying the content
	);

	add_submenu_page(
		$parent_slug, // Parent Slug
		__( 'Manage Metadata', AM_TEXT ), // Page Title
		__( 'Manage Metadata', AM_TEXT ), // Menu Title
		$caps, // Capabilities required to see the menu link
		'manage_metadata', // Menu slug
		'am_display_html' // Callback function to display the content
	);

	/**
	 * Remove the main page created for our plugin to clean up the menu a bit.
	 */
	remove_submenu_page( $parent_slug, 'asset_manager' );

//	add_submenu_page(
//		$parent_slug,
//		__( AM_NAME, AM_TEXT ),
//		__( 'System Information', AM_TEXT ),
//		$caps,
//		'about_asset_manager',
//		'am_display_settings'
//	);

	if ( $post_types ) {

		// Iterator so we can output our spacer at the proper time.
		$i = 0;

		foreach ( $post_types as $cpt ) {
			if ( empty( $cpt['show_in_menu'] ) || false === $cpt['show_in_menu'] ) {

				/**
				 * Output a separator to provide some visual distance
				 * between the created post types and our management
				 * and plugin information pages
				 *
				 * @since 0.5.0
				 */
				if ( 0 === $i ) {
					add_submenu_page(
						$parent_slug,
						'wp-menu-separator',
						'',
						'read',
						'',
						''
					);
				}

				/**
				 * Add the submenu page inside our plugin menu for each
				 * post_type that should not be displayed in the top-level
				 * WordPress menu
				 *
				 * @since 0.5.0
				 */
				add_submenu_page(
					$parent_slug, // Parent Slug
					$cpt['label'], // Page Title
					$cpt['label'], // Menu Title
					$caps, // Capabilities required to see the menu link
					'edit.php?post_type=' . $cpt['name'], //
					''
				);

				// Advance the iterator
				$i ++;
			}
		}
	}
}

add_action( 'admin_menu', 'am_create_plugin_menu' );