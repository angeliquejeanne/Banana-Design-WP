<?php
/**
 * Confirmation modal for when deleting things. It's used in:
 * -Main dashboard page	=> for deleting modules.
 * -All listing pages	=> for deleting modules and for deleting their tracking data.
 * -Emails lists page	=> for deleting submission entries.
 * -Settings page		=> for deleting palettes and IPs.
 */
?>
<div id="hustle-dialog--delete" class="sui-dialog sui-dialog-alt sui-dialog-sm" aria-hidden="true" tabindex="-1">

	<div class="sui-dialog-overlay sui-fade-out" data-a11y-dialog-hide="hustle-dialog--delete"></div>

	<div role="dialog"
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription">

		<div class="sui-box" role="document">

			<div class="sui-box-header sui-block-content-center">

				<h3 id="hustle-dialog-title" class="sui-box-title"></h3>

				<button class="sui-dialog-close" data-a11y-dialog-hide="hustle-dialog--delete">
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

			</div>

			<div id="hustle-delete-dialog-content"></div>

		</div>

	</div>

</div>

<script type="text/template" id="hustle-dialog--delete-tpl">

	<div class="sui-box-body sui-box-body-slim sui-block-content-center">

		<p id="dialogDescription" class="sui-description">
			<# if ( 'undefined' !== typeof description ) { #>
				{{ description }}
			<# } #>
		</p>

		<form id="hustle-delete-form" method="post" style="margin-bottom: 10px;">

			<# if ( 'undefined' !== typeof action ) { #>
				<input type="hidden" name="hustle_action" value="{{ action }}" />
			<# } #>

			<# if ( 'undefined' !== typeof id ) { #>
				<input type="hidden" name="id" value="{{ id }}" />
				<input type="hidden" name="moduleId" value="{{ id }}" />
			<# } #>
			
			<?php // Used in Entries -> bulk actions ?>
			<# if ( 'undefined' !== typeof ids ) { #>
				<input type="hidden" name="ids" value="{{ ids }}" />
			<# } #>

			<# if ( 'undefined' !== typeof nonce ) { #>
				<input type="hidden" id="hustle_nonce" name="hustle_nonce" value="{{ nonce }}" />
			<# } #>

			<button type="button" class="sui-button sui-button-ghost" data-a11y-dialog-hide="hustle-dialog--delete">
				<?php esc_attr_e( 'Cancel', 'hustle' ); ?>
			</button>

			<button 
				class="sui-button sui-button-ghost sui-button-red hustle-delete-confirm {{ 'undefined' === typeof actionClass ? 'hustle-single-module-button-action' : actionClass }}"
				data-hustle-action="{{ 'undefined' === typeof action ?  'delete' : action }}"
				data-form-id="hustle-delete-form"
			>
				<span class="sui-loading-text">
					<i class="sui-icon-trash" aria-hidden="true"></i> <?php esc_attr_e( 'Delete', 'hustle' ); ?>
				</span>
				<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
			</button>

		</form>

	</div>

</script>
