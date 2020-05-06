<?php
$retain_tracking_forever   = '1' === $settings['retain_tracking_forever'];
$tracking_retention_number = $settings['tracking_retention_number'];
$tracking_retention_unit   = $settings['tracking_retention_number_unit'];
?>

<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Tracking Data Privacy', 'hustle' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Choose how you want to handle the tracking data (views and conversions) of modules.', 'hustle' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<label class="sui-settings-label"><?php esc_html_e( 'Tracking Data Retention', 'hustle' ); ?></label>
		<span class="sui-description"><?php esc_html_e( 'Choose how long to retain the tracking data of your modules.', 'hustle' ); ?></span>

		<div class="sui-side-tabs" style="margin-top: 10px;">

			<div class="sui-tabs-menu">

				<label class="sui-tab-item">
					<input type="radio"
					name="retain_tracking_forever"
					id="hustle-retain-tracking-forever--on"
					value="1"
					<?php checked( $retain_tracking_forever, true ); ?> />
					<?php esc_html_e( 'Forever', 'hustle' ); ?>
				</label>

				<label class="sui-tab-item">
					<input type="radio"
					name="retain_tracking_forever"
					id="hustle-retain-tracking-forever--off"
					data-tab-menu="tracking-retention-number"
					value="0"
					<?php checked( $retain_tracking_forever, false ); ?> />
					<?php esc_html_e( 'Custom', 'hustle' ); ?>
				</label>
			</div>

			<div class="sui-tabs-content">
				<div class="sui-tab-boxed" data-tab-content="tracking-retention-number">
					<div class="sui-row">
						<div class="sui-col-md-6">
							<input type="number"
								name="tracking_retention_number"
								value="<?php echo esc_attr( $tracking_retention_number ); ?>"
								placeholder="0"
								class="sui-form-control" />
						</div>
						<div class="sui-col-md-6" >
							<select name="tracking_retention_number_unit" id="hustle-select-tracking_retention_number_unit">
								<option value="days" <?php selected( 'days', $tracking_retention_unit, true ); ?>><?php esc_html_e( 'day(s)', 'hustle' ); ?></option>
								<option value="weeks"  <?php selected( 'weeks', $tracking_retention_unit, true ); ?>><?php esc_html_e( 'week(s)', 'hustle' ); ?></option>
								<option value="months" <?php selected( 'months', $tracking_retention_unit, true ); ?>><?php esc_html_e( 'month(s)', 'hustle' ); ?></option>
								<option value="years" <?php selected( 'years', $tracking_retention_unit, true ); ?>><?php esc_html_e( 'year(s)', 'hustle' ); ?></option>
							</select>
						</div>
					</div>
				</div>

			</div>

		</div>

	</div>

</div>
