<div
	id="hustle-dialog-migrate--aweber"
	class="sui-dialog sui-dialog-alt sui-dialog-sm"
	aria-hidden="true"
	tabindex="-1"
>

	<div class="sui-dialog-overlay sui-fade-out" data-a11y-dialog-hide="hustle-dialog--remove-active-integration"></div>

	<div
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription"
		role="dialog"
	>

		<div class="sui-box" role="document">

			<div class="sui-box-header sui-dialog-with-image sui-block-content-center">
				<?php $aweber = Hustle_Aweber::get_instance(); ?>
				<div class="sui-dialog-image" aria-hidden="true">
					<img src="<?php echo esc_url( $aweber->get_logo_2x() ); ?>" alt="" class="sui-image sui-image-center">
				</div>
				<h3 id="dialogTitle" class="sui-box-title"><?php esc_html_e( 'Migrate Aweber', 'hustle' ); ?></h3>

				<button class="sui-dialog-close" data-a11y-dialog-hide="hustle-dialog--remove-active-integration">
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

			</div>

			<div class="sui-box-body sui-box-body-slim sui-block-content-center">

				<p class="sui-description">
					<?php printf( esc_html__( "Click on the %s\"Get authorization code\"%s link to generate your authorization code and paste it below to re-authenticate your Aweber integration via oAuth 2.0.", 'hustle' ), '<b>', '</b>' ); ?>
				</p>
				<form>
					<div class="sui-form-field">
						<label for="reuth-aweber" id="label-reuth-aweber" class="sui-label">
							<?php esc_html_e( 'Authorization code', 'hustle' ); ?>
							<span class="sui-label-note">
								<?php
								$api = $aweber->get_api();
								$auth_url = $api->get_authorization_uri( 0, true, Hustle_Module_Admin::INTEGRATIONS_PAGE );
								if( $auth_url ): ?>
									<a
									class="hustle-aweber-migrate-link"
									href="<?php echo esc_url( $auth_url ); ?>"
									data-id=""
									target="_blank"
									>
										<?php esc_html_e( 'Get authorization code', 'hustle' ); ?>
									</a>
								<?php endif; ?>
							</span>
						</label>
						<input
							placeholder="<?php printf( esc_html__( 'Enter authorization code here', 'hustle' ) ); ?>"
							id="reuth-aweber"
							class="sui-form-control"
							aria-labelledby="label-reuth-aweber"
							aria-describedby="error-unique-id description-unique-id"
							name="api_key"
						/>
						<span class="sui-error-message sui-hidden"><?php esc_html_e( 'Please enter a valid Aweber authorization code', 'hustle' ); ?></span>

					</div>
				</form>
			</div>
			<div class="sui-box-footer sui-box-footer-right">
				<a
				id="integration-migrate"
				class="hustle-aweber-migrate sui-button"
				href="#"
				data-id=""
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_provider_action' ) ); ?>" >
					<span class="sui-loading-text">
						<?php esc_html_e( 'Re-Authenticate', 'hustle' ); ?>
					</span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</a>
			</div>
		</div>

	</div>

</div>
