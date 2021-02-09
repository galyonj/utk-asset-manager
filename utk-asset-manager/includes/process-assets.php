<?php
/**
 * Functions to capture the data from the
 * assets CRUD form, process/manipulate that data,
 * store it, and use it to register/edit a custom
 * post type.
 *
 * @since      0.0.1
 * @package    UTK_Asset_Manager
 * @subpackage includes/process-assets
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Verify that the $_POST data is legitimate,
 * and that it's from our form, then send it
 * off for processing.
 *
 * @since 0.0.1
 *
 * @return string|void
 */
function am_process_cpt() {
	$result = '';

	if ( ! is_admin() || wp_doing_ajax() || 'manage_assets' !== $_GET['page'] ) {
		return;
	}

	if ( empty( $_POST ) || ! wp_verify_nonce( 'am_process_cpt_action', $_POST['_wpnonce'] ) ) {
		return;
	} else {
		if ( isset( $_POST['new_asset'] ) || isset( $_POST['edit_asset'] ) ) {
			$result = am_update_cpt( $_POST );
		} else {
			if ( isset( $_POST['delete_asset'] ) ) {
				$result = am_delete_cpt( $_POST );
			}
		}
	}

	return $result;
}
add_action( 'init', 'am_process_cpt' );


function am_update_cpt( $data = [] ) {

	/**
	 * Sample $data:
	 *
	 * [label_singular] => Asset
	 * [label_plural] => Assets
	 * [menu_icon] => dashicons-controls-back
	 * [public] => true
	 * [show_in_admin_bar] => true
	 * [show_in_menu] => true
	 * [show_in_nav_menus] => true
	 * [set_expiration] => true
	 * [hierarchical] => false
	 * [exclude_from_search] => false
	 * [description] => weee
	 */

	/**
	 * Run through our form data and do what formatting
	 * we can early, so that there's a lower chance of
	 * the data getting buggered up during the registration process.
	 */
	$pattern        = array(
		'/[\'"]/',
		'/[ ]/',
	);
	$replace        = array(
		'',
		'_',
	);
	$label          = sanitize_text_field( $data['label_plural'] );
	$label_singular = sanitize_text_field( $data['label_singular'] );
	$name           = sanitize_key( $label );

	/**
	 * Build the settings array for this post type
	 * before we store it.
	 *
	 * @since 0.0.1
	 */
	$cpt[ $name ] = [
		'name'                => $name,
		'label'               => ucwords( $label ),
		'singular_label'      => ucwords( $label_singular ),
		'menu_icon'           => sanitize_text_field( $data['menu_icon'] ),
		'public'              => coerce_bool( $data['public'] ),
		'show_in_admin_bar'   => coerce_bool( $data['show_in_admin-bar'] ),
		'show_in_menu'        => coerce_bool( $data['show_in_menu'] ),
		'show_in_nav_menus'   => coerce_bool( $data['show_in_nav_menus'] ),
		'set_expiration'      => coerce_bool( $data['set_expiration'] ),
		'hierarchical'        => coerce_bool( $data['hierarchical'] ),
		'exclude_from_search' => coerce_bool( $data['exclude_from_search'] ),
		'description'         => sanitize_textarea_field( $data['description'] ),
	];

	$cpt = apply_filters( 'am_pre_save_cpt', $cpt, $name );

	/**
	 * Has somebody else been saving post types on our turf?
	 * We better check.
	 *
	 * @since 0.0.1
	 */
	if ( false === ( $success = apply_filters( 'am_cpt_update_save', false, $cpt, $data ) ) ) {
		$success = update_option( 'am_cpt', $cpt );
	}

	/**
	 * Create a transient so that we'll know if
	 * we need to flush rewrite rules on init
	 */
	set_transient( 'am_flush_rewrite_rules', true, 5 * 60 );

	if ( isset( $success ) && 'new' === $data['am_cpt_status'] ) {
		return 'add_success';
	}

	return 'update_success';
}

function am_delete_cpt( $data = [] ) {

}

