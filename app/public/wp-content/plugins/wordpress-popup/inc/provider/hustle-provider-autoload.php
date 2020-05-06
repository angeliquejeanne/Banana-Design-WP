<?php

/**
 * Class Hustle_Provider_Autoload
 * Handling Autoloader
 */
class Hustle_Provider_Autoload{
	
	protected $pro_providers = array();
	
	public function __construct( $pro_providers = array() ) {
		$this->pro_providers = $pro_providers;
	}

	public function load() {
		$pro_providers_dir = Opt_In::$plugin_path . 'inc/providers/';
		
		// Load Available Pro Providers
		$directory = new DirectoryIterator( $pro_providers_dir );
		foreach ( $directory as $d ) {
			if ( $d->isDot() || $d->isFile() ) {
				continue;
			}
			// Take directory name as provider name
			$provider_name = $d->getBasename();

			/**
			 * Add the new Provider.
			 * A valid provider should have `provider_name.php` inside its main directory
			 */
			$provider_initiator = $d->getPathname() . DIRECTORY_SEPARATOR . $provider_name . '.php';
			include_once $provider_initiator;
		}
	}
}
