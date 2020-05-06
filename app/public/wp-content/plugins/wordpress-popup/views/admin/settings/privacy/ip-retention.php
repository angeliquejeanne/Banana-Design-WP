<?php
$retain_ip_forever   = '1' === $settings['retain_ip_forever'];
$ip_retention_number = $settings['ip_retention_number'];
$ip_retention_unit   = $settings['ip_retention_number_unit'];
?>

<fieldset class="sui-form-field">

	<label class="sui-settings-label"><?php esc_html_e( 'IP Retention', 'hustle' ); ?></label>

	<span class="sui-description"><?php esc_html_e( 'Choose how long to retain IP address before submission or tracking data entry is anonymized in your database.', 'hustle' ); ?></span>

	<div class="sui-side-tabs" style="margin-top: 10px;">

		<div class="sui-tabs-menu">

			<label class="sui-tab-item">
				<input type="radio"
				name="retain_ip_forever"
				id="hustle-retain-ip-forever--on"
				value="1"
				<?php checked( $retain_ip_forever, true ); ?> />
				<?php esc_html_e( 'Forever', 'hustle' ); ?>
			</label>

			<label class="sui-tab-item">
				<input type="radio"
				name="retain_ip_forever"
				id="hustle-retain-ip-forever--off"
				data-tab-menu="ip-retention-number"
				value="0"
				<?php checked( $retain_ip_forever, false ); ?> />
				<?php esc_html_e( 'Custom', 'hustle' ); ?>
			</label>
		</div>

		<div class="sui-tabs-content">
			<div class="sui-tab-boxed" data-tab-content="ip-retention-number">
				<div class="sui-row">
					<div class="sui-col-md-6">
						<input type="number"
							name="ip_retention_number"
							value="<?php echo esc_attr( $ip_retention_number ); ?>"
							placeholder="0"
							class="sui-form-control" />
					</div>
					<div class="sui-col-md-6" >
						<select name="ip_retention_number_unit" id="hustle-select-ip_retention_number_unit">
							<option value="days" <?php selected( 'days', $ip_retention_unit, true ); ?>><?php esc_html_e( 'day(s)', 'hustle' ); ?></option>
							<option value="weeks"  <?php selected( 'weeks', $ip_retention_unit, true ); ?>><?php esc_html_e( 'week(s)', 'hustle' ); ?></option>
							<option value="months" <?php selected( 'months', $ip_retention_unit, true ); ?>><?php esc_html_e( 'month(s)', 'hustle' ); ?></option>
							<option value="years" <?php selected( 'years', $ip_retention_unit, true ); ?>><?php esc_html_e( 'year(s)', 'hustle' ); ?></option>
						</select>
					</div>
				</div>
			</div>
		</div>

	</div>

</fieldset>
