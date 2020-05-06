<div class="sui-box" <?php echo ( 'content' !== $section ) ? ' style="display: none;"' : ''; ?> data-tab="content">

	<div class="sui-box-header">

		<h2 class="sui-box-title"><?php esc_html_e( 'Content', 'hustle' ); ?></h2>

	</div>

	<div id="hustle-wizard-content" class="sui-box-body">

		<?php
		// SETTING: Title.
		self::static_render(
			'admin/commons/sui-wizard/tab-content/title',
			[
				'settings'           => $settings,
				'smallcaps_singular' => $smallcaps_singular,
			]
		);

		// SETTING: Feature Image.
		self::static_render(
			'admin/commons/sui-wizard/tab-content/feature-image',
			[ 'feature_image' => $settings['feature_image'] ]
		);

		// SETTING: Main Content.
		self::static_render(
			'admin/commons/sui-wizard/tab-content/main-content',
			[ 'main_content' => $settings['main_content'] ]
		);

		// SETTING: Call To Action.
		self::static_render(
			'admin/commons/sui-wizard/tab-content/call-to-action',
			[
				'settings'           => $settings,
				'smallcaps_singular' => $smallcaps_singular,
			]
		);

		if ( ! empty( $module_type ) && 'embedded' !== $module_type ) {

			// SETTING: "Never See This Link" Again.
			self::static_render(
				'admin/commons/sui-wizard/tab-content/never-see-link',
				[ 'settings' => $settings ]
			);
		}
		?>

	</div>

	<div class="sui-box-footer">

		<div class="sui-actions-right">
			<button class="sui-button sui-button-icon-right wpmudev-button-navigation" data-direction="next">
				<span class="sui-loading-text">
					<?php echo $is_optin ? esc_html__( 'Emails', 'hustle' ) : esc_html__( 'Appearance', 'hustle' ); ?> <i class="sui-icon-arrow-right" aria-hidden="true"></i>
				</span>
				<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
			</button>
		</div>

	</div>

</div>
