<?php

/**
 * Class Hustle_Module_Model
 *
 * @property Hustle_Module_Decorator $decorated
 */

class Hustle_Module_Model extends Hustle_Model {

	/**
	 * @var $_provider_details object
	 */
	private $_provider_details;

	public static function instance() {
		return new self();
	}

	/**
	 * Get the sub-types for social sharing modules.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public static function get_sshare_types( $with_titles = false ) {
		if ( ! $with_titles ) {
			return array( Hustle_Sshare_Model::FLOAT_MODULE, 'inline', 'widget', 'shortcode' );
		} else {
			return array(
				Hustle_Sshare_Model::FLOAT_MODULE => __( 'Floating', 'hustle' ),
				'inline' => __( 'Inline', 'hustle' ),
				'widget' => __( 'Widget', 'hustle' ),
				'shortcode' => __( 'Shortcode', 'hustle' ),
			);
		}
	}

	/**
	 * Get the sub-types for embedded modules.
	 *
	 * @since the beggining of time
	 * @since 4.0 "after_content" changed to "inline"
	 *
	 * @return array
	 */
	public static function get_embedded_types( $with_titles = false ) {
		if ( ! $with_titles ) {
			return array( 'inline', 'widget', 'shortcode' );
		} else {
			return array(
				'inline' => __( 'Inline', 'hustle' ),
				'widget' => __( 'Widget', 'hustle' ),
				'shortcode' => __( 'Shortcode', 'hustle' ),
			);
		}
	}

	/**
	 * Get the sub-types for this module.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function get_sub_types( $with_titles = false ) {
		if ( self::EMBEDDED_MODULE === $this->module_type ) {
			return self::get_embedded_types( $with_titles );
		} elseif ( self::SOCIAL_SHARING_MODULE === $this->module_type ) {
			return self::get_sshare_types( $with_titles );
		}

		return array();
	}

	/**
	 * Get the possible module types.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public static function get_module_types() {
		return array( self::POPUP_MODULE, self::SLIDEIN_MODULE, self::EMBEDDED_MODULE, self::SOCIAL_SHARING_MODULE );
	}

	/**
	 * Decorates current model
	 *
	 * @return Hustle_Module_Decorator
	 */
	public function get_decorated() {

		if ( ! $this->_decorator ) {
			$this->_decorator = new Hustle_Module_Decorator( $this ); }

		return $this->_decorator;
	}

	/**
	 * Content Model based upon module type.
	 *
	 * @return Class
	 */
	public function get_content() {
		$data = $this->get_settings_meta( self::KEY_CONTENT, '{}', true );
		// If redirect url is set then esc it.
		if ( isset( $data['redirect_url'] ) ) {
			$data['redirect_url'] = esc_url( $data['redirect_url'] );
		}

		return new Hustle_Popup_Content( $data, $this );
	}

	/**
	 * Get the content of the data stored under 'emails' meta.
	 *
	 * @since 4.0
	 *
	 * @return Hustle_Popup_Emails
	 */
	public function get_emails() {
		$data = $this->get_settings_meta( self::KEY_EMAILS, '{}', true );

		return new Hustle_Popup_Emails( $data, $this );
	}

	/**
	 * Get the module's settings for the given provider.
	 *
	 * @since 4.0
	 *
	 * @param string $slug
	 * @param bool $get_cached
	 * @return array
	 */
	public function get_provider_settings( $slug, $get_cached = true ) {
		return $this->get_settings_meta( $slug . self::KEY_PROVIDER, '{}', true, $get_cached );
	}

	/**
	 * Save the module's settings for the given provider.
	 *
	 * @since 4.0
	 *
	 * @param string $slug
	 * @param array $data
	 * @return array
	 */
	public function set_provider_settings( $slug, $data ) {
		return $this->update_meta( $slug . self::KEY_PROVIDER, $data );
	}

