<?php
$is_popup = Hustle_Module_Model::POPUP_MODULE === $module_type;

?>
<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Additional Closing Methods', 'hustle' ); ?></span>
		<span class="sui-description"><?php printf( esc_html__( 'Choose the additional closing methods for your %s apart from closing it by clicking on “x”.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<?php // SETTINGS: Auto Close. ?>

		<div class="sui-form-field">

			<label for="hustle-methods--auto-hide" class="sui-toggle hustle-toggle-with-container" data-toggle-on="auto-hide">
				<input type="checkbox"
					id="hustle-methods--auto-hide"
					name="auto_hide" 
					data-attribute="auto_hide"
					<?php checked( $settings['auto_hide'], '1' ); ?>
				/>
				<span class="sui-toggle-slider"></span>
			</label>

			<label for="hustle-methods--auto-hide"><?php printf( esc_html__( 'Auto-Close %s', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></label>

			<span class="sui-description sui-toggle-description" style="margin-top: 0;"><?php printf( esc_html__( 'This will automatically close your %s after specified time.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

			<div class="sui-border-frame sui-toggle-content" data-toggle-content="auto-hide">

				<label class="sui-label"><?php printf( esc_html__( 'Automatically close %s after', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></label>

				<div class="sui-row">

					<div class="sui-col-md-6">

						<input type="number"
							value="<?php echo esc_attr( $settings['auto_hide_time'] ); ?>"
							min="1"
							class="sui-form-control"
							name="auto_hide_time"
							data-attribute="auto_hide_time">

					</div>

					<div class="sui-col-md-6">

						<select name="auto_hide_unit" data-attribute="auto_hide_unit">

							<option value="seconds"
								<?php selected( $settings['auto_hide_unit'], 'seconds' ); ?>>
								<?php esc_html_e( 'seconds', 'hustle' ); ?>
							</option>

							<option value="minutes"
								<?php selected( $settings['auto_hide_unit'], 'minutes' ); ?>>
								<?php esc_html_e( 'minutes', 'hustle' ); ?>
							</option>

							<option value="hours"
								<?php selected( $settings['auto_hide_unit'], 'hours' ); ?>>
								<?php esc_html_e( 'hours', 'hustle' ); ?>
							</option>

						</select>

					</div>

				</div>

			</div>

		</div>

		<?php
		// SETTINGS: Close when click outside.
		if ( Hustle_Module_Model::POPUP_MODULE === $module_type ) :
			?>

			<div class="sui-form-field">

				<label for="hustle-methods--close-mask" class="sui-toggle hustle-toggle-with-container" data-toggle-on="close-on-background-click">
					<input type="checkbox"
						id="hustle-methods--close-mask"
						name="close_on_background_click"
						data-attribute="close_on_background_click"
						<?php checked( $settings['close_on_background_click'], '1' ); ?>
					/>
					<span class="sui-toggle-slider"></span>
				</label>

				<label for="hustle-methods--close-mask"><?php printf( esc_html__( 'Close %1$s when clicked outside', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></label>

				<span class="sui-description sui-toggle-description" style="margin-top: 0;"><?php printf( esc_html__( 'This will close the %1$s when a user clicks anywhere outside of the %1$s.', 'hustle' ), esc_html( $smallcaps_singular ), esc_html( $smallcaps_singular ) ); ?></span>

			</div>

		<?php endif; ?>

		<?php // SETTINGS: Close after CTA conversion. ?>

		<div class="sui-form-field" data-toggle-content="show-cta">

			<label for="hustle-close-cta" class="sui-toggle hustle-toggle-with-container" data-toggle-on="close-cta">
				<input type="checkbox"
					id="hustle-close-cta"
					name="close_cta"
					data-attribute="close_cta"
					<?php checked( $settings['close_cta'], '1' ); ?>
				/>
				<span class="sui-toggle-slider"></span>
			</label>
			
			<label for="hustle-close-cta"><?php printf( esc_html__( 'Close %s after CTA conversion', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></label>

			<span class="sui-description sui-toggle-description"><?php printf( esc_html__( 'Choose whether to close the %s after a user has clicked on the CTA button.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

			<div class="sui-border-frame sui-toggle-content" data-toggle-content="close-cta">

				<label class="sui-label"><?php esc_html_e( 'Add delay', 'hustle' ); ?></label>

				<div class="sui-row">

					<div class="sui-col-md-6">

						<input type="number"
							value="<?php echo esc_attr( $settings['close_cta_time'] ); ?>"
							min="1"
							class="sui-form-control"
							name="close_cta_time"
							data-attribute="close_cta_time">

					</div>

					<div class="sui-col-md-6">

						<select name="close_cta_unit" data-attribute="close_cta_unit">

							<option value="seconds"
								<?php selected( $settings['close_cta_unit'], 'seconds' ); ?>>
								<?php esc_html_e( 'seconds', 'hustle' ); ?>
							</option>

							<option value="minutes"
								<?php selected( $settings['close_cta_unit'], 'minutes' ); ?>>
								<?php esc_html_e( 'minutes', 'hustle' ); ?>
							</option>

							<option value="hours"
								<?php selected( $settings['close_cta_unit'], 'hours' ); ?>>
								<?php esc_html_e( 'hours', 'hustle' ); ?>
							</option>

						</select>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>
