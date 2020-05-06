<?php
$fields = [
	'name'       => [
		'icon'  => 'profile-male',
		'label' => __( 'Name', 'hustle' ),
	],
	'email'      => [
		'icon'  => 'mail',
		'label' => __( 'Email', 'hustle' ),
	],
	'phone'      => [
		'icon'  => 'phone',
		'label' => __( 'Phone', 'hustle' ),
	],
	'address'    => [
		'icon'  => 'pin',
		'label' => __( 'Address', 'hustle' ),
	],
	'url'        => [
		'icon'  => 'web-globe-world',
		'label' => __( 'Website', 'hustle' ),
	],
	'text'       => [
		'icon'  => 'style-type',
		'label' => __( 'Text', 'hustle' ),
	],
	'number'     => [
		'icon'  => 'element-number',
		'label' => __( 'Number', 'hustle' ),
	],
	'datepicker' => [
		'icon'  => 'calendar',
		'label' => __( 'Datepicker', 'hustle' ),
	],
	'timepicker' => [
		'icon'  => 'clock',
		'label' => __( 'Timepicker', 'hustle' ),
	],
	'recaptcha'  => [
		'icon'   => 'recaptcha',
		'label'  => __( 'reCaptcha', 'hustle' ),
		'single' => true,
	],
	'gdpr'       => [
		'icon'   => 'gdpr',
		'label'  => __( 'GDPR Approval', 'hustle' ),
		'single' => true,
	],
	'hidden'     => [
		'icon'  => 'eye-hide',
		'label' => __( 'Hidden Field', 'hustle' ),
	],
];

?>
<div id="hustle-dialog--optin-fields" class="sui-dialog" aria-hidden="true" tabindex="-1">

	<div class="sui-dialog-overlay sui-fade-out"></div>

	<div role="dialog"
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription">

		<div class="sui-box" role="document">

			<div class="sui-box-header">


		     	<h3 class="sui-box-title" id="dialogTitle"><?php esc_html_e( 'Insert Fields', 'hustle' ); ?></h3>

		     	<div class="sui-actions-right">

		     		<button class="hustle-cancel-insert-fields sui-dialog-close">
		     			<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
		     		</button>

				</div>

			</div>

			<div class="sui-box-body">

				<p><?php esc_html_e( 'Choose which fields you want to insert into your opt-in form.', 'hustle' ); ?></p>

			</div>

			<div class="sui-box-selectors sui-box-selectors-col-5">

				<ul class="sui-spacing-slim">

					<?php foreach ( $fields as $field_type => $data ) : ?>

						<li><label for="hustle-optin-insert-field--<?php echo esc_attr( $field_type ); ?>" class="sui-box-selector sui-box-selector-vertical <?php echo empty( $data['single'] ) ? '' : 'hustle-skip'; ?>">
							<input
								id="hustle-optin-insert-field--<?php echo esc_attr( $field_type ); ?>"
								type="checkbox"
								value="<?php echo esc_attr( $field_type ); ?>"
								name="optin_fields"
								<?php
								if ( ! empty( $data['single'] ) ) {
									disabled( array_key_exists( $field_type, $form_elements ) );
									checked( array_key_exists( $field_type, $form_elements ) );
								}
								?>
							/>
							<span>
								<i class="sui-icon-<?php echo esc_attr( $data['icon'] ); ?>" aria-hidden="true"></i>
								<?php echo esc_html( $data['label'] ); ?>
							</span>
						</label></li>

					<?php endforeach; ?>

				</ul>

			</div>

			<div class="sui-box-footer">

				<button class="sui-button sui-button-ghost hustle-cancel-insert-fields">
					<?php esc_attr_e( 'Cancel', 'hustle' ); ?>
				</button>

				<div class="sui-actions-right">

					<button id="hustle-insert-fields" class="sui-button sui-button-blue">
						<span class="sui-loading-text"><?php esc_attr_e( 'Insert Fields', 'hustle' ); ?></span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</button>

				</div>

			</div>

		</div>

	</div>

</div>