	/**
	 * Get the all-integrations module's settings.
	 * This is not each provider's settings. Instead, these are per module settings
	 * that are applied to all the active providers of this module.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function get_integrations_settings() {
		return new Hustle_Popup_Integrations( $this->get_settings_meta( self::KEY_INTEGRATIONS_SETTINGS, '{}', true ), $this );

	}

	public function get_design() {
		return new Hustle_Popup_Design( $this->get_settings_meta( self::KEY_DESIGN, '{}', true ), $this );
	}

	/**
	 * Get the stored settings for the "Display" tab.
	 * Used for Embedded.
	 *
	 * @since 4.0
	 *
	 * @return Hustle_Embedded_Display
	 */
	public function get_display() {
		return new Hustle_Embedded_Display( $this->get_settings_meta( self::KEY_DISPLAY_OPTIONS, '{}', true ), $this );
	}

	/**
	 * Get the stored settings for the "Visibility" tab.
	 *
	 * @since 4.0
	 *
	 * @return Hustle_Popup_Visibility
	 */
	public function get_visibility() {
		return new Hustle_Popup_Visbility( $this->get_settings_meta( self::KEY_VISIBILITY, '{}', true ), $this );
	}

	/**
	 * Used when populating data with "get".
	 *
	 */
	public function get_settings() {
		$saved = $this->get_settings_meta( self::KEY_SETTINGS, '{}', true );

		// The default value for 'triggers' was an empty string in old versions.
		// This will bring php errors if it persists.
		// Let's remove that troubling value and let the module grab the new defaults.
		if ( isset( $saved['triggers'] ) && empty( $saved['triggers'] ) ) {
			unset( $saved['triggers'] );
		}

		if ( self::POPUP_MODULE === $this->module_type ) {
			return new Hustle_Popup_Settings( $saved, $this );

		} elseif ( self::EMBEDDED_MODULE === $this->module_type ) {
			return new Hustle_Embedded_Settings( $saved, $this );

		} elseif ( self::SLIDEIN_MODULE === $this->module_type ) {
			return new Hustle_Slidein_Settings( $saved, $this );
		}

		return array();
	}

	public function get_shortcode_id() {
		return $this->id;
	}

	public function get_custom_field( $key, $value ) {
		$custom_fields = $this->get_content()->__get( 'form_elements' );

		if ( is_array( $custom_fields ) ) {
			foreach ( $custom_fields as $field ) {
				if ( isset( $field[ $key ] ) && $value === $field[ $key ] ) {
					return $field;
				}
			}
		}
	}

	/**
	 * Get wizard page for this module type.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_wizard_page() {
		return Hustle_Module_Admin::get_wizard_page_by_module_type( $this->module_type );
	}

	/**
	 * Get the listing page for this module type.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_listing_page() {
		return Hustle_Module_Admin::get_listing_page_by_module_type( $this->module_type );
	}

	/**
	 * Get the module's data. Used to display it.
	 *
	 * @since 3.0.7
	 *
	 * @param bool is_preview
	 * @return array
	 */
	public function get_module_data_to_display() {

		if ( 'social_sharing' === $this->module_type ) {
			$data = $this->get_data();

		} else {
			$settings = array( 'settings' => $this->get_settings()->to_array() );
			$data = array_merge( $settings, $this->get_data() );

		}

		return $data;
	}

	/**
	 * Get the form fields of this module, if any.
	 *
	 * @since 4.0
	 *
	 * @return null|array
	 */
	public function get_form_fields() {

		if ( 'social_sharing' === $this->module_type || 'informational' === $this->module_mode ) {
			return null;
		}

		$emails_data = $this->get_emails()->to_array();
		/**
		 * Edit module fields
		 * @since 4.1.1
		 * @param string $form_elements Current module fields.
		 */
		$form_fields = apply_filters( 'hustle_form_elements', $emails_data['form_elements'] );

		return $form_fields;

	}

	/**
	 * Get visibility behavior by default, to display or not
	 *
	 * @return bool
	 */
	private function get_default_group_behavior( $conditions ) {
		return !empty( $conditions['show_or_hide_conditions'] ) && 'hide' === $conditions['show_or_hide_conditions'];

	}

