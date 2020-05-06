<div class="sui-box" <?php echo 'display' !== $section ? 'style="display: none;"' : ''; ?> data-tab="display">

	<div class="sui-box-header">

		<h2 class="sui-box-title"><?php esc_html_e( 'Display Options', 'hustle' ); ?></h2>

	</div>

	<div id="hustle-wizard-display" class="sui-box-body">

		<?php
		// SETTING: Floating Social.
		self::static_render(
			'admin/sshare/display-options/tpl--floating-social',
			[ 'settings' => $settings ]
		);

		// SETTING: Inline Content.
		self::static_render(
			'admin/sshare/display-options/tpl--inline-content',
			[ 'settings' => $settings ]
		);

		// SETTING: Widget.
		self::static_render(
			'admin/sshare/display-options/tpl--widget',
			[ 'is_widget_enabled' => $settings['widget_enabled'] ]
		);

		// SETTING: Shortcode.
		self::static_render(
			'admin/sshare/display-options/tpl--shortcode',
			[
				'shortcode_id'         => $shortcode_id,
				'is_shortcode_enabled' => $settings['shortcode_enabled'],
			]
		);
		?>

	</div>

	<div class="sui-box-footer">

		<button class="sui-button wpmudev-button-navigation" data-direction="prev">
			<i class="sui-icon-arrow-left" aria-hidden="true"></i> <?php esc_html_e( 'Services', 'hustle' ); ?>
		</button>

		<div class="sui-actions-right">

			<button class="sui-button sui-button-icon-right wpmudev-button-navigation" data-direction="next">
				<?php esc_html_e( 'Appearance', 'hustle' ); ?> <i class="sui-icon-arrow-right" aria-hidden="true"></i>
			</button>

		</div>

	</div>

</div>
