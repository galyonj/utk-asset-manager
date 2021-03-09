<?php
/**
 * Functions for processing CRUD operations from our
 * custom post type and custom taxonomy management pages
 *
 * @since      0.5.0
 * @package    UTK_Asset_Manager
 * @subpackage includes/processing
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Verify that the $_POST data is legitimate,
 * and that it's from one of our forms, then
 * send it off for processing based on the form
 * it came from.
 *
 * @since 0.5.0
 */
function am_process_post_data() {
	global $hook_suffix;

	if ( ! is_admin() || wp_doing_ajax() ) {
		return;
	}

	if( !empty($_GET) && isset( $_GET['page'] ) && ( 'manage_taxonomies' !== $_GET || 'manage_assets' !== $_GET)) {
		if('manage_taxonomies' )
	}
}