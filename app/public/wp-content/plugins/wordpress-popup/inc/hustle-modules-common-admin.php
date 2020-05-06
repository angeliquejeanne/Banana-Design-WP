<?php
if ( ! class_exists( 'Hustle_Modules_Common_Admin' ) ) :

	/**
	 * Class Hustle_Modules_Common_Admin
	 * Handle actions that are common among module types.
	 *
	 * @since 4.0
	 *
	 */
	class Hustle_Modules_Common_Admin {

		/**
		 * Create a new module of the provided mode and type.
		 *
		 * @since 4.0
		 *
		 * @param array $data Must contain the Module's 'mode', 'name' and 'type.
		 * @return int|false Module ID if successfully saved. False otherwise.
		 */
		public function create_new( $data ) {

			// Verify it's a valid module type.
			if ( ! in_array( $data['module_type'], array( Hustle_Module_Model::POPUP_MODULE, Hustle_Module_Model::SLIDEIN_MODULE, Hustle_Module_Model::EMBEDDED_MODULE, Hustle_Module_Model::SOCIAL_SHARING_MODULE ), true ) ) {
				return false;
			}

			$is_social_share = ( Hustle_Module_Model::SOCIAL_SHARING_MODULE === $data['module_type'] );

			// Abort if it's not a Social Share module and the mode isn't set.
			if ( ! $is_social_share && ! in_array( $data['module_mode'], array( 'optin', 'informational' ), true ) ) {
				return false;
			}

			if ( ! $is_social_share ) {
				$module = Hustle_Module_Model::instance();
			} else {
				$module = Hustle_SShare_Model::instance();
			}

			// save to modules table
			$module->module_name = sanitize_text_field( $data['module_name'] );
			$module->module_type = $data['module_type'];
			$module->active = 0;
			$module->module_mode = ! $is_social_share ? $data['module_mode'] : '';
			$module->save();

			// Save the new module's meta.
			$this->store_new_module_meta( $module, $data );

			// Activate providers
			$module->activate_providers( $data );

			return $module->id;
		}

		/**
		 * Store the defaults meta when creating a new module.
		 *
		 * @since 4.0
		 *
		 * @param Hustle_Module_Model $module
		 */
		public function store_new_module_meta( Hustle_Module_Model $module, $data ) {

			// All modules types except Social sharing modules. //
			if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE !== $module->module_type ) {

				$def_content = apply_filters( 'hustle_module_get_' . Hustle_Module_Model::KEY_CONTENT . '_defaults', $module->get_content()->to_array(), $module, $data );
				$content_data = empty( $data['content'] ) ? $def_content : array_merge( $def_content, $data['content'] );

				$def_emails = apply_filters( 'hustle_module_get_' . Hustle_Module_Model::KEY_EMAILS . '_defaults', $module->get_emails()->to_array(), $module, $data );
				$emails_data = empty( $data['emails'] ) ? $def_emails : array_merge( $def_emails, $data['emails'] );

				$def_design = apply_filters( 'hustle_module_get_' . Hustle_Module_Model::KEY_DESIGN . '_defaults', $module->get_design()->to_array(), $module, $data );
				$design_data = empty( $data['design'] ) ? $def_design : array_merge( $def_design, $data['design'] );

				$def_integrations_settings = apply_filters( 'hustle_module_get_' . Hustle_Module_Model::KEY_INTEGRATIONS_SETTINGS . '_defaults', $module->get_integrations_settings()->to_array(), $module, $data );
				$integrations_settings_data = empty( $data['integrations_settings'] ) ? $def_integrations_settings : array_merge( $def_integrations_settings, $data['integrations_settings'] );

				$def_settings = apply_filters( 'hustle_module_get_' . Hustle_Module_Model::KEY_SETTINGS . '_defaults', $module->get_settings()->to_array(), $module, $data );
				$settings_data = empty( $data['settings'] ) ? $def_settings : array_merge( $def_settings, $data['settings'] );

				// save to meta table
				$module->update_meta( Hustle_Module_Model::KEY_CONTENT, $content_data );
				$module->update_meta( Hustle_Module_Model::KEY_EMAILS, $emails_data );
				$module->update_meta( Hustle_Module_Model::KEY_INTEGRATIONS_SETTINGS, $integrations_settings_data );
				$module->update_meta( Hustle_Module_Model::KEY_DESIGN, $design_data );
				$module->update_meta( Hustle_Module_Model::KEY_SETTINGS, $settings_data );

			} else {

				// Social sharing only. //
				$def_content = apply_filters( 'hustle_module_get_' . Hustle_Module_Model::KEY_CONTENT . '_defaults', $module->get_content()->to_array(), $module, $data );
				$content_data = empty( $data['content'] ) ? $def_content : array_merge( $def_content, $data['content'] );

				$def_design = apply_filters( 'hustle_module_get_' . Hustle_Module_Model::KEY_DESIGN . '_defaults', $module->get_design()->to_array(), $module, $data );
				$design_data = empty( $data['design'] ) ? $def_design : array_merge( $def_design, $data['design'] );

				// save to meta table
				$module->update_meta( Hustle_Module_Model::KEY_CONTENT, $content_data );
				$module->update_meta( Hustle_Module_Model::KEY_DESIGN, $design_data );
			}

			// Embedded and Social sharing only. //
			if ( Hustle_Module_Model::EMBEDDED_MODULE === $module->module_type ||  Hustle_Module_Model::SOCIAL_SHARING_MODULE === $module->module_type ) {

				// Display options.
				$def_display = apply_filters( 'hustle_module_get_' . Hustle_Module_Model::KEY_DISPLAY_OPTIONS . '_defaults', $module->get_display()->to_array(), $module, $data );
				$display_data = empty( $data['display'] ) ? $def_display : array_merge( $def_display, $data['display'] );

				// Save Display to meta table.
				$module->update_meta( Hustle_Module_Model::KEY_DISPLAY_OPTIONS, $display_data );
			}

			// For all module types. //

			// Visibility settings.
			$def_visibility = apply_filters( 'hustle_module_get_' . Hustle_Module_Model::KEY_VISIBILITY . '_defaults', $module->get_visibility()->to_array(), $module, $data );
			$visibility_data = empty( $data['visibility'] ) ? $def_visibility : array_merge( $def_visibility, $data['visibility'] );
			$module->update_meta( Hustle_Module_Model::KEY_VISIBILITY, $visibility_data );
		}

		/**
		 * Export single module
		 *
		 * @since 4.0.0
		 */
		public static function export() {

			$nonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			if ( ! wp_verify_nonce( $nonce, 'hustle_module_export' ) ) {
				return;
			}
			$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
			if ( ! $id ) {
				return;
			}
			/**
			 * plugin data
			 */
			$plugin = get_plugin_data( WP_PLUGIN_DIR.'/'.Opt_In::$plugin_base_file );
			/**
			 * get module
			 */
			$module = Hustle_Module_Model::instance()->get( $id );
			if ( is_wp_error( $module ) ) {
				return;
			}
			/**
			 * Export data
			 */
			$settings = array(
				'plugin' => array(
					'name' => $plugin['Name'],
					'version' => Opt_In::VERSION,
					'network' => $plugin['Network'],
				),
				'timestamp' => time(),
				'attributes' => $module->get_attributes(),
				'data' => $module->get_data(),
				'meta' => array(),
			);

			if ( 'optin' === $module->module_mode ) {
				$integrations = array();
				$providers = Hustle_Providers::get_instance()->get_providers();
				foreach ( $providers as $slug => $provider ) {
					$provider_data = $module->get_provider_settings( $slug, false );
					if ( $provider_data && $provider->is_connected()
							&& $provider->is_form_connected( $id ) ) {
						$integrations[ $slug ] = $provider_data;
					}
				}

				$settings['meta']['integrations'] = $integrations;
			}

			$meta_names = $module->get_module_meta_names();
			foreach ( $meta_names as $meta_key ) {
				$settings['meta'][ $meta_key ] = json_decode( $module->get_meta( $meta_key ) );
			}
			/**
			 * Filename
			 */
			$filename = sprintf(
				'hustle-%s-%s-%s-%s.json',
				$module->module_type,
				date( 'Ymd-his' ),
				get_bloginfo( 'name' ),
				$module->module_name
			);
			$filename = strtolower( $filename );
			$filename = sanitize_file_name( $filename );
			/**
			 * Print HTTP headers
			 */
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: application/bin; charset=' . get_option( 'blog_charset' ), true );
			/**
			 * Check PHP version, for PHP < 3 do not add options
			 */
			$version = phpversion();
			$compare = version_compare( $version, '5.3', '<' );
			if ( $compare ) {
				echo wp_json_encode( $settings );
				exit;
			}
			$option = defined( 'JSON_PRETTY_PRINT' )? JSON_PRETTY_PRINT : null;
			echo wp_json_encode( $settings, $option );
			exit;
		}
	}

endif;
