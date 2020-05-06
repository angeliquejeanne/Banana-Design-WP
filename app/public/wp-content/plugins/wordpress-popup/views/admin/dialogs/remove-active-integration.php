<div
	id="hustle-dialog--remove-active"
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

				<button class="sui-dialog-back hustle-remove-active-integration-back" aria-label="Back"></button>

				<div id="sui-box-modal-header"></div>

				<div class="sui-dialog-image" aria-hidden="true"></div>

				<button class="sui-dialog-close" data-a11y-dialog-hide="hustle-dialog--remove-active-integration">
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

			</div>

			<div class="sui-box-body sui-box-body-slim sui-block-content-center">

				<div id="sui-box-modal-content"></div>

				<div id="hustle-integration-active-modules" class="hustle-active-module-list">

					<span class="sui-label" style="padding-left: 10px;"><?php esc_html_e( 'Modules', 'hustle' ); ?></span>

					<table class="sui-table hui-table--apps-off">

						<tbody></tbody>

					</table>

				</div>

			</div>

			<div class="sui-box-footer sui-box-footer-center">

				<button class="sui-button sui-button-ghost "
				data-a11y-dialog-hide="hustle-dialog--remove-active-integration-cancel"
				id="hustle-remove-active-button-cancel">
					<?php esc_html_e( 'Cancel', 'hustle' ); ?>
				</button>

				<button
					id="hustle-remove-active-button"
					class="sui-button sui-button-ghost sui-button-red"
				>
				<span class="sui-loading-text"><?php esc_html_e( 'DISCONNECT ANYWAY', 'hustle' ); ?><i class="sui-icon-loader sui-loading" aria-hidden="true"></i></span>
				</button>
			</div>

		</div>

	</div>

</div>

<script id="hustle-modules-active-integration-tpl" type="text/template">

	<tr>

		<td class="sui-table-item-title">

			<span class="hui-app--wrap">
				<span class="hui-app--title"><i class="sui-icon-{{type}}" aria-hidden="true"></i> {{name}}</span>
				<span class="hui-app--link"><a href="{{editUrl}}" target="_blank" class="sui-button-icon">
					<i class="sui-icon-pencil" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Edit your module integrations', 'hustle' ); ?></span>
				</a></span>
			</span>

		</td>

	</tr>

</script>

<script id="hustle-modules-active-integration-img-tpl" type="text/template">

	<?php //Image ?>

	<img
		src="{{ image }}"
		alt="{{ title }}"
		class="sui-image sui-image-center"
	/>

</script>

<script id="hustle-modules-active-integration-header-tpl" type="text/template">

	<?php // Title ?>
	<h3 id="dialogTitle" class="sui-box-title"> <?php esc_html_e( 'Disconnect ', 'hustle' ); ?> {{ title }}</h3>

</script>

<script id="hustle-modules-active-integration-desc-tpl" type="text/template">

	<?php // Title ?>
	<p class="sui-description">{{title}}<?php esc_html_e( " is active (collecting data) on the following modules. Are you sure you wish to disconnect it? Note that if disconnecting this app results into modules without an active app, we'll activate the Hustle's Local List for those modules.", 'hustle' ); ?></p>

</script>
