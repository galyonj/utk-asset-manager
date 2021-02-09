<?php
/**
 * Display the admin settings page
 *
 * @since      0.0.2
 * @subpackage includes/display-manage-assets
 *
 * @package    UTK_Asset_Manager
 */

function am_display_manage_assets() {

	/**
	 * Get the status of the page to determine whether
	 * we're making a new cpt or editing an existing one.
	 */
	$status = 'new';
	$ui     = new AM_Admin_UI();
	?>
    <div class="asset-manager-wrapper">
        <h1><?php echo get_admin_page_title(); ?></h1>
        <hr>
        <div class="row">
            <div class="col-12 col-lg-4">
                <form method="post" action="<?php esc_url( admin_url( 'admin-post.php' ) ); ?>" id="utk-am-form">
					<?php wp_nonce_field( 'am_process_cpt_action' ); ?>
                    <input type="hidden" name="action" value="am_process_cpt">
                    <input type="hidden" name="am_cpt_status" id="am_cpt_status" value="<?php echo $status; ?>">
                    <input type="hidden" name="name" id="name">
                    <div class="form-section">
                        <div class="form-section-header">
                            <span><?php echo esc_html( 'Add New Asset' ); ?></span>
                        </div>
                        <div class="form-section-body">
							<?php
							echo $ui->make_text_field( [
								'desc'        => esc_attr__( 'Please use only alphanumeric characters and spaces', AM_TEXT ),
								'label_text'  => esc_attr__( 'Singular Label', AM_TEXT ),
								'maxlength'   => 20,
								'name'        => 'label_singular',
								'placeholder' => esc_attr__( '(e.g. Asset)', AM_TEXT ),
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
								'placeholder' => esc_attr__( '(e.g. Assets)', AM_TEXT ),
								'required'    => true,
								'value'       => ( ! empty( $current ) ) ? $current['label_plural'] : '',
							] );
							?>
							<?php
							echo $ui->make_input_group( [
								'btn_text'    => esc_attr__( 'Select Icon', AM_TEXT ),
								'desc'        => esc_attr__( 'Select an icon for your asset type', AM_TEXT ),
								'label_text'  => esc_attr__( 'Menu Icon', AM_TEXT ),
								'name'        => 'menu_icon',
								'placeholder' => 'wordpress_dashicons-admin-generic',
								'required'    => false,

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

							$selected           = isset( $current ) ? am_coerce_bool( $current['public'] ) : '';
							$select['selected'] = ( ! empty( $selected ) ) ? $current['public'] : '';

							echo $ui->make_select_field( [
								'classes'    => false,
								'desc'       => esc_attr__( 'Allow assets of this type to be displayed publicly?', AM_TEXT ),
								'label_text' => esc_attr__( 'Public', AM_TEXT ),
								'name'       => 'public',
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

							$selected           = isset( $current ) ? am_coerce_bool( $current['show_in_admin_bar'] ) : '';
							$select['selected'] = ( ! empty( $selected ) ) ? $current['show_in_admin_bar'] : '';

							echo $ui->make_select_field( [
								'classes'    => false,
								'desc'       => esc_attr__( 'Make this post type available from the + menu on the top administration bar', AM_TEXT ),
								'label_text' => esc_attr__( 'Show in Admin Bar', AM_TEXT ),
								'name'       => 'show_in_admin_bar',
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

							$selected           = isset( $current ) ? am_coerce_bool( $current['show_in_menu'] ) : '';
							$select['selected'] = ( ! empty( $selected ) ) ? $current['show_in_menu'] : '';

							echo $ui->make_select_field( [
								'classes'    => false,
								'desc'       => esc_attr__( 'Make this post type available in the administration menu', AM_TEXT ),
								'label_text' => esc_attr__( 'Show in Menu', AM_TEXT ),
								'name'       => 'show_in_menu',
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

							$selected           = isset( $current ) ? am_coerce_bool( $current['show_in_nav_menus'] ) : '';
							$select['selected'] = ( ! empty( $selected ) ) ? $current['show_in_nav_menus'] : '';

							echo $ui->make_select_field( [
								'classes'    => false,
								'desc'       => esc_attr__( 'Make this post type available for selection in navigation menus', AM_TEXT ),
								'label_text' => esc_attr__( 'Show in Nav Menus', AM_TEXT ),
								'name'       => 'show_in_nav_menus',
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

							$selected           = isset( $current ) ? am_coerce_bool( $current['set_expiration'] ) : '';
							$select['selected'] = ( ! empty( $selected ) ) ? $current['set_expiration'] : '';

							echo $ui->make_select_field( [
								'classes'    => false,
								'desc'       => esc_attr__( 'Set an expiration period for assets of this type?',
									AM_TEXT ),
								'label_text' => esc_attr__( 'Post Expiration', AM_TEXT ),
								'name'       => 'set_expiration',
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

							$selected           = isset( $current ) ? am_coerce_bool( $current['hierarchical'] ) : '';
							$select['selected'] = ( ! empty( $selected ) ) ? $current['hierarchical'] : '';

							echo $ui->make_select_field( [
								'classes'    => false,
								'desc'       => esc_attr__( 'Should assets of this type have parent/child relationships (like posts)?', AM_TEXT ),
								'label_text' => esc_attr__( 'Hierarchical', AM_TEXT ),
								'name'       => 'hierarchical',
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

							$selected           = isset( $current ) ? am_coerce_bool( $current['exclude_from_search'] ) : '';
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
							echo $ui->make_textarea_field( [
								'desc'        => esc_attr__( 'This may or may not appear on the front-end depending on theme settings.',
									AM_TEXT ),
								'label_text'  => esc_attr__( 'Description', AM_TEXT ),
								'name'        => 'description',
								'placeholder' => esc_attr__( 'Enter a short description of your asset type', AM_TEXT ),
								'rows'        => '4'
							] );
							?>
                        </div>
                        <div class="form-section-footer">
                            <input type="submit" class="button button-primary" id="new_asset" value="<?php echo esc_attr__( 'Add New Asset', AM_TEXT ); ?>">
                            <input type="reset" class="button button-secondary ml-2" value="<?php echo esc_attr__( 'Reset Form', AM_TEXT ); ?>">
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12 col-lg-8">
				<?php if ( am_is_dev() ) : ?>
                    <pre>
                        <?php print_r( is_string( 'true' ) ); ?>
                        <hr>
                        <?php print_r( $_POST ); ?>
                    </pre>
				<?php endif; ?>
                <table class="table table-striped table-bordered" id="utk-am-table">

                </table>
            </div>
        </div>
    </div>
	<?php

	am_icons_modal();
}


function am_icons_modal() {
	/**
	 * Get the svg codes array so that we can output
	 * it in the modal for selection.
	 *
	 * @since 0.0.5
	 *
	 * @uses  \am_get_svg_codes();
	 */
	$svgs = am_get_svg_codes();
	?>
    <div class="modal fade" id="iconsModal" tabindex="-1" aria-labelledby="iconsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="iconsModalLabel"><strong><?php esc_attr_e( 'Choose your menu icon', AM_TEXT ); ?></strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
						<?php
						if ( ! empty( $svgs ) && is_array( $svgs ) ) {
							$keys = array_keys( $svgs );
							for ( $i = 0; $i < count( $keys ); $i ++ ) {
								$key = ( 'dashicons' !== $keys[ $i ] ) ? 'Font Awesome ' . ucwords( $keys[ $i ] ) : 'WordPress ' . ucwords( $keys[ $i ] );
								?>
                                <div class="row">
                                    <div class="col">
                                        <h5 class="icon-title"><strong><?php echo $key; ?></strong></h5>
                                        <ul class="icons-list">
											<?php
											foreach ( $svgs[ $keys[ $i ] ] as $k => $v ) {
												?>
                                                <li class="icon" id="<?php echo $keys[ $i ] . '[' . $k . ']'; ?>">
													<?php echo $v; ?>
                                                </li>
												<?php
											}
											?>
                                        </ul>
                                    </div>
                                </div>
								<?php
							}
						}
						?>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <!--                        <div class="col selected-icon">-->
                        <!--                            Selected Icon: <span></span>-->
                        <!--                        </div>-->
                        <div class="col">
                            <button type="button" class="button button-primary" id="icon-select-btn">Save selection</button>
                            <button type="button" class="button button-secondary ml-2" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php
}