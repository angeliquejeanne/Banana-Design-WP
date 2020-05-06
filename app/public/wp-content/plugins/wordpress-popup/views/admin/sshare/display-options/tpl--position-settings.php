<div class="sui-form-field">

	<label for="hustle-settings--<?php echo esc_attr( $prefix ); ?>-enable" class="sui-toggle hustle-toggle-with-container" data-toggle-on="<?php echo esc_attr( $prefix ); ?>-enabled">
		<input
			type="checkbox"
			name="<?php echo esc_html( $prefix ); ?>_enabled"
			data-attribute="<?php echo esc_html( $prefix ); ?>_enabled"
			id="hustle-settings--<?php echo esc_html( $prefix ); ?>-enable"
			<?php checked( $settings[ $prefix . '_enabled' ], '1' ); ?>
		/>
		<span class="sui-toggle-slider"></span>
	</label>

	<label for="hustle-settings--<?php echo esc_html( $prefix ); ?>-enable"><?php printf( esc_html__( 'Enable %s', 'hustle' ), esc_html( $label ) ); ?></label>

	<div class="sui-toggle-content" data-toggle-content="<?php echo esc_attr( $prefix ); ?>-enabled">

		<span class="sui-description"><?php echo esc_html( $description ); ?></span>

		<div class="sui-border-frame">

			<?php // SETTINGS: Horizontal Position. ?>
			<div class="sui-form-field">

				<?php if ( 'inline' !== $prefix ) : ?>

					<label class="sui-settings-label"><?php esc_html_e( 'Horizontal Position', 'hustle' ); ?></label>
					<span class="sui-description"><?php esc_html_e( 'Choose the horizontal position of the Floating Social.', 'hustle' ); ?></span>

				<?php else : ?>

					<label class="sui-settings-label"><?php esc_html_e( 'Position', 'hustle' ); ?></label>
					<span class="sui-description"><?php esc_html_e( 'Choose the position for the Floating Social.', 'hustle' ); ?></span>

				<?php endif; ?>

				<?php if ( isset( $positions ) && ( '' !== $positions ) ) : ?>

					<div style="margin-top: 10px;">

						<?php foreach ( $positions as $pkey => $position ) : ?>

							<label
								for="hustle-position-<?php echo esc_html( $prefix ); ?>-<?php echo esc_html( $pkey ); ?>"
								class="sui-radio-image"
							>

								<?php
								echo Opt_In_Utils::render_image_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									esc_url( self::$plugin_url . 'assets/images/' . $position['image1x'] ),
									esc_url( self::$plugin_url . 'assets/images/' . $position['image2x'] ),
									''
								);
								?>

								<span class="sui-radio">
									<input
										type="radio"
										name="<?php echo esc_html( $prefix ); ?>_position"
										data-attribute="<?php echo esc_html( $prefix ); ?>_position"
										value="<?php echo esc_html( $pkey ); ?>"
										id="hustle-position-<?php echo esc_html( $prefix ); ?>-<?php echo esc_html( $pkey ); ?>"
										<?php checked( $settings[ $prefix . '_position' ], $pkey ); ?>
									/>
									<span aria-hidden="true"></span>
									<span><?php echo esc_html( $position['label'] ); ?></span>
								</span>

							</label>

						<?php endforeach; ?>

					</div>

				<?php endif; ?>

			</div>

			<?php // SETTINGS: Vertical Position. ?>
			<?php if ( isset( $offset_y ) && ( true === $offset_y ) ) : ?>

				<div class="sui-form-field">

					<label class="sui-settings-label"><?php esc_html_e( 'Vertical Position', 'hustle' ); ?></label>
					<span class="sui-description" style="margin-bottom: 10px;"><?php esc_html_e( 'Choose the vertical position of the Floating Social.', 'hustle' ); ?></span>

					<div class="sui-side-tabs">

						<div class="sui-tabs-menu">

							<label for="hustle-<?php echo esc_html( $prefix ); ?>-offset--top" class="sui-tab-item">
								<input
									type="radio"
									name="<?php echo esc_html( $prefix ); ?>_position_y"
									data-attribute="<?php echo esc_html( $prefix ); ?>_position_y"
									value="top"
									id="hustle-<?php echo esc_html( $prefix ); ?>-offset--top"
									<?php checked( $settings[ $prefix . '_position_y' ], 'top' ); ?>
								/>
								<?php esc_html_e( 'Top', 'hustle' ); ?>
							</label>

							<label for="hustle-<?php echo esc_html( $prefix ); ?>-offset--bottom" class="sui-tab-item">
								<input
									type="radio"
									name="<?php echo esc_html( $prefix ); ?>_position_y"
									data-attribute="<?php echo esc_html( $prefix ); ?>_position_y"
									value="bottom"
									id="hustle-<?php echo esc_html( $prefix ); ?>-offset--bottom"
									<?php checked( $settings[ $prefix . '_position_y' ], 'bottom' ); ?>
								/>
								<?php esc_html_e( 'Bottom', 'hustle' ); ?>
							</label>

						</div>

					</div>

				</div>

			<?php endif; ?>

			<?php // SETTINGS: Offset. ?>
			<?php if ( ! empty( $offset_x ) || ! empty( $offset_y ) ) : ?>

				<div class="sui-form-field">

					<span class="sui-settings-label"><?php esc_html_e( 'Offset', 'hustle' ); ?></span>
					<span class="sui-description"><?php esc_html_e( "You can choose to offset the Floating Social relative to the screen of visitor's device or a specific CSS selector.", 'hustle' ); ?></span>

				</div>

				<?php // SETTINGS: Relative to. ?>
				<div class="sui-form-field">

					<label class="sui-label"><?php esc_html_e( 'Relative to', 'hustle' ); ?></label>

					<div class="sui-side-tabs">

						<div class="sui-tabs-menu">

							<label for="hustle-<?php echo esc_attr( $prefix ); ?>-offset--screen" class="sui-tab-item">
								<input
									type="radio"
									name="<?php echo esc_attr( $prefix ); ?>_offset"
									data-attribute="<?php echo esc_attr( $prefix ); ?>_offset"
									value="screen"
									id="hustle-<?php echo esc_attr( $prefix ); ?>-offset--screen"
									<?php checked( $settings[ $prefix . '_offset' ], 'screen' ); ?>
								/>
								<?php esc_html_e( 'Screen', 'hustle' ); ?>
							</label>

							<label for="hustle-<?php echo esc_attr( $prefix ); ?>-offset--css" class="sui-tab-item">
								<input
									type="radio"
									name="<?php echo esc_attr( $prefix ); ?>_offset"
									data-attribute="<?php echo esc_attr( $prefix ); ?>_offset"
									value="css_selector"
									id="hustle-<?php echo esc_attr( $prefix ); ?>-offset--css"
									data-tab-menu="<?php echo esc_attr( $prefix ); ?>-offset-css"
									<?php checked( $settings[ $prefix . '_offset' ], 'css_selector' ); ?>
								/>
								<?php esc_html_e( 'CSS selector', 'hustle' ); ?>
							</label>

						</div>

						<div class="sui-tabs-content sui-tabs-content-lg">

							<div class="sui-tab-content" data-tab-content="<?php echo esc_attr( $prefix ); ?>-offset-css">

								<div class="sui-form-field hustle-css-selector">

									<label for="hustle-offset--<?php echo esc_attr( $prefix ); ?>-selector" class="sui-label"><?php esc_html_e( 'CSS selector of the element', 'hustle' ); ?></label>

									<input
										type="text"
										name="<?php echo esc_html( $prefix ); ?>_css_selector"
										data-attribute="<?php echo esc_html( $prefix ); ?>_css_selector"
										value="<?php echo esc_attr( $settings[ $prefix . '_css_selector' ] ); ?>"
										placeholder="#css-id"
										id="hustle-offset--<?php echo esc_html( $prefix ); ?>-selector"
										class="sui-form-control"
									/>

									<span class="sui-error-message" style="display: none; text-align: right;"><?php esc_html_e( 'CSS selector is required.', 'hustle' ); ?></span>

								</div>

							</div>

						</div>

					</div>

				</div>

				<?php // SETTINGS: Offset value. ?>
				<div class="sui-row">

					<div
						id="hustle-<?php echo esc_attr( $prefix ); ?>-offset-x-wrapper"
						class="sui-col<?php echo 'center' === $settings[ $prefix . '_position' ] ? ' sui-hidden' : ''; ?>"
					>

						<div class="sui-form-field">

							<label
								for="hustle-<?php echo esc_html( $prefix ); ?>-offset-pixels-x"
								id="hustle-<?php echo esc_attr( $prefix ); ?>-left-offset-label"
								class="sui-label<?php echo 'right' === $settings[ $prefix . '_position' ] ? ' sui-hidden' : ''; ?>"
							>
								<?php esc_html_e( 'Left offset value (px)', 'hustle' ); ?>
							</label>

							<label
								for="hustle-<?php echo esc_html( $prefix ); ?>-offset-pixels-x"
								id="hustle-<?php echo esc_attr( $prefix ); ?>-right-offset-label"
								class="sui-label<?php echo 'right' !== $settings[ $prefix . '_position' ] ? ' sui-hidden' : ''; ?>"
							>
								<?php esc_html_e( 'Right offset value (px)', 'hustle' ); ?>
							</label>

							<input
								type="number"
								name="<?php echo esc_html( $prefix ); ?>_offset_x"
								value="<?php echo esc_attr( $settings[ $prefix . '_offset_x' ] ); ?>"
								placeholder="0"
								id="hustle-<?php echo esc_html( $prefix ); ?>-offset-pixels-x"
								class="sui-form-control"
								data-attribute="<?php echo esc_html( $prefix ); ?>_offset_x"
							/>

						</div>

					</div>

					<div class="sui-col">

						<div class="sui-form-field">

							<label
								for="hustle-<?php echo esc_html( $prefix ); ?>-offset-pixels-y"
								id="hustle-<?php echo esc_attr( $prefix ); ?>-top-offset-label"
								class="sui-label<?php echo 'top' !== $settings[ $prefix . '_position_y' ] ? ' sui-hidden' : ''; ?>"
							>
									<?php esc_html_e( 'Top offset value (px)', 'hustle' ); ?>
							</label>

							<label
								for="hustle-<?php echo esc_html( $prefix ); ?>-offset-pixels-y"
								id="hustle-<?php echo esc_attr( $prefix ); ?>-bottom-offset-label"
								class="sui-label<?php echo 'top' === $settings[ $prefix . '_position_y' ] ? ' sui-hidden' : ''; ?>"
							>
								<?php esc_html_e( 'Bottom offset value (px)', 'hustle' ); ?>
							</label>

							<input
								type="number"
								name="<?php echo esc_html( $prefix ); ?>_offset_y"
								data-attribute="<?php echo esc_html( $prefix ); ?>_offset_y"
								value="<?php echo esc_attr( $settings[ $prefix . '_offset_y' ] ); ?>"
								placeholder="0"
								id="hustle-<?php echo esc_html( $prefix ); ?>-offset-pixels-y"
								class="sui-form-control"
							/>

						</div>

					</div>

				</div>

			<?php endif; ?>

			<?php // SETTINGS: Alignment. ?>
			<?php if ( ! empty( $alignment ) ) : ?>

				<div class="sui-form-field">

					<label class="sui-settings-label"><?php esc_html_e( 'Alignment', 'hustle' ); ?></label>
					<span class="sui-description"><?php esc_html_e( 'You can choose between Left align, Middle or Right align. For example, choosing the left align will push the social bar to the left of the parent container.', 'hustle' ); ?></span>

					<div class="sui-side-tabs" style="margin-top: 10px;">

						<div class="sui-tabs-menu">

							<label for="hustle-<?php echo esc_html( $prefix ); ?>-align--left" class="sui-tab-item">
								<input
									type="radio"
									name="<?php echo esc_html( $prefix ); ?>_align"
									data-attribute="<?php echo esc_html( $prefix ); ?>_align"
									value="left"
									id="hustle-<?php echo esc_html( $prefix ); ?>-align--left"
									<?php checked( $settings[ $prefix . '_align' ], 'left' ); ?>
								/>
								<i class="sui-icon-align-left sui-md" aria-hidden="true"></i>
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Left', 'hustle' ); ?></span>
							</label>

							<label for="hustle-<?php echo esc_html( $prefix ); ?>-align--center" class="sui-tab-item">
								<input
									type="radio"
									name="<?php echo esc_html( $prefix ); ?>_align"
									data-attribute="<?php echo esc_html( $prefix ); ?>_align"
									value="center"
									id="hustle-<?php echo esc_html( $prefix ); ?>-align--center"
									<?php checked( $settings[ $prefix . '_align' ], 'center' ); ?>
								/>
								<i class="sui-icon-align-center sui-md" aria-hidden="true"></i>
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Center', 'hustle' ); ?></span>
							</label>

							<label for="hustle-<?php echo esc_html( $prefix ); ?>-align--right" class="sui-tab-item">
								<input
									type="radio"
									name="<?php echo esc_html( $prefix ); ?>_align"
									data-attribute="<?php echo esc_html( $prefix ); ?>_align"
									value="right"
									id="hustle-<?php echo esc_html( $prefix ); ?>-align--right"
									<?php checked( $settings[ $prefix . '_align' ], 'right' ); ?>
								/>
								<i class="sui-icon-align-right sui-md" aria-hidden="true"></i>
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Right', 'hustle' ); ?></span>
							</label>

						</div>

					</div>

				</div>

			<?php endif; ?>

		</div>

	</div>

</div>
