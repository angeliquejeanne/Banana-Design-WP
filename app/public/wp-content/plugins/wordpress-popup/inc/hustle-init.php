<?php
if ( !class_exists("Hustle_Init") ) :

/**
 * Class Hustle_Init
 */
class Hustle_Init {

	public function __construct( Opt_In $hustle ){

		Hustle_Db::maybe_create_tables();

		// Hustle Migration.
		Hustle_Migration::get_instance();

		// Admin
		if( is_admin() ) {
			$module_admin = new Hustle_Module_Admin( $hustle );

			$modules_common_admin = new Hustle_Modules_Common_Admin();
			new Hustle_Modules_Common_Admin_Ajax( $modules_common_admin );

			new Hustle_Dashboard_Admin( $hustle ); // $hustle is unused here. Adding while the abstract class requires it.

			new Hustle_Popup_Admin( $hustle );

			new Hustle_Slidein_Admin( $hustle );

			new Hustle_Embedded_Admin( $hustle );

			new Hustle_SShare_Admin( $hustle ); // $hustle is unused here. Adding while the abstract class requires it.

			// Global Integrations page
			new Hustle_Providers_Admin( $hustle );

			new Hustle_Entries_Admin( $hustle );

			new Hustle_Settings_Page( $hustle );
			new Hustle_Settings_Admin_Ajax();
		}

		new Hustle_General_Data_Protection();
		// Front
		$module_front = new Hustle_Module_Front($hustle);
		$module_front_ajax = new Hustle_Module_Front_Ajax($hustle);
	}
}

endif;
