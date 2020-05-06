<div class="sui-box" <?php if ( 'services' !== $section ) echo ' style="display: none;"'; ?> data-tab="services">

	<div class="sui-box-header">

		<h2 class="sui-box-title"><?php esc_html_e( 'Services', 'hustle' ); ?></h2>

	</div>

	<div id="hustle-wizard-content" class="sui-box-body">

		<?php
		// SETTING: Counter.
		self::static_render(
			'admin/sshare/services/tpl--counter',
			[ 'counter_enabled' => $settings['counter_enabled'] ]
		);

		// SETTING: Social Services.
		self::static_render( 'admin/sshare/services/tpl--social-services' );
		?>

	</div>

	<div class="sui-box-footer">

		<div class="sui-actions-right">
			<button class="sui-button sui-button-icon-right wpmudev-button-navigation" data-direction="next">
				<span class="sui-loading-text">
					<?php esc_html_e( 'Display Options', 'hustle' ); ?> <i class="sui-icon-arrow-right" aria-hidden="true"></i>
				</span>
				<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
			</button>
		</div>

	</div>

</div>
