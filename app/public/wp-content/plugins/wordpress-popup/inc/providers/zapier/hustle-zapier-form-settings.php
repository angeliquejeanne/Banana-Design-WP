<?php
if( !class_exists("Hustle_Zapier_Form_Settings") ):

/**
 * Class Hustle_Zapier_Form_Settings
 * Form Settings Zapier Process
 *
 */
class Hustle_Zapier_Form_Settings extends Hustle_Provider_Form_Settings_Abstract {

	/**
	 * For settings Wizard steps
	 *
	 * @since 3.0.5
	 * @return array
	 */
	public function form_settings_wizards() {
		// already filtered on Abstract
		// numerical array steps
		return array(
			// 0
			array(
				'callback'     => array( $this, 'first_step_callback' ),
				'is_completed' => array( $this, 'first_step_is_completed' ),
			),
		);
	}

	/**
	 * Check if step is completed
	 *
	 * @since 3.0.5
	 * @return bool
	 */
	public function first_step_is_completed( $submitted_data ) {

		$is_connected = ( isset( $submitted_data['api_key'] ) && ! empty( $submitted_data['api_key'] ) && filter_var( $submitted_data['api_key'], FILTER_VALIDATE_URL ) );

		return $is_connected;
	}

	/**
	 * Returns all settings and conditions for 1st step of Zapier settings
	 *
	 * @since 3.0.5
	 * @since 4.0 param $validate removed.
	 *
	 * @param array $submitted_data
	 * @return array
	 */
	public function first_step_callback( $submitted_data ) {
		$this->addon_form_settings = $this->get_form_settings_values();
		$current_data = array(
			'name'	=> '',
			'api_key' => '',
		);

		$current_data = $this->get_current_data( $current_data, $submitted_data );

		$is_submit = ! empty( $submitted_data['hustle_is_submit'] );

		if ( $is_submit && ! $this->first_step_is_completed( $submitted_data ) ) {
			$error_message = __( 'Please add a valid Webhook.', 'hustle' );
		}

		$options = $this->get_first_step_options( $current_data );

		$step_html = Hustle_Provider_Utils::get_integration_modal_title_markup( __( 'Setup Webhook', 'hustle' ), __( 'Put your ZAP Webhook URL below.', 'hustle' ) );
		$step_html .= Hustle_Provider_Utils::get_html_for_options( $options );

		if( ! isset( $error_message ) ) {
			$has_errors = false;
		} else {
			$step_html .= '<span class="sui-error-message">' . $error_message . '</span>';
			$has_errors = true;
		}

		$buttons = array();
		if ( $this->first_step_is_completed( $current_data ) ) {
			$buttons['disconnect'] = array(
				'markup' => Hustle_Provider_Utils::get_provider_button_markup(
					__( 'Disconnect', 'hustle' ),
					'sui-button-ghost sui-button-left',
					'disconnect_form',
					true
				),
			);
		}

		$buttons['save'] = array(
			'markup' => Hustle_Provider_Utils::get_provider_button_markup(
				__( 'Save', 'hustle' ),
				'sui-button-right',
				'next',
				true
			),
		);

		$response = array(
			'html'       => $step_html,
			'buttons'    => $buttons,
			'has_errors' => $has_errors,
		);

		// Save only after the step has been validated and there are no errors
		if ( $is_submit && ! $has_errors ) {
			$this->save_form_multi_id_settings_values( $submitted_data );
		}

		return $response;
	}

	/**
	 * Return an array of options used to display the settings of the 1st step.
	 *
	 * @since 4.0
	 *
	 * @param array $submitted_data
	 * @return array
	 */
	private function get_first_step_options( $submitted_data ) {
		$webhook = ! empty( $submitted_data['api_key'] ) ? $submitted_data['api_key'] : '' ;
		$name = ! empty( $submitted_data['name'] ) ? $submitted_data['name'] : '' ;

		$options =  array(
			array(
				'type'     => 'wrapper',
				'elements' => array(
					'label'   => array(
						'for'   => 'friendly-name',
						'type'  => 'label',
						'value' => __( 'Integration Name', 'hustle' ),
					),
					'webhook' => array(
						'type'        => 'text',
						'name'        => 'name',
						'value'       => $name,
						'placeholder' => __( 'Friendly Name', 'hustle' ),
						'id'          => 'friendly-name',
						'icon'        => 'web-globe-world',
					),
				),
			),
			array(
				'type'     => 'wrapper',
				'style'    => 'margin-bottom: 0;',
				'elements' => array(
					'label'   => array(
						'for'   => 'webhook',
						'type'  => 'label',
						'value' => __( 'Webhook URL', 'hustle' ),
					),
					'webhook' => array(
						'type'        => 'url',
						'name'        => 'api_key',
						'value'       => $webhook,
						'placeholder' => __( 'Webhook URL', 'hustle' ),
						'id'          => 'webhook',
						'icon'        => 'link',
					),
				),
			),
		);

		return $options;
	}

	/**
	 * Get the first found aactive connection of the provider.
	 *
	 * @since 4.0
	 *
	 * @param string $multi_id
	 * @param array $settings
	 * @return boolean
	 */
	public function is_multi_form_settings_complete( $multi_id, $settings ) {

		if ( true === $this->first_step_is_completed( $settings ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Disconnect multi_id instance of a provider from a module.
	 *
	 * @since 4.0
	 * @param array $submitted_data
	 */
	public function disconnect_form( $submitted_data ) {

		// only execute if the multi_id is provided on the submitted data.
		if ( isset( $submitted_data['multi_id'] ) && ! empty( $submitted_data['multi_id'] ) ) {
			$addon_form_settings = $this->get_form_settings_values();
			unset( $addon_form_settings[ $submitted_data['multi_id'] ] );
			$this->save_form_settings_values( $addon_form_settings, true );
		}
	}

} // Class end.

endif;
