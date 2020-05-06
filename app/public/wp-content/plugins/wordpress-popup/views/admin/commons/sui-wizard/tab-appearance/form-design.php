<div class="sui-box-settings-row" data-toggle-content="use-vanilla">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Form Design', 'hustle' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Choose the settings for your opt-in form as per your liking.', 'hustle' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<?php
		// SETTING: Form fields style ?>
		<div class="sui-form-field">

			<label class="sui-label"><?php esc_html_e( 'Form fields style', 'hustle' ); ?></label>

			<div class="sui-side-tabs">

				<div class="sui-tabs-menu">

					<label for="hustle-field-style--flat" class="sui-tab-item">
						<input type="radio"
							name="form_fields_style"
							data-attribute="form_fields_style"
							value="flat"
							id="hustle-field-style--flat"
							<?php checked( $settings['form_fields_style'], 'flat' ); ?>
						/>
						<?php esc_html_e( 'Flat', 'hustle' ); ?>
					</label>

					<label for="hustle-field-style--outlined" class="sui-tab-item">
						<input type="radio"
							name="form_fields_style"
							data-attribute="form_fields_style"
							value="outlined"
							id="hustle-field-style--outlined"
							data-tab-menu="hustle-field-style"
							<?php checked( $settings['form_fields_style'], 'outlined' ); ?>
						/>
						<?php esc_html_e( 'Outlined', 'hustle' ); ?>
					</label>

				</div>

				<div class="sui-tabs-content">

					<div class="sui-tab-content sui-tab-boxed" data-tab-content="hustle-field-style">

						<div class="sui-row">

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--form-border-radius" class="sui-label"><?php esc_html_e( 'Radius', 'hustle' ); ?></label>

									<input type="number"
										value="<?php echo esc_attr( $settings['form_fields_border_radius'] ); ?>"
										data-attribute="form_fields_border_radius"
										id="hustle-module--form-border-radius"
										class="sui-form-control" />

								</div>

							</div>

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--form-border-weight" class="sui-label"><?php esc_html_e( 'Weight', 'hustle' ); ?></label>

									<input type="number"
										value="<?php echo esc_attr( $settings['form_fields_border_weight'] ); ?>"
										data-attribute="form_fields_border_weight"
										id="hustle-module--form-border-weight"
										class="sui-form-control" />

								</div>

							</div>

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--form-border-type" class="sui-label"><?php esc_html_e( 'Border type', 'hustle' ); ?></label>

									<select id="hustle-module--form-border-type" data-attribute="form_fields_border_type">
										<option value="solid" <?php selected( $settings['form_fields_border_type'], 'solid' ); ?>><?php esc_attr_e( "Solid", 'hustle' ); ?></option>
										<option value="dotted" <?php selected( $settings['form_fields_border_type'], 'dotted' ); ?>><?php esc_attr_e( "Dotted", 'hustle' ); ?></option>
										<option value="dashed" <?php selected( $settings['form_fields_border_type'], 'dashed' ); ?>><?php esc_attr_e( "Dashed", 'hustle' ); ?></option>
										<option value="double" <?php selected( $settings['form_fields_border_type'], 'double' ); ?>><?php esc_attr_e( "Double", 'hustle' ); ?></option>
										<option value="none" <?php selected( $settings['form_fields_border_type'], 'none' ); ?>><?php esc_attr_e( "None", 'hustle' ); ?></option>
									</select>

								</div>

							</div>

						</div>

						<span class="sui-description"><?php esc_html_e( 'Note: Set the color of the border in the Colors Palette area below.', 'hustle' ); ?></span>

					</div>

				</div>

			</div>

		</div>

		<?php // SETTING: Form field icon. ?>
		<div class="sui-form-field">

			<label class="sui-label"><?php esc_html_e( 'Form field icon', 'hustle' ); ?></label>

			<div class="sui-side-tabs">

				<div class="sui-tabs-menu">

					<label for="hustle-field-icon--none" class="sui-tab-item">
						<input type="radio"
							name="form_fields_icon"
							data-attribute="form_fields_icon"
							value="none"
							id="hustle-field-icon--none"
							<?php checked( $settings['form_fields_icon'], 'none' ); ?>
						/>
						<?php esc_html_e( 'No icon', 'hustle' ); ?>
					</label>

					<label for="hustle-field-icon--static" class="sui-tab-item">
						<input type="radio"
							name="form_fields_icon"
							data-attribute="form_fields_icon"
							value="static"
							id="hustle-field-icon--static"
							<?php checked( $settings['form_fields_icon'], 'static' ); ?>
						/>
						<?php esc_html_e( 'Static icon', 'hustle' ); ?>
					</label>

					<label for="hustle-field-icon--animated" class="sui-tab-item">
						<input type="radio"
							name="form_fields_icon"
							data-attribute="form_fields_icon"
							value="animated"
							id="hustle-field-icon--animated"
							<?php checked( $settings['form_fields_icon'], 'animated' ); ?>
						/>
						<?php esc_html_e( 'Animated icon', 'hustle' ); ?>
					</label>

				</div>

			</div>

		</div>

		<?php // SETTING: Form fields proximity. ?>
		<div class="sui-form-field">

			<label class="sui-label"><?php esc_html_e( 'Form fields proximity', 'hustle' ); ?></label>

			<div class="sui-side-tabs">

				<div class="sui-tabs-menu">

					<label for="hustle-field-proximity--separated" class="sui-tab-item">
						<input type="radio"
							name="form_fields_proximity"
							data-attribute="form_fields_proximity"
							value="separated"
							id="hustle-field-proximity--separated"
							<?php checked( $settings['form_fields_proximity'], 'separated' ); ?>
						/>
						<?php esc_html_e( 'Separated', 'hustle' ); ?>
					</label>

					<label for="hustle-field-proximity--joined" class="sui-tab-item">
						<input type="radio"
							name="form_fields_proximity"
							data-attribute="form_fields_proximity"
							value="joined"
							id="hustle-field-proximity--joined"
							<?php checked( $settings['form_fields_proximity'], 'joined' ); ?>
						/>
						<?php esc_html_e( 'Joined', 'hustle' ); ?>
					</label>

				</div>

			</div>

		</div>

		<?php // SETTING: Button style. ?>
		<div class="sui-form-field">

			<label class="sui-label"><?php esc_html_e( 'Button style', 'hustle' ); ?></label>

			<div class="sui-side-tabs">

				<div class="sui-tabs-menu">

					<label for="hustle-button-style--flat" class="sui-tab-item">
						<input type="radio"
							name="button_style"
							data-attribute="button_style"
							value="flat"
							id="hustle-button-style--flat"
							<?php checked( $settings['button_style'], 'flat' ); ?>
						/>
						<?php esc_html_e( 'Flat', 'hustle' ); ?>
					</label>

					<label for="hustle-button-style--outlined" class="sui-tab-item">
						<input type="radio"
							name="button_style"
							data-attribute="button_style"
							value="outlined"
							id="hustle-button-style--outlined"
							data-tab-menu="hustle-button-style"
							<?php checked( $settings['button_style'], 'outlined' ); ?>
						/>
						<?php esc_html_e( 'Outlined', 'hustle' ); ?>
					</label>

				</div>

				<div class="sui-tabs-content">

					<div class="sui-tab-content sui-tab-boxed" data-tab-content="hustle-button-style">

						<div class="sui-row">

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--button-border-radius" class="sui-label"><?php esc_html_e( 'Radius', 'hustle' ); ?></label>

									<input type="number"
										value="<?php echo esc_attr( $settings['button_border_radius'] ); ?>"
										data-attribute="button_border_radius"
										id="hustle-module--button-border-radius"
										class="sui-form-control" />

								</div>

							</div>

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--button-border-weight" class="sui-label"><?php esc_html_e( 'Weight', 'hustle' ); ?></label>

									<input type="number"
										value="<?php echo esc_attr( $settings['button_border_weight'] ); ?>"
										data-attribute="button_border_weight"
										id="hustle-module--button-border-weight"
										class="sui-form-control" />

								</div>

							</div>

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--button-border-type" class="sui-label"><?php esc_html_e( 'Border type', 'hustle' ); ?></label>

									<select id="hustle-module--button-border-type"
										data-attribute="button_border_type">

										<option value="solid" <?php selected( $settings['button_border_type'], 'solid' ); ?>>
											<?php esc_attr_e( 'Solid', 'hustle' ); ?>
										</option>

										<option value="dotted" <?php selected( $settings['button_border_type'], 'dotted' ); ?>>
											<?php esc_attr_e( 'Dotted', 'hustle' ); ?>
										</option>

										<option value="dashed" <?php selected( $settings['button_border_type'], 'dashed' ); ?>>
											<?php esc_attr_e( 'Dashed', 'hustle' ); ?>
										</option>

										<option value="double" <?php selected( $settings['button_border_type'], 'double' ); ?>>
											<?php esc_attr_e( 'Double', 'hustle' ); ?>
										</option>

										<option value="none" <?php selected( $settings['button_border_type'], 'none' ); ?>>
											<?php esc_attr_e( 'None', 'hustle' ); ?>
										</option>

									</select>

								</div>

							</div>

						</div>

						<span class="sui-description"><?php esc_html_e( 'Note: Set the color of the border in the Colors Palette area below.', 'hustle' ); ?></span>

					</div>

				</div>

			</div>

		</div>

		<?php // SETTING: Checkbox style. ?>
		<div class="sui-form-field">

			<label class="sui-label"><?php esc_html_e( 'Checkbox style', 'hustle' ); ?></label>

			<div class="sui-side-tabs">

				<div class="sui-tabs-menu">

					<label for="hustle-gdpr-style--flat" class="sui-tab-item">
						<input type="radio"
							name="gdpr_checkbox_style"
							data-attribute="gdpr_checkbox_style"
							value="flat"
							id="hustle-gdpr-style--flat"
							<?php checked( $settings['gdpr_checkbox_style'], 'flat' ); ?>
						/>
						<?php esc_html_e( 'Flat', 'hustle' ); ?>
					</label>

					<label for="hustle-gdpr-style--outlined" class="sui-tab-item">
						<input type="radio"
							name="gdpr_checkbox_style"
							data-attribute="gdpr_checkbox_style"
							value="outlined"
							id="hustle-gdpr-style--outlined"
							data-tab-menu="hustle-gdpr-style"
							<?php checked( $settings['gdpr_checkbox_style'], 'outlined' ); ?>
						/>
						<?php esc_html_e( 'Outlined', 'hustle' ); ?>
					</label>

				</div>

				<div class="sui-tabs-content">

					<div class="sui-tab-content sui-tab-boxed" data-tab-content="hustle-gdpr-style">

						<div class="sui-row">

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--gdpr-border-radius" class="sui-label"><?php esc_html_e( 'Radius', 'hustle' ); ?></label>

									<input type="number"
										value="<?php echo esc_attr( $settings['gdpr_border_radius'] ); ?>"
										data-attribute="gdpr_border_radius"
										id="hustle-module--gdpr-border-radius"
										class="sui-form-control" />

								</div>

							</div>

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--gdpr-border-weight" class="sui-label"><?php esc_html_e( 'Weight', 'hustle' ); ?></label>

									<input type="number"
										value="<?php echo esc_attr( $settings['gdpr_border_weight'] ); ?>"
										data-attribute="gdpr_border_weight"
										id="hustle-module--gdpr-border-weight"
										class="sui-form-control" />

								</div>

							</div>

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--gdpr-border-type" class="sui-label"><?php esc_html_e( 'Border type', 'hustle' ); ?></label>

									<select id="hustle-module--gdpr-border-type"
										data-attribute="gdpr_border_type">

										<option value="solid" <?php selected( $settings['gdpr_border_type'], 'solid' ); ?>>
											<?php esc_attr_e( 'Solid', 'hustle' ); ?>
										</option>

										<option value="dotted" <?php selected( $settings['gdpr_border_type'], 'dotted' ); ?>>
											<?php esc_attr_e( 'Dotted', 'hustle' ); ?>
										</option>

										<option value="dashed" <?php selected( $settings['gdpr_border_type'], 'dashed' ); ?>>
											<?php esc_attr_e( 'Dashed', 'hustle' ); ?>
										</option>

										<option value="double" <?php selected( $settings['gdpr_border_type'], 'double' ); ?>>
											<?php esc_attr_e( 'Double', 'hustle' ); ?>
										</option>

										<option value="none" <?php selected( $settings['gdpr_border_type'], 'none' ); ?>>
											<?php esc_attr_e( 'None', 'hustle' ); ?>
										</option>

									</select>

								</div>

							</div>

						</div>

						<span class="sui-description"><?php esc_html_e( 'Note: Set the color of the border in the Colors Palette area below.', 'hustle' ); ?></span>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>
