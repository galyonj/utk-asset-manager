<?php
/**
 * Collection of functions for making contextual metaboxes
 * for custom post types and custom taxonomies.
 *
 * @since      0.0.5
 * @package    UTK_Asset_Manager
 * @subpackage includes/make-meta-boxes
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}


function am_expiration_meta_box() {
	$post_types = am_get_registered_post_types();

	add_meta_box(
		'am_expiration_date',
		__( 'Expiration Date', AM_TEXT ),
		'am_expiration_meta_box_callback',
		array_keys( $post_types ),
		'side',
		'high',
	);
}

function am_expiration_meta_box_callback() {
	?>
    <div class="test">
        <p>This is a test.</p>
    </div>
	<?php
}