<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Opt-in Form Fields', 'hustle' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Configure the fields you want to be displayed in the opt-in form.', 'hustle' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<div class="sui-box-builder" style="margin-bottom: 10px;">

			<div class="sui-box-builder-header">

				<button class="sui-button sui-button-purple hustle-optin-field--add">
					<i class="sui-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Insert Field', 'hustle' ); ?>
				</button>

			</div>

			<div class="sui-box-builder-body">

				<div id="hustle-form-fields-container" class="sui-builder-fields"></div>

				<button class="sui-button sui-button-dashed hustle-optin-field--add">
					<i class="sui-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Insert Field', 'hustle' ); ?>
				</button>

			</div>

			<div class="sui-box-builder-footer">

				<div id="hustle-optin-field--submit" class="sui-builder-field sui-can_open" data-field-id="submit">

					<div class="sui-builder-field-label">

						<i class="sui-icon-send" aria-hidden="true"></i>

						<span class="hustle-field-label"><?php esc_html_e( 'Submit', 'hustle' ); ?></span>

					</div>

					<div class="sui-dropdown">

						<button class="sui-button-icon sui-dropdown-anchor">
							<i class="sui-icon-widget-settings-config" aria-hidden="true"></i>
							<span class="sui-screen-reader-text"><?php esc_html_e( 'Submit settings', 'hustle' ); ?></span>
						</button>

						<ul>
							<li><button class="hustle-optin-field--edit">
								<i class="sui-icon-pencil" aria-hidden="true"></i> <?php esc_html_e( 'Edit Field', 'hustle' ); ?>
							</button></li>
						</ul>

					</div>

				</div>

				<div id="hustle-optin-field--gdpr" class="sui-builder-field sui-can_open sui-hidden" data-field-id="gdpr">

					<div class="sui-builder-field-label">

						<i class="sui-icon-gdpr" aria-hidden="true"></i>

						<span class="hustle-field-label"><?php esc_html_e( 'GDPR', 'hustle' ); ?></span>

					</div>

					<div class="sui-dropdown">

						<button class="sui-button-icon sui-dropdown-anchor">
							<i class="sui-icon-widget-settings-config" aria-hidden="true"></i>
							<span class="sui-screen-reader-text"><?php esc_html_e( 'GDPR settings', 'hustle' ); ?></span>
						</button>

						<ul>
							<li><button class="hustle-optin-field--edit">
								<i class="sui-icon-pencil" aria-hidden="true"></i> <?php esc_html_e( 'Edit Field', 'hustle' ); ?>
							</button></li>
							<li><button class="hustle-optin-field--delete">
								<i class="sui-icon-trash" aria-hidden="true"></i> <?php esc_html_e( 'Delete', 'hustle' ); ?>
							</button></li>
						</ul>

					</div>

				</div>

				<div id="hustle-optin-field--recaptcha" class="sui-builder-field sui-can_open sui-hidden" data-field-id="recaptcha">

					<div class="sui-builder-field-label">

						<i class="sui-icon-recaptcha" aria-hidden="true"></i>

						<span class="hustle-field-label"><?php esc_html_e( 'reCaptcha', 'hustle' ); ?></span>

					</div>

					<div class="sui-dropdown">

						<button class="sui-button-icon sui-dropdown-anchor">
							<i class="sui-icon-widget-settings-config" aria-hidden="true"></i>
							<span class="sui-screen-reader-text"><?php esc_html_e( 'reCaptcha settings', 'hustle' ); ?></span>
						</button>

						<ul>
							<li><button class="hustle-optin-field--edit">
								<i class="sui-icon-pencil" aria-hidden="true"></i> <?php esc_html_e( 'Edit Field', 'hustle' ); ?>
							</button></li>
							<li><button class="hustle-optin-field--delete">
								<i class="sui-icon-trash" aria-hidden="true"></i> <?php esc_html_e( 'Delete', 'hustle' ); ?>
							</button></li>
						</ul>

					</div>

				</div>

			</div>

		</div>

		<span class="sui-description"><?php esc_html_e( 'You can re-arrange the form fields by dragging and dropping.', 'hustle' ); ?></span>

	</div>

</div>
