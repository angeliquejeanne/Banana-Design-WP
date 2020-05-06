<div class="sui-box-settings-row" data-toggle-content="use-vanilla">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php printf( esc_html__( 'Custom %s Size', 'hustle' ), esc_html( $capitalize_singular ) ); ?></span>
		<span class="sui-description"><?php printf( esc_html__( 'Choose a custom size for your %s.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<label for="hustle-customize-size" class="sui-toggle hustle-toggle-with-container" data-toggle-on="customize-size">
			<input type="checkbox"
				name="customize_size"
				data-attribute="customize_size"
				id="hustle-customize-size"
				<?php checked( $settings['customize_size'], '1' ); ?>
			/>
			<span class="sui-toggle-slider"></span>
		</label>

		<label for="hustle-customize-size"><?php esc_html_e( 'Enable custom size', 'hustle' ); ?></label>

		<div class="sui-toggle-content" data-toggle-content="customize-size">

			<div class="sui-border-frame" style="margin-bottom: 10px;">

				<div class="sui-form-field">

					<label class="sui-label"><?php esc_html_e( 'Apply to', 'hustle' ); ?></label>

					<div class="sui-side-tabs" style="margin-bottom: 10px;">

						<div class="sui-tabs-menu">

							<label for="hustle-module--desktop-custom-size" class="sui-tab-item">
								<input type="radio"
									name="apply_custom_size_to"
									data-attribute="apply_custom_size_to"
									value="desktop"
									id="hustle-module--desktop-custom-size"
									<?php checked( $settings['apply_custom_size_to'], 'desktop' ); ?>
								/>
								<?php esc_html_e( 'Desktop Only', 'hustle' ); ?>
							</label>

							<label for="hustle-module--all-custom-size" class="sui-tab-item">
								<input type="radio"
									name="apply_custom_size_to"
									data-attribute="apply_custom_size_to"
									value="all"
									id="hustle-module--all-custom-size"
									<?php checked( $settings['apply_custom_size_to'], 'all' ); ?>
								/>
								<?php esc_html_e( 'All Devices', 'hustle' ); ?>
							</label>

						</div>

					</div>

					<span class="sui-description"><?php printf( esc_html__( "We recommend applying the custom size to Desktop only. We'll resize the %s on the smaller devices and keep it responsive.", 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

				</div>

				<div class="sui-row">

					<div class="sui-col-md-6">

						<div class="sui-form-field">

							<label class="sui-label"><?php esc_html_e( 'Width', 'hustle' ); ?> (px)</label>

							<input type="number"
								value="<?php echo esc_attr( $settings['custom_width'] ); ?>"
								data-attribute="custom_width"
								class="sui-form-control" />

						</div>

					</div>

					<div class="sui-col-md-6">

						<div class="sui-form-field">

							<label class="sui-label"><?php esc_html_e( 'Height', 'hustle' ); ?> (px)</label>

							<input type="number"
								value="<?php echo esc_attr( $settings['custom_height'] ); ?>"
								data-attribute="custom_height"
								class="sui-form-control" />

						</div>

					</div>

				</div>

			</div>

			<span class="sui-description"><?php printf( esc_html__( 'Use Preview to ensure your %s looks good on the choosen custom size.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

		</div>

	</div>

</div>