	/**
	 * Get relevant conditions based on subtype or return FALSE if it shouldn't be shown
	 *
	 * @param string $subtype
	 * @return array|false
	 */
	private function get_conditions( $subtype = null ) {
		$all_conditions = $this->get_visibility()->to_array();

		// Return all. No need to filter per subtype.
		if ( is_null( $subtype ) || empty( $all_conditions['conditions'] ) ) {
			return $all_conditions;
		}

		// Remove the conditions that are not for this subtype.
		$conditions_removed = false;
		foreach ( $all_conditions['conditions'] as $group_id => $data ) {
			if ( isset( $data['apply_on_' . $subtype ] ) && 'false' === $data['apply_on_' . $subtype ] ||
					'shortcode' === $subtype && !isset( $data['apply_on_' . $subtype ] ) ) {
				$conditions_removed = true;
				unset( $all_conditions['conditions'][ $group_id ] );
			}
		}

		// No conditions are left after filtering per subtype.
		if ( $conditions_removed && empty( $all_conditions['conditions'] ) ) {
			return false;
		}

		return $all_conditions;
	}

	/**
	 * Checks if this module is allowed to be displayed
	 *
	 * @return bool
	 */
	public function is_allowed_to_display( $module_type, $sub_type = null ) {
		$global_behavior = false;

		// If the module isn't published, it shouldn't display.
		if ( ! $this->active ) {
			return false;
		}

		$all_conditions = $this->get_conditions( $sub_type );

		if ( false === $all_conditions ) {
			if ( 'shortcode' === $sub_type ) {
				return true;
			}
			return false;
		}
		if ( empty( $all_conditions['conditions'] ) ) {
			return true;
		}

		$display = null;
		foreach ( $all_conditions['conditions'] as $group_id => $conditions ) {
			$any_true = false;
			$any_false = false;
			$default_behavior = $this->get_default_group_behavior( $conditions );
			if ( $default_behavior ) {
				$global_behavior = true;
			}

			/**
			 * condition type
			 */
			$filter_type = isset( $conditions['filter_type'] ) &&
					'any' === $conditions['filter_type']
				? $conditions['filter_type'] : 'all';

			foreach ( $conditions as $condition_key => $args ) {

				// These are not conditions but group's properties we don't need to check here.
				if ( in_array( $condition_key, [ 'group_id', 'filter_type', 'apply_on_inline', 'apply_on_widget', 'apply_on_shortcode', 'show_or_hide_conditions' ], true ) ) {
					continue;
				}

				// only cpt have 'postType' and 'postTypeLabel' properties.
				if ( is_array( $args ) && isset( $args['postType'] ) && isset( $args['postTypeLabel'] ) ) {
					$condition_key = 'cpt';
				}
				$condition = Hustle_Condition_Factory::build( $condition_key, $args );
				if ( $condition ) {
					$some_conditions = true;
					$condition->set_type( $module_type );
					$condition->module = $this;
					$current = (bool) $condition->is_allowed();
					if ( false === $current ) {
						$any_false = true;
					} else {
						$any_true = true;
					}
				}
			}

			if ( 'any' === $filter_type ) {
				if ( $any_true ) {
					$display = $display || $any_true && ! $default_behavior ;
				} else if ( $any_false ) {
					$display = $display || $default_behavior;
				}
			}
			if ( 'all' === $filter_type ) {
				if ( $any_false ) {
					$display = $display || $default_behavior;
				} else if ( $any_true ) {
					$display = $display || $any_true && ! $default_behavior;
				}
			}
		}

		// show module if there are no conditions
		if ( empty( $some_conditions ) ) {
			return true;
		}

		if ( is_null( $display ) ) {
			return $global_behavior;
		}

		return $display;
	}

