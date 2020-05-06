<script id="hustle-form-field-row-tpl" type="text/template">

	<div
		id="hustle-optin-field--{{ name }}"
		class="sui-builder-field sui-can-move ui-sortable-handle"
		data-field-id="{{ name }}"
	>

		<i class="sui-icon-drag" aria-hidden="true"></i>

		<div class="sui-builder-field-label">

			<i class="sui-icon-{{ icon }}" aria-hidden="true"></i>

			<span class="hustle-field-label"><span class="hustle-field-label-text">{{ label }}</span> <span class="sui-error"{{ ( _.isFalse( required ) ) ? 'style=display:none;' : '' }}>*</span></span>

		</div>

		<div class="sui-dropdown">

			<button class="sui-button-icon sui-dropdown-anchor">
				<i class="sui-icon-widget-settings-config" aria-hidden="true"></i>
				<span class="sui-screen-reader-text">{{ label }} <?php esc_html_e( 'field settings', 'hustle' ); ?></span>
			</button>

			<ul>

				<li><button class="hustle-optin-field--edit">
					<i class="sui-icon-pencil" aria-hidden="true"></i> <?php esc_html_e( 'Edit Field', 'hustle' ); ?>
				</button></li>

				<li><button class="hustle-optin-field--copy">
					<i class="sui-icon-copy" aria-hidden="true"></i> <?php esc_html_e( 'Duplicate', 'hustle' ); ?>
				</button></li>

				<# if ( 'undefined' !== typeof can_delete && _.isTrue( can_delete ) ) { #>
					<li><button class="hustle-optin-field--delete">
						<i class="sui-icon-trash" aria-hidden="true"></i> <?php esc_html_e( 'Delete', 'hustle' ); ?>
					</button></li>
				<# } #>

			</ul>

		</div>

	</div>

</script>
