<?php
$selectors = [
	'modal_selectors' => [
		[
			'name'     => 'Layout',
			'selector' => '.hustle-layout .hustle-layout-content ',
		],
		[
			'name'     => 'Title',
			'selector' => '.hustle-layout .hustle-title',
		],
		[
			'name'     => 'Subtitle',
			'selector' => '.hustle-layout .hustle-subtitle ',
		],
		[
			'name'     => 'Feat. Image',
			'selector' => '.hustle-layout .hustle-layout-content .hustle-image img ',
		],
		[
			'name'     => 'Main Content',
			'selector' => '.hustle-layout .hustle-layout-content .hustle-group-content p ',
		],
		[
			'name'     => 'CTA Button',
			'selector' => '.hustle-layout .hustle-button-cta ',
		],
	],

	'form_selectors' => [
		[
			'name'     => 'Form Container',
			'selector' => '.hustle-layout .hustle-layout-body .hustle-layout-form ',
		],
		[
			'name'     => 'Input',
			'selector' => '.hustle-layout .hustle-layout-body .hustle-layout-form .hustle-input ',
		],
		[
			'name'     => 'Submit',
			'selector' => '.hustle-layout .hustle-layout-body .hustle-layout-form .hustle-button-submit ',
		],
		[
			'name'     => 'Success Container',
			'selector' => '.hustle-success ',
		],
		[
			'name'     => 'Success Message',
			'selector' => '.hustle-success .hustle-success-content p ',
		],
	],

	'form_extra_selectors' => [
		[
			'name'     => 'Container',
			'selector' => '.hustle-layout .hustle-layout-body .hustle-form-options ',
		],
		[
			'name'     => 'Title',
			'selector' => '.hustle-layout .hustle-layout-body .hustle-form-options .hustle-group-title ',
		],
		[
			'name'     => 'Radio',
			'selector' => '.hustle-layout .hustle-layout-body .hustle-radio span[aria-hidden]',
		],
		[
			'name'     => 'Radio (Label)',
			'selector' => '.hustle-layout .hustle-layout-body .hustle-radio span:not([aria-hidden])',
		],
		[
			'name'     => 'Checkbox',
			'selector' => '.hustle-layout .hustle-layout-body .hustle-checkbox span[aria-hidden]',
		],
		[
			'name'     => 'Checkbox (Label)',
			'selector' => '.hustle-layout .hustle-layout-body .hustle-checkbox span:not([aria-hidden])',
		],
	],
];

if ( Hustle_Module_Model::EMBEDDED_MODULE !== $module_type ) {
	$selectors['modal_selectors'][] = [
		'name'     => 'Close Button',
		'selector' => '.hustle-button-close',
	];
}
?>
<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Custom CSS', 'hustle' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'For more advanced customization options use custom CSS.', 'hustle' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<label
			for="hustle-customize-css"
			class="sui-toggle hustle-toggle-with-container"
			data-toggle-on="customize-css"
		>
			<input
				type="checkbox"
				id="hustle-customize-css"
				data-attribute="customize_css"
				<?php checked( $settings['customize_css'], '1' ); ?>
			/>
			<span class="sui-toggle-slider" aria-hidden="true"></span>
		</label>

		<label for="hustle-customize-css"><?php esc_html_e( 'Enable custom CSS', 'hustle' ); ?></label>

		<div class="sui-background-frame" data-toggle-content="customize-css">

			<label class="sui-label"><?php esc_html_e( 'Modal selectors', 'hustle' ); ?></label>

			<div class="sui-ace-selectors">

				<?php foreach ( $selectors['modal_selectors'] as $data ) : ?>
					<a href="#" class="sui-selector hustle-css-stylable" data-stylable="<?php echo esc_attr( $data['selector'] ); ?>" >
						<?php echo esc_html( $data['name'] ); ?>
					</a>
				<?php endforeach; ?>

			</div>

			<?php if ( $is_optin ) { ?>

				<label class="sui-label"><?php esc_html_e( 'Form selectors', 'hustle' ); ?></label>

				<div class="sui-ace-selectors">

					<?php foreach ( $selectors['form_selectors'] as $data ) : ?>
						<a href="#" class="sui-selector hustle-css-stylable" data-stylable="<?php echo esc_attr( $data['selector'] ); ?>" >
							<?php echo esc_html( $data['name'] ); ?>
						</a>
					<?php endforeach; ?>

				</div>

				<label class="sui-label"><?php esc_html_e( 'Form options selectors', 'hustle' ); ?></label>
				<label class="sui-label" style="font-weight: 400;"><?php esc_html_e( 'These are added through "Integrations" like Mailchimp that allow extra fields for users to select custom information requested.', 'hustle' ); ?></label>

				<div class="sui-ace-selectors">

					<?php foreach ( $selectors['form_extra_selectors'] as $data ) : ?>
						<a href="#" class="sui-selector hustle-css-stylable" data-stylable="<?php echo esc_attr( $data['selector'] ); ?>" >
							<?php echo esc_html( $data['name'] ); ?>
						</a>
					<?php endforeach; ?>

				</div>

			<?php } ?>

			<div id="hustle_custom_css" style="height: 210px;"><?php echo $settings['custom_css']; ?></div>

		</div>

	</div>

</div>
