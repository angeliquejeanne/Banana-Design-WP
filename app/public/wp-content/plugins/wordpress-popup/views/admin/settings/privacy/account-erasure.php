<?php
$submission_erasure_enabled = '1' === $settings['retain_sub_on_erasure'];
?>

<fieldset class="sui-form-field">

	<label class="sui-settings-label"><?php esc_html_e( 'Account Erasure Requests', 'hustle' ); ?></label>

	<span class="sui-description"><?php echo( sprintf( __( 'When handling an <a href="%s">account erasure request</a> that contains an email associated with a submission, what do you want to do?', 'hustle' ), esc_url( admin_url( 'tools.php?page=remove_personal_data' ) ) ) ); // wpcs: xss ok. ?></span>

	<div class="sui-side-tabs" style="margin-top: 10px;">

		<div class="sui-tabs-menu">

			<label for="retain_sub_on_erasure-true" class="sui-tab-item">
				<input type="radio"
					name="retain_sub_on_erasure"
					value="1"
					id="retain_sub_on_erasure-true"
					<?php checked( $submission_erasure_enabled, true ); ?> />
				<?php esc_html_e( 'Retain Submission', 'hustle' ); ?>
			</label>

			<label for="retain_sub_on_erasure-false" class="sui-tab-item">
				<input type="radio"
					name="retain_sub_on_erasure"
					value="0"
					id="retain_sub_on_erasure-false"
					<?php checked( $submission_erasure_enabled, false ); ?> />
				<?php esc_html_e( 'Remove Submission', 'hustle' ); ?>
			</label>

		</div>

	</div>

</fieldset>
