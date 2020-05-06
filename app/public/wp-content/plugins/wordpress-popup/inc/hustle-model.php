<?php

/**
 * Class Hustle_Model
 *
 * @property int $module_id
 * @property string $module_name
 * @property string $module_type
 * @property int $active
 *
 */
abstract class Hustle_Model extends Hustle_Data {

	const POPUP_MODULE = 'popup';
	const SLIDEIN_MODULE = 'slidein';
	const EMBEDDED_MODULE = 'embedded';
	const SOCIAL_SHARING_MODULE = 'social_sharing';
	const OPTIN_MODE = 'optin';
	const INFORMATIONAL_MODE = 'informational';
	const INLINE_MODULE = 'inline';
	const WIDGET_MODULE = 'widget';
	const SHORTCODE_MODULE = 'shortcode';
	const SUBSCRIPTION  = 'subscription';

	/**
	 * Optin id
	 *
	 * @since 1.0.0
	 *
	 * @var $id int
	 */
	public $id;

	protected $_track_types = array();

	protected $_decorator = false;

	public function __get($field) {
		$from_parent = parent::__get($field);
		if( !empty( $from_parent ) )
			return $from_parent;

		$meta = $this->get_meta( $field );
		if( !is_null( $meta )  )
			return $meta;
	}

	/**
	 * Returns optin based on provided id
	 *
	 * @param $id
	 * @return $this
	 */
	public function get( $id ){
		$cache_group = 'hustle_model_data';
		$this->_data  = wp_cache_get( $id, $cache_group );
		$this->id = (int) $id;
		if( false === $this->_data ){
            $data = $this->_wpdb->get_row( $this->_wpdb->prepare( "SELECT * FROM  " . Hustle_Db::modules_table() . " WHERE `module_id`=%d", $this->id ), OBJECT );
            if ( empty( $data ) ) {
                return new WP_Error( 'hustle-module', __( 'Module does not exist!', 'hustle' ) );
            }
            $this->_data = $data;
			wp_cache_set( $id, $this->_data, $cache_group );
		}
        $this->_populate();
		return $this;
    }

	private function _populate() {
		if ( $this->_data ) {
			$this->id = $this->_data->module_id;
			foreach ( $this->_data as $key => $data ) {
				$method       = 'get_' . $key;
				$_d           = method_exists( $this, $method ) ? $this->{$method}() : $data;
				$this->{$key} = $_d;
			}
		}
		$this->get_tracking_types();
	}

	/**
	 * Returns optin based on shortcode id
	 *
	 * @todo make this return an instance of Hustle_Module_Model, or Hustle_Sshare_Model when it should.
	 *
	 * @param string $shortcode_id
	 * @param bool $enforce_type Whether to get only embeds or sshares.
	 * @return $this
	 */
	public function get_by_shortcode( $shortcode_id, $enforce_type = true ){
		$shortcode_id = trim( $shortcode_id );

		$cache_group = 'hustle_shortcode_data';
		//$key = "hustle_shortcode_data_" . $shortcode_id;
		$this->_data = wp_cache_get( $shortcode_id, $cache_group );

		// If not cached.
		if( false === $this->_data ) {
			// Enforce embedded/social_sharing type or not.
			$and_force = $enforce_type ? "AND (`module_type` = 'embedded' OR `module_type` = 'social_sharing')" : '';

			$sql = $this->_wpdb->prepare(
				"SELECT modules.`module_id` FROM  `" . Hustle_Db::modules_table() . "` as modules
				JOIN `" . Hustle_Db::modules_meta_table() . "` as meta
				ON modules.`module_id`=meta.`module_id`
				WHERE `meta_key`='shortcode_id'
				$and_force
				AND `meta_value`=%s",
				$shortcode_id
			);
			$module_id = $this->_wpdb->get_var( $sql );

			if ( empty( $module_id ) ) {
				$module_id = $shortcode_id;
			}

			$this->get( $module_id );
			wp_cache_set( $shortcode_id, $this->_data, $cache_group );
		} else {
			$this->_populate();
		}

		return $this;
	}