	/**
	 * Creates and store the nonce used to validate email unsubscriptions.
	 *
	 * @since 3.0.5
	 * @param string $email Email to be unsubscribed.
	 * @param array $lists_id IDs of the modules to which it will be unsubscribed.
	 * @return boolean
	 */
	public function create_unsubscribe_nonce( $email, array $lists_id ) {
		// Since we're supporting php 5.2, random_bytes or other strong rng are not available. So using this instead.
		$nonce = hash_hmac( 'md5', $email, wp_rand() . time() );

		$data = get_option( self::KEY_UNSUBSCRIBE_NONCES, array() );

		// If the email already created a nonce and didn't use it, replace its data.
		$data[ $email ] = array(
			'nonce' => $nonce,
			'lists_id' => $lists_id,
			'date_created' => time(),
		);

		$updated = update_option( self::KEY_UNSUBSCRIBE_NONCES, $data );
		if ( $updated ) {
			return $nonce;
		} else {
			return false;
		}
	}

	/**
	 * Does the actual email unsubscription.
	 *
	 * @since 3.0.5
	 * @param string $email Email to be unsubscribed.
	 * @param string $nonce Nonce associated with the email for the unsubscription.
	 * @return boolean
	 */
	public function unsubscribe_email( $email, $nonce ) {
		$data = get_option( self::KEY_UNSUBSCRIBE_NONCES, false );
		if ( ! $data ) {
			return false;
		}
		if ( ! isset( $data[ $email ] ) || ! isset( $data[ $email ]['nonce'] ) || ! isset( $data[ $email ]['lists_id'] ) ) {
			return false;
		}
		$email_data = $data[ $email ];
		if ( ! hash_equals( (string) $email_data['nonce'], $nonce ) ) {
			return false;
		}
		// Nonce expired. Remove it. Currently giving 1 day of life span.
		if ( ( time() - (int) $email_data['date_created'] ) > DAY_IN_SECONDS ) {
			unset( $data[ $email ] );
			update_option( self::KEY_UNSUBSCRIBE_NONCES, $data );
			return false;
		}

		// Proceed to unsubscribe
		foreach ( $email_data['lists_id'] as $id ) {
			$unsubscribed = $this->remove_local_subscription_by_email_and_module_id( $email, $id );
		}

		// The email was unsubscribed and the nonce was used. Remove it from the saved list.
		unset( $data[ $email ] );
		update_option( self::KEY_UNSUBSCRIBE_NONCES, $data );

		return true;

	}

	/**
	 * Duplicate a module.
	 *
	 * @since 3.0.5
	 * @since 4.0 moved from Hustle_Popup_Admin_Ajax to here. New settings added.
	 *
	 * @return bool
	 */
	public function duplicate_module() {

		if ( ! $this->id ) {
			return false;
		}

		// TODO: make use of the sshare model to extend this instead.
		if ( self::SOCIAL_SHARING_MODULE !== $this->module_type ) {

			$data = array(
				'content' => $this->get_content()->to_array(),
				'emails' => $this->get_emails()->to_array(),
				'design' => $this->get_design()->to_array(),
				'settings' => $this->get_settings()->to_array(),
				'visibility' => $this->get_visibility()->to_array(),
				self::KEY_INTEGRATIONS_SETTINGS	=> $this->get_integrations_settings()->to_array(),
			);

			if ( self::EMBEDDED_MODULE === $this->module_type ) {
				$data['display'] = $this->get_display()->to_array();
			}

			// Pass integrations

			if ( 'optin' === $this->module_mode ) {
				$integrations = array();
				$providers = Hustle_Providers::get_instance()->get_providers();
				foreach ( $providers as $slug => $provider ) {
					$provider_data = $this->get_provider_settings( $slug, false );
					//if ( 'local_list' !== $slug && $provider_data && $provider->is_connected()
					if ( $provider_data && $provider->is_connected()
							&& $provider->is_form_connected( $this->module_id ) ) {
						$integrations[ $slug ] = $provider_data;
					}
				}

				$data['integrations'] = $integrations;
			}

		} else {
			$data = array(
				'content' => $this->get_content()->to_array(),
				'display' => $this->get_display()->to_array(),
				'design' => $this->get_design()->to_array(),
				'visibility' => $this->get_visibility()->to_array(),
			);
		}

		unset( $this->id );

		//rename
		$this->module_name .= __( ' (copy)', 'hustle' );

		//turn status off
		$this->active = 0;

		//save
		$result = $this->save();

		if ( $result && ! is_wp_error( $result ) ) {

			$this->update_module( $data );

			return true;
		}

		return false;
	}

