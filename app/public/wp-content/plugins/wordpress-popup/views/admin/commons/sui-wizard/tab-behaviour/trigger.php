<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php printf( esc_html__( '%s Trigger', 'hustle' ), esc_html( $capitalize_singular ) ); ?></span>
		<span class="sui-description"><?php printf( esc_html__( '%s can be triggered after a certain amount of Time, when the user Scrolls past an element, on Click, if the user tries to Leave or if we detect AdBlock.', 'hustle' ), esc_html( $capitalize_plural ) ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<div class="sui-side-tabs">

			<div class="sui-tabs-menu">

				<label for="hustle-trigger--time"
					class="sui-tab-item">
					<input type="radio"
						name="trigger"
						data-attribute="triggers.trigger"
						value="time"
						id="hustle-trigger--time"
						data-tab-menu="trigger-time"
						<?php checked( $triggers['trigger'], 'time' ); ?>
					/>
					<?php esc_html_e( 'Time', 'hustle' ); ?>
				</label>

				<label for="hustle-trigger--scroll"
					class="sui-tab-item">
					<input type="radio"
						name="trigger"
						data-attribute="triggers.trigger"
						value="scroll"
						id="hustle-trigger--scroll"
						data-tab-menu="trigger-scroll"
						<?php checked( $triggers['trigger'], 'scroll' ); ?>
					/>
					<?php esc_html_e( 'Scroll', 'hustle' ); ?>
				</label>

				<label for="hustle-trigger--click"
					class="sui-tab-item">
					<input type="radio"
						name="trigger"
						data-attribute="triggers.trigger"
						value="click"
						id="hustle-trigger--click"
						data-tab-menu="trigger-click"
						<?php checked( $triggers['trigger'], 'click' ); ?>
					/>
					<?php esc_html_e( 'Click', 'hustle' ); ?>
				</label>

				<label for="hustle-trigger--exit"
					class="sui-tab-item">
					<input type="radio"
						name="trigger"
						data-attribute="triggers.trigger"
						value="exit_intent"
						id="hustle-trigger--exit"
						data-tab-menu="trigger-exit"
						<?php checked( $triggers['trigger'], 'exit_intent' ); ?>
					/>
					<?php esc_html_e( 'Exit intent', 'hustle' ); ?>
				</label>

				<label for="hustle-trigger--adblock"
					class="sui-tab-item">
					<input type="radio"
						name="trigger"
						data-attribute="triggers.trigger"
						value="adblock"
						id="hustle-trigger--adblock"
						data-tab-menu="trigger-adblock"
						<?php checked( $triggers['trigger'], 'adblock' ); ?>
					/>
					<?php esc_html_e( 'AdBlock', 'hustle' ); ?>
				</label>

			</div>

			<div class="sui-tabs-content sui-tabs-content-lg">

				<?php // TRIGGER: Time. ?>
				<div class="sui-tab-content" data-tab-content="trigger-time">

					<label class="sui-settings-label"><?php printf( esc_html__( 'Show %s on page load', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></label>
					<span class="sui-description"><?php printf( esc_html__( '%s will be shown as soon as page is loaded. If you want to add some delay, use the option below:', 'hustle' ), esc_html( $capitalize_singular ) ); ?></span>

					<div class="sui-border-frame">

						<label class="sui-label"><?php esc_html_e( 'Add delay', 'hustle' ); ?></label>

						<div class="sui-row">

							<div class="sui-col-md-6">

								<input type="number"
									value="<?php echo esc_attr( $triggers['on_time_delay'] ); ?>"
									min="0"
									class="sui-form-control"
									name="trigger_on_time_delay"
									data-attribute="triggers.on_time_delay" />

							</div>

							<div class="sui-col-md-6">

								<select name="on_time_unit" data-attribute="triggers.on_time_unit">

									<option value="seconds"
										<?php selected( $triggers['on_time_unit'], 'seconds' ); ?>
									>
										<?php esc_html_e( 'seconds', 'hustle' ); ?>
									</option>

									<option value="minutes"
										<?php selected( $triggers['on_time_unit'], 'minutes' ); ?>
									>
										<?php esc_html_e( 'minutes', 'hustle' ); ?>
									</option>

									<option value="hours"
										<?php selected( $triggers['on_time_unit'], 'hours' ); ?>
									>
										<?php esc_html_e( 'hours', 'hustle' ); ?>
									</option>

								</select>

							</div>

						</div>

					</div>

				</div>

				<?php // TRIGGER: Scroll. ?>
				<div class="sui-tab-content" data-tab-content="trigger-scroll">

					<?php // SETTINGS: After the amount of page scroll. ?>
					<div class="sui-form-field">

						<label for="hustle-trigger-scroll--percentage"
							class="sui-radio sui-radio-stacked"
							style="margin-bottom: 0;">
							<input type="radio"
								value="scrolled"
								id="hustle-trigger-scroll--percentage"
								name="trigger_on_scroll"
								data-attribute="triggers.on_scroll"
								<?php checked( $triggers['on_scroll'], 'scrolled' ); ?>
							/>
							<span aria-hidden="true"></span>
							<span><?php esc_html_e( 'After the amount of page scroll', 'hustle' ); ?></span>
						</label>

						<span class="sui-description sui-checkbox-description"><?php printf( esc_html__( '%s will be shown as the page has been scrolled by certain percentage.', 'hustle' ), esc_html( $capitalize_singular ) ); ?></span>

						<div id="hustle-on-scroll--scrolled-toggle-wrapper" class="sui-border-frame{{ ( 'scrolled' !== triggers.on_scroll ) ? ' sui-hidden' : '' }}"
							style="margin-left: 26px;">

							<label class="sui-label"><?php printf( esc_html__( 'Scroll &#37; to trigger the %s (anything between 0 - 100&#37;)', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></label>

							<input type="number"
								value="<?php echo esc_attr( $triggers['on_scroll_page_percent'] ); ?>"
								min="0"
								max="100"
								class="sui-form-control"
								name="trigger_on_scroll_page_percent"
								data-attribute="triggers.on_scroll_page_percent" />

						</div>

					</div>

					<?php // SETTINGS: After the passed selector. ?>
					<div class="sui-form-field">

						<label for="hustle-trigger-scroll--selector"
							class="sui-radio sui-radio-stacked"
							style="margin-bottom: 0;">
							<input type="radio"
								value="selector"
								id="hustle-trigger-scroll--selector"
								name="trigger_on_scroll"
								data-attribute="triggers.on_scroll"
								<?php checked( $triggers['on_scroll'], 'selector' ); ?>
							/>
							<span aria-hidden="true"></span>
							<span><?php esc_html_e( 'After the passed selector', 'hustle' ); ?></span>
						</label>

						<span class="sui-description sui-checkbox-description"><?php printf( esc_html__( '%s will be shown as the user has passed a CSS selector', 'hustle' ), esc_html( $capitalize_singular ) ); ?></span>

						<div id="hustle-on-scroll--selector-toggle-wrapper" class="sui-border-frame <?php echo 'selector' !== $triggers['on_scroll'] ? 'sui-hidden' : ''; ?>"
							style="margin-left: 26px;">

							<label for="hustle-trigger-scroll--selector-name" class="sui-label"><?php esc_html_e( 'CSS selector', 'hustle' ); ?></label>

							<input type="text"
								placeholder="<?php esc_html_e( 'Enter selector Class or Id', 'hustle' ); ?>"
								value="<?php echo esc_attr( $triggers['on_scroll_css_selector'] ); ?>"
								id="hustle-trigger-scroll--selector-name"
								class="sui-form-control"
								name="trigger_on_scroll_css_selector"
								data-attribute="triggers.on_scroll_css_selector" />

							<span class="sui-description"><?php esc_html_e( 'You can enter the class as .css-class and id as #css-id', 'hustle' ); ?></span>

						</div>

					</div>

				</div>

				<?php // TRIGGER: Click. ?>
				<div class="sui-tab-content" data-tab-content="trigger-click">

					<?php // SETTINGS: Click on existing element. ?>
					<div class="sui-form-field">

						<label for="hustle-trigger-click--selector" class="sui-toggle hustle-toggle-with-container" data-toggle-on="trigger-click-selector">
							<input type="checkbox"
								id="hustle-trigger-click--selector"
								name="trigger_on_click_selector"
								data-attribute="triggers.enable_on_click_element"
								<?php checked( $triggers['enable_on_click_element'], '1' ); ?>
							/>
							<span class="sui-toggle-slider"></span>
						</label>

						<label for="hustle-trigger-click--selector"><?php esc_html_e( 'Click on existing element', 'hustle' ); ?></label>

						<span class="sui-description sui-toggle-description" style="margin-top: 0;"><?php printf( esc_html__( '%s will be shown when a user clicks on an existing HTML element.', 'hustle' ), esc_html( $capitalize_singular ) ); ?></span>

						<div class="sui-border-frame sui-toggle-content" data-toggle-content="trigger-click-selector">

							<label class="sui-label"><?php esc_html_e( 'CSS selector', 'hustle' ); ?></label>

							<input type="text"
								placeholder="<?php esc_attr_e( 'Enter selector Class or Id', 'hustle' ); ?>"
								value="<?php echo esc_attr( $triggers['on_click_element'] ); ?>"
								class="sui-form-control"
								name="trigger_on_click_element"
								data-attribute="triggers.on_click_element" />

						</div>

					</div>

					<?php // SETTINGS: Render a new button. ?>
					<div class="sui-form-field">

						<label for="hustle-trigger-click--shortcode" class="sui-toggle hustle-toggle-with-container" data-toggle-on="trigger-click-shortcode">
							<input type="checkbox"
								id="hustle-trigger-click--shortcode"
								name="trigger_on_click_shortcode"
								data-attribute="triggers.enable_on_click_shortcode"
								<?php checked( $triggers['enable_on_click_shortcode'], '1' ); ?>
							/>
							<span class="sui-toggle-slider"></span>
						</label>

						<label for="hustle-trigger-click--shortcode"><?php esc_html_e( 'Render a new button', 'hustle' ); ?></label>

						<span class="sui-description sui-toggle-description" style="margin-top: 0;"><?php printf( esc_html__( 'You can render a new button which will tigger the %s using the shortcode.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

						<div class="sui-border-frame sui-toggle-content" data-toggle-content="trigger-click-shortcode">

							<label class="sui-label"><?php esc_html_e( 'Shortcode to render the trigger element', 'hustle' ); ?></label>

							<div class="sui-with-button sui-with-button-inside">
								<input type="text"
									class="sui-form-control"
									value='[wd_hustle id="<?php echo esc_attr( $shortcode_id ); ?>" type="<?php echo esc_attr( $module_type ); ?>"]<?php esc_attr_e( 'Click', 'hustle' ); ?>[/wd_hustle]'
									readonly="readonly">
								<button class="sui-button-icon hustle-copy-shortcode-button">
									<i aria-hidden="true" class="sui-icon-copy"></i>
									<span class="sui-screen-reader-text"><?php esc_html_e( 'Copy shortcode', 'hustle' ); ?></span>
								</button>
							</div>

						</div>

					</div>

				</div>

				<?php // TRIGGER: Exit intent. ?>
				<div class="sui-tab-content" data-tab-content="trigger-exit">
				<div class="sui-notice"><p><?php printf( esc_html__( "%1\$sNote:%2\$s This doesn't work on mobile and tablet because we use mouse movements to detect the exit intent.", 'hustle' ), '<b>', '</b>' ); ?></p></div>

					<?php // SETTINGS: Trigger once per session. ?>
					<div class="sui-form-field">

						<label for="hustle-trigger-exit--session" class="sui-toggle">
							<input type="checkbox"
								id="hustle-trigger-exit--session"
								name="trigger_on_exit_intent_per_session"
								data-attribute="triggers.on_exit_intent_per_session"
								<?php checked( $triggers['on_exit_intent_per_session'], '1' ); ?>
							/>
							<span class="sui-toggle-slider"></span>
						</label>

						<label for="hustle-trigger-exit--session"><?php esc_html_e( 'Trigger once per session', 'hustle' ); ?></label>

						<span class="sui-description sui-toggle-description" style="margin-top: 0;"><?php printf( esc_html__( 'Enabling this will trigger the %s only for the first time user tries to leave your website in a session.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

					</div>

					<?php // SETTINGS: Add delay. ?>
					<div class="sui-form-field">

						<label for="hustle-trigger-exit--delay" class="sui-toggle hustle-toggle-with-container" data-toggle-on="trigger-exit-delay">
							<input type="checkbox"
								id="hustle-trigger-exit--delay"
								name="trigger_on_exit_intent_delayed"
								data-attribute="triggers.on_exit_intent_delayed"
								<?php checked( $triggers['on_exit_intent_delayed'], '1' ); ?>
							/>
							<span class="sui-toggle-slider"></span>
						</label>

						<label for="hustle-trigger-exit--delay"><?php esc_html_e( 'Add delay', 'hustle' ); ?></label>

						<span class="sui-description sui-toggle-description" style="margin-top: 0;"><?php printf( esc_html__( 'This will delay the appearance of the %s after the user attempts to exit.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

						<div class="sui-border-frame sui-toggle-content" data-toggle-content="trigger-exit-delay">

							<label class="sui-label"><?php esc_html_e( 'Delay time', 'hustle' ); ?></label>

							<div class="sui-row">

								<div class="sui-col-md-6">

									<input type="number"
										value="<?php echo esc_attr( $triggers['on_exit_intent_delayed_time']); ?>"
										min="0"
										class="sui-form-control"
										name="trigger_on_exit_intent_delayed_time"
										data-attribute="triggers.on_exit_intent_delayed_time" />

								</div>

								<div class="sui-col-md-6">

									<select name="trigger_on_exit_intent_delayed_unit" data-attribute="triggers.on_exit_intent_delayed_unit">

										<option value="seconds"
											<?php selected( $triggers['on_exit_intent_delayed_unit'], 'seconds' ); ?>
										>
											<?php esc_html_e( 'seconds', 'hustle' ); ?>
										</option>

										<option value="minutes"
											<?php selected( $triggers['on_exit_intent_delayed_unit'], 'minutes' ); ?>
										>
											<?php esc_html_e( 'minutes', 'hustle' ); ?>
										</option>

										<option value="hours"
											<?php selected( $triggers['on_exit_intent_delayed_unit'], 'hours' ); ?>
										>
											<?php esc_html_e( 'hours', 'hustle' ); ?>
										</option>

									</select>

								</div>

							</div>

						</div>

					</div>

				</div>

				<?php // TRIGGER: AdBlock. ?>
				<div class="sui-tab-content" data-tab-content="trigger-adblock">

					<div class="sui-form-field">

						<label for="hustle-trigger-adblock" class="sui-toggle">
							<input type="checkbox"
								id="hustle-trigger-adblock"
								name="trigger_on_adblock"
								data-attribute="triggers.on_adblock"
								<?php checked( $triggers['on_adblock'], '1' ); ?>
							/>
							<span class="sui-toggle-slider"></span>
						</label>

						<label for="hustle-trigger-adblock"><?php esc_html_e( 'Trigger when adblock is detected', 'hustle' ); ?></label>

						<span class="sui-description sui-toggle-description" style="margin-top: 0;"><?php printf( esc_html__( 'Enabling this will trigger the %s everytime an AdBlock is detected in your visitor’s browser.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>
