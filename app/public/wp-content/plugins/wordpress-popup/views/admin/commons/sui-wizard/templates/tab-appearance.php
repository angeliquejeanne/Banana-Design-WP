<div class="sui-box" <?php echo 'appearance' !== $section ? 'style="display: none;"' : ''; ?> data-tab="appearance">

	<div class="sui-box-header">

		<h2 class="sui-box-title"><?php esc_html_e( 'Appearance', 'hustle' ); ?></h2>

	</div>

	<div id="hustle-wizard-appearance" class="sui-box-body">

		<?php

		// SETTING: Layout.
		self::static_render(
			'admin/commons/sui-wizard/tab-appearance/layout',
			array(
				'is_optin'           => $is_optin,
				'layout'             => $is_optin ? $settings['form_layout'] : $settings['style'],
				'smallcaps_singular' => $smallcaps_singular,
			)
		);

		// SETTING: Feature Image.
		self::static_render(
			'admin/commons/sui-wizard/tab-appearance/feature-image',
			array(
				'is_optin'           => $is_optin,
				'smallcaps_singular' => $smallcaps_singular,
				'settings'           => $settings,
				'feature_image'      => $feature_image,
			)
		);

		// SETTING: Vanilla theme.
		self::static_render(
			'admin/commons/sui-wizard/tab-appearance/vanilla-theme',
			array(
				'smallcaps_singular' => isset( $smallcaps_singular ) ? $smallcaps_singular : esc_html__( 'module', 'hustle' ),
				'settings'           => $settings,
			)
		);

		if ( $is_optin ) {

			// SETTING: Form Design.
			self::static_render( 'admin/commons/sui-wizard/tab-appearance/form-design',
				[ 'settings' => $settings ]
			);

		}

		// SETTING: CTA Button Design.
		self::static_render(
			'admin/commons/sui-wizard/tab-appearance/cta-design',
			[ 'settings' => $settings ]
		);

		// SETTING: Colors Palette.
		self::static_render(
			'admin/commons/sui-wizard/tab-appearance/colors-palette',
			array(
				'is_optin'           => $is_optin,
				'module_type'        => $module_type,
				'settings'           => $settings,
				'smallcaps_singular' => $smallcaps_singular,
			)
		);

		// SETTING: Border.
		self::static_render(
			'admin/commons/sui-wizard/tab-appearance/border',
			array(
				'settings'           => $settings,
				'module_type'        => $module_type,
				'smallcaps_singular' => $smallcaps_singular,
			)
		);

		// SETTING: Drop Shadow.
		self::static_render(
			'admin/commons/sui-wizard/tab-appearance/drop-shadow',
			array(
				'settings'           => $settings,
				'module_type'        => $module_type,
				'smallcaps_singular' => $smallcaps_singular,
			)
		);

		// SETTING: Custom Size.
		self::static_render(
			'admin/commons/sui-wizard/tab-appearance/custom-size',
			array(
				'settings'            => $settings,
				'capitalize_singular' => $capitalize_singular,
				'smallcaps_singular'  => $smallcaps_singular,
			)
		);

		// SETTING: Custom CSS.
		self::static_render(
			'admin/commons/sui-wizard/tab-appearance/custom-css',
			[
				'is_optin'    => $is_optin,
				'module_type' => $module_type,
				'settings'    => $settings,
			]
		);
		?>

	</div>

	<div class="sui-box-footer">

		<button class="sui-button wpmudev-button-navigation" data-direction="prev">
			<i class="sui-icon-arrow-left" aria-hidden="true"></i> <?php echo $is_optin ? esc_html__( 'Integrations', 'hustle' ) : esc_html__( 'Content', 'hustle' ); ?>
		</button>

		<div class="sui-actions-right">

			<button class="sui-button sui-button-icon-right wpmudev-button-navigation" data-direction="next">
				<?php echo 'embedded' === $module_type ? esc_html_e( 'Display Options', 'hustle' ) : esc_html_e( 'Visibility', 'hustle' ); ?> <i class="sui-icon-arrow-right" aria-hidden="true"></i>
			</button>

		</div>

	</div>

</div>

