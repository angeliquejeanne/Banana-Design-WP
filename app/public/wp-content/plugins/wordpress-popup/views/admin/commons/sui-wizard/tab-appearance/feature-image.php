<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Feature Image', 'hustle' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Choose the feature image settings as per your liking.', 'hustle' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">


		<?php // SETTING: Position. ?>
		<div id="hustle-feature-image-position-option" class="sui-form-field">

			<span class="sui-settings-label"><?php esc_html_e( 'Position', 'hustle' ); ?></span>
			<span class="sui-description"><?php printf( esc_html__( 'Choose the position of your feature image relative to the content of the %s in your chosen layout.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

			<div class="sui-side-tabs"
				style="margin-top: 10px;">

				<div class="sui-tabs-menu">

					<label id="hustle-feature-image-left-label" for="hustle-feature-image-left" class="sui-tab-item">
						<input type="radio"
							name="feature_image_position"
							value="left"
							data-attribute="feature_image_position"
							id="hustle-feature-image-left"
							<?php checked( $settings['feature_image_position'], 'left' ); ?>
						/>
						<?php esc_attr_e( "Left", 'hustle' ); ?>
					</label>

					<?php if ( $is_optin ) { ?>

						<label id="hustle-feature-image-above-label" for="hustle-feature-image-above" class="sui-tab-item<?php echo 'one' !== $settings['form_layout'] ? ' sui-hidden' : ''; ?>">
							<input type="radio"
								name="feature_image_position"
								value="above"
								data-attribute="feature_image_position"
								id="hustle-feature-image-above"
								<?php checked( $settings['feature_image_position'], 'above' ); ?>
							/>
							<?php esc_attr_e( "Above Content", 'hustle' ); ?>
						</label>

						<label id="hustle-feature-image-below-label" for="hustle-feature-image-below" class="sui-tab-item<?php echo 'one' !== $settings['form_layout'] ? ' sui-hidden' : ''; ?>">
							<input type="radio"
								name="feature_image_position"
								value="below"
								data-attribute="feature_image_position"
								id="hustle-feature-image-below"
								<?php checked( $settings['feature_image_position'], 'below' ); ?>
								/>
							<?php esc_attr_e( "Below Content", 'hustle' ); ?>
						</label>

					<?php } ?>

					<label id="hustle-feature-image-right-label" for="hustle-feature-image-right" class="sui-tab-item">
						<input type="radio"
							name="feature_image_position"
							value="right"
							data-attribute="feature_image_position"
							id="hustle-feature-image-right"
							<?php checked( $settings['feature_image_position'], 'right' ); ?>
						/>
						<?php esc_attr_e( "Right", 'hustle' ); ?>
					</label>

				</div>

			</div>

		</div>

		<div id="hustle-appearance-feature-image-settings" <?php if ( empty( $feature_image ) ) echo ' style="display:none;"'; ?>>

			<?php // SETTING: Fitting. ?>
			<div class="sui-form-field">

				<span class="sui-settings-label"><?php esc_html_e( 'Fitting', 'hustle' ); ?></span>
				<span class="sui-description"><?php printf( esc_html__( 'Choose the feature image fitting type. You can preview the %s to check how each option affects the feature image.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

				<div class="sui-side-tabs"
					style="margin-top: 10px;">

					<div class="sui-tabs-menu">

						<label for="hustle-feature-image-cover" class="sui-tab-item">
							<input type="radio"
								name="feature_image_fit"
								data-attribute="feature_image_fit"
								value="cover"
								id="hustle-feature-image-cover"
								data-tab-menu="hustle-focus-image"
								<?php checked( $settings['feature_image_fit'], 'cover' ); ?>
							/>
							<?php esc_attr_e( "Cover", 'hustle' ); ?>
						</label>

						<label for="hustle-feature-image-fill" class="sui-tab-item">
							<input type="radio"
								name="feature_image_fit"
								data-attribute="feature_image_fit"
								value="fill"
								id="hustle-feature-image-fill"
								<?php checked( $settings['feature_image_fit'], 'fill' ); ?>
							/>
							<?php esc_attr_e( "Fill", 'hustle' ); ?>
						</label>

						<label for="hustle-feature-image-contain" class="sui-tab-item">
							<input type="radio"
								name="feature_image_fit"
								data-attribute="feature_image_fit"
								value="contain"
								id="hustle-feature-image-contain"
								data-tab-menu="hustle-focus-image"
								<?php checked( $settings['feature_image_fit'], 'contain' ); ?>
							/>
							<?php esc_attr_e( "Contain", 'hustle' ); ?>
						</label>

						<label for="hustle-feature-image-none" class="sui-tab-item">
							<input type="radio"
								name="feature_image_fit"
								data-attribute="feature_image_fit"
								value="none"
								id="hustle-feature-image-none"
								<?php checked( $settings['feature_image_fit'], 'none' ); ?>
							/>
							<?php esc_attr_e( "None", 'hustle' ); ?>
						</label>

					</div>

					<div class="sui-tabs-content">

						<div class="sui-tab-content sui-tab-boxed" data-tab-content="hustle-focus-image">

							<?php
							self::static_render( 'admin/commons/sui-wizard/elements/focal-point', array(
								'feature_image' => $feature_image,
								'settings'      => $settings,
							) );
							?>

						</div>

					</div>

				</div>

			</div>

			<?php // OPTION: Visibility on mobile. ?>
			<div class="sui-form-field">

				<span class="sui-settings-label"><?php esc_html_e( 'Visibility on mobile', 'hustle' ); ?></span>
				<span class="sui-description"><?php esc_html_e( 'Make the feature image visibile or hidden on mobile devices.', 'hustle' ); ?></span>

				<div class="sui-side-tabs"
					style="margin-top: 10px;">

					<div class="sui-tabs-menu">

						<label for="hustle-feature-image-visible" class="sui-tab-item">
							<input type="radio"
								name="feature_image_hide_on_mobile"
								data-attribute="feature_image_hide_on_mobile"
								value="0"
								id="hustle-feature-image-visible"
								<?php checked( $settings['feature_image_hide_on_mobile'], '0' ); ?>
							/>
							<?php esc_attr_e( "Visible", 'hustle' ); ?>
						</label>

						<label for="hustle-feature-image-hidden" class="sui-tab-item">
							<input type="radio"
								name="feature_image_hide_on_mobile"
								data-attribute="feature_image_hide_on_mobile"
								value="1"
								id="hustle-feature-image-hidden"
								<?php checked( $settings['feature_image_hide_on_mobile'], '1' ); ?>
							/>
							<?php esc_attr_e( "Hidden", 'hustle' ); ?>
						</label>

					</div>

				</div>

			</div>

		</div>

		<div id="hustle-appearance-feature-image-placeholder"<?php echo ( ! empty( $feature_image ) ) ? ' style="display:none;"' : ''; ?>>

			<div class="sui-notice">
				<p><?php esc_html_e( "There's no feature image. Upload an image in the \"Content\" tab to adjust fitting and image visibility on mobiles.", 'hustle' ); ?></p>
			</div>

		</div>

	</div>

</div>
