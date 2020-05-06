<?php

abstract class Hustle_Meta {

	protected  $data;
	protected  $model;

	protected $defaults = array();

	public function __construct( array $data, Hustle_Model $model ){
		$this->data = $data;
		$this->model = $model;
		$this->defaults = $this->get_defaults();
	}

	/**
	 * Implements getter magic method
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $field
	 * @return mixed
	 */
	public function __get( $field ){

		if( method_exists( $this, "get_" . $field ) )
			return $this->{"get_". $field}();

		if( !empty( $this->data ) && isset( $this->data[ $field ] ) ){
			$val = $this->data[ $field ];
			if( "true" === $val  )
				return true;
			if( "false" === $val )
				return false;
			if( "null" === $val )
				return null;

			return $val;
		}

	}

	public function to_object(){
		return (object) $this->to_array();
	}

	public function to_array(){

		$defaults = $this->get_defaults();
		if ( $defaults ) {
			return wp_parse_args( $this->data, $defaults );
		}

		return $this->data;
	}

	/**
	 * Return an array with the default values.
	 * Can be overridden to return an array of default values
	 * without restricting to static values.
	 *
	 * @since 4.0
	 *
	 * @return array|false
	 */
	public function get_defaults() {
		if ( isset( $this->defaults ) && is_array( $this->defaults   ) )
			return apply_filters( 'hustle_meta_get_defaults', $this->defaults );

		return false;
	}

	protected function get_emails_base_defaults() {
		return array(
			'form_elements' => Opt_In::default_form_fields(),
			'after_successful_submission' => 'show_success',
			'success_message' => '',
			'auto_close_success_message' => '0',
			'auto_close_time' => 5,
			'auto_close_unit' => 'seconds',
			'redirect_url' => '',
			'automated_email' => '0',
			'email_time' => 'instant',
			'recipient' => '{email}',
			'day' => '',
			'time' => '',
			'auto_email_time' => '5',
			'schedule_auto_email_time' => '5',
			'auto_email_unit' => 'seconds',
			'schedule_auto_email_unit' => 'seconds',
			'email_subject' => '',
			'email_body'=> '',
		);
	}

	protected function get_settings_base_defaults() {
		return array(
			'auto_close_success_message' => '0',
			'triggers'                   => [
				'trigger'                     => 'time',
				'on_time_delay'               => 0,
				'on_time_unit'                => 'seconds',
				'on_scroll'                   => 'scrolled',
				'on_scroll_page_percent'      => 20,
				'on_scroll_css_selector'      => '',
				'enable_on_click_element'     => '1',
				'on_click_element'            => '',
				'enable_on_click_shortcode'   => '1',
				'on_exit_intent_per_session'  => '1',
				'on_exit_intent_delayed'      => '0',
				'on_exit_intent_delayed_time' => 5,
				'on_exit_intent_delayed_unit' => 'seconds',
				'on_adblock'                  => '0',
			],
			'animation_in' => 'no_animation',
			'animation_out' => 'no_animation',
			'after_close_trigger' => [ 'click_close_icon' ],
			'after_close' => 'keep_show',
			'expiration' => 365,
			'expiration_unit' => 'days',
			'on_submit' => 'nothing', // default|close|nothing|redirect
			'on_submit_delay' => '5',
			'on_submit_delay_unit' => 'seconds',
			'close_cta' => '0',
			'close_cta_time' => '0',
			'close_cta_unit' => 'seconds',
			'hide_after_cta' => 'keep_show', // keep_show|no_show_on_post|no_show_all
			'hide_after_subscription' => 'keep_show' // keep_show|no_show_on_post|no_show_all
		);
	}

}
