<?php
$module_steps = 1;
$dialog_class = 'sui-dialog sui-dialog-sm sui-dialog-alt';

$is_social_share = ( Hustle_Module_Model::SOCIAL_SHARING_MODULE === $module_type );

if ( ! $is_social_share ) {
	$module_steps = 2;
	$dialog_class = 'sui-dialog sui-dialog-alt';
}

$hide_branding = apply_filters( 'wpmudev_branding_hide_branding', false );
?>

<div id="hustle-new-module--dialog">

	<?php if ( ! $is_social_share ) { ?>

		<div class="sui-modal sui-modal-lg">

			<div
				role="dialog"
				id="hustle-new-module--type"
				class="sui-modal-content"
				aria-modal="true"
				aria-labelledby="hustle-new-module--type-title"
				aria-describedby="hustle-new-module--type-description"
			>

				<div role="document" class="sui-box">

					<div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">

						<span class="sui-box-steps sui-md sui-steps-float" aria-hidden="true">
							<span class="sui-current"></span>
							<span></span>
						</span>

						<button id="hustle-new-module--type-close" class="sui-button-icon sui-button-float--right" data-modal-close="">
							<i class="sui-icon-close sui-md" aria-hidden="true"></i>
							<span class="sui-screen-reader-text"><?php esc_html_e( 'Close create modal', 'hustle' ); ?></span>
						</button>

						<h4 id="hustle-new-module--type-title" class="sui-box-title sui-lg"><?php esc_html_e( 'Choose Content Type', 'hustle' ); ?></h4>

						<p id="hustle-new-module--type-description" class="sui-description"><?php esc_html_e( "Let's start by choosing an appropriate content type based on your goal.", 'hustle' ); ?></p>

					</div>

					<div class="sui-box-selectors sui-box-selectors-col-2">

						<ul role="radiogroup" id="hustle-module-types">

							<li><label for="optin" class="sui-box-selector">
								<input type="radio" name="mode" id="optin" value="optin" checked="checked" />
								<span><i class="sui-icon-mail" aria-hidden="true"></i> <?php esc_html_e( 'Email Opt-in', 'hustle' ); ?></span>
								<span><?php esc_html_e( 'Perfect for Newsletter signups, or collecting user data in general.', 'hustle' ); ?></span>
							</label></li>

							<li><label for="informational" class="sui-box-selector">
								<input type="radio" name="mode" id="informational" value="informational" />
								<span><i class="sui-icon-info" aria-hidden="true"></i> <?php esc_html_e( 'Informational', 'hustle' ); ?></span>
								<span><?php esc_html_e( 'Perfect for promotional offers with Call to Action.', 'hustle' ); ?></span>
							</label></li>

						</ul>

					</div>

					<div class="sui-box-footer sui-flatten sui-content-right">

						<button id="hustle-select-mode" class="sui-button"><?php esc_html_e( 'Next', 'hustle' ); ?></button>

					</div>

					<?php if ( ! $hide_branding ) { ?>
						<img
							src="<?php echo esc_url( self::$plugin_url . 'assets/images/hustle-create.png' ); ?>"
							srcset="<?php echo esc_url( self::$plugin_url . 'assets/images/hustle-create.png' ); ?> 1x, <?php echo esc_url( self::$plugin_url . 'assets/images/hustle-create@2x.png' ); ?> 2x"
							alt="<?php printf( esc_html__( 'Create New %s', 'hustle' ), esc_html( $capitalize_singular ) ); ?>"
							class="sui-image sui-image-center"
							aria-hidden="true"
						/>
					<?php } ?>

				</div>

			</div>

		</div>

	<?php } ?>

	<div class="sui-modal sui-modal-sm">

		<div
			role="dialog"
			id="hustle-new-module--create"
			class="sui-modal-content"
			aria-modal="true"
			aria-labelledby="hustle-new-module--create-title"
			aria-describedby="hustle-new-module--create-description"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_create_new_module' ) ); ?>"
		>

			<div role="document" class="sui-box">

				<div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">

					<?php if ( ! $is_social_share ) { ?>

						<span class="sui-box-steps sui-md sui-steps-float" aria-hidden="true">
							<span></span>
							<span class="sui-current"></span>
						</span>

						<button id="hustle-new-module--create-back" class="sui-button-icon sui-button-float--left">
							<i class="sui-icon-chevron-left sui-md" aria-hidden="true"></i>
							<span class="sui-screen-reader-text"><?php esc_html_e( 'Go back to choose module content type', 'hustle' ); ?></span>
						</button>

					<?php } ?>

					<button id="hustle-new-module--create-close" class="sui-button-icon sui-button-float--right" data-modal-close="">
						<i class="sui-icon-close sui-md" aria-hidden="true"></i>
						<span class="sui-screen-reader-text"><?php esc_html_e( 'Close create modal', 'hustle' ); ?></span>
					</button>

					<h4 id="hustle-new-module--create-title" class="sui-box-title sui-lg"><?php printf( esc_html__( 'Create %s', 'hustle' ), esc_html( $capitalize_singular ) ); ?></h4>

					<p id="hustle-new-module--create-description" class="sui-description"><?php printf( esc_html__( "Let's give your new %s module a name. What would you like to name it?", 'hustle' ), esc_html( $smallcaps_singular ) ); ?></p>

				</div>

				<div class="sui-box-body">

					<div class="sui-form-field">

						<label for="hustle-module-name" class="sui-screen-reader-text"><?php printf( esc_html__( '%s name', 'hustle' ), esc_html( $capitalize_singular ) ); ?></label>

						<div class="sui-with-button sui-inside">

							<input
								type="text"
								name="name"
								autocomplete="off"
								placeholder="<?php esc_html_e( 'E.g. Newsletter', 'hustle' ); ?>"
								id="hustle-module-name"
								class="sui-form-control sui-required"
								autofocus
							/>

							<button id="hustle-create-module" class="sui-button-icon sui-button-blue sui-button-filled sui-button-lg" disabled>
								<span class="sui-loading-text">
									<i class="sui-icon-arrow-right" aria-hidden="true"></i>
								</span>
								<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Done', 'hustle' ); ?></span>
							</button>

						</div>

						<span id="error-empty-name" class="sui-error-message" style="display: none;"><?php esc_html_e( 'Please add a name for this module.', 'hustle' ); ?></span>

						<span id="error-saving-settings" class="sui-error-message" style="display: none;"><?php esc_html_e( 'Something went wrong saving the settings. Make sure everything is okay.', 'hustle' ); ?></span>

						<span class="sui-description"><?php esc_html_e( 'This will not be visible anywhere on your website', 'hustle' ); ?></span>

					</div>

				</div>

				<?php if ( ! $hide_branding ) { ?>
					<img
						src="<?php echo esc_url( self::$plugin_url . 'assets/images/hustle-create.png' ); ?>"
						srcset="<?php echo esc_url( self::$plugin_url . 'assets/images/hustle-create.png' ); ?> 1x, <?php echo esc_url( self::$plugin_url . 'assets/images/hustle-create@2x.png' ); ?> 2x"
						alt="<?php printf( esc_html__( 'Create New %s', 'hustle' ), esc_html( $capitalize_singular ) ); ?>"
						class="sui-image sui-image-center"
						aria-hidden="true"
					/>
				<?php } ?>

			</div>

		</div>

	</div>

</div>
