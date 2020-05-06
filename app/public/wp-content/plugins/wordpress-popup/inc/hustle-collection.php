<?php

class Hustle_Collection {

	/**
	 * @return Hustle_Collection
	 */
	public static function instance() {
		return new self();
	}

	/**
	 * Reference to $wpdb global var
	 *
	 * @since 1.0.0
	 *
	 * @var $_db WPDB
	 * @access private
	 */
	protected static $_db;

	public function __construct() {
		global $wpdb;
		self::$_db = $wpdb;
	}

	public function get_count() {
		return self::$_db->num_rows;
	}

	/**
	 * Prepare DB string value.
	 *
	 * @since 4.0.0
	 *
	 */
	protected function _wrap_string( $v ) {
		return self::$_db->prepare( '%s', $v );
	}
}
