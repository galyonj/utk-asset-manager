<?php
/**
 * Display the admin settings page
 *
 * @since      0.0.1
 * @subpackage includes/display-manage-assets
 *
 * @package    UTK_Asset_Manager
 */

function am_display_manage_assets() {
	global $hook_suffix;
	?>
	<div class="wrap">
		<h1><?php echo get_admin_page_title(); ?></h1>
		<hr>
		<div class="row">
			<div class="col-sm-4">
				<form method="post" action="<?php esc_url( admin_url( 'admin-post.php' ) ); ?>" id="utk-am-form">

				</form>
			</div>
			<div class="col-sm-8">

			</div>
		</div>
	</div>
	<?php
}
