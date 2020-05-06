<?php

if ( ! class_exists( 'Hustle_SShare_Admin' ) ) :

	class Hustle_SShare_Admin extends Hustle_Module_Page_Abstract {

		protected function set_page_properties() {

			$this->module_type = Hustle_Module_Model::SOCIAL_SHARING_MODULE;

			$this->page_title = Opt_In_Utils::get_module_type_display_name( $this->module_type, false, true );

			$this->page_template_path = '/admin/sshare/listing';
			$this->page_edit_template_path = '/admin/sshare/wizard';
		}

		public function register_current_json( $current_array ) {

			$current_array = parent::register_current_json( $current_array );

			if ( $this->page_edit === $this->current_page ) {

				$current_array['social_platforms'] = Opt_In_Utils::get_social_platform_names();
				$current_array['social_platforms_with_endpoints'] = Hustle_Sshare_Model::get_sharing_endpoints();
				$current_array['social_platforms_with_api'] = Hustle_Sshare_Model::get_networks_counter_endpoint();
				$current_array['social_platforms_data'] = [
					'email_message_default' => __( "I've found an excellent article on {page_url} which may interest you.", 'hustle' ),
				];
			}

			return $current_array;
		}

		/**
		 * Get the args for the wizard page.
		 *
		 * @since 4.0.1
		 * @return array
		 */
		protected function get_page_edit_template_args() {

			$current_section = Hustle_Module_Admin::get_current_section();

			return array(
				'section' => ( ! $current_section ) ? 'services' : $current_section,
				'module_id' => $this->module->module_id,
				'module' => $this->module,
				'is_active' => (bool) $this->module->active,
			);
		}
	}

endif;
