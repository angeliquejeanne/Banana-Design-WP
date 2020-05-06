<?php
if( !class_exists("Hustle_ConstantContact_Form_Settings") ):

/**
 * Class Hustle_ConstantContact_Form_Settings
 * Form Settings ActiveCampaign Process
 *
 */
class Hustle_ConstantContact_Form_Settings extends Hustle_Provider_Form_Settings_Abstract {

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
	public function first_step_is_completed() {
		$this->addon_form_settings = $this->get_form_settings_values();
		if ( ! isset( $this->addon_form_settings['list_id'] ) ) {
			// preliminary value
			$this->addon_form_settings['list_id'] = 0;

			return false;
		}

		if ( empty( $this->addon_form_settings['list_id'] ) ) {
			return false;
		}

		return true;
	}
	/**
	 * Returns all settings and conditions for 1st step of ConstantContact settings
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
			'list_id' => '',
		);
		$current_data = $this->get_current_data( $current_data, $submitted_data );
		$is_submit = ! empty( $submitted_data['hustle_is_submit'] );

		if ( $is_submit && empty( $submitted_data['list_id'] ) ) {
			$error_message = __( 'The email list is required.', 'hustle' );
		} else {
			$error_message = '';
		}

		$options = $this->get_first_step_options( $current_data );

		$step_html = Hustle_Provider_Utils::get_integration_modal_title_markup(
			__( 'Choose your list', 'hustle' ),
			__( 'Choose the list you want to send form data to.', 'hustle' )
		);

		$step_html .= Hustle_Provider_Utils::get_html_for_options( $options );

		if ( ! is_ssl() ) {
			$error_message .= __( 'Constant Contact requires your site to have SSL certificate.', 'hustle' );
		}

		if ( empty( $error_message ) ) {
			$has_errors = false;
		} else {
			$step_html .= '<div class="sui-notice sui-notice-error" style="margin-bottom: 0;"><p>' . $error_message . '</p></div>';
			$has_errors = true;
		}


		$disabled = !is_ssl();
		$buttons = array(
			'disconnect' => array(
				'markup' => Hustle_Provider_Utils::get_provider_button_markup(
					__( 'Disconnect', 'hustle' ),
					'sui-button-ghost',
					'disconnect_form',
					true
				),
			),
			'save' => array(
				'markup' => Hustle_Provider_Utils::get_provider_button_markup(
					__( 'Save', 'hustle' ),
					'',
					'next',
					true,
					$disabled
				),
			),
		);

		$response = array(
			'html'       => $step_html,
			'buttons'    => $buttons,
			'has_errors' => $has_errors,
		);

		// Save only after the step has been validated and there are no errors
		if( $is_submit && ! $has_errors ){
			// Save additional data for submission's entry
			if ( !empty( $current_data['list_id'] ) ) {
				$current_data['list_name'] = !empty( $this->lists[ $current_data['list_id'] ] )
						? $this->lists[ $current_data['list_id'] ] . ' (' . $current_data['list_id'] . ')' : $current_data['list_id'];
			}
			$this->save_form_settings_values( $current_data );
		}

		return $response;
	}

	/**
	 * Refresh list array via API
	 *
	 * @param object $provider
	 * @param string $global_multi_id
	 * @return array
	 */
	public function refresh_global_multi_lists( $provider, $global_multi_id ) {
		$api = $provider->api();
		$is_authorize = (bool) $api->get_token( 'access_token' );

		$lists = array();

		if ( $is_authorize ) {
			$lists_data = $api->get_contact_lists();
			$lists = wp_list_pluck( $lists_data, 'name', 'id' );
		}

		return $lists;
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
		$lists = $this->get_global_multi_lists();
		$this->lists = $lists;
		$selected_list = $this->get_selected_list( $submitted_data );

		$options =  array(
			'list_id_setup' => array(
				'type'     => 'wrapper',
				'style'    => 'margin-bottom: 0;',
				'elements' => array(
					'label' => array(
						'type'  => 'label',
						'for'   => 'list_id',
						'value' => __( 'Email List', 'hustle' ),
					),
					'wrapper' => array(
						'type'     => 'wrapper',
						'class'    => 'hui-select-refresh',
						'is_not_field_wrapper' => true,
						'elements' => array(
							'lists' => array(
								'type'     => 'select',
								'id'       => 'list_id',
								'class'    => 'sui-select',
								'name'     => 'list_id',
								'value'    => $selected_list,
								'options'  => $lists,
								'selected' => $selected_list,
							),
							'refresh' => array(
								'type' => 'raw',
								'value' => Hustle_Provider_Utils::get_provider_button_markup( __( 'Refresh', 'hustle' ), '', 'refresh_list', true ),
							),
						),
					),
				),
			),
		);

		return $options;
	}

} // Class end.

endif;
