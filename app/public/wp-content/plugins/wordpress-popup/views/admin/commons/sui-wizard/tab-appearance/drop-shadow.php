<div class="sui-box-settings-row" data-toggle-content="use-vanilla">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Drop Shadow', 'hustle' ); ?></span>
		<span class="sui-description"><?php printf( esc_html__( 'Add a shadow to your %s.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<label for="hustle-drop-shadow" class="sui-toggle hustle-toggle-with-container" data-toggle-on="drop-shadow">
			<input type="checkbox"
				name="drop_shadow"
				data-attribute="drop_shadow"
				id="hustle-drop-shadow"
				<?php checked( $settings['drop_shadow'], '1' ); ?>
			/>
			<span class="sui-toggle-slider"></span>
		</label>

		<label for="hustle-drop-shadow"><?php esc_html_e( 'Show drop shadow', 'hustle' ); ?></label>

		<div class="sui-border-frame sui-toggle-content" data-toggle-content="drop-shadow">

			<div class="sui-row">

				<div class="sui-col-md-3">

					<div class="sui-form-field">

						<label for="hustle-module--shadow-x" class="sui-label"><?php esc_html_e( 'X-offset', 'hustle' ); ?></label>

						<input type="number"
							value="<?php echo esc_attr( $settings['drop_shadow_x'] ); ?>"
							data-attribute="drop_shadow_x"
							id="hustle-module--shadow-x"
							class="sui-form-control" />

					</div>

				</div>

				<div class="sui-col-md-3">

					<div class="sui-form-field">

						<label for="hustle-module--shadow-y" class="sui-label"><?php esc_html_e( 'Y-offset', 'hustle' ); ?></label>

						<input type="number"
							value="<?php echo esc_attr( $settings['drop_shadow_y'] ); ?>"
							data-attribute="drop_shadow_y"
							id="hustle-module--shadow-y"
							class="sui-form-control" />

					</div>

				</div>

				<div class="sui-col-md-3">

					<div class="sui-form-field">

						<label for="hustle-module--shadow-blur" class="sui-label"><?php esc_html_e( 'Blur', 'hustle' ); ?></label>

						<input type="number"
							value="<?php echo esc_attr( $settings['drop_shadow_blur'] ); ?>"
							data-attribute="drop_shadow_blur"
							id="hustle-module--shadow-blur"
							class="sui-form-control" />

					</div>

				</div>

				<div class="sui-col-md-3">

					<div class="sui-form-field">

						<label for="hustle-module--shadow-spread" class="sui-label"><?php esc_html_e( 'Spread', 'hustle' ); ?></label>

						<input type="number"
							value="<?php echo esc_attr( $settings['drop_shadow_spread'] ); ?>"
							data-attribute="drop_shadow_spread"
							id="hustle-module--shadow-spread"
							class="sui-form-control" />

					</div>

				</div>

			</div>

			<div class="sui-form-field">

				<label id="<?php echo esc_attr( $module_type ); ?>_modal_shadow" class="sui-label"><?php esc_html_e( 'Color', 'hustle' ); ?></label>

				<?php Opt_In_Utils::sui_colorpicker( esc_attr( $module_type ) . '_modal_shadow', 'drop_shadow_color', 'true', false, $settings['drop_shadow_color'] ); ?>

			</div>

		</div>

	</div>

</div>
