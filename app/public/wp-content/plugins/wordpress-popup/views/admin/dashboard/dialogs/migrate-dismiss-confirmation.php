<div
	id="hustle-dialog--migrate-dismiss-confirmation"
	class="sui-dialog sui-dialog-alt sui-dialog-sm"
	aria-hidden="true"
	tabindex="-1"
>

	<div class="sui-dialog-overlay sui-fade-out" data-a11y-dialog-hide="hustle-dialog--migrate-dismiss-confirmation"></div>

	<div
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription"
		role="dialog"
	>

		<div class="sui-box" role="document">

			<div class="sui-box-header sui-block-content-center">

				<h3 id="dialogTitle" class="sui-box-title"><?php esc_html_e( 'Dismiss Migrate Data Notice', 'hustle' ); ?></h3>

				<button class="sui-dialog-close" data-a11y-dialog-hide="hustle-dialog--migrate-dismiss-confirmation">
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

			</div>

			<div class="sui-box-body sui-box-body-slim sui-block-content-center">

				<p class="sui-description"><?php esc_html_e( "Are you sure you wish to dismiss this notice? Make sure you've already migrated data of your existing modules, and you don't need to migrate data anymore.", 'hustle' ); ?></p>

			</div>

			<div class="sui-box-footer sui-box-footer-center">

				<button class="sui-button sui-button-ghost" data-a11y-dialog-hide="hustle-dialog--migrate-dismiss-confirmation"><?php esc_html_e( 'Cancel', 'hustle' ); ?></button>

				<button 
					id="hustle-dismiss-modal-button" 
					class="sui-button sui-button-ghost sui-button-red"
					data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_dismiss_notification' ) ); ?>"
					data-name="<?php echo esc_attr( Hustle_Dashboard_Admin::MIGRATE_NOTICE_NAME ); ?>"
					data-a11y-dialog-hide="hustle-dialog--migrate-dismiss-confirmation"
				>
					<?php esc_html_e( 'Dismiss Forever', 'hustle' ); ?>
				</button>

			</div>

		</div>

	</div>

</div>
