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
	$base_dir = dirname( plugin_dir_path( __FILE__ ) ) . '/assets/svgs';
	$dirs     = array_diff( scandir( $base_dir ), [ '.', '..', 'brands' ] );
	$replace  = [
		'<svg height="50" width="50"',
		'<path fill="black"',
	];
	$search   = [
		'<svg',
		'<path',
	];
	$svg_arr  = [];
	$ui       = new AM_Admin_UI();

	foreach ( $dirs as $dir ) {
		$files = array_diff( scandir( "{$base_dir}/{$dir}/" ), [ '.', '..' ] );

		$svg_arr[ $dir ] = [];

		foreach ( $files as $file ) {
			$svg_file = pathinfo( "{$base_dir}/{$dir}/{$file}" );
			$contents = str_replace( $search, $replace, file_get_contents( "{$base_dir}/{$dir}/{$file}" ) );

			$svg_arr[ $dir ][ $svg_file['filename'] ] = $contents;
		}

	}
	?>
    <div class="asset-manager-wrapper">
        <h1><?php echo get_admin_page_title(); ?></h1>
        <hr>
        <div class="row">
            <div class="col-sm-4">
                <form method="post" action="<?php esc_url( admin_url( 'admin-post.php' ) ); ?>" id="utk-am-form">
                    <div class="form-section">
                        <form method="post" action="<?php esc_url( admin_url( 'admin-post.php' ) ); ?>"
                              id="utk-am-form">
                            <div class="form-section">
                                <div class="form-section-header">
                                    <span><?php echo esc_html( 'Add New Asset' ); ?></span>
                                </div>
                                <div class="form-section-body">
									<?php
									/**
									 * Singular Label
									 * Plural Label
									 * Menu Icon
									 * Hide From Search
									 * Post Expiration
									 * Public
									 * Hierarchical
									 * Show in Nav Menus
									 * Description
									 */
									echo $ui->make_text_field( [
										'desc'        => esc_attr__( 'Please use only alphanumeric characters and spaces', AM_TEXT ),
										'label_text'  => esc_attr__( 'Singular Label', AM_TEXT ),
										'maxlength'   => 20,
										'name'        => 'label_singular',
										'placeholder' => esc_attr__( '(e.g. Method)', AM_TEXT ),
										'required'    => true,
										'value'       => ( ! empty( $current ) ) ? $current['label_singular'] : '',
									] );
									?>
									<?php
									echo $ui->make_text_field( [
										'desc'        => esc_attr__( 'Please use only alphanumeric characters and spaces', AM_TEXT ),
										'label_text'  => esc_attr__( 'Plural Label', AM_TEXT ),
										'maxlength'   => 20,
										'name'        => 'label_plural',
										'placeholder' => esc_attr__( '(e.g. Method)', AM_TEXT ),
										'required'    => true,
										'value'       => ( ! empty( $current ) ) ? $current['label_plural'] : '',
									] );
									?>
									<?php
									echo $ui->make_input_group( [
										'desc'       => esc_attr__( 'You must select an icon for your asset type', AM_TEXT ),
										'label_text' => esc_attr__( 'Menu Icon', AM_TEXT ),
										'name'       => 'menu_icon',
										'required'   => false,
										'btn_text'   => esc_attr__( 'Select Icon', AM_TEXT ),

									] );
									?>
									<?php
									$select['options'] = [
										[
											'text'  => esc_attr__( 'No', AM_TEXT ),
											'value' => 'false'
										],
										[
											'default' => true,
											'text'    => esc_attr__( 'Yes', AM_TEXT ),
											'value'   => 'true'
										]
									];

									$selected           = isset( $current ) ? coerce_bool( $current['public'] ) : '';
									$select['selected'] = ( ! empty( $selected ) ) ? $current['public'] : '';

									echo $ui->make_select_field( [
										'classes'    => false,
										'desc'       => esc_attr__( 'Allow assets of this type to be displayed publicly', AM_TEXT ),
										'label_text' => esc_attr__( 'Public', AM_TEXT ),
										'name'       => 'public',
										'selections' => $select,
									] );
									?>
									<?php
									$select['options'] = [
										[
											'default' => true,
											'text'    => esc_attr__( 'No', AM_TEXT ),
											'value'   => 'false'
										],
										[
											'text'  => esc_attr__( 'Yes', AM_TEXT ),
											'value' => 'true'
										]
									];

									$selected           = isset( $current ) ? coerce_bool( $current['exclude_from_search'] ) : '';
									$select['selected'] = ( ! empty( $selected ) ) ? $current['exclude_from_search'] : '';

									echo $ui->make_select_field( [
										'classes'    => false,
										'desc'       => esc_attr__( 'Do not allow this asset type to appear in front-end search results', AM_TEXT ),
										'label_text' => esc_attr__( 'Hide in Search', AM_TEXT ),
										'name'       => 'exclude_from_search',
										'selections' => $select,
									] );
									?>
									<?php
									$select['options'] = [
										[
											'text'  => esc_attr__( 'No', AM_TEXT ),
											'value' => 'false'
										],
										[
											'default' => true,
											'text'    => esc_attr__( 'Yes', AM_TEXT ),
											'value'   => 'true'
										]
									];

									$selected           = isset( $current ) ? coerce_bool( $current['set_expiration'] ) : '';
									$select['selected'] = ( ! empty( $selected ) ) ? $current['set_expiration'] : '';

									echo $ui->make_select_field( [
										'classes'    => false,
										'desc'       => esc_attr__( 'Add the ability to set an expiration period for assets of this type',
											AM_TEXT ),
										'label_text' => esc_attr__( 'Expiration', AM_TEXT ),
										'name'       => 'set_expiration',
										'selections' => $select,
									] );
									?>
									<?php
									echo $ui->make_textarea_field( [
										'desc'       => esc_attr__( '(Optional) Enter a short description of your asset type', AM_TEXT ),
										'label_text' => esc_attr__( 'Description', AM_TEXT ),
										'name'       => 'description',
										'rows'       => '4'
									] );
									?>
                                </div>
                                <div class="form-section-footer">
                                    <hr>
                                    <button type="submit" class="button button-primary"><?php echo esc_attr__( 'Add New Asset', AM_TEXT ); ?></button>
                                </div>
                            </div> <!-- form-section -->
                        </form>
                    </div>
                </form>
            </div>
            <div class="col-sm-8">
				<?php if ( is_dev() ) {

					echo '<pre>';
					print_r( $svg_arr['dashicons'] );
					echo '</pre>';
				} ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="iconsModal" tabindex="-1" aria-labelledby="iconsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="iconsModalLabel"><strong><?php esc_attr_e( 'Choose your menu icon', AM_TEXT ); ?></strong></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
						<?php
						if ( ! empty( $svg_arr ) && is_array( $svg_arr ) ) {
							$keys = array_keys( $svg_arr );
							foreach ( $keys as $key ) {
								$key = ( 'dashicons' !== $key ) ? 'FontAwesome ' . ucwords( $key ) . ' Icons' : ucwords( $key );
								?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h3><?php echo $key; ?></h3>
                                    </div>
                                </div>
								<?php
							}
						}
						?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button button-primary">Save selection</button>
                    <button type="button" class="button button-secondary ml-2" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
	<?php
}