	/**
	 * Get data needed for rendering the tracking charts in the listing pages
	 * Retrieved via AJAX.
	 *
	 * @since 4.0.4
	 */
	public function get_tracking_data() {

		if ( ! $this->id ) {
			return '';
		}

		$tracking_model           = Hustle_Tracking_Model::get_instance();
		$total_module_conversions = $tracking_model->count_tracking_data( $this->id, 'all_conversion' );
		$total_module_views       = $tracking_model->count_tracking_data( $this->id, 'view' );
		$last_entry_time          = Opt_In_Utils::get_latest_conversion_time_by_module_id( $this->id );
		$rate                     = $total_module_views ? round( ( $total_module_conversions * 100 ) / $total_module_views, 1 ) : 0;
		$module_sub_types         = $this->get_sub_types( true );
		$is_cta                   = ! empty( $this->get_content()->__get( 'show_cta' ) );

		$multiple_charts = array();

		// Get each sub type's tracking data if the module type has sub types.
		if ( ! empty( $module_sub_types ) ) {

			foreach ( $module_sub_types as $slug => $display_name ) {

				$subtype         = $this->module_type . '_' . $slug;
				$views           = $tracking_model->count_tracking_data( $this->id, 'view', $subtype );
				$conversions     = $tracking_model->count_tracking_data( $this->id, 'all_conversion', $subtype );
				$conversion_rate = $views ? round( ( $conversions * 100 ) / $views, 1 ) : 0;

				$multiple_charts[ $slug ] = array(
					'display_name'    => $display_name,
					'last_entry_time' => Opt_In_Utils::get_latest_conversion_time_by_module_id( $this->id, $subtype ),
					'views'           => $views,
					'conversions'     => $conversions,
					'conversion_rate' => $conversion_rate,
				);
			}
		}

		$render_arguments = [
			'module'                   => $this,
			'total_module_views'       => $total_module_views,
			'total_module_conversions' => $total_module_conversions,
			'tracking_types'           => $this->get_tracking_types(),
			'last_entry_time'          => $last_entry_time,
			'rate'                     => $rate,
			'is_cta'                   => $is_cta,
		];

		if ( $is_cta ) {
			$notice_for_old_data = $tracking_model->has_old_tracking_data( $this->id );
			$render_arguments['notice_for_old_data'] = $notice_for_old_data;
		}

		ob_start();

		// ELEMENT: Tracking data.
		Opt_In::static_render(
			'admin/commons/sui-listing/elements/tracking-data',
			[
				'render_arguments' => $render_arguments,
				'multiple_charts'  => $multiple_charts,
			]
		);

		$html = ob_get_clean();

		$charts_data = $this->get_charts_data( array_keys( $module_sub_types ), $total_module_views );

		$data = array(
			'html'        => $html,
			'charts_data' => $charts_data,
		);

		return $data;
	}

