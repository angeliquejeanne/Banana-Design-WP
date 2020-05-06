<div class="sui-box-settings-row" data-toggle-content="use-vanilla">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Border', 'hustle' ); ?></span>
		<span class="sui-description"><?php printf( esc_html__( 'This will add a customizable border to your %s.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<label for="hustle-module-border" class="sui-toggle hustle-toggle-with-container" data-toggle-on="border">
			<input type="checkbox"
				name="border"
				data-attribute="border"
				id="hustle-module-border"
				<?php checked( $settings['border'], '1' ); ?>
			/>
			<span class="sui-toggle-slider"></span>
		</label>

		<label for="hustle-module-border"><?php esc_html_e( 'Show border', 'hustle' ); ?></label>

		<div class="sui-border-frame sui-toggle-content" data-toggle-content="border">

			<div class="sui-row">

				<div class="sui-col-md-4">

					<div class="sui-form-field">

						<label for="hustle-module--border-radius" class="sui-label"><?php esc_html_e( 'Border radius', 'hustle' ); ?></label>

						<input type="number"
							value="<?php echo esc_attr( $settings['border_radius'] ); ?>"
							data-attribute="border_radius"
							id="hustle-module--border-radius"
							class="sui-form-control" />

					</div>

				</div>

				<div class="sui-col-md-4">

					<div class="sui-form-field">

						<label for="hustle-module--border-weight" class="sui-label"><?php esc_html_e( 'Border weight', 'hustle' ); ?></label>

						<input type="number"
							value="<?php echo esc_attr( $settings['border_weight'] ); ?>"
							data-attribute="border_weight"
							id="hustle-module--border-weight"
							class="sui-form-control" />

					</div>

				</div>

				<div class="sui-col-md-4">

					<div class="sui-form-field">

						<label for="hustle-module--border-type" class="sui-label"><?php esc_html_e( 'Border type', 'hustle' ); ?></label>

						<select id="hustle-module--border-type" data-attribute="border_type">
							<option value="solid" <?php selected( $settings['border_type'], 'solid' ); ?>><?php esc_attr_e( "Solid", 'hustle' ); ?></option>
							<option value="dotted" <?php selected( $settings['border_type'], 'dotted' ); ?>><?php esc_attr_e( "Dotted", 'hustle' ); ?></option>
							<option value="dashed" <?php selected( $settings['border_type'], 'dashed' ); ?>><?php esc_attr_e( "Dashed", 'hustle' ); ?></option>
							<option value="double" <?php selected( $settings['border_type'], 'double' ); ?>><?php esc_attr_e( "Double", 'hustle' ); ?></option>
							<option value="none" <?php selected( $settings['border_type'], 'none' ); ?>><?php esc_attr_e( "None", 'hustle' ); ?></option>
						</select>

					</div>

				</div>

			</div>

			<div class="sui-form-field">

				<label class="sui-label"><?php esc_html_e( 'Border color', 'hustle' ); ?></label>

				<?php Opt_In_Utils::sui_colorpicker( esc_attr( $module_type ) . '_modal_border', 'border_color', 'true', false, $settings['border_color'] ); ?>

			</div>

		</div>

	</div>

</div>
