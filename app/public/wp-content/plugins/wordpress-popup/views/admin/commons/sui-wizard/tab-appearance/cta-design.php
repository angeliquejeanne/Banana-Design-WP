<div class="sui-box-settings-row" data-toggle-content="use-vanilla">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'CTA Design', 'hustle' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Choose the settings for your call to action button.', 'hustle' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<?php
		// SETTING: Button style ?>
		<div class="sui-form-field">

			<label class="sui-label"><?php esc_html_e( 'Button style', 'hustle' ); ?></label>

			<div class="sui-side-tabs">

				<div class="sui-tabs-menu">

					<label for="hustle-cta-style--flat" class="sui-tab-item">
						<input
							type="radio"
							name="cta_style"
							data-attribute="cta_style"
							value="flat"
							id="hustle-cta-style--flat"
							<?php checked( $settings['cta_style'], 'flat' ); ?>
						/>
						<?php esc_html_e( 'Flat', 'hustle' ); ?>
					</label>

					<label for="hustle-cta-style--outlined" class="sui-tab-item">
						<input
							type="radio"
							name="cta_style"
							data-attribute="cta_style"
							value="outlined"
							id="hustle-cta-style--outlined"
							data-tab-menu="hustle-cta-style"
							<?php checked( $settings['cta_style'], 'outlined' ); ?>
						/>
						<?php esc_html_e( 'Outlined', 'hustle' ); ?>
					</label>

				</div>

				<div class="sui-tabs-content">

					<div class="sui-tab-content sui-tab-boxed" data-tab-content="hustle-cta-style">

						<div class="sui-row">

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--cta-border-radius" class="sui-label"><?php esc_html_e( 'Radius', 'hustle' ); ?></label>

									<input
										type="number"
										value="<?php echo esc_attr( $settings['cta_border_radius'] ); ?>"
										data-attribute="cta_border_radius"
										id="hustle-module--cta-border-radius"
										class="sui-form-control"
									/>

								</div>

							</div>

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--cta-border-weight" class="sui-label"><?php esc_html_e( 'Weight', 'hustle' ); ?></label>

									<input
										type="number"
										value="<?php echo esc_attr( $settings['cta_border_weight'] ); ?>"
										data-attribute="cta_border_weight"
										id="hustle-module--cta-border-weight"
										class="sui-form-control"
									/>

								</div>

							</div>

							<div class="sui-col-md-4">

								<div class="sui-form-field">

									<label for="hustle-module--cta-border-type" class="sui-label"><?php esc_html_e( 'Border type', 'hustle' ); ?></label>

									<select id="hustle-module--cta-border-type"
										data-attribute="cta_border_type">

										<option value="solid" <?php selected( $settings['cta_border_type'], 'solid' ); ?>>
											<?php esc_attr_e( 'Solid', 'hustle' ); ?>
										</option>

										<option value="dotted" <?php selected( $settings['cta_border_type'], 'dotted' ); ?>>
											<?php esc_attr_e( 'Dotted', 'hustle' ); ?>
										</option>

										<option value="dashed" <?php selected( $settings['cta_border_type'], 'dashed' ); ?>>
											<?php esc_attr_e( 'Dashed', 'hustle' ); ?>
										</option>

										<option value="double" <?php selected( $settings['cta_border_type'], 'double' ); ?>>
											<?php esc_attr_e( 'Double', 'hustle' ); ?>
										</option>

										<option value="none" <?php selected( $settings['cta_border_type'], 'none' ); ?>>
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
