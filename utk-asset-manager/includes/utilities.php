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
function coerce_bool( $val ): bool {
	return filter_var( $val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
}

/**
 * Cheater function to make it easy to hide
 * certain blocks of debugging code in production.
 *
 * @since 0.0.1
 *
 * @return bool
 */
function is_dev(): bool {
	$host = $_SERVER['HTTP_HOST'];

	if ( 'localhost:8080' !== $host ) {
		return false;
	}

	return true;
}

/**
 * In the interest of making it easy for the user to choose a menu
 * icon for their new custom post type, this function loops through
 * the children of {project}/assets/svgs and returns a key=>value
 * array of svg names and base64-encoded svg strings
 *
 * This will do two things:
 * 1. Populate the modal dialog fired when the user interacts with the
 *    choose_menu_icon field
 * 2. Return the name of the chosen icon to the field
 * 3. Given the name of the chosen icon, use the icon name to populate the
 *    menu icon in register_post_type($args).
 *
 * @since 0.0.1
 *
 * @param null $icon selected icon name.
 *
 * @return mixed
 */
function get_svg_codes( $icon = null ) {

	$base_dir = dirname( plugin_dir_path( __FILE__ ) ) . '/assets/svgs';
	$dirs     = array_diff( scandir( $base_dir ), [ '.', '..' ] );
	$svg_arr  = [];
	$search   = [
		'<path',
	];
	$replace  = [
		'<path fill="black"',
	];

	foreach ( $dirs as $dir ) {
		$files = array_diff( scandir( "{$base_dir}/{$dir}/" ), [ '.', '..' ] );

		foreach ( $files as $file ) {
			$svg_file = pathinfo( "{$base_dir}/{$dir}/{$file}" );
			$contents = str_replace( $search, $replace, file_get_contents( "{$base_dir}/{$dir}/{$file}" ) );

			$svg_arr[ $dir . '_' . $svg_file['filename'] ] = $contents;
		}

	}

	if ( is_null( $icon ) ) {
		return $svg_arr;
	} else {
		return $svg_arr[ $icon ];
	}

}