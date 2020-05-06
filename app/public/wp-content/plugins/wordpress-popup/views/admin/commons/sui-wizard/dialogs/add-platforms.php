<div
	id="hustle-dialog--add-platforms"
	class="sui-dialog"
	tabindex="-1"
	aria-hidden="true"
>

	<div class="sui-dialog-overlay sui-fade-out"></div>

	<div
		role="dialog"
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription"
	>

		<div class="sui-box" role="document">

			<div class="sui-box-header">

				<h3 id="dialogTitle" class="sui-box-title"><?php esc_html_e( 'Add Platform', 'hustle' ); ?></h3>

				<div class="sui-actions-right">

					<button class="hustle-discard-changes sui-dialog-close hustle-cancel-platforms">
						<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
					</button>

				</div>

			</div>

			<div class="sui-box-body">

				<p><?php esc_html_e( 'Choose the platforms to insert into your social sharing module.', 'hustle' ); ?></p>

			</div>

			<div class="sui-box-selectors sui-box-selectors-col-5">

				<ul class="sui-spacing-slim" id="hustle_add_platforms_container"></ul>

			</div>

			<div class="sui-box-footer">

				<button class="sui-button sui-button-ghost hustle-cancel-platforms">
					<?php esc_html_e( 'Cancel', 'hustle' ); ?>
				</button>

				<div class="sui-actions-right">

					<button id="hustle-add-platforms" class="sui-button sui-button-blue">
						<span class="sui-loading-text"><?php esc_html_e( 'Add Platform', 'hustle' ); ?></span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</button>

				</div>

			</div>

		</div>

	</div>

</div>
