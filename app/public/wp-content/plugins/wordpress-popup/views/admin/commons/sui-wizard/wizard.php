<main class="<?php echo implode( ' ', apply_filters( 'hustle_sui_wrap_class', null ) ); ?>">

	<div class="sui-header sui-header-inline sui-with-floating-input">
		<h1 class="sui-header-title"><?php printf( esc_html__( 'Edit %s', 'hustle' ), esc_html( $capitalize_singular ) ); ?></h1>
		<?php self::static_render( 'admin/commons/view-documentation' ); ?>
	</div>

	<div id="<?php echo esc_attr( $page_id ); ?>" class="sui-row-with-sidenav" data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_save_module_wizard' ) ); ?>" data-id="<?php echo $module_id ? esc_attr( $module_id ) : '-1'; ?>">

		<?php
		// ELEMENT: Side Navigation
		self::static_render(
			'admin/commons/sui-wizard/navigation',
			array(
				'is_optin'    => isset( $module_mode ) ? $module_mode : false,
				'section'     => $page_tab,
				'wizard_tabs' => $wizard_tabs,
				'module_name' => $module_name,
				'module_type' => $module_type,
			)
		); ?>

		<div class="hustle-wizard-main-view">

			<?php
			// ELEMENT: Status Bar
			self::static_render(
				'admin/commons/sui-wizard/status-bar',
				array( 'is_active' => $module_status )
			); ?>

			<?php foreach ( $wizard_tabs as $option ) {

				$option_array = array();

				if ( isset( $option['support'] ) ) {
					$option_array = $option['support'];
				}

				if ( isset( $option['is_optin'] ) ) {

					if ( $module_mode ) :

						self::static_render(
							$option['template'],
							$option_array
						);

					endif;

				} else {

					self::static_render(
						$option['template'],
						$option_array
					);
				}
			} ?>

		</div>

	</div>

	<?php self::static_render( 'admin/footer/footer' ); ?>

	<?php if ( isset( $module_mode ) && $module_mode ) : ?>

		<?php
		// DIALOG: Integrations
		self::static_render(
			'admin/dialogs/modal-integration',
			array( 'module_type' => $module_type )
		); ?>

		<?php
		// DIALOG: Optin Fields
		self::static_render(
			'admin/dialogs/optin-fields',
			array(
				'form_elements' => $form_elements,
			)
		); ?>

		<?php
		// DIALOG: Edit Field
		self::static_render( 'admin/dialogs/edit-field', array(
			'available_recaptchas' => Hustle_Settings_Admin::get_available_recaptcha_versions(),
		) ); ?>

		<?php
		// DIALOG: Final Field
		self::static_render( 'admin/dialogs/final-integration-form-delete', array() ); ?>

		<?php
		// Row: Optin Field Row template
		self::static_render( 'admin/commons/sui-wizard/elements/form-field', array() ); ?>

	<?php endif; ?>

	<?php
	// CHECK: Visibility Tab
	if ( array_key_exists( 'visibility', $wizard_tabs ) ) {

		// DIALOG: Visibility
		self::static_render(
			'admin/commons/sui-wizard/dialogs/visibility-options',
			array( 'smallcaps_singular' => $smallcaps_singular )
		);

		// TEMPLATE: Conditions
		self::static_render(
			'admin/commons/sui-wizard/tab-visibility/conditions',
			array(
				'smallcaps_singular' => $smallcaps_singular,
				'module_type'        => $module_type,
			)
		);
	}

	// CHECK: Services Tab
	if ( array_key_exists( 'services', $wizard_tabs ) ) {

		// DIALOG: Social Platforms
		self::static_render(
			'admin/commons/sui-wizard/dialogs/add-platforms',
			array()
		);
	}

	// DIALOG: Publish Flow
	self::static_render(
		'admin/commons/sui-wizard/dialogs/publish-flow',
		array(
			'capitalize_singular' => $capitalize_singular,
			'smallcaps_singular'  => $smallcaps_singular,
		)
	);

	// If embedded or social sharing, show the preview dialog to embed the module into.
	if ( Hustle_Module_Model::EMBEDDED_MODULE === $module_type || Hustle_Module_Model::SOCIAL_SHARING_MODULE === $module_type ) {
		// DIALOG: Preview
		self::static_render( 'admin/dialogs/preview-dialog' );
	}

	// DIALOG: Dissmiss migrate tracking notice modal confirmation.
	if ( Hustle_Module_Admin::is_show_migrate_tracking_notice() ) {
		self::static_render( 'admin/dashboard/dialogs/migrate-dismiss-confirmation' );
	}
	?>
</main>
