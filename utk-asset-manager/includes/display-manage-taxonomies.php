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
	$action      = ( empty( $_GET['action'] ) ) ? 'new' : $_GET['action'];
	$action_type = ( ! 'edit' !== $action ) ? 'Add New' : 'Edit';
	$current     = null;
	$is_dev      = am_is_dev();
	$post_types  = am_get_registered_post_types();
	$taxes       = am_get_registered_taxes();
	$which       = am_which_page();
	$ui          = new AM_Admin_UI();
	?>

    <div class="asset-manager-wrapper">
        <h1><?php echo get_admin_page_title(); ?></h1>
        <form method="post" action="<?php esc_url( admin_url( 'admin-post.php' ) ); ?>" id="asset-manager-form">
            <div class="row">
                <div class="col-xs-12 col-lg-5">
                    <div class="section-wrapper">
                        <div class="section-header">
                            <h3><?php echo sprintf( __( '%s %s', AM_TEXT ), $action_type, $which ); ?></h3>
                        </div>
                        <div class="section-body">
							<?php wp_nonce_field( "am_form_nonce_action", "am_form_nonce_field" ); ?>
                            <input type="hidden" id="data_type" name="data_type" value="<?php echo strtolower( $which ); ?>">
                            <input type="hidden" id="page_action" name="page_action" value="<?php echo $action; ?>">
							<?php

							/**
							 * Make the singular label text field
							 *
							 * @since 0.5.0
							 */
							echo $ui->make_text_field( [
								'desc'        => esc_attr__( 'Please use only alphanumeric characters and spaces. This field also creates your asset type slug.', AM_TEXT ),
								'label_text'  => esc_attr__( 'Singular Label', AM_TEXT ),
								'maxlength'   => 20,
								'name'        => 'singular_label',
								'placeholder' => esc_attr__( '(e.g. Asset)', AM_TEXT ),
								'required'    => true,
								'value'       => ( ! empty( $current ) ) ? $current['singular_label'] : '',
							] );

							/**
							 * Make the plural label text field
							 *
							 * @since 0.5.0
							 */
							echo $ui->make_text_field( [
								'desc'        => esc_attr__( 'Please use only alphanumeric characters and spaces.', AM_TEXT ),
								'label_text'  => esc_attr__( 'Plural Label', AM_TEXT ),
								'maxlength'   => 20,
								'name'        => 'label',
								'placeholder' => esc_attr__( '(e.g. Assets)', AM_TEXT ),
								'required'    => true,
								'value'       => ( ! empty( $current ) ) ? $current['label'] : '',
							] );

							/**
							 * Make the name field, which is hidden
							 * and takes value from either the singular_label
							 * field (lowercased), or the value of $current['name'].
							 *
							 * @since 0.5.0
							 */
							echo $ui->make_text_field( [
								'desc'       => esc_attr__( 'This is the slug of your new metadata. It is generated by filling in the Singular Label fields above, but you may change it if necessary. Please use only letters, numbers, and the underscore character.', AM_TEXT ),
								'label_text' => esc_attr__( 'Name', AM_TEXT ),
								'maxlength'  => 20,
								'name'       => 'name',
								'value'      => ( ! empty( $current ) ) ? $current['name'] : '',
								'visible'    => true,
							] );
							?>
							<?php
							/**
							 * Is the custom content type public?
							 */
							$select['options'] = [
								[
									'text'  => esc_attr__( 'No', AM_TEXT ),
									'value' => '0'
								],
								[
									'default' => true,
									'text'    => esc_attr__( 'Yes', AM_TEXT ),
									'value'   => '1'
								]
							];

							$selected           = isset( $current ) ? am_coerce_bool( $current['public'] ) : '';
							$select['selected'] = ( ! empty( $selected ) ) ? $current['public'] : '';

							echo $ui->make_select_field( [
								'classes'    => false,
								'desc'       => esc_attr__( 'Allow this metadata to be displayed publicly?', AM_TEXT ),
								'label_text' => esc_attr__( 'Public', AM_TEXT ),
								'name'       => 'public',
								'selections' => $select,
							] );
							?>
							<?php
							/**
							 * Where should this asset type be displayed in the
							 * admin sidebar menu?
							 *
							 * @since 0.5.0
							 */
							$select['options'] = [
								[
									'default' => true,
									'text'    => esc_attr__( 'No', AM_TEXT ),
									'value'   => '0'
								],
								[
									'text'  => esc_attr__( 'Yes', AM_TEXT ),
									'value' => '1'
								]
							];

							$selected           = isset( $current ) ? am_coerce_bool( $current['show_in_menu'] ) : '';
							$select['selected'] = ( ! empty( $selected ) ) ? $current['show_in_menu'] : '';

							echo $ui->make_select_field( [
								'classes'    => false,
								'desc'       => esc_attr__( 'Make this asset type available in the top-level administration menu? Metadata will only be visible in the menu if the asset type to which the metadata is assigned is also set to be visible in the menu.', AM_TEXT ),
								'label_text' => esc_attr__( 'Show in Menu', AM_TEXT ),
								'name'       => 'show_in_menu',
								'selections' => $select,
							] );
							?>
							<?php
							/**
							 * Should this asset type be hierarchical, like pages?
							 *
							 * @since 0.5.0
							 */
							$select['options'] = [
								[
									'default' => true,
									'text'    => esc_attr__( 'No', AM_TEXT ),
									'value'   => '0'
								],
								[
									'text'  => esc_attr__( 'Yes', AM_TEXT ),
									'value' => '1'
								]
							];

							$selected           = isset( $current ) ? am_coerce_bool( $current['hierarchical'] ) : '';
							$select['selected'] = ( ! empty( $selected ) ) ? $current['hierarchical'] : '';

							echo $ui->make_select_field( [
								'classes'    => false,
								'desc'       => esc_attr__( 'Should assets of this type have parent/child relationships (like pages)?', AM_TEXT ),
								'label_text' => esc_attr__( 'Hierarchical', AM_TEXT ),
								'name'       => 'hierarchical',
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
							<?php
							if ( ! empty( $post_types ) ) {
								// TODO Change this so that it only displays taxonomies created through our plugin? Need to discuss.
								$checkboxes['boxes'] = [];

								foreach ( $post_types as $post_type ) {
									$checkboxes['boxes'][] = [
										'checked'    => false,
										'value'      => $post_type['name'],
										'value_type' => 'added_post_types',
										'label'      => $post_type['label'],
									];
								}
								echo $ui->make_checkboxes( [
									'description' => esc_attr__( 'Choose from available registered asset types to assign this metadata', AM_TEXT ),
									'label_text'  => esc_attr__( 'Asset Types', AM_TEXT ),
									'name'        => 'post_types',
									'checkboxes'  => $checkboxes,
								] );
							} else {
								?>
                                <div class="row">
                                    <div class="col">
                                        <p class="text-center"><?php echo __( 'No asset types have been created. You may continue with asset type creation and assign metadata later.',
												AM_TEXT ); ?></p>
                                    </div>
                                </div>
								<?php
							}
							?>
                        </div>
                        <div class="section-footer">
                            <input type="submit" class="button button-primary" id="am_submit" name="am_submit"
                                   value="<?php echo sprintf( __( '%s Metadata', AM_TEXT ), $action_type ); ?>">
                            <input type="reset" class="button button-secondary ml-2" value="<?php echo esc_attr__( 'Reset Form', AM_TEXT ); ?>">
                        </div>
                    </div>
                </div>
				<?php am_metadata_table( $is_dev, $post_types, $taxes ); ?>
            </div>
        </form>
    </div>
	<?php
}

/**
 * Output the metadata information table
 *
 * @param bool  $is_dev     are we on localhost?
 * @param array $post_types array of registered post types
 * @param array $taxes      array of registered taxonomies
 *
 * @since 0.5.0
 *
 */
function am_metadata_table( bool $is_dev, array $post_types, array $taxes ) {
	$url = 'admin.php?page=manage_metadata';
	?>
    <div class="col-xs-12 col-lg-7 table-col">
        <div class="section-wrapper at-a-glance">
            <div class="section-body asset-manager-table-wrapper">
                <table class="table table-striped asset-manager-table">
                    <thead>
                    <tr>
                        <th scope="col">
                            Metadata
                        </th>
                        <th scope="col">
                            Description
                        </th>
                        <th scope="col">
                            Asset Types
                        </th>
                        <th class="text-center" scope="col">
                            Terms
                        </th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ( $taxes as $tax ) {
						?>
                        <tr>
                            <th scope="row">
                                <a class="row-title" href="<?php echo admin_url( 'edit.php?taxonomy=' . $tax['name'] ); ?>">
                                    <strong><?php echo $tax['label']; ?></strong>
                                </a>
                                <br>
                                <ul class="row-options">
                                    <li><a class="button button-primary" href="<?php echo admin_url( $url . '&action=edit&name=' . $tax['name'] ); ?>">Edit</a></li>
                                    <li><a class="button button-primary" href="<?php echo admin_url( $url . '&action=delete&name=' . $tax['name'] ); ?>">Delete</a></li>
                                    <li><a class="button button-primary" href="<?php echo admin_url( 'edit.php?post_type=' . $tax['name'] ); ?>">View</a></li>
                                </ul>
                            </th>
                            <td>
								<?php echo $tax['description']; ?>
                            </td>
                            <td>
								<?php if ( ! empty( $tax['asset_types'] ) ) : ?>
                                    <ul class="assigned-post-types">
										<?php foreach ( $tax['asset_types'] as $post_type ) : ?>
                                            <li>
                                                <a href="<?php echo admin_url( 'edit.php?post_type=' . $post_type['name'] ); ?>">
													<?php echo $post_type['label']; ?>
                                                </a>
                                            </li>
										<?php endforeach; ?>
                                    </ul>
								<?php else : ?>
                                    No asset type assigned!
								<?php endif; ?>
                            </td>
                            <td>
								<?php
								$args  = [
									'taxonomy'   => $tax['name'],
									'hide_empty' => false,
								];
								$terms = wp_count_terms( $tax['name'] );

								if ( ! $terms ) {
									echo '0';
								} else {
									echo $terms;
								}
								?>
                            </td>
                        </tr>
						<?php
					}
					?>
                    </tbody>
                </table>
				<?php
				if ( $is_dev ) {
					?>
                    <pre style="margin: 15px;">
                        <?php print_r( $is_dev ); ?>
                    </pre>
					<?php
				}
				?>
            </div>
        </div>
    </div>
	<?php
}