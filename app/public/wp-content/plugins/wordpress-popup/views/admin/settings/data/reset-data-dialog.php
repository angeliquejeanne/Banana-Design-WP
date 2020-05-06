<div class="sui-dialog sui-dialog-sm" aria-hidden="true" tabindex="-1" id="hustle-dialog--reset-data-settings">

	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>

	<div
		class="sui-dialog-content"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription"
		role="dialog"
	>

		<div class="sui-box" role="document">

			<div class="sui-box-header">

				<h3 id="dialogTitle" class="sui-box-title"></h3>

				<div class="sui-actions-right">
					<button class="sui-dialog-close" aria-label="<?php esc_html_e( 'Close dialog', 'hustle' ); ?>" data-a11y-dialog-hide></button>
				</div>

			</div>

			<div class="sui-box-body">

				<p id="dialogDescription"></p>

			</div>

			<div class="sui-box-footer">

				<button class="sui-button sui-button-ghost" data-a11y-dialog-hide><?php esc_html_e( 'Cancel', 'hustle' ); ?></button>

				<button
					id="hustle-reset-settings"
					class="sui-button sui-button-red sui-button-ghost"
					data-notice="hustle-notice-success--reset-settings"
					data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_reset_settings' ) ); ?>"
				>
					<span class="sui-loading-text">
						<i class="sui-icon-undo" aria-hidden="true"></i> <?php esc_html_e( 'Reset', 'hustle' ); ?>
					</span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>

			</div>

		</div>

	</div>

</div>
