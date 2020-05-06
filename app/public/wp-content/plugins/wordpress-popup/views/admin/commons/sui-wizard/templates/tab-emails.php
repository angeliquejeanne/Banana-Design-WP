<div class="sui-box" <?php echo 'emails' !== $section ? 'style="display: none;"' : ''; ?> data-tab="emails">

	<div class="sui-box-header">

		<h2 class="sui-box-title"><?php esc_html_e( 'Emails', 'hustle' ); ?></h2>

	</div>

	<div id="hustle-wizard-emails" class="sui-box-body">

		<?php
		// SETTING: Opt-in Form Fields.
		self::static_render( 'admin/commons/sui-wizard/tab-emails/form-fields' );

		// SETTING: Submission Behavior.
		self::static_render(
			'admin/commons/sui-wizard/tab-emails/submission-behaviour',
			[ 'settings' => $settings ]
		);

		// SETTING: Automated Email.
		self::static_render(
			'admin/commons/sui-wizard/tab-emails/automated-email',
			[ 'settings' => $settings ]
		);
		?>

	</div>

	<div class="sui-box-footer">

		<button class="sui-button wpmudev-button-navigation" data-direction="prev"><i class="sui-icon-arrow-left" aria-hidden="true"></i> <?php esc_html_e( 'Content', 'hustle' ); ?></button>

		<div class="sui-actions-right">
			<button class="sui-button sui-button-icon-right wpmudev-button-navigation" data-direction="next"><?php esc_html_e( 'Integrations', 'hustle' ); ?> <i class="sui-icon-arrow-right" aria-hidden="true"></i></button>
		</div>

	</div>

</div>
