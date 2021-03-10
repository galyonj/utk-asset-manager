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
 *
 * @return string base64 encoded SVG data string
 */
function am_get_menu_icon(): string {

	// Save the SVG data from FontAwesome's icon
	$icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" height="20" width="20"><!-- Font Awesome Free 5.15.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path fill="black" d="M519.442 288.651c-41.519 0-59.5 31.593-82.058 31.593C377.409 320.244 432 144 432 144s-196.288 80-196.288-3.297c0-35.827 36.288-46.25 36.288-85.985C272 19.216 243.885 0 210.539 0c-34.654 0-66.366 18.891-66.366 56.346 0 41.364 31.711 59.277 31.711 81.75C175.885 207.719 0 166.758 0 166.758v333.237s178.635 41.047 178.635-28.662c0-22.473-40-40.107-40-81.471 0-37.456 29.25-56.346 63.577-56.346 33.673 0 61.788 19.216 61.788 54.717 0 39.735-36.288 50.158-36.288 85.985 0 60.803 129.675 25.73 181.23 25.73 0 0-34.725-120.101 25.827-120.101 35.962 0 46.423 36.152 86.308 36.152C556.712 416 576 387.99 576 354.443c0-34.199-18.962-65.792-56.558-65.792z"/></svg>';

	// Return that data as a base64 encoded string
	return 'data:image/svg+xml;base64,' . base64_encode( $icon );

}

/**
 * Given a supplied $val, returns a boolean.
 *
 * @param mixed $val value to be coerced
 *
 * @since 0.0.1
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

function am_get_admin_url( $path = 'admin.php' ) {
	if ( is_multisite() && is_network_admin() ) {
		return network_admin_url( $path );
	}

	return admin_url( $path );
}

/**
 * Return all post types created by our plugin
 *
 * @since 0.5.0
 *
 * @return array
 */
function am_get_registered_post_types(): array {
	return apply_filters( 'am_get_registered_cpts', get_option( 'am_post_types', [] ), get_current_blog_id() );
}

/**
 * Return all taxonomies created by our plugin
 *
 * @since 0.5.0
 *
 * @return array|
 */
function am_get_registered_taxes(): array {
	return apply_filters( 'am_get_registered_taxes', get_option( 'am_taxonomies', [] ), get_current_blog_id() );
}

function am_which_page() {
	global $hook_suffix;

	return ( strstr( $hook_suffix, 'metadata' ) ) ? 'metadata' : 'post_type';
}

/**
 * Create an array of all the terms that we're aware
 * and/or can find documentation have already been reserved
 * by WordPress.
 *
 * @since 0.5.0
 *
 * @return array
 */
function am_reserved_terms(): array {

	$reserved_post_types = [
		'post',
		'page',
		'attachment',
		'revision',
		'nav_menu_item',
		'action',
		'order',
		'theme',
		'themes',
		'fields',
		'custom_css',
		'customize_changeset',
		'author',
		'post_type',
	];

	$reserved_taxonomies = [
		'action',
		'attachment',
		'attachment_id',
		'author',
		'author_name',
		'calendar',
		'cat',
		'category',
		'category__and',
		'category__in',
		'category__not_in',
		'category_name',
		'comments_per_page',
		'comments_popup',
		'customize_messenger_channel',
		'customized',
		'cpage',
		'day',
		'debug',
		'error',
		'exact',
		'feed',
		'fields',
		'hour',
		'include',
		'link_category',
		'm',
		'minute',
		'monthnum',
		'more',
		'name',
		'nav_menu',
		'nonce',
		'nopaging',
		'offset',
		'order',
		'orderby',
		'p',
		'page',
		'page_id',
		'paged',
		'pagename',
		'pb',
		'perm',
		'post',
		'post__in',
		'post__not_in',
		'post_format',
		'post_mime_type',
		'post_status',
		'post_tag',
		'post_type',
		'posts',
		'posts_per_archive_page',
		'posts_per_page',
		'preview',
		'robots',
		's',
		'search',
		'second',
		'sentence',
		'showposts',
		'static',
		'subpost',
		'subpost_id',
		'tag',
		'tag__and',
		'tag__in',
		'tag__not_in',
		'tag_id',
		'tag_slug__and',
		'tag_slug__in',
		'taxonomy',
		'tb',
		'term',
		'theme',
		'type',
		'w',
		'withcomments',
		'withoutcomments',
		'year',
		'output',
	];

	/**
	 * Returns an array of reserved terms -- both post types
	 * and taxonomies -- which users should never use when
	 * registering a custom post type or taxonomy.
	 *
	 * @since 0.5.1
	 */
	return array_merge( $reserved_post_types, $reserved_taxonomies );
}

/**
 * Use the submitted slug to perform a series of best-effort
 * comparisons against registered post type names, registered taxonomy names,
 * the slugs of any published content of any type, and the
 * array(s) of terms reserved for WordPress from the am_reserved_terms function.
 *
 * @param string $slug submitted slug for new post type or taxonomy
 *
 * @since 0.5.1
 *
 * @return bool return false if the new slug doesn't match
 */
function am_slug_exists( string $slug ): bool {

	$all_post_types = array_merge( get_post_types( [ '_builtin' => true ] ), get_post_types( [ '_builtin' => false ] ) );
	$all_taxonomies = array_merge( get_taxonomies( [ '_builtin' => true ] ), get_taxonomies( [ '_builtin' => false ] ) );
	$all_slugs      = get_page_by_path( $slug, 'OBJECT', $all_post_types );
	$reserved_terms = am_reserved_terms();
	$terms          = get_terms( [
		'slug' => $slug
	] );

	// Check if the submitted slug is already registered as a post_type of any sort
	if ( in_array( $slug, $all_post_types, true ) ) {
		return true;
	}

	// Check of the submitted slug is already registered as a taxonomy of any sort
	if ( in_array( $slug, $all_taxonomies, true ) ) {
		return true;
	}

	// Check if the submitted slug matches any term
	if ( is_wp_error( $terms ) || in_array( $slug, $reserved_terms ) ) {
		return true;
	}

	// Check if the submitted slug matches any registered content regardless of post type
	if ( null !== $all_slugs ) {
		return true;
	}

	// If we've made it this far, the slug doesn't exist
	return false;

}

/**
 * Helper function to get the object name from
 * the $_POST global based on the object type.
 *
 * @since 0.5.0
 *
 * @return string object name
 */
function am_get_object_type(): string {
	if ( isset( $_POST['data_type'] ) ) {
		if ( 'post' === $_POST['data_type'] ) {
			return stripslashes( sanitize_text_field( $_POST['name'] ) );
		} else {
			return esc_html__( 'Object', AM_TEXT );
		}
	}
}