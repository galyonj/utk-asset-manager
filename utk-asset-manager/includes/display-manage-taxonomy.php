<?php
/**
 * Display the taxonomy management page
 *
 * @since      0.0.1
 * @subpackage includes/display-manage-taxonomy
 *
 * @package    UTK_Asset_Manager
 */

function am_display_manage_taxonomies() {
	global $hook_suffix;
	?>
	<div class="wrap">
		<h1><?php echo get_admin_page_title(); ?></h1>
		<p>
			<?php print_r( $hook_suffix ); ?>
		</p>
	</div>
	<?php
}