	/**
	 * Get tracking data for building charts on listing page
	 *
	 * @since 4.0.4
	 * @param array $sub_types Module's sub types.
	 * @param int   $views Module's views count.
	 * @return array
	 */
	private function get_charts_data( $sub_types, $views ) {

		$sql_month_start_date = date( 'Y-m-d H:i:s', strtotime( '-30 days midnight' ) );
		$tracking_model       = Hustle_Tracking_Model::get_instance();
		$days_array           = [];
		$default_array        = [];

		for ( $h = 30; $h >= 0; $h-- ) {
			$time                   = strtotime( '-' . $h . ' days' );
			$date                   = date( 'Y-m-d', $time );
			$default_array[ $date ] = 0;
			$days_array[]           = date( 'M j, Y', $time );
		}

		$sub_types[]      = 'overall';
		$conversion_types = [ 'all' ];

		if ( self::SOCIAL_SHARING_MODULE !== $this->module_type ) {
			$conversion_types[] = 'cta';

			if ( self::OPTIN_MODE === $this->module_mode ) {
				$conversion_types[] = 'optin';
			}
		}

		$data = [];
		foreach ( $sub_types as $sub_type ) {

			$chart_container_id = sprintf(
				'hustle-%1$s-%2$s-stats--%3$s',
				$this->module_type,
				$this->module_id,
				$sub_type
			);
			$data[ $sub_type ]  = [
				'id'   => $chart_container_id,
				'days' => $days_array,
			];

			foreach ( $conversion_types as $conversion_type ) {

				$last_month_conversions = $tracking_model->get_form_latest_tracking_data_count_grouped_by_day( $this->module_id, $sql_month_start_date, $conversion_type . '_conversion', $this->module_type, $sub_type );
				$last_month_views       = $tracking_model->get_form_latest_tracking_data_count_grouped_by_day( $this->module_id, $sql_month_start_date, 'view', $this->module_type, $sub_type );

				if ( ! $last_month_conversions ) {
					$submissions_data = $default_array;
				} else {
					$submissions_array = wp_list_pluck( $last_month_conversions, 'tracked_count', 'date_created' );
					$submissions_data  = array_merge( $default_array, array_intersect_key( $submissions_array, $default_array ) );
				}

				if ( ! $last_month_views ) {
					$views_data = $default_array;
				} else {
					$views_array = wp_list_pluck( $last_month_views, 'tracked_count', 'date_created' );
					$views_data  = array_merge( $default_array, array_intersect_key( $views_array, $default_array ) );
				}

				$query_sub_type        = 'overall' === $sub_type ? null : $sub_type;
				$query_conversion_type = $conversion_type . '_conversion';
				$sub_type_conversions  = $tracking_model->count_tracking_data( $this->id, $query_conversion_type, $query_sub_type );

				$data[ $sub_type ][ $conversion_type ] = [
					'conversions_count' => $sub_type_conversions,
					'conversion_rate'   => $views ? round( ( $sub_type_conversions * 100 ) / $views, 1 ) : 0,
					'conversions'       => array_values( $submissions_data ),
				];
			}

			$data[ $sub_type ]['views'] = array_values( $views_data );
			$data[ $sub_type ]['conversions'] = array_values( $submissions_data );
		}

		return $data;
	}

	/**
	 * Get selector for chart on listing page
	 *
	 * @param int $module_id
	 * @param string $module_type
	 * @param string $module_subtype
	 * @param string $conversion_type all|ctp|optin
	 * @return string
	 */
	private function get_chart_id( $module_id, $module_type, $module_subtype = 'overall', $conversion_type = 'all' ) {
		$id = "hustle-{$module_type}-{$module_id}-stats" . ( $module_subtype ? '--' . $module_subtype : '' );

		return $id;
	}


	/**
	 * Get a new and unique shortcode id.
	 *
	 * @since 4.0
	 *
	 * @param string $id
	 * @return string
	 */
	private function sanitize_shortcode_id( $id ) {
		$id = str_replace( ' ', '_', $id );
		$id = preg_replace('/[^A-Za-z0-9 ]/', '', $id);

		return $id;
	}

	/**
	 * Get the module type by module id
	 * without the overhead of populating the model.
	 *
	 * @since 4.0
	 *
	 * @param integer $module_id
	 * @return string|null
	 */
	public function get_module_type_by_module_id( $module_id ) {

		$query = $this->_wpdb->prepare( "
			SELECT module_type FROM `" . Hustle_Db::modules_table() . "`
			WHERE `module_id`=%s",
			$module_id
		);

		return $this->_wpdb->get_var( $query );
	}

	/**
	 * Render the module.
	 *
	 * @since 4.0
	 *
	 * @param string $sub_type
	 * @param string $custom_classes
	 * @param bool $is_preview
	 * @return string
	 */
	public function display( $sub_type = null, $custom_classes = '', $is_preview = false ) {
		if ( ! $this->id ) {
			return;
		}
		$renderer = $this->get_renderer();
		return $renderer->display( $this, $sub_type, $custom_classes, $is_preview );
	}