	/**
	 * Saves or updates optin
	 *
	 * @since 1.0.0
	 *
	 * @return false|int
	 */
	public function save(){
		$data = get_object_vars($this);
		$table = Hustle_Db::modules_table();
		if( empty( $this->id ) ){
			$this->_wpdb->insert($table, $this->_sanitize_model_data( $data ), array_values( $this->get_format() ));
			$this->id = $this->_wpdb->insert_id;

			/**
			 * Action Hustle after creation module
			 *
			 * @since 3.0.7
			 *
			 * @param string $module_type module type
			 * @param array $data module data
			 * @param int $id module id
			 */
			do_action( 'hustle_after_create_module', $this->module_type, $data, $this->id );
		}else{
			$this->_wpdb->update($table, $this->_sanitize_model_data( $data ), array( "module_id" => $this->id ), array_values( $this->get_format() ), array("%d") );

			/**
			 * Action Hustle after updating module
			 *
			 * @since 3.0.7
			 *
			 * @param string $module_type module type
			 * @param array $data module data
			 */
			do_action( 'hustle_after_update_module', $this->module_type, $data );
		}

		// Clear cache as well.
		$this->clean_module_cache( 'data' );

		return $this->id;
	}

	/**
	 * Update the module's data.
	 *
	 * @since 4.0
	 *
	 * @param array $data
	 * @return bool|int
	 */
	public function update_module( $data ) {

		// TODO: Sanitize!

		// Save to modules table
		if ( isset( $data['module'] ) ) {
			$this->module_name = $data['module']['module_name'];
			$this->active = (int) $data['module']['active'];
			$this->save();
		}

		// All modules types except Social sharing modules. //
		if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE !== $this->module_type ) {

			//emails tab
			if( isset( $data['emails'] ) ){
				$emails = $data['emails'];
				if( isset( $emails['form_elements'] ) ){
					$emails['form_elements'] = $this->sanitize_form_elements( $emails['form_elements'] );
				}
				$this->update_meta( self::KEY_EMAILS, $emails );
			}

			//settings tab
			if( isset( $data['settings'] ) ){
				$this->update_meta( self::KEY_SETTINGS, $data['settings'] );
			}

			//integrations tab
			if( isset( $data['integrations_settings'] ) ){
				$this->update_meta( self::KEY_INTEGRATIONS_SETTINGS, $data['integrations_settings'] );
			}

		}

		// save to meta table
		if( isset( $data['content'] ) ){
			$this->update_meta( self::KEY_CONTENT, $data['content'] );
		}
		if( isset( $data['design'] ) ){
			$this->update_meta( self::KEY_DESIGN, $data['design'] );
		}
		if( isset( $data['visibility'] ) ){
			$this->update_meta( self::KEY_VISIBILITY, $data['visibility'] );
		}

