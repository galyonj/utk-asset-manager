<?php

/**
 * UI for the icon selection modal triggered on
 * the manage assets page
 *
 * @since      0.0.5
 * @package    UTK_Asset_Manager
 * @subpackage includes/icons-modal
 */
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

                        <div class="col">
                            <div class="col selected-icon">
                                Selected Icon: <span></span>
                            </div>
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