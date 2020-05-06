<div
	id="hustle-dialog-migrate--constantcontact"
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

			<div class="sui-box-header sui-block-content-center">

				<h3 id="dialogTitle" class="sui-box-title"><?php esc_html_e( 'Migrate Constant Contact?', 'hustle' ); ?></h3>

				<button class="sui-dialog-close" data-a11y-dialog-hide="hustle-dialog--remove-active-integration">
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

			</div>

			<div class="sui-box-body sui-box-body-slim sui-block-content-center">

				<p class="sui-description">
					<?php esc_html_e( "Click on the re-authenticate button below and authorize Hustle to retrieve access tokens for v3.0 API to update your integration to the latest API version.", 'hustle' ); ?>
				</p>

			</div>
			<?php
			$api = Hustle_ConstantContact::get_instance()->api();
			if( method_exists( $api , 'get_migrate_authorization_uri') ): ?>
				<div class="sui-box-footer sui-box-footer-center">
					<a class="sui-button hustle-ctct-migrate" href="<?php echo esc_url( $api->get_migrate_authorization_uri() ); ?>">
						<span class="sui-loading-text"><?php esc_html_e( 'Re-Authenticate', 'hustle' ); ?></span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</a>
				</div>
			<?php else: ?>
				<div class="sui-box-footer sui-box-footer-center">
					<button disabled type="button" class="sui-button hustle-ctct-migrate hustle-onload-icon-action sui-button-ghost"><?php esc_html_e( 'Re-Authenticate', 'hustle' ); ?></span><i class="sui-icon-loader sui-loading" aria-hidden="true"></i></button>
				</div>
			<?php endif; ?>
		</div>

	</div>

</div>
