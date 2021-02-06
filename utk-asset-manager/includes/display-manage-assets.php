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
	$ui = new AM_Admin_UI();
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
									echo $ui->make_text_field( [
										'desc'        => esc_attr__( 'Please use only alphanumeric characters and spaces', AM_TEXT ),
										'label_text'  => esc_attr__( 'Singular Label', AM_TEXT ),
										'maxlength'   => 32,
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
										'maxlength'   => 32,
										'name'        => 'label_plural',
										'placeholder' => esc_attr__( '(e.g. Method)', AM_TEXT ),
										'required'    => true,
										'value'       => ( ! empty( $current ) ) ? $current['label_plural'] : '',
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
										'desc'       => esc_attr__( 'Should assets of this type ever be displayed publicly?', AM_TEXT ),
										'label_text' => esc_attr__( 'Public', AM_TEXT ),
										'name'       => 'public',
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
                                    <input type="submit" class="button button-primary" value="<?php echo esc_attr__( 'Add New Asset', AM_TEXT ); ?>">
                                </div>
                            </div> <!-- form-section -->
                        </form>
                    </div>
                </form>
            </div>
            <div class="col-sm-8">
				<?php if ( is_dev() ) : ?>
                    <pre>
                        <?php echo is_dev(); ?>
                        <hr>
                        <?php $icon = 'dashicons_edit';
                        print_r( get_svg_codes( $icon ) ); ?>
                    </pre>
				<?php endif; ?>
            </div>
        </div>
    </div>
	<?php
}
