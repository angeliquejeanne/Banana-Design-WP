<?php
// define method that MUST be implemented by addon here, if its optional, put it on abstract
interface Hustle_Provider_Interface {

	/**
	 * Use it to instantiate provider class
	 *
	 * @param string $class_name We can't avoid it via `static::` because we're supporting PHP 5.2
	 * @return self
	 */
	public static function get_instance();

}
