<?php
/**
 * Plugin utilities
 *
 * @since      0.0.1
 * @author     John Galyon
 * @package    UTK_ASSET_MANAGER
 * @subpackage includes/utlities
 *
 */

/**
 * Get the icon for use in the WordPress admin menu
 *
 * @since  0.0.1
 * @return string base64 encoded SVG data string
 * @author John Galyon
 */
function am_get_menu_icon() {

	// Save the SVG data from FontAwesome's icon
	$icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" height="20" width="20"><!-- Font Awesome Free 5.15.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path fill="black" d="M519.442 288.651c-41.519 0-59.5 31.593-82.058 31.593C377.409 320.244 432 144 432 144s-196.288 80-196.288-3.297c0-35.827 36.288-46.25 36.288-85.985C272 19.216 243.885 0 210.539 0c-34.654 0-66.366 18.891-66.366 56.346 0 41.364 31.711 59.277 31.711 81.75C175.885 207.719 0 166.758 0 166.758v333.237s178.635 41.047 178.635-28.662c0-22.473-40-40.107-40-81.471 0-37.456 29.25-56.346 63.577-56.346 33.673 0 61.788 19.216 61.788 54.717 0 39.735-36.288 50.158-36.288 85.985 0 60.803 129.675 25.73 181.23 25.73 0 0-34.725-120.101 25.827-120.101 35.962 0 46.423 36.152 86.308 36.152C556.712 416 576 387.99 576 354.443c0-34.199-18.962-65.792-56.558-65.792z"/></svg>';

	// Return that data as a base64 encoded string
	return 'data:image/svg+xml;base64,' . base64_encode( $icon );

}

/**
 * Given a supplied $val, returns a boolean.
 *
 * @since 0.0.1
 *
 * @param mixed $val value to be coerced
 *
 * @return bool result of filter_var on $val
 */
function am_coerce_bool( $val ): bool {
	return filter_var( $val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
}

/**
 * Cheater function to make it easy to hide
 * certain blocks of debugging code in production.
 *
 * @since 0.0.2
 *
 * @return bool
 */
function am_is_dev(): bool {
	$host = $_SERVER['HTTP_HOST'];

	if ( 'localhost:8080' !== $host ) {
		return false;
	}

	return true;
}

/**
 * This function looks for a short-lived transient that will be created
 * during the CRUD process for our custom post types and transients.
 * If that transient is found, we do a soft flush of the rewrite rules
 * upon activation of any of the CRUD operations performed by our plugin.
 *
 * Flushing the rewrite rules is really memory-intensive and isn't something
 * one should aspire to do any more often than they absolutely have to,
 * but, as the creation of custom post types and custom taxonomies almost always
 * involves creating new rewrite rules...we have to.
 *
 * @since 0.0.1
 *
 * @link  https://developer.wordpress.org/reference/functions/flush_rewrite_rules/
 * @link  https://developer.wordpress.org/reference/functions/get_transient/
 */
function am_flush_rewrite_rules() {

	if ( wp_doing_ajax() || ! is_admin() ) {
		return;
	}

	/**
	 * Check that our transient exists and has not
	 * expired. If so, we flush rewrite rules for the site
	 * before deleting the transient.
	 *
	 * @since 0.0.1
	 */
	if ( true === ( $value = get_transient( 'am_flush_rewrite_rules' ) ) ) {

		flush_rewrite_rules( false );

		delete_transient( 'am_flush_rewrite_rules' );
	}
}

/**
 * In the interest of making it easy for the user to choose a menu
 * icon for their new custom post type, and making it easy for us
 * to return and use their selected icon value, this function loops
 * through the children of {project}/assets/svgs and returns a key=>value
 * array of svg names and base64-encoded svg strings
 *
 * This will accomplish several things:
 * 1. Populate the modal dialog fired when the user interacts with the
 *    choose_menu_icon field
 * 2. Allow us to capture the name of the chosen icon and set it as the
 *    value of the menu_icon field in our form.
 * 3. Given the name of the chosen icon, use the icon name to populate the
 *    menu icon in register_post_type($args['menu_icon']).
 *
 * @since 0.0.2
 *
 * @return array
 */
function am_get_svg_codes(): array {

	$base_dir = dirname( plugin_dir_path( __FILE__ ) ) . '/assets/svgs';
	$dirs     = array_diff( scandir( $base_dir ), [ '.', '..', 'fontawesome_brands' ] );
	$svg_arr  = [];

	foreach ( $dirs as $dir ) {
		$files = array_diff( scandir( "{$base_dir}/{$dir}/" ), [ '.', '..', '.git', 'font-awesome-logo-full.svg' ] );

		$svg_arr[ $dir ] = [];

		foreach ( $files as $file ) {
			$file     = pathinfo( "{$base_dir}/{$dir}/{$file}" );
			$contents = file_get_contents( "{$base_dir}/{$dir}/{$file['filename']}.{$file['extension']}" );

			$svg_arr[ $dir ][ $file['filename'] ] = $contents;
		}

	}

	/**
	 * Finally, we return our array of SVG files
	 *
	 * @since 0.0.2
	 */
	return $svg_arr;
}