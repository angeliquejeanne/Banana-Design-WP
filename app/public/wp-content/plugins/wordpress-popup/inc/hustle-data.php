<?php

/**
 * Abstract class for optin model and stats
 *
 * Class Opt_In_Data
 */
abstract class Hustle_Data {

	const KEY_CONTENT				= 'content';
	const KEY_EMAILS				= 'emails';

	/**
	 * Per provider settings. Used as {slug}_provider_settings.
	 * @since 4.0
	 */
	const KEY_PROVIDER				= '_provider_settings';
	const KEY_DESIGN                = 'design';
	const KEY_DISPLAY_OPTIONS       = 'display';
	const KEY_VISIBILITY			= 'visibility';

	/**
	 * Per module settings applied to all integrations.
	 * @since 4.0
	 */
	const KEY_INTEGRATIONS_SETTINGS	= 'integrations_settings';
	const KEY_SETTINGS              = 'settings';
	const KEY_TYPES              	= 'types';
	const KEY_VIEW                  = 'view';
	const KEY_CONVERSION            = 'conversion';
	const KEY_PAGE_SHARES           = 'page_shares';
	const KEY_SHORTCODE_ID			= 'shortcode_id';
	const TEST_TYPES                = 'test_types';
	const TRACK_TYPES               = 'track_types';
	const KEY_SERVICES 				= 'services';
	const KEY_FLOATING_SOCIAL 		= 'floating_social';
	const ACTIVE_FOR_ADMIN 			= 'active_for_admin';
	const ACTIVE_FOR_LOGGED_IN 		= 'active_for_logged_in_user';
	const KEY_UNSUBSCRIBE_NONCES 	= 'hustle_unsubscribe_nonces';
	const KEY_MODULE_META_PERMISSIONS = 'edit_roles';

	/**
	 * Optins types
	 *
	 * @var array
	 */
	/* protected $types = array(
		'popup',
		'slide_in',
		'after_content',
		'shortcode',
		'widget'
	); */

	/**
	 *
	 * @since 1.0.0
	 *
	 * @var array $_data
	 */
	protected $_data;

	/**
	 * Reference to $wpdb global var
	 *
	 * @since 1.0.0
	 *
	 * @var $wpdb WPDB
	 * @access private
	 */
	protected $_wpdb;

	/**
	 *
	 * Opt_In_Data constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->_wpdb = $wpdb;
	}

	/**
	 * Returns format for optin table
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_format() {
		return array(
			'module_name' => '%s',
			'module_type' => '%s',
			'active' => '%d',
			'module_mode' => '%s',
		);
	}

	/**
	 * Implements setter magic method
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $property
	 * @param $val
	 */
	public function __set( $property, $val ) {
		$this->{$property} = $val;
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
	public function __get( $field ) {

		if ( method_exists( $this, 'get_' . $field ) ) {
			return $this->{'get_'. $field}(); }

		if ( ! empty( $this->_data ) && isset( $this->_data->{$field} ) ) {
			return $this->_data->{$field}; }

	}
}
