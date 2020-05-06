<?php
/**
 * Dialog used in the modules' listing pages for importing modules.
 */
$is_ssharing = Hustle_Module_Model::SOCIAL_SHARING_MODULE === $module_type;

if ( ! $is_ssharing ) {

	$module_type_display_name = Opt_In_Utils::get_module_type_display_name( $module_type );

	$metas_optin           = Hustle_Module_Model::instance()->get_module_meta_names( $module_type, Hustle_Module_Model::OPTIN_MODE, true );
	$optin_settings_markup = self::static_render(
		'admin/commons/sui-listing/dialogs/import-module-settings-section',
		[
			'metas' => $metas_optin,
			'id'    => 'optin',
		],
		true
	);

	$metas_info           = Hustle_Module_Model::instance()->get_module_meta_names( $module_type, Hustle_Module_Model::INFORMATIONAL_MODE, true );
	$info_settings_markup = self::static_render(
		'admin/commons/sui-listing/dialogs/import-module-settings-section',
		[
			'metas' => $metas_info,
			'id'    => 'info',
		],
		true
	);

} else {
	$metas                    = Hustle_Module_Model::instance()->get_module_meta_names( $module_type, '', true );
	$ssharing_settings_markup = self::static_render(
		'admin/commons/sui-listing/dialogs/import-module-settings-section',
		[
			'metas' => $metas,
			'id'    => 'ssharing',
		],
		true
	);
}
?>

<div class="sui-modal sui-modal-md">

	<div
		role="dialog"
		id="hustle-dialog--import"
		class="sui-modal-content"
		aria-modal="true"
		aria-labelledby="hustle-dialog--import-title"
		aria-describedby="hustle-dialog--import-description"
	>

		<div role="document" class="sui-box">

			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">

				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

				<?php /* translators: current module type display name capitalized and singular */ ?>
				<h3 id="hustle-dialog--import-title" class="sui-box-title sui-lg"><?php printf( esc_html__( 'Import %s', 'hustle' ), esc_html( $capitalize_singular ) ); ?></h3>

				<p id="hustle-dialog--import-description" class="sui-description"><?php esc_html_e( "Choose the configuration file and the settings you want to import. We'll import the settings which are available and apply to this module and keep the other settings to their default values.", 'hustle' ); ?></p>

			</div>

			<form id="hustle-import-module-form">

				<div class="sui-box-body sui-box-body-slim">

					<div role="alert" class="sui-notice sui-notice-error" style="display:none;" hidden>
						<p></p>
					</div>

					<div class="sui-form-field">

						<label class="sui-label"><?php esc_html_e( 'Configuration file', 'hustle' ); ?></label>

						<div class="sui-upload">

							<input
								id="hustle-import-file-input"
								class="hustle-file-input"
								type="file"
								name="import_file"
								value=""
								readonly="readonly"
								accept=".json"
							/>

							<button class="sui-upload-button" type="button" for="hustle-import-file-input">
								<i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php esc_html_e( 'Upload file', 'hustle' ); ?>
							</button>

							<div class="sui-upload-file">

								<span></span>

								<button type="button" aria-label="Remove file">
									<i class="sui-icon-close" aria-hidden="true"></i>
								</button>

							</div>

						</div>

						<span class="sui-description" style="margin-top: 10px;"><?php esc_html_e( 'Choose the configuration file (.json) to import the settings from.', 'hustle' ); ?></span>

					</div>

					<div id="hustle-import-modal-options" class="sui-form-field"></div>

				</div>

				<div class="sui-box-footer sui-content-separated">

					<button type="button" class="sui-button sui-button-ghost" data-modal-close>
						<?php esc_html_e( 'Cancel', 'hustle' ); ?>
					</button>

					<button
						id="hustle-import-module-submit-button"
						class="hustle-single-module-button-action sui-button"
						data-hustle-action="import"
						data-form-id="hustle-import-module-form"
						data-type="<?php echo esc_attr( $module_type ); ?>"
						disabled
					>

						<span class="sui-loading-text">
							<i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php esc_html_e( 'Import', 'hustle' ); ?>
						</span>

						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

					</button>

				</div>

			</form>

		</div>

	</div>

	<script id="hustle-import-modal-options-tpl" type="text/template">

		<?php
		/**
		 * Non Social Sharing Markup.
		 * We have different set of settings for an "optin" and an "informational" module,
		 * reason why we need to split these import settings from social sharing settings
		 * to avoid any error in the future.
		 */
		if ( ! $is_ssharing ) : ?>

			<# if ( isNew ) { #>

				<label class="sui-label"><?php esc_html_e( 'Module type', 'hustle' ); ?></label>

				<div class="sui-tabs sui-side-tabs">

					<input tabindex="-1" type="radio" name="module_mode" value="default" id="hustle-import-options--default" style="display: none;" aria-hidden="true" hidden checked />
					<input tabindex="-1" type="radio" name="module_mode" value="<?php echo esc_attr( Hustle_Module_Model::OPTIN_MODE ); ?>" id="hustle-import-options--optin" style="display: none;" aria-hidden="true" hidden />
					<input tabindex="-1" type="radio" name="module_mode" value="<?php echo esc_attr( Hustle_Module_Model::INFORMATIONAL_MODE ); ?>" id="hustle-import-options--info" style="display: none;" aria-hidden="true" hidden />

					<div role="tablist" class="sui-tabs-menu">

						<button
							type="button"
							role="tab"
							id="hustle-import-options--default-tab"
							class="sui-tab-item active"
							aria-controls="hustle-import-options--default-content"
							aria-selected="true"
							data-label-for="hustle-import-options--default"
						>
							<?php esc_html_e( 'Default', 'hustle' ); ?>
						</button>

						<button
							type="button"
							role="tab"
							id="hustle-import-options--optin-tab"
							class="sui-tab-item"
							aria-controls="hustle-import-options--optin-content"
							aria-selected="false"
							data-label-for="hustle-import-options--optin"
						>
							<?php esc_html_e( 'Email Opt-in', 'hustle' ); ?>
						</button>

						<button
							type="button"
							role="tab"
							id="hustle-import-options--info-tab"
							class="sui-tab-item"
							aria-controls="hustle-import-options--info-content"
							aria-selected="false"
							data-label-for="hustle-import-options--info"
							tabindex="-1"
						>
							<?php esc_html_e( 'Informational', 'hustle' ); ?>
						</button>

					</div>

					<div class="sui-tabs-content">

						<div
							role="tabpanel"
							tabindex="0"
							id="hustle-import-options--optin-content"
							class="sui-tab-content sui-border-frame"
							aria-labelledby="hustle-import-options--optin-tab"
						>

							<?php echo $optin_settings_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

						</div>

						<div
							role="tabpanel"
							tabindex="0"
							id="hustle-import-options--info-content"
							class="sui-tab-content sui-border-frame"
							aria-labelledby="hustle-import-options--info-tab"
							hidden
						>

							<?php echo $info_settings_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

						</div>

					</div>

					<p class="sui-description"><?php printf( __( 'Choose the module type of the %s you want to create. The default is to take the module type from the configuration file and import all the settings from it.', 'hustle' ), $module_type_display_name ); ?></p>

				</div>

			<# } else { #>

				<# if ( isOptin ) { #>
					<?php echo $optin_settings_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<# } else { #>
					<?php echo $info_settings_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<# } #>

			<# } #>

		<?php else : ?>

			<?php echo $ssharing_settings_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php endif; ?>

	</script>

</div>