	public function get_renderer() {
		return new Hustle_Module_Renderer();
	}

	/**
	 * Return whether the module's sub_type is active.
	 *
	 * @since the beginning of time
	 * @since 4.0 method name changed.
	 *
	 * @param string $type
	 * @return boolean
	 */
	public function is_display_type_active( $type ) {
		$settings = $this->get_display()->to_array();

		if ( isset( $settings[ $type . '_enabled' ] ) && in_array( $settings[ $type . '_enabled' ], array( '1', 1, 'true' ), true ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Load a palette array.
	 *
	 * @param string $name  Palette name = file name.
	 *
	 * @return string
	 */
	public static function get_palette_file( $name ) {
		$file    = Opt_In::$plugin_path . "palettes/{$name}.php";
		$content = array();

		if ( is_file( $file ) ) {
			/* @noinspection PhpIncludeInspection */
			$content = include $file;
		}

		return $content;

	}

	/**
	 * Returns palettes used to color optins
	 *
	 * @return array
	 */
	public static function get_default_palettes() {

		$default_palettes_slugs = self::get_palettes_names();
		$default_palettes = array();

		foreach( $default_palettes_slugs as $slug ) {
			$default_palettes[ $slug ] = self::get_palette_array( $slug );
		}

		return $default_palettes;
	}

	/**
	 * Get all palettes, defaults + customized ones.
	 *
	 * @since 4.0.3
	 * @return array
	 */
	public static function get_all_palettes() {

		$default_palettes = self::get_default_palettes();

		$custom_palettes_array = Hustle_Settings_Admin::get_custom_color_palettes();
		$custom_palettes = array();

		foreach( $custom_palettes_array as $slug => $data ) {
			// Merge with one of the default palettes in case we introduce a new property.
			$custom_palettes[ $slug ] = array_merge( $default_palettes['gray_slate'], $data['palette'] );
		}

		return array_merge( $default_palettes, $custom_palettes );
	}

	/**
	 * Get all the default palettes and custom ones slugs and display names.
	 *
	 * @since 4.0.3
	 * @return array
	 */
	public static function get_all_palettes_slug_and_name() {

		$stored_palettes = Hustle_Settings_Admin::get_custom_color_palettes();

		$stored_names = wp_list_pluck( $stored_palettes, 'name', 'slug' );

		$default_palettes = self::get_palettes_names( true );

		return array_merge( $stored_names, $default_palettes );
	}

	/**
	 * Get the names of the existing color palettes.
	 * Watch out if you change these. They are used during 3.x -> 4.x migration.
	 *
	 * @since 4.0
	 * @since 4.0.3 $get_display_name parameter added
	 *
	 * @param bool $get_display_name
	 * @return array
	 */
	public static function get_palettes_names( $get_display_name = false ) {

		if ( ! $get_display_name ) {

			return array(
				'gray_slate', 'coffee', 'ectoplasm', 'blue', 'sunrise', 'midnight',
			);
		}

		return array(
			'gray_slate' => __( 'Gray Slate', 'hustle' ),
			'coffee' => __( 'Coffee', 'hustle' ),
			'ectoplasm' => __( 'Ectoplasm', 'hustle' ),
			'blue' => __( 'Blue', 'hustle' ),
			'sunrise' => __( 'Sunrise', 'hustle' ),
			'midnight'=> __( 'Midnight', 'hustle' ),
		);
	}

	/**
	 * Returns palette array for palette name
	 *
	 * @param string $palette_name e.g. "gray_slate"
	 *
	 * @return array
	 */
	public static function get_palette_array( $palette_name ) {

		$palette_data = array();

		//since this is just used for comparision
		//while creating custom palette.
		if( 'info-module' === $palette_name ){
			$palette_data = self::get_palette_file( $palette_name );
		}

		// If it's a default palette, get the array from the file.
		if ( in_array( $palette_name, self::get_palettes_names(), true ) ) {
			$palette_data = self::get_palette_file( $palette_name );

		} else {
			// If it's custom, retrieve it from the stored settings.
			$saved_palettes = Hustle_Settings_Admin::get_custom_color_palettes();

			// Check if the palette name still exists.
			if ( isset( $saved_palettes[ $palette_name ] ) ) {
				$palette = $saved_palettes[ $palette_name ];

				// Merge it with a default in case we introduced new settings not present in the stored array.
				$palette_data = array_merge( self::get_palette_file( 'gray_slate' ), $palette['palette'] );

			}
		}

		return $palette_data;
	}

	/**
	 * Sanitize the form fields name replacing spaces by underscores.
	 * This way the data is handled properly along hustle.
	 *
	 * @since 4.0
	 * @param string $name
	 * @return string
	 */
	public static function sanitize_form_field_name( $name ) {
		$sanitized_name = apply_filters( 'hustle_sanitize_form_field_name', str_replace( ' ', '_', trim( $name ) ), $name );
		return $sanitized_name;
	}

	public static function sanitize_form_fields_names( $names_to_sanitize, $form_fields ) {

		// Replace the name without changing the array's order.
		$names_array = array_keys( $form_fields );
		foreach ( $names_to_sanitize as $name ) {
			$index = array_search( $name, $names_array, true );
			$sanitized_name = self::sanitize_form_field_name( $name );
			$form_fields[ $name ]['name'] = $sanitized_name;

			$names_array[ $index ] = $sanitized_name;

		}
		$sanitized_fields = array_combine( $names_array, array_values( $form_fields ) );

		return $sanitized_fields;
	}

	public function sanitize_form_elements( $form_elements ) {
		// Sanitize GDPR message
		if ( isset( $form_elements['gdpr']['gdpr_message'] ) ) {
			$allowed_html = array(
				'a' => array(
					'href' => true,
					'title' => true,
					'target' => true,
					'alt' => true,
				),
				'b' => array(),
				'strong' => array(),
				'i' => array(),
				'em' => array(),
				'del' => array(),
			);
			$form_elements['gdpr']['gdpr_message'] = wp_kses( wp_unslash( $form_elements['gdpr']['gdpr_message'] ), $allowed_html );
		}

		$names_to_sanitize = array();
		foreach ( $form_elements as $name => $field_data ) {
			if ( false !== stripos( $name, ' ' ) ) {
				$names_to_sanitize[] = $name;
			}
		}

		// All good, return the data.
		if ( empty( $names_to_sanitize ) ) {
			return $form_elements;
		}

		$form_elements = self::sanitize_form_fields_names( $names_to_sanitize, $form_elements );

		return $form_elements;
	}

	/**
	 * Update Custom Fields for Sendgrid New Campaigns
	 */
	public function maybe_update_custom_fields() {
		$connected_addons = Hustle_Provider_Utils::get_addons_instance_connected_with_module( $this->module_id );

		foreach ( $connected_addons as $addon ) {
			// Change logic only for sendgrid for now
			if ( 'sendgrid' !== $addon->get_slug() ) {
				continue;
			}
			$global_multi_id = $addon->selected_global_multi_id;
			$new_campaigns = $addon->get_setting( 'new_campaigns', '', $global_multi_id );
			// only if it's the New Sendgrid Campaigns
			if ( 'new_campaigns' !== $new_campaigns ) {
				continue;
			}
			$emails = $this->get_emails()->to_array();
			$custom_fields = [];

			$api_key = $addon->get_setting( 'api_key', '', $global_multi_id );
			$api = $addon::api( $api_key, $new_campaigns );

			foreach ( $emails['form_elements'] as $element ) {
				if ( empty( $element['type'] ) || in_array( $element['type'], [ 'submit', 'recaptcha' ], true ) ) {
					continue;
				}
				$custom_fields[] = [
					'type' => 'text',
					'name' => $element['name'],
				];
			}

			if ( !empty( $custom_fields ) ) {
				$api->add_custom_fields( $custom_fields );
			}
		}
	}

}
