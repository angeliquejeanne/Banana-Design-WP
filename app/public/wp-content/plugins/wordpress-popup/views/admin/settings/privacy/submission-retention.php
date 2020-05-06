<?php
$retain_submission_forever = '1' === $settings['retain_submission_forever'];
$submissions_retention_unit = $settings['submissions_retention_number_unit'];
?>

<fieldset class="sui-form-field">

	<label class="sui-settings-label"><?php esc_html_e( 'Submissions Retention', 'hustle' ); ?></label>

	<span class="sui-description"><?php esc_html_e( 'Choose how long to retain each module’s submissions for.', 'hustle' ); ?></span>

	<div class="sui-side-tabs" style="margin-top: 10px;">

		<div class="sui-tabs-menu">

			<label class="sui-tab-item">
				<input type="radio"
				name="retain_submission_forever"
				id="hustle-retain-submission-forever--on"
				value="1"
				<?php checked( $retain_submission_forever, true ); ?> />
				<?php esc_html_e( 'Forever', 'hustle' ); ?>
			</label>

			<label class="sui-tab-item">
				<input type="radio"
				name="retain_submission_forever"
				id="hustle-retain-submission-forever--off"
				data-tab-menu="retention-number"
				value="0"
				<?php checked( $retain_submission_forever, false ); ?> />
				<?php esc_html_e( 'Custom', 'hustle' ); ?>
			</label>
		</div>

		<div class="sui-tabs-content">
			<div class="sui-tab-boxed" data-tab-content="retention-number">
				<div class="sui-row">
					<div class="sui-col-md-6">
						<input type="number"
							name="submissions_retention_number"
							value="<?php echo esc_attr( $settings['submissions_retention_number'] ); ?>"
							placeholder="0"
							class="sui-form-control" />
					</div>
					<div class="sui-col-md-6" >
						<select name="submissions_retention_number_unit" id="hustle-select-submissions_retention_number_unit">
							<option value="days" <?php selected( 'days', $submissions_retention_unit, true ); ?>><?php esc_html_e( 'day(s)', 'hustle' ); ?></option>
							<option value="weeks"  <?php selected( 'weeks', $submissions_retention_unit, true ); ?>><?php esc_html_e( 'week(s)', 'hustle' ); ?></option>
							<option value="months" <?php selected( 'months', $submissions_retention_unit, true ); ?>><?php esc_html_e( 'month(s)', 'hustle' ); ?></option>
							<option value="years" <?php selected( 'years', $submissions_retention_unit, true ); ?>><?php esc_html_e( 'year(s)', 'hustle' ); ?></option>
						</select>
					</div>
				</div>
			</div>
		</div>

	</div>

</fieldset>