		// Embedded only meta.
		if ( Hustle_Module_Model::EMBEDDED_MODULE === $this->module_type ||  Hustle_Module_Model::SOCIAL_SHARING_MODULE === $this->module_type ) {
			if( isset( $data['display'] ) ){
				$this->update_meta( self::KEY_DISPLAY_OPTIONS, $data['display'] );
			}

			// Force all counters to retrieve the data from the APIs.
			if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE === $this->module_type ) {
				Hustle_Sshare_Model::refresh_all_counters();
			}
		}

		// Activate integrations if passed.
		if ( isset( $data['integrations'] ) ) {
			$this->activate_providers( $data );
		}

		$this->clean_module_cache();

		return $this->id;
	}

	/**
	 * validates the module's data.
	 *
	 * @since 4.0.3
	 *
	 * @param array $data
	 * @return array
	 */
	public function validate_module( $data ){
		//validation for sshare module
		if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE === $this->module_type ) {

			//validation
			$icons 		= isset( $data['content']['social_icons'] ) ? $data['content']['social_icons'] : array();
			$display 	= $data['display'];
			$selector 	= array(
				'desktop' => isset( $display['float_desktop_offset'] ) ? $display['float_desktop_offset'] : '',
				'mobile'  => isset( $display['float_mobile_offset'] ) ? $display['float_mobile_offset'] : ''
			);

			$errors = array();

			//social platform url check
			if( ! empty( $icons ) ){
				foreach ($icons as $key => $icon) {
					$icon_with_enpoints = Hustle_Sshare_Model::get_sharing_endpoints();
					if( ! in_array( $icon['platform'], $icon_with_enpoints, true ) && empty( $icon['link'] ) ){
						$errors['error']['icon_error'][] = $icon['platform'];
					}
				}

			}

			//css selector check
			if( ! empty( $selector ) ){
				if( 'css_selector' === $selector['desktop'] && empty( $display['float_desktop_css_selector'] )
				&& ! empty( $display['float_desktop_enabled'] ) ) {
					$errors['error']['selector_error'][] = 'float_desktop';
				}
				if( 'css_selector' === $selector['mobile'] && empty( $display['float_mobile_css_selector'] ) && ! empty( $display['float_mobile_enabled'] ) ) {
					$errors['error']['selector_error'][] = 'float_mobile';
				}
			}
		}

		//return errors if any.
		if( ! empty( $errors ) ){
			$errors['success'] = false;
			return $errors;
		} else {
			return $res['success'] = true;
		}

	}

	/**
	 * Activate the passed providers.
	 *
	 * @since 4.0
	 *
	 * @param array $data
	 */
	public function activate_providers( $data ) {

		if ( 'optin' !== $this->module_mode ) {
			return;
		}

		// Activate other saved providers
		if ( ! empty( $data['integrations'] ) ) {
			$providers = Hustle_Providers::get_instance()->get_providers();
			foreach ( $providers as $slug => $provider ) {
				if ( !empty( $data['integrations'][ $slug ] ) ) {
					$this->set_provider_settings( $slug, $data['integrations'][ $slug ] );
				}
			}
		} else {
			// Activate Local list provider if there are no integrations.
			$slug = 'local_list';
			$provider_data = array(
				'local_list_name' => $this->id,
			);
			$this->set_provider_settings( $slug, $provider_data );
		}
	}

	/**
	 * Clean all (or certain) the cache related to a module.
	 *
	 * @since 3.0.7
	 *
	 * @param string $type Optional. Type of cache which should be removed ( data | meta | shortcode )
	 * @return void
	 */
	public function clean_module_cache( $type = '' ) {

		$id = $this->id;

		if ( empty( $type ) || in_array( $type, array( 'shortcode', 'data' ), true ) ) {
			$shortcode_id = $this->get_shortcode_id();
			$shortcode_group = 'hustle_shortcode_data';
			wp_cache_delete( $shortcode_id, $shortcode_group );
		}

		if ( empty( $type ) || 'data' === $type ) {
			$module_group = 'hustle_model_data';
			wp_cache_delete( $id, $module_group );
		}

		if ( empty( $type ) || 'meta' === $type ) {
			$module_meta_group = 'hustle_module_meta';
			wp_cache_delete( $id, $module_meta_group );
		}

	}

	/**
	 * Returns populated model attributes
	 *
	 * @return array
	 */
	public function get_attributes(){
		return $this->_sanitize_model_data( $this->data );
	}

	/**
	 * Matches given data to the data format
	 *
	 * @param $data
	 * @return array
	 */
	private function _sanitize_model_data( array $data ){
		$d = array();
		foreach($this->get_format() as $key => $format ){
			$d[ $key ] = isset( $data[ $key ] ) ? $data[ $key ] : "";
		}
		return $d;
	}

	/**
	 * Adds meta for the current optin
	 *
	 * @since 1.0.0
	 *
	 * @param $meta_key
	 * @param $meta_value
	 * @return false|int
	 */
	public function add_meta( $meta_key, $meta_value ){
		$this->clean_module_cache( 'meta' );

		return $this->_wpdb->insert( Hustle_Db::modules_meta_table(), array(
			"module_id" => $this->id,
			"meta_key" => $meta_key,
			"meta_value" => is_array( $meta_value ) || is_object( $meta_value ) ?  wp_json_encode( $meta_value ) : $meta_value
		), array(
			"%d",
			"%s",
			"%s",
		));
	}

	/**
	 * Updates meta for the current optin
	 *
	 * @since 1.0.0
	 *
	 * @param $meta_key
	 * @param $meta_value
	 * @return false|int
	 */
	public function update_meta( $meta_key, $meta_value ){

		if( $this->has_meta( $meta_key ) ) {
			$res = $this->_wpdb->update( Hustle_Db::modules_meta_table(), array(
				"meta_value" => is_array($meta_value) || is_object($meta_value) ? wp_json_encode($meta_value) : $meta_value
			), array(
				'module_id' => $this->id,
				'meta_key' => $meta_key
			),
				array(
					"%s",
				),
				array(
					"%d",
					"%s"
				)
			);

			$this->clean_module_cache( 'meta' );
			if ( self::KEY_SHORTCODE_ID === $meta_key ) {
				$this->clean_module_cache( 'shortcode' );
			}

			return false !== $res;

		}

		return $this->add_meta( $meta_key, $meta_value );

	}

	/**
	 * Checks if optin has $meta_key added disregarding the meta_value
	 *
	 * @param $meta_key
	 * @return bool
	 */
	public function has_meta( $meta_key ){
		return (bool)$this->_wpdb->get_row( $this->_wpdb->prepare( "SELECT * FROM " . Hustle_Db::modules_meta_table() .  " WHERE `meta_key`=%s AND `module_id`=%d", $meta_key, (int) $this->id ) );
	}

	/**
	 * Retrieves optin meta from db
	 *
	 * @since ??
	 * @since 4.0 param $get_cached added.
	 *
	 * @param string $meta_key
	 * @param mixed $default
	 * @param bool $get_cached
	 * @return null|string|$default
	 */
	public function get_meta( $meta_key, $default = null, $get_cached = true ){
		$cache_group = 'hustle_module_meta';

		$module_meta = wp_cache_get( $this->id, $cache_group );

		if ( !$get_cached || false === $module_meta || ! array_key_exists( $meta_key, $module_meta ) ) {

			if ( false === $module_meta ) {
				$module_meta = array();
			}

			$value = $this->_wpdb->get_var( $this->_wpdb->prepare( "SELECT `meta_value` FROM " . Hustle_Db::modules_meta_table() .  " WHERE `meta_key`=%s AND `module_id`=%d", $meta_key, (int) $this->id ) );
			$module_meta[ $meta_key ] = $value;
			wp_cache_set( $this->id, $module_meta, $cache_group );

		}

		return  is_null( $module_meta[ $meta_key ] ) ? $default : $module_meta[ $meta_key ];
	}

	/**
	 * Returns db data for current optin
	 *
	 * @return array
	 */
	public function get_data(){
		return (array) $this->_data;
	}

	/**
	 * Toggles state of optin or optin type
	 *
	 * @param null $environment
	 * @return false|int|WP_Error
	 */
	public function toggle_state( $environment = null ){
		// Clear cache.
		$this->clean_module_cache( 'data' );

		if( is_null( $environment ) ){ // so we are toggling state of the optin
			return $this->_wpdb->update( Hustle_Db::modules_table(), array(
				"active" => (1 - $this->active)
			), array(
				"module_id" => $this->id
			), array(
				"%d"
			) );
		}
	}

	/**
	 * Deactivate module
	 *
	 * @param null $environment
	 */
	public function deactivate( $environment = null ) {
		// Clear cache.
		$this->clean_module_cache( 'data' );

		if( is_null( $environment ) ){ // so we are toggling state of the optin
			return $this->_wpdb->update(
				Hustle_Db::modules_table(),
				array( "active" => 0 ),
				array( "module_id" => $this->id ),
				array( "%d" )
			);
		}
	}

	/**
	 * Activate module
	 *
	 * @param null $environment
	 */
	public function activate( $environment = null ) {
		// Clear cache.
		$this->clean_module_cache( 'data' );

		if( is_null( $environment ) ){ // so we are toggling state of the optin
			return $this->_wpdb->update(
				Hustle_Db::modules_table(),
				array( "active" => 1 ),
				array( "module_id" => $this->id),
				array( "%d" )
			);
		}
	}

	/**
	 * Deletes optin from optin table and optin meta table
	 *
	 * @return bool
	 */
	public function delete() {

		$this->clean_module_cache();

		// delete optin
		$result = $this->_wpdb->delete( Hustle_Db::modules_table(), array(
			"module_id" => $this->id
		),
			array(
				"%d"
			)
		);

		//delete metas
		$result = $result && $this->_wpdb->delete( Hustle_Db::modules_meta_table(), array(
			"module_id" => $this->id
		),
			array(
				"%d"
			)
		);

		//delete tracking data
		$this->_wpdb->delete( Hustle_Db::tracking_table(),
			array(
				"module_id" => $this->id
			),
			array(
				"%d"
			)
		);

		//delete entries
		Hustle_Entry_Model::delete_entries( $this->id );

		return $result;
	}
	/**
	 * Retrieves active tracking types from db
	 *
	 * @return null|array
	 */
	public function get_tracking_types(){
		$this->_track_types = json_decode( $this->get_meta( self::TRACK_TYPES ), true );
		return $this->_track_types;
	}

	/**
	 * Get the "edit roles" stored for this module.
	 *
	 * @since 4.1.0
	 * @return array
	 */
	public function get_edit_roles() {
		$meta_edit_roles = $this->get_meta( self::KEY_MODULE_META_PERMISSIONS );
		$meta_edit_roles = ! empty( $meta_edit_roles ) ? json_decode( $meta_edit_roles, true ) : array();

		return apply_filters( 'hustle_module_get_edit_roles_meta', $meta_edit_roles, $this );
	}

	/**
	 * Checks if $type is active
	 *
	 * @since 4.0
	 *
	 * @param $type
	 * @return bool
	 */
	public function is_tracking_enabled( $type ) {
		$tracking_types = $this->get_tracking_types();

		$is_tracking_enabled = (
			is_array( $tracking_types )
			&& array_key_exists( $type, $tracking_types )
			&& true === $tracking_types[ $type ]
		);

		$is_tracking_enabled = apply_filters( 'hustle_is_tracking_enabled', $is_tracking_enabled, $this, $type );

		return $is_tracking_enabled;
	}

	/**
	 * Edit the modules' "edit_roles" meta.
	 *
	 * @since 4.0
	 * @param array $roles Roles
	 * @return false|integer
	 */
	public function update_edit_roles( $roles ) {

		$available_roles = Opt_In_Utils::get_user_roles();
		$roles           = array_intersect( $roles, array_keys( $available_roles ) );

		return $this->update_meta( self::KEY_MODULE_META_PERMISSIONS, $roles );
	}

	/**
	 * Checks if $type is allowed to track views and conversions
	 *
	 * @param $type
	 * @return bool
	 */
	public function is_track_type_active( $type ){
		return isset( $this->_track_types[ $type ] );
	}

	/**
	 * Toggles $type's tracking mode
	 *
	 * @param $type
	 * @return bool
	 */
	public function toggle_type_track_mode( $type ) {

		if ( $this->is_track_type_active( $type ) ) {
			unset( $this->_track_types[ $type ] );
		} else {
			$this->_track_types[ $type ] = true;
		}
		$res = $this->update_meta( self::TRACK_TYPES, $this->_track_types );

		return $res;
    }

	/**
	 * Disable $type's tracking mode
	 *
	 * @param $type
	 * @param bool $force
	 * @return bool
	 */
	public function disable_type_track_mode( $type, $force = false ) {
		if ( $force && ! empty( $this->_track_types ) ) {
			$this->_track_types = [];
			$updated = true;
		} elseif ( $this->is_track_type_active( $type ) ) {
			unset( $this->_track_types[ $type ] );
			$updated = true;
		}
		if ( !empty( $updated ) ) {
			$res = $this->update_meta( self::TRACK_TYPES, $this->_track_types );
		}
	}

	/**
	 * enable $type's tracking mode
	 *
	 * @param $type
	 * @param bool $force
	 * @return bool
	 */
	public function enable_type_track_mode( $type, $force = false ) {
		if ( $force && 'social_sharing' === $type ) {
			$subtypes = static::get_sshare_types();
			$this->_track_types = array_fill_keys( $subtypes, true );
			$updated = true;
		} elseif ( $force && 'embedded' === $type ) {
			$subtypes = static::get_embedded_types();
			$this->_track_types = array_fill_keys( $subtypes, true );
			$updated = true;
		} elseif ( !$this->is_track_type_active( $type ) ) {
			$this->_track_types[ $type ] = true;
			$updated = true;
		}
		if ( !empty( $updated ) ) {
			$res = $this->update_meta( self::TRACK_TYPES, $this->_track_types );
		}
	}

	/**
	 * Turn on or off the tracking for the passed types.
	 * The array should have the key-value pairs:
	 * { tracking mode } => { boolean }
	 *
	 * @since 4.0
	 *
	 * @param array $tracking_types
	 */
	private function set_sub_type_tracking_status( $tracking_types ) {

		foreach( $tracking_types as $type => $status ) {
			if ( ! is_bool( $status ) ) {
				continue;
			}

			if ( $status ) {
				$this->_track_types[ $type ] = true;
			} elseif ( isset( $this->_track_types[ $type ] ) ) {
				unset( $this->_track_types[ $type ] );
			}

		}

		$res = $this->update_meta( self::TRACK_TYPES, $this->_track_types );

		return $res;
	}

	/**
	 * Create an array with the submitted data to update the tracking types.
	 *
	 * @since 4.0
	 *
	 * @param array $submitted_types
	 */
	public function update_submitted_tracking_types( $submitted_types ) {

		$tracking_types = $this->get_sub_types();

		$tracking_to_update = array();
		foreach ( $tracking_types as $type ) {
			$tracking_to_update[ $type ] = in_array( $type, $submitted_types, true );
		}

		$res = $this->set_sub_type_tracking_status( $tracking_to_update );

		return $res;
	}

	/**
	 * Returns settings saved as meta
	 *
	 * @since 2.0
	 * @param string $key
	 * @param string $default json string
	 * @return object|array
	 */
	protected function get_settings_meta( $key, $default = "{}", $as_array = false, $get_cached = true ){
		$settings_json = $this->get_meta( $key, null, $get_cached );
		return json_decode( $settings_json ? $settings_json : $default, $as_array );
	}

	/**
	 * Load the model with the data to preview.
	 *
	 * @since 4.0
	 *
	 * @param array $data
	 * @return Hustle_Module_Model
	 */
	public function load_preview( $data ) {

		if ( ! $this->module_id ) {
			return false;
		}

		$properties_to_remove = array( 'module_id', 'module_type', 'module_mode', 'active', 'blog_id' );

		foreach( $properties_to_remove as $property ) {
			if ( isset( $data[ $property ] ) ) {
				unset( $data[ $property ] );
			}
		}

		$metas = $this->get_module_meta_names();

		foreach( $metas as $meta ) {

			// Get meta's defaults.
			$method =  'get_' . $meta;
			if ( method_exists( $this, $method ) ) {
				$default = $this->{$method}()->to_array();
			} else {
				$default = array();
			}

			// Merge the passed value with the default.
			if ( isset( $data[ $meta ] ) ) {
				$new_meta = array_merge( $default, $data[ $meta ] );
			} else {
				$new_meta = $default;
			}

			$this->$meta = (object) $new_meta;
		}

		return $this;
	}

	/**
	 * Load the model with its metas.
	 *
	 * @since 4.0
	 *
	 * @return Hustle_Module_Model
	 */
	public function load() {
		if ( ! $this->module_id ) {
			return false;
		}

		$module_metas = $this->get_module_meta_names();

		foreach( $module_metas as $meta ) {
			$method =  "get_" . $meta;
			$value =  method_exists( $this, $method ) ? $this->{$method}()->to_array() : array();
			$this->{$meta} = (object) $value;

		}

		return $this;
	}

	/**
	 * Get the module's meta values.
	 * Note: it's not including the shortcode id, integrations' settings, nor edit roles.
	 *
	 * @since 4.0
	 * @since 4.0.3 $module_type, $module_mode and $with_display_name params added.
	 *
	 * @param string $module_type
	 * @param string $module_mode
	 * @param bool $with_display_name
	 *
	 * @return array
	 */
	public function get_module_meta_names( $module_type = '', $module_mode = '', $with_display_name = false ) {

		$module_type = empty( $module_type ) ? $this->module_type : $module_type;
		$module_mode = empty( $module_mode ) ? $this->module_mode : $module_mode;

		if ( ! $with_display_name ) {
			$metas = array( self::KEY_CONTENT, self::KEY_DESIGN, self::KEY_VISIBILITY );

			if ( 'optin' === $module_mode ) {
				$metas[] = self::KEY_EMAILS;
				$metas[] = self::KEY_INTEGRATIONS_SETTINGS;
			}

			if ( self::SOCIAL_SHARING_MODULE !== $module_type ) {
				$metas[] = self::KEY_SETTINGS;
			}

			if ( self::SOCIAL_SHARING_MODULE === $module_type || self::EMBEDDED_MODULE === $module_type ) {
				$metas[] = self::KEY_DISPLAY_OPTIONS;
			}

		} else {
			// 0 Content
			// 1 Emails
			// 2 Integrations
			// 3 Appearance
			// 4 Display Options
			// 5 Visibility
			// 6 Behavior

			$metas = [];
			$metas[0] = [
				'name'  => self::KEY_CONTENT,
				'label' => self::SOCIAL_SHARING_MODULE !== $module_type ? __( 'Content', 'hustle' ) : __( 'Services', 'hustle' ),
			];

			$metas[3] = [
				'name'  => self::KEY_DESIGN,
				'label' => __( 'Appearance', 'hustle' ),
			];

			$metas[5] = [
				'name'  => self::KEY_VISIBILITY,
				'label' => __( 'Visibility', 'hustle' ),
			];

			if ( 'optin' === $module_mode ) {

				$metas[1] = [
					'name'  => self::KEY_EMAILS,
					'label' => __( 'Emails', 'hustle' ),
				];

				$metas[2] = [
					'name'  => self::KEY_INTEGRATIONS_SETTINGS,
					'label' => __( 'Integrations', 'hustle' ),
				];
			}

			if ( self::SOCIAL_SHARING_MODULE !== $module_type ) {

				$metas[6] = [
					'name'  => self::KEY_SETTINGS,
					'label' => __( 'Behavior', 'hustle' ),
				];
			}

			if ( self::SOCIAL_SHARING_MODULE === $module_type || self::EMBEDDED_MODULE === $module_type ) {

				$metas[4] = [
					'name'  => self::KEY_DISPLAY_OPTIONS,
					'label' => __( 'Display Options', 'hustle' ),
				];
			}

			// Order and return without the keys.
			ksort( $metas );
			$metas = array_values( $metas );
		}

		return $metas;
	}

	/**
	 * Retrieve the module's metas as an array.
	 *
	 * @since 4.0.1
	 * @return array
	 */
	public function get_module_metas_as_array() {

		$metas_array = array();
		$module_metas = $this->get_module_meta_names();

		foreach( $module_metas as $meta ) {
			$method =  "get_" . $meta;
			$value =  method_exists( $this, $method ) ? $this->{$method}()->to_array() : array();
			$metas_array[ $meta ] = $value;

		}

		return $metas_array;
	}

	/**
	 * Return whether the provided role can edit at least one module.
	 *
	 * @since 4.1.0
	 * @param string $role_slug The slug of the role to be checked.
	 * @return boolean
	 */
	public static function can_role_edit_one_module( $role_slug ) {

		global $wpdb;
		$cap   = 'hustle_edit_module';
		$table = Hustle_Db::modules_meta_table();

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$query = $wpdb->prepare( "SELECT module_id FROM `{$table}` WHERE `meta_key`='edit_roles' AND meta_value LIKE %s LIMIT 1", '%"' . $role_slug . '"%' );
		return $wpdb->get_var( $query );
	}

	/**
     * Special save used in migration.
	 * It keeps the passed module id when saving a new module.
	 * It's useful when adding old modules in new tables in MU.
	 *
	 * @since 4.0.0
	 * @return false|int
	 */
    public function save_from_migration() {
        $module_data = get_object_vars( $this );
		$table = Hustle_Db::modules_table();
        $data = $this->_sanitize_model_data( $module_data );
        $format = $this->get_format();
        $format = array_values( $format );
        /**
         * Add ID
         */
        $data['module_id'] = $this->module_id;
        $this->id = $this->module_id;
        $format[] = '%d';
        $this->_wpdb->insert( $table, $data, $format );
        /**
         * Action Hustle after migration module
         *
         * @since 4.0.0
         *
         * @param string $module_type module type
         * @param array $data module data
         * @param int $id module id
         */
        do_action( 'hustle_after_migrate_module', $this->module_type, $module_data, $this->id );

        // Clear cache as well.
        $this->clean_module_cache( 'data' );
        return $this->id;
    }

}
