<?php $ip_tracking = 'on' === $settings['ip_tracking']; ?>

<fieldset class="sui-form-field">

	<label class="sui-settings-label"><?php esc_html_e( 'IP Tracking', 'hustle' ); ?></label>

	<span class="sui-description"><?php esc_html_e( 'Choose whether you want to track the IP address of your visitors while collecting tracking data and submissions.', 'hustle' ); ?></span>

	<div class="sui-side-tabs" style="margin-top: 10px;">

		<div class="sui-tabs-menu">

			<label class="sui-tab-item">
				<input type="radio"
				name="ip_tracking"
				id="hustle-ip-tracking--on"
				value="on"
				data-tab-menu="exclude-ips"
				<?php checked( $ip_tracking, true ); ?> />
				<?php esc_html_e( 'Enable', 'hustle' ); ?>
			</label>

			<label class="sui-tab-item">
				<input type="radio"
				name="ip_tracking"
				id="hustle-ip-tracking--off"
				value="off"
				<?php checked( $ip_tracking, false ); ?> />
				<?php esc_html_e( 'Disable', 'hustle' ); ?>
			</label>

		</div>

	</div>

</fieldset>
