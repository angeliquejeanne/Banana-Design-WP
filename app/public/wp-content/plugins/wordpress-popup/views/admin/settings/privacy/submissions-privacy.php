<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Submissions Privacy', 'hustle' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Choose how you want to handle the storage of module submissions.', 'hustle' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<?php
		// Submission retention.
		$this->render(
			'admin/settings/privacy/submission-retention',
			array( 'settings' => $settings )
		);
		?>

		<?php
		// On Account erasure retention.
		$this->render(
			'admin/settings/privacy/account-erasure',
			array( 'settings' => $settings )
		);
		?>

	</div>

</div>
