<?php

/**
 * Conditions utils
 *
 * Most of the methods are courtesy Philipp Stracker
 *
 * Class Opt_In_Utils
 */
class Opt_In_Utils {

	/**
	 * Instance of Opt_In_Geo
	 *
	 * @var Opt_In_Geo
	 */
	private $_geo;

	/**
	 * CPT
	 *
	 * @var array
	 */
	private static $post_types;

	/**
	 * Array of administrator roles
	 *
	 * @var array
	 */
	private static $admin_roles;

	public static $comment;

	public function __construct( Opt_In_Geo $geo ) {
		$this->_geo = $geo;
	}

	/**
	 * Checks if user has already commented
	 *
	 * @return bool|int
	 */
	public function has_user_commented() {
		if ( null === self::$comment ) {
			// Guests (and maybe logged in users) are tracked via a cookie.
			self::$comment = isset( $_COOKIE[ 'comment_author_' . COOKIEHASH ] ) ? 1 : 0;

			if ( ! self::$comment && is_user_logged_in() ) {
				// For logged-in users we can also check the database.
				$count = get_comments( [
					'count' => true,
					'user_id' => get_current_user_id(),
				] );
				self::$comment = $count > 0;
			}
		}
		return self::$comment;
	}

	/**
	 * Returns the referrer.
	 *
	 * @return string
	 */
	public function get_referrer() {
		$referrer = '';

		$is_ajax = (defined( 'DOING_AJAX' ) && DOING_AJAX)
			|| ( ! empty( $_POST['_po_method_'] ) && 'raw' === $_POST['_po_method_'] ); // WPCS: CSRF ok.

		if ( isset( $_REQUEST['thereferrer'] ) ) { // WPCS: CSRF ok.
			$referrer = $_REQUEST['thereferrer']; // WPCS: CSRF ok.
		} else if ( ! $is_ajax && isset( $_SERVER['HTTP_REFERER'] ) ) {
			// When doing Ajax request we NEVER use the HTTP_REFERER!
			$referrer = $_SERVER['HTTP_REFERER'];
		}

		return $referrer;
	}

	/**
	 * Tests if the current referrer is one of the referers of the list.
	 * Current referrer has to be specified in the URL param "thereferer".
	 *
	 *
	 * @param  array $list List of referers to check.
	 * @return bool
	 */
	public function test_referrer( $list ) {
		$response = false;
		if ( is_string( $list ) ) {
			$list = preg_split( '/\r\n|\r|\n/', $list );
		}
		if ( ! is_array( $list ) ) {
			return true;
		}

		$referrer = $this->get_referrer();

		if ( ! empty( $referrer ) ) {
			foreach ( $list as $item ) {
				$item = trim( $item );
				$res = stripos( $referrer, $item );
				if ( false !== $res ) {
					$response = true;
					break;
				}
			}
		}

		return $response;
	}

	/**
	 * Tests if the $test_url matches any pattern defined in the $list.
	 *
	 * @since  4.6
	 * @param  string $test_url The URL to test.
	 * @param  array $list List of URL-patterns to test against.
	 * @return bool
	 */
	public function check_url( $list ) {
		$response = false;

		$list = array_map( 'trim', (array) $list );
		if ( empty( $list ) ) {
			$response = true;

		} else {

			$test_url = strtok( $this->get_current_actual_url( true ), '#' );
			$test_url_no_protocol = strtok( $this->get_current_actual_url(), '#' );

			foreach ( $list as $match ) {
				$match = strtok( $match, '#' );

				// We're using '%' at the beggining of the string in visibility conditions to differentiate
				// regular urls from regex. If it's not regex, use regular url check.
				if ( 0 !== strpos( $match, '%' ) ) {

					// Check if we're using a wildcard.
					if ( false === strpos( $match, '*' ) ) {
						$match = preg_quote( $match, null );
						if ( false === strpos( $match, '://' ) ) {
							$match = '\w+://' . $match;
						}
						if ( '/' !== substr( $match, -1 ) ) {
							$match .= '/?';
						} else {
							$match .= '?';
						}
						$exp = '#^' . $match . '$#i';

						$res = preg_match( $exp, $test_url );

					} else {
						// Check wildcards.
						$res = fnmatch( $match, $test_url_no_protocol );
					}
				} else {
					// Check for regex urls.
					$match = ltrim( $match, '%' );
					$exp = $match;

					$res = preg_match( $exp, $test_url );
				}

				if ( $res ) {
					$response = true;
					break;
				}
			}
		}

		return $response;
	}

	/**
	 * Returns current url
	 * should only be called after plugins_loaded hook is fired
	 *
	 * @return string
	 */
	public static function get_current_url() {
		if ( ! did_action( 'plugins_loaded' ) ) {
			new Exception( 'This method should only be called after plugins_loaded hook is fired' ); }

		global $wp;
		return add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
	}

	/**
	 * Returns current actual url, the one seen on browser
	 *
	 * @return string
	 */
	public function get_current_actual_url( $with_protocol = false ) {
		if ( ! did_action( 'plugins_loaded' ) ) {
			new Exception( 'This method should only be called after plugins_loaded hook is fired' ); }

		if ( ! $with_protocol ) {
			return "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}
		return 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	/**
	 * Checks if the current user IP belongs to one of the countries defined in
	 * country_codes list.
	 *
	 * @param  array $country_codes List of country codes.
	 * @return bool
	 */
	public function test_country( $country_codes ) {
		$response = true;
		$country = $this->_geo->get_user_country();

		if ( 'XX' === $country ) {
			return $response;
		}

		return in_array( $country, (array) $country_codes, true );
	}

	/**
	 * Checks if user is allowed to perform the ajax actions
	 *
	 * @since 4.0
	 * @param array $capability Hustle capability
	 * @param int $module_id Optional. Module id
	 */
	public static function is_user_allowed_ajax( $capability, $module_id = null ) {
		if ( is_null( $module_id ) ) {
			$allowed = current_user_can( $capability );
		} else {
			$allowed = self::is_user_allowed( $capability, $module_id );
		}

		if ( ! $allowed ) {
			wp_send_json_error( __( 'Invalid request, you are not allowed to make this request', 'hustle' ) );
		}
	}

	/**
	 * Check is it admin role or not
	 *
	 * @param string|array $role
	 * @return bool
	 */
	public static function is_admin_role( $role ) {
		$admin_roles = array_keys( self::get_admin_roles() );

		if ( ! is_array( $role ) ) {
			return in_array( $role, $admin_roles, true );
		}

		return (bool) array_intersect( $role, $admin_roles );
	}

	/**
	 * Get admin role array
	 *
	 * @since 4.1.0
	 * @return array
	 */
	public static function get_admin_roles() {

		if ( is_null( self::$admin_roles ) ) {
			$admins    = [];
			$all_roles = wp_roles();

			if ( $all_roles->is_role( 'administrator' ) ) {
				$admins['administrator'] = ucfirst( translate_user_role( 'administrator', 'hustle' ) );

			} else {
				foreach ( $all_roles->roles as $name => $data ) {
					if ( ! empty( $data['capabilities']['manage_options'] ) && true === $data['capabilities']['manage_options'] ) {
						$admins[ $name ] = $data['name'];
					}
				}
			}

			self::$admin_roles = apply_filters( 'hustle_get_admin_roles', $admins );
		}

		return self::$admin_roles;
	}

	/**
	 * Get short days names html escaped and translated
	 *
	 * @since 4.0
	 * @return array
	 */
	public static function get_short_days_names() {
		return array(
			esc_html__( 'Su', 'hustle' ),
			esc_html__( 'Mo', 'hustle' ),
			esc_html__( 'Tu', 'hustle' ),
			esc_html__( 'We', 'hustle' ),
			esc_html__( 'Th', 'hustle' ),
			esc_html__( 'Fr', 'hustle' ),
			esc_html__( 'Sa', 'hustle' ),
		);
	}

	/**
	 * Get months names html escaped and translated
	 *
	 * @since 4.0
	 * @return array
	 */
	public static function get_months_names() {
		return array(
			esc_html__( 'January', 'hustle' ),
			esc_html__( 'February', 'hustle' ),
			esc_html__( 'March', 'hustle' ),
			esc_html__( 'April', 'hustle' ),
			esc_html__( 'May', 'hustle' ),
			esc_html__( 'June', 'hustle' ),
			esc_html__( 'July', 'hustle' ),
			esc_html__( 'August', 'hustle' ),
			esc_html__( 'September', 'hustle' ),
			esc_html__( 'October', 'hustle' ),
			esc_html__( 'November', 'hustle' ),
			esc_html__( 'December', 'hustle' ),
		);
	}

	/**
	 * Checks if user has the capability
	 *
	 * @since 4.0
	 * @param array $capability Hustle capability
	 * @param int $module_id Optional. Module id
	 * @return bool
	 */
	public static function is_user_allowed( $capability, $module_id = null ) {

		// Super admins can do everything.
		if ( current_user_can( 'setup_network' ) ) {
			return true;
		}

		$user               = wp_get_current_user();
		$current_user_caps  = (array) $user->allcaps;
		$current_user_roles = (array) $user->roles;

		if ( self::is_admin_role( $current_user_roles ) ) {
			// If editing a module and the user is godish, allow.
			return true;

		} elseif ( 'hustle_edit_module' === $capability && ! empty( $current_user_caps['hustle_create'] ) ) {
			// If the user can create, it also can edit. Allow.
			return true;

		} elseif ( is_null( $module_id ) ) {
			// If we're not editing a module, check for the requested capability.
			return ! empty( $current_user_caps[ $capability ] );

		} else {

			// If editing a module and the user isn't godish...
			$module = Hustle_Module_Model::instance()->get( $module_id );

			// If the module isn't valid, abort.
			if ( is_wp_error( $module ) ) {
				return false;
			}

			// Check for the specific allowed roles.
			$allowed_roles = $module->get_edit_roles();
			return (bool) array_intersect( $allowed_roles, $current_user_roles );
		}

		return false;
	}

	/**
	 * Checks if the ajax
	 *
	 * @since 1.0
	 * @param $action string ajax call action name
	 */
	public static function validate_ajax_call( $action ) {
		if ( ! check_ajax_referer( $action, false, false ) ) {
			wp_send_json_error( __( 'Invalid request, you are not allowed to make this request', 'hustle' ) ); }
	}

	/**
	 * Verify if current version is FREE
	 **/
	public static function _is_free() {
		$is_free = ! file_exists( Opt_In::$plugin_path . 'lib/wpmudev-dashboard/wpmudev-dash-notification.php' );

		return $is_free;
	}

	/**
	 * Remove "-pro" that came from the menu which causes template not to work
	 **/
	public static function clean_current_screen( $screen ) {
		return str_replace( 'hustle-pro', 'hustle', $screen );
	}

	/**
	 * Get the user roles options.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public static function get_user_roles() {

		global $wp_roles;
		$roles = $wp_roles->get_names();

		return apply_filters( 'hustle_get_module_permissions_roles', $roles );
	}

	// ====================================
	// INTEGRATIONS
	// ====================================

	/**
	 * Used for sanitizing form submissions.
	 * This method will do a simple sanitation of $post_data. It applies sanitize_text_field() to the keys and values of the first level array.
	 * The keys from second level arrays are converted to numbers, and their values are sanitized with sanitize_text_field() as well.
	 * This method doesn’t do an exhaustive sanitation, so you should handled special cases if your integration requires something different.
	 * The names passed on $required_fields are searched into $post_data array keys. If the key is not set, an array with the key “errors” is returned.
	 *
	 * @since 3.0.5
	 * @param array $post_data The data to be sanitized and validated.
	 * @param array $required_fields Fields that must exist on $post_data so the validation doesn't fail.
	 * @return array
	 */
	public static function validate_and_sanitize_fields( $post_data, $required_fields = array() ) {
		//for serialized data or form
		if ( ! is_array( $post_data ) && is_string( $post_data ) ) {
			$post_string = $post_data;
			$post_data   = array();
			wp_parse_str( $post_string, $post_data );
		}

		$errors = array();
		foreach ( $required_fields as $key => $required_field ) {
			if ( ! isset( $post_data[ $required_field ] ) || ( empty( trim( $post_data[ $required_field ] ) ) && '0' !== $post_data[ $required_field ] ) ) {
				/* translators: ... */
				$errors[ $required_field ] = sprintf( __( 'Field %s is required.', 'hustle' ), $required_field );
				continue;
			}
		}

		if ( ! empty( $errors ) ) {
			return array( 'errors' => $errors );
		}

		$sanitized_data = array();
		foreach ( $post_data as $key => $post_datum ) {
			/**
			 * Sanitize here every request so we dont need to sanitize it again on other methods,
			 *  unless special treatment is required.
			 */
			$sanitized_data[ sanitize_text_field( $key ) ] = self::sanitize_text_input_deep( $post_datum );
		}

		return $sanitized_data;
	}

	/**
	 * Sanitizes the values of a multi-dimensional array.
	 * The keys of the sub-arrays are converted to numerical arrays.
	 * Sub-arrays are expected to have numerical indexes.
	 *
	 * @since 3.0.5
	 * @param array|string $value
	 * @return string
	 */
	public static function sanitize_text_input_deep( $value, $key = null ) {
		$value = is_array( $value ) ?
					array_map( array( 'Opt_In_Utils', 'sanitize_text_input_deep' ), $value, array_keys( $value ) ) :
					sanitize_text_field( $value );

		return $value;
	}

	/**
	 * Adds an entry to debug log
	 *
	 * By default it will check `WP_DEBUG` and HUSTLE_DEBUG to decide whether to add the log,
	 * then will check `filters`.
	 *
	 * @since 3.0.5
	 * @since 4.0 also checks HUSTLE_DEBUG
	 */
	public static function maybe_log() {

		$wp_debug_enabled = ( defined( 'WP_DEBUG' ) && WP_DEBUG );

		$enabled = ( defined( 'HUSTLE_DEBUG' ) && HUSTLE_DEBUG );

		$stored_settings = Hustle_Settings_Admin::get_general_settings();
		$debug_setting_enabled = '1' === $stored_settings['debug_enabled'];

		$enabled = ( $wp_debug_enabled && ( $debug_setting_enabled || $enabled ) );

		/**
		 * Filter to enable or disable log for Hustle
		 *
		 * By default it will check `WP_DEBUG`
		 *
		 * @since 3.0.5
		 *
		 * @param bool $enabled current enabled status
		 */
		$enabled = apply_filters( 'hustle_enable_log', $enabled );

		if ( $enabled ) {
			$args    = func_get_args();
			$message = wp_json_encode( $args );
			if ( false !== $message ) {
				error_log( '[Hustle] ' . $message ); // phpcs:ignore
			}
		}
	}


	// ====================================
	// MARKUPS
	// ====================================

	/**
	 * Image function
	 *
	 * Return image element with 2x and 1x support.
	 *
	 * @since 4.0.0
	 */
	public static function hustle_image( $image_path, $image_suffix, $image_class, $support ) {
		$image = '';
		/**
		 * White labeling based on Dash Plugin Settings
		 */
		$hide_branding = apply_filters( 'wpmudev_branding_hide_branding', false );
		if ( $hide_branding ) {
			return $image;
		}
		$image_name   = esc_html__( 'Hustle image', 'hustle' );
		if ( ( true === $support ) || ( '2x' === $support ) ) {
			if ( '' !== $image_class ) {
				$image = '<img src="' . $image_path . '.' . $image_suffix . '" srcset="' . $image_path . '.' . $image_suffix . ' 1x, ' . $image_path . '@2x' . '.' . $image_suffix . ' 2x" alt="' . $image_name . '" class="' . $image_class . '" aria-hidden="true">';
			} else {
				$image = '<img src="' . $image_path . '.' . $image_suffix . '" srcset="' . $image_path . '.' . $image_suffix . ' 1x, ' . $image_path . '@2x' . '.' . $image_suffix . ' 2x" alt="' . $image_name . '" aria-hidden="true">';
			}
		} else {
			if ( '' !== $image_class ) {
				$image = '<img src="' . $image_path . '.' . $image_suffix . '" alt="' . $image_name . '" class="' . $image_class . '" aria-hidden="true">';
			} else {
				$image = '<img src="' . $image_path . '.' . $image_suffix . '" alt="' . $image_name . '" aria-hidden="true">';
			}
		}
		echo $image; // phpcs:ignore
	}

	/**
	 * Color Picker
	 *
	 * Return the correct color picker markup that's compatible with Shared UI 2.0
	 *
	 * @since 4.0.0
	 * @since 4.1.1 Params $is_js_template and $value added.
	 *
	 * @param string $id "id" attribute of the input.
	 * @param string $name "name" attribute of the input.
	 * @param string $alpha "false"/"true". Enables or disables the alpha selector in the colorpicker.
	 * @param bool   $is_js_template whether this colorpicker will be filled via js templating.
	 * @param string $value Value to be used when js templating isn't used.
	 */
	public static function sui_colorpicker( $id, $name, $alpha = 'false', $is_js_template = true, $value = false ) {

		$value = ( ! $is_js_template && $value ) ? esc_attr( $value ) : '{{ ' . esc_attr( $name ) . ' }}';

		echo '<div class="sui-colorpicker-wrap">

			<div class="sui-colorpicker" aria-hidden="true">
				<div class="sui-colorpicker-value">
					<span role="button">
						<span style="background-color: ' . $value . '"></span>
					</span>
					<input type="text"
						value="' . $value . '"
						readonly="readonly" />
					<button><i class="sui-icon-close" aria-hidden="true"></i></button>
				</div>
				<button class="sui-button">' . esc_html__( 'Select', 'hustle' ) . '</button>
			</div>

			<input type="text"
				name="' . esc_attr( $name ) . '"
				value="' . $value . '"
				id="' . esc_attr( $id ) . '"
				class="sui-colorpicker-input"
				data-alpha="' . esc_attr( $alpha ) . '"
				data-attribute="' . esc_attr( $name ) . '" />

		</div>';

	}

	/**
	 * Return the image markup for retina and no retina images
	 *
	 * @since 4.0
	 *
	 * @param string $image_path
	 * @param string $class
	 * @param string $retina_image_path
	 * @return string
	 */
	public static function render_image_markup( $image_path, $retina_image_path = '', $class = '', $max_width = '', $max_height = '', $branding = true ) {
		$image = '';
		/**
		 * White labeling based on Dash Plugin Settings
		 */
		$hide_branding = false;
		if ( $branding ) {
			$hide_branding = apply_filters( 'wpmudev_branding_hide_branding', $hide_branding );
		}
		if ( $hide_branding ) {
			return $image;
		}
		$image_name = esc_html__( 'Hustle image', 'hustle' );
		$image_path = esc_url( $image_path );
		$retina_image_path = esc_url( $retina_image_path );
		$styles = '';
		if ( '' !== $max_width || '' !== $max_height ) {
			$styles .= ' style="';
			if ( '' !== $max_width ) {
				$styles .= 'max-width: ' . $max_width . ';';
				if ( '' !== $max_height ) {
					$styles .= ' ';
				}
			}
			if ( '' !== $max_height ) {
				$styles .= 'max-height: ' . $max_height . ';';
			}
			$styles .= '"';
		}
		$image .= '<img';
		$image .= ' src="' . $image_path . '"';
		if ( ! empty( $retina_image_path ) || '' !== $retina_image_path ) {
			$image .= ' srcset="' . esc_url( $image_path ) . ' 1x, ' . esc_url( $retina_image_path ) . ' 2x"';
		}
		$image .= ' alt="' . $image_name . '"';
		if ( ! empty( $class ) || '' !== $class ) {
			$image .= ' class="' . esc_attr( $class ) . '"';
		}
		$image .= $styles;
		$image .= ' aria-hidden="true"';
		$image .= '/>';
		return $image;
	}


	// ====================================
	// MISC?
	// ====================================

	/**
	 * Returns list of optin providers based on their declared classes that implement Opt_In_Provider_Interface
	 *
	 * @return array
	 */
	public static function get_post_types() {
		if ( empty( self::$post_types ) ) {
			/**
			 * Add all custom post types
			 */
			$post_types = array();
			$cpts = get_post_types( array(
				'public'   => true,
				'_builtin' => false,
			), 'objects' );
			foreach ( $cpts as $cpt ) {

				// skip ms_invoice
				if ( 'ms_invoice' === $cpt->name ) {
					continue;
				}

				$cpt_array['name'] = $cpt->name;
				$cpt_array['label'] = $cpt->label;
				$cpt_array['data'] = self::get_select2_data( $cpt->name );

				$post_types[ $cpt->name ] = $cpt_array;
			}
			self::$post_types = $post_types;
		}
		return self::$post_types;
	}


	/**
	 * Get usable object for select2
	 *
	 * @param $post_type post type
	 * @return array
	 */
	public static function get_select2_data( $post_type, $include_ids = null ) {
		$data = [];
		$args = [
			'numberposts' => -1,
			'post_type' => $post_type,
			'post_status' => 'publish',
			'order' => 'ASC',
		];

		if ( ! empty( $include_ids ) ) {
			$args['post__in'] = $include_ids;
		}

		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$data[] = (object)[
				'id' => $post->ID,
				'text' => $post->post_title,
			];
		}

		return $data;
	}


	/**
	 * Return time periods (AM,PM)
	 *
	 * @since 4.0
	 * @return array
	*/
	public static function get_time_periods() {
		$periods = array(
			'am' => __( 'AM', 'hustle' ),
			'pm' => __( 'PM', 'hustle' ),
			);

		return $periods;
	}


	/**
	 * Return date formats
	 *
	 * @since 4.0
	 * @return array
	*/
	public static function get_date_formats() {
		$formats = array(
			'yy/mm/dd' => __( '2012/07/31', 'hustle' ),
			'mm/dd/yy' => __( '07/31/2012', 'hustle' ),
			'dd/mm/yy' => __( '31/07/2012', 'hustle' ),
			'yy, MM d' => __( '2012, July 31', 'hustle' ),
			'd MM, yy' => __( '31 July, 2012', 'hustle' ),
			'MM d, yy' => __( 'July 31, 2012', 'hustle' ),
			'dd-mm-yy' => __( '31-07-2012', 'hustle' ),
			'mm-dd-yy' => __( '07-31-2012', 'hustle' ),
			'yy-mm-dd' => __( '2012-07-31', 'hustle' ),
			'dd.mm.yy' => __( '31.07.2012', 'hustle' ),
			'mm.dd.yy' => __( '07.31.2012', 'hustle' ),
			'yy.mm.dd' => __( '2012.07.31', 'hustle' ),
			);

		$formats = apply_filters( 'hustle_date_formats', $formats );

		return $formats;
	}

	/**
	 * Get the months as translatable strings.
	 *
	 * @since 4.0.4
	 * @param string $version full|short
	 * @return array
	 */
	public static function get_months( $version = 'full' ) {

		if ( 'full' === $version ) {
			$months = [
				__( 'January', 'hustle' ),
				__( 'February', 'hustle' ),
				__( 'March', 'hustle' ),
				__( 'April', 'hustle' ),
				__( 'May', 'hustle' ),
				__( 'June', 'hustle' ),
				__( 'July', 'hustle' ),
				__( 'August', 'hustle' ),
				__( 'September', 'hustle' ),
				__( 'October', 'hustle' ),
				__( 'November', 'hustle' ),
				__( 'December', 'hustle' ),
			];

		} else {
			$months = [
				__( 'Jan', 'hustle' ),
				__( 'Feb', 'hustle' ),
				__( 'Mar', 'hustle' ),
				__( 'Apr', 'hustle' ),
				__( 'May', 'hustle' ),
				__( 'Jun', 'hustle' ),
				__( 'Jul', 'hustle' ),
				__( 'Aug', 'hustle' ),
				__( 'Sep', 'hustle' ),
				__( 'Oct', 'hustle' ),
				__( 'Nov', 'hustle' ),
				__( 'Dec', 'hustle' ),
			];
		}

		return apply_filters( 'hustle_get_months', $months, $version );
	}

	/**
	 * Get the week days as translatable strings.
	 *
	 * @since 4.0.4
	 * @param string $version full|short|min
	 * @return array
	 */
	public static function get_week_days( $version = 'full' ) {

		if ( 'full' === $version ) {
			$days = [
				__( 'Sunday', 'hustle' ),
				__( 'Monday', 'hustle' ),
				__( 'Tuesday', 'hustle' ),
				__( 'Wednesday', 'hustle' ),
				__( 'Thursday', 'hustle' ),
				__( 'Friday', 'hustle' ),
				__( 'Saturday', 'hustle' ),
			];

		} elseif ( 'short' === $version ) {
			$days = [
				__( 'Sun', 'hustle' ),
				__( 'Mon', 'hustle' ),
				__( 'Tue', 'hustle' ),
				__( 'Wed', 'hustle' ),
				__( 'Thu', 'hustle' ),
				__( 'Fri', 'hustle' ),
				__( 'Sat', 'hustle' ),
			];

		} else {
			$days = [
				__( 'Su', 'hustle' ),
				__( 'Mo', 'hustle' ),
				__( 'Tu', 'hustle' ),
				__( 'We', 'hustle' ),
				__( 'Th', 'hustle' ),
				__( 'Fr', 'hustle' ),
				__( 'Sa', 'hustle' ),
			];
		}

		return apply_filters( 'hustle_get_months', $days, $version );
	}

	/**
	 * Convert some unit of time to microseconds.
	 *
	 * @since 4.0
	 *
	 * @param int $value
	 * @param string $unit
	 * @return int
	 */
	public static function to_microseconds( $value, $unit ) {

		if ( 'seconds' === $unit ) {
			return intval( $value, 10 ) * 1000;

		} else if ( 'minutes' === $unit ) {
			return intval( $value, 10 ) * 60 * 1000;

		} else {
			return intval( $value, 10 ) * 60 * 60 * 1000;
		}
	}

	/**
	 * Get analytics ranges for dashboard widget
	 *
	 * @return array
	 */
	public static function get_analytic_ranges() {
		$ranges = [
			1 => __( 'Last 24 hrs', 'hustle' ),
			7 => __( 'Last 7 days', 'hustle' ),
			30 => __( 'Last 30 days', 'hustle' ),
		//	90 => __( 'Last 90 days', 'hustle' ),
		];

		return $ranges;
	}


	/**
	 * Get social patform names
	 *
	 * @return array
	 */
	public static function get_social_platform_names() {
		$social_platform_names = array(
			'facebook' => esc_html__( 'Facebook', 'hustle' ),
			'twitter' => esc_html__( 'Twitter', 'hustle' ),
			'pinterest' => esc_html__( 'Pinterest', 'hustle' ),
			'reddit' => esc_html__( 'Reddit', 'hustle' ),
			'linkedin' => esc_html__( 'LinkedIn', 'hustle' ),
			'vkontakte' => esc_html__( 'Vkontakte', 'hustle' ),
			'fivehundredpx' => esc_html__( '500px', 'hustle' ),
			'houzz' => esc_html__( 'Houzz', 'hustle' ),
			'instagram' => esc_html__( 'Instagram', 'hustle' ),
			'twitch' => esc_html__( 'Twitch', 'hustle' ),
			'youtube' => esc_html__( 'YouTube', 'hustle' ),
			'telegram' => esc_html__( 'Telegram', 'hustle' ),
			'whatsapp' => esc_html__( 'WhatsApp', 'hustle' ),
			'email'    	  => esc_html__( 'Email', 'hustle' ),
		);

		/**
		 * Social networks list
		 *
		 * @since 4.0.4
		 *
		 * @param array $social_platform_names {slug} => {name}
		 */
		return apply_filters( 'hustle_social_platform_names', $social_platform_names );
	}

	/**
	 * Return reCAPTCHA languages
	 *
	 * @since 4.0
	 * @return array
	*/
	public static function get_captcha_languages() {
		return apply_filters(
			'hustle_captcha_languages',
			array(
				'ar' => esc_html__( 'Arabic', 'hustle' ),
				'af' => esc_html__( 'Afrikaans', 'hustle' ),
				'am' => esc_html__( 'Amharic', 'hustle' ),
				'hy' => esc_html__( 'Armenian', 'hustle' ),
				'az' => esc_html__( 'Azerbaijani', 'hustle' ),
				'eu' => esc_html__( 'Basque', 'hustle' ),
				'bn' => esc_html__( 'Bengali', 'hustle' ),
				'bg' => esc_html__( 'Bulgarian', 'hustle' ),
				'ca' => esc_html__( 'Catalan', 'hustle' ),
				'zh-HK' => esc_html__( 'Chinese (Hong Kong)', 'hustle' ),
				'zh-CN' => esc_html__( 'Chinese (Simplified)', 'hustle' ),
				'zh-TW' => esc_html__( 'Chinese (Traditional)', 'hustle' ),
				'hr' => esc_html__( 'Croatian', 'hustle' ),
				'cs' => esc_html__( 'Czech', 'hustle' ),
				'da' => esc_html__( 'Danish', 'hustle' ),
				'nl' => esc_html__( 'Dutch', 'hustle' ),
				'en-GB' => esc_html__( 'English (UK)', 'hustle' ),
				'en' => esc_html__( 'English (US)', 'hustle' ),
				'et' => esc_html__( 'Estonian', 'hustle' ),
				'fil' => esc_html__( 'Filipino', 'hustle' ),
				'fi' => esc_html__( 'Finnish', 'hustle' ),
				'fr' => esc_html__( 'French', 'hustle' ),
				'fr-CA' => esc_html__( 'French (Canadian)', 'hustle' ),
				'gl' => esc_html__( 'Galician', 'hustle' ),
				'ka' => esc_html__( 'Georgian', 'hustle' ),
				'de' => esc_html__( 'German', 'hustle' ),
				'de-AT' => esc_html__( 'German (Austria)', 'hustle' ),
				'de-CH' => esc_html__( 'German (Switzerland)', 'hustle' ),
				'el' => esc_html__( 'Greek', 'hustle' ),
				'gu' => esc_html__( 'Gujarati', 'hustle' ),
				'iw' => esc_html__( 'Hebrew', 'hustle' ),
				'hi' => esc_html__( 'Hindi', 'hustle' ),
				'hu' => esc_html__( 'Hungarain', 'hustle' ),
				'is' => esc_html__( 'Icelandic', 'hustle' ),
				'id' => esc_html__( 'Indonesian', 'hustle' ),
				'it' => esc_html__( 'Italian', 'hustle' ),
				'ja' => esc_html__( 'Japanese', 'hustle' ),
				'kn' => esc_html__( 'Kannada', 'hustle' ),
				'ko' => esc_html__( 'Korean', 'hustle' ),
				'lo' => esc_html__( 'Laothian', 'hustle' ),
				'lv' => esc_html__( 'Latvian', 'hustle' ),
				'lt' => esc_html__( 'Lithuanian', 'hustle' ),
				'ms' => esc_html__( 'Malay', 'hustle' ),
				'ml' => esc_html__( 'Malayalam', 'hustle' ),
				'mr' => esc_html__( 'Marathi', 'hustle' ),
				'mn' => esc_html__( 'Mongolian', 'hustle' ),
				'no' => esc_html__( 'Norwegian', 'hustle' ),
				'fa' => esc_html__( 'Persian', 'hustle' ),
				'pl' => esc_html__( 'Polish', 'hustle' ),
				'pt' => esc_html__( 'Portuguese', 'hustle' ),
				'pt-BR' => esc_html__( 'Portuguese (Brazil)', 'hustle' ),
				'pt-PT' => esc_html__( 'Portuguese (Portugal)', 'hustle' ),
				'ro' => esc_html__( 'Romanian', 'hustle' ),
				'ru' => esc_html__( 'Russian', 'hustle' ),
				'sr' => esc_html__( 'Serbian', 'hustle' ),
				'si' => esc_html__( 'Sinhalese', 'hustle' ),
				'sk' => esc_html__( 'Slovak', 'hustle' ),
				'sl' => esc_html__( 'Slovenian', 'hustle' ),
				'es' => esc_html__( 'Spanish', 'hustle' ),
				'es-419' => esc_html__( 'Spanish (Latin America)', 'hustle' ),
				'sw' => esc_html__( 'Swahili', 'hustle' ),
				'sv' => esc_html__( 'Swedish', 'hustle' ),
				'ta' => esc_html__( 'Tamil', 'hustle' ),
				'te' => esc_html__( 'Telugu', 'hustle' ),
				'th' => esc_html__( 'Thai', 'hustle' ),
				'tr' => esc_html__( 'Turkish', 'hustle' ),
				'uk' => esc_html__( 'Ukrainian', 'hustle' ),
				'ur' => esc_html__( 'Urdu', 'hustle' ),
				'vi' => esc_html__( 'Vietnamese', 'hustle' ),
				'zu' => esc_html__( 'Zulu', 'hustle' ),
			)
		);
	}

	/**
	 * Gets post property
	 *
	 * @since 4.0.4
	 * @param string $property Requested post property.
	 * @param string $default Fallback value.
	 * @return string
	 */
	public static function get_post_data( $property, $default = '' ) {
		global $post;

		if ( ! $post ) {
			// fallback on wp_ajax, `global $post` not available.
			$wp_referer = wp_get_referer();
			if ( $wp_referer ) {
				$post_id = ! function_exists( 'wpcom_vip_url_to_postid' ) ? url_to_postid( $wp_referer ) : wpcom_vip_url_to_postid( $wp_referer );
				if ( $post_id ) {
					$post_object = get_post( $post_id );
					// make sure it's wp_post.
					if ( $post_object instanceof WP_Post ) {
						// set global $post as $post_object retrieved from `get_post` for next usage.
						$post = $post_object; // phpcs:ignore
					}
				}
			}
		}

		$post_data = (array) $post;
		if ( isset( $post_data[ $property ] ) ) {
			return $post_data[ $property ];
		} else {
			return $default;
		}
	}

	/**
	 * Return local timestamp
	 *
	 * @since 4.0.4
	 * @param int $timestamp
	 * @return mixed
	 */
	public static function get_local_timestamp( $timestamp = null ) {
		// If no timestamp, get it current.
		if ( is_null( $timestamp ) ) {
			$timestamp = time();
		}

		return $timestamp + ( get_option( 'gmt_offset' ) * 3600 );
	}

	// ====================================
	// MODULES HELPERS
	// ====================================


	/**
	 * Get Time of latest tracked conversion based on $module_id
	 *
	 * @since 4.0
	 *
	 * @param $module_id
	 * @param string $subtype
	 * @param string $cta_or_optin Optional. cta_conversion|optin_conversion|all_conversion CTA or Opt-in conversion
	 * @return string
	 */
	public static function get_latest_conversion_time_by_module_id( $module_id, $subtype = '', $cta_or_optin = 'all_conversion' ) {
		$tracking_model = Hustle_Tracking_Model::get_instance();
		$latest_entry = $tracking_model->get_latest_conversion_date_by_module_id( $module_id, $subtype, $cta_or_optin );
		if ( $latest_entry ) {
			$entry_date = date_i18n( 'j M Y @ H:i A', strtotime( $latest_entry ) );
			return $entry_date;
		} else {
			return esc_html__( 'Never', 'hustle' );
		}
	}

	/**
	 * Get the time of the latest tracked conversion based on $entry_type
	 * [popup,slide-in,embedded]
	 *
	 * @since 4.0
	 *
	 * @param $entry_type
	 * @return string
	 */
	public static function get_latest_conversion_time( $entry_type ) {
		$tracking_model = Hustle_Tracking_Model::get_instance();
		$date = $tracking_model->get_latest_conversion_date( $entry_type );

		if ( $date ) {
			$last_entry_time = mysql2date( 'U', $date );
			$time_diff       = human_time_diff( current_time( 'timestamp' ), $last_entry_time );
			$last_entry_time = sprintf( __( '%s ago', 'hustle' ), $time_diff );

			return $last_entry_time;
		} else {
			return __( 'Never', 'hustle' );
		}
	}

	/**
	 * Get Time of latest entry created based on $module_id
	 *
	 * @since 4.0
	 *
	 * @param $module_id
	 * @return string
	 */
	public static function get_latest_entry_time_by_module_id( $module_id ) {
		$latest_entry = Hustle_Entry_Model::get_latest_entry_by_module_id( $module_id );
		if ( $latest_entry instanceof Hustle_Entry_Model ) {
			return $latest_entry->time_created;
		} else {
			return esc_html__( 'Never', 'hustle' );
		}
	}

	/**
	 * Get Time of latest entry created based on $entry_type
	 * [popup,slide-in,embedded]
	 *
	 * @since 4.0
	 *
	 * @param $entry_type
	 * @return string
	 */
	public static function get_latest_entry_time( $entry_type ) {
		$latest_entry = Hustle_Entry_Model::get_latest_entry( $entry_type );
		if ( $latest_entry instanceof Hustle_Entry_Model ) {
			$last_entry_time = mysql2date( 'U', $latest_entry->date_created_sql );
			$time_diff       = human_time_diff( current_time( 'timestamp' ), $last_entry_time );
			$last_entry_time = sprintf( __( '%s ago', 'hustle' ), $time_diff );

			return $last_entry_time;
		} else {
			return __( 'Never', 'hustle' );
		}
	}

	/**
	 * Get current post id
	 *
	 * @since 4.0
	 *
	 * @return int|string
	 */
	public static function get_post_id() {
		return get_post() ? get_the_ID() : '0';
	}

	/**
	 * get formated date
	 *
	 * @since 4.0.0
	 *
	 * return string $date Current date, formated bu i18n.
	 */
	public static function get_current_date() {
		$date = date_i18n( 'Y-m-d H:i:s' );
		return $date;
	}

	/**
	 * Gets the a current user's property.
	 *
	 * @since 4.0.4
	 * @param string $property The user's property to be retrieved.
	 * @return string
	 */
	public static function get_user_data( $property ) {
		$current_user = wp_get_current_user();

		if ( $current_user && $current_user->exists() ) {
			return $current_user->get( $property );
		}
		return '';
	}

	/**
	 * Replace a key in an array without changing its order.
	 *
	 * @since 4.0
	 *
	 * @param string $old_key
	 * @param string $new_key
	 * @param array $array
	 * @return array
	 */
	public static function replace_array_key( $old_key, $new_key, $array ) {

		// Replace the name without changing the array's order.
		$keys_array = array_keys( $array );
		$index = array_search( $old_key, $keys_array, true );

		if ( false === $index ) {
			return $array;
		}

		$keys_array[ $index ] = $new_key;

		$new_array = array_combine( $keys_array, array_values( $array ) );

		return $new_array;
	}

	/*
	 * Get the display name of a module type.
	 *
	 * @since 4.0
	 *
	 * @param string $module_type
	 * @param boolean $plural
	 * @param boolean $capitalized
	 * @return string
	 */
	public static function get_module_type_display_name( $module_type, $plural = false, $capitalized = false ) {

		$display_name = '';

		if ( Hustle_Module_Model::POPUP_MODULE === $module_type ) {
			if ( ! $plural ) {
				$display_name = __( 'pop-up', 'hustle' );
			} else {
				$display_name = __( 'pop-ups', 'hustle' );
			}

		} elseif ( Hustle_Module_Model::SLIDEIN_MODULE === $module_type ) {
			if ( ! $plural ) {
				$display_name = __( 'slide-in', 'hustle' );
			} else {
				$display_name = __( 'slide-ins', 'hustle' );
			}

		} elseif ( Hustle_Module_Model::EMBEDDED_MODULE === $module_type ) {
			if ( ! $plural ) {
				$display_name = __( 'embed', 'hustle' );
			} else {
				$display_name = __( 'embeds', 'hustle' );
			}

		} elseif ( Hustle_Module_Model::SOCIAL_SHARING_MODULE === $module_type ) {
			if ( ! $plural ) {
				$display_name = __( 'social sharing', 'hustle' );
			} else {
				$display_name = __( 'social shares', 'hustle' );
			}

		}

		if ( $capitalized ) {
			$display_name = ucwords( $display_name );
		}

		return $display_name;
	}

	/**
	 * Get page templates
	 *
	 * @since 4.0.3
	 */
	public static function hustle_get_page_templates() {
		$templates = get_page_templates();
		$page_templates = array();
		foreach ( $templates as $template_name => $template_filename ) {
			$page_templates[$template_filename] = $template_name;
		}
		return $page_templates;
	}

	/**
	 * Check if WooCommerce is active or not
	 *
	 * @return bool
	 */
	public static function is_woocommerce_active() {
		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Get the global placeholders for display.
	 * The array's key has the placeholder value, that's what's inserted between
	 * brackets and then replaced by self::replace_global_placeholders().
	 * The array's value has the display name for the placeholder.
	 *
	 * @since 4.0.3
	 * @see Opt_In_Utils::replace_global_placeholders()
	 * @return array
	 */
	public static function get_global_placeholders() {

		$placeholders = [
			'site_url'   => __( 'Site URL', 'hustle' ),
			'site_name'  => __( 'Site name', 'hustle' ),
			'post_url'   => __( 'Post/page URL', 'hustle' ),
			'post_title' => __( 'Post/page title', 'hustle' ),
		];

		/**
		 * Filter the available global placeholders.
		 * These are used in some text fields, to be replaced by
		 * self::replace_global_placeholders().
		 *
		 * @since 4.0.3
		 * @see Opt_In_Utils::replace_global_placeholders()
		 * @return array
		 */
		return apply_filters( 'hustle_get_global_placeholders', $placeholders );
	}

	/**
	 * Replace the global placeholders from a string.
	 * These are added to some text fields by the admin.
	 * The available ones are returned by self::get_global_placeholders().
	 *
	 * @since 4.0.3
	 * @see Opt_In_Utils::replace_global_placeholders()
	 * @param string $string String with placeholders to be replaced.
	 * @return string
	 */
	public static function replace_global_placeholders( $string ) {

		preg_match_all( '/\{[^}]*\}/', $string, $matches );

		if ( ! empty( $matches[0] ) && is_array( $matches[0] ) ) {

			$defined_placeholders = [
				'{site_url}'   => site_url(),
				'{site_name}'  => get_bloginfo( 'name' ),
				'{post_url}'   => get_permalink(),
				'{post_title}' => esc_html( get_the_title() ),
			];

			/**
			 * Filter the placeholders and their values.
			 * The keys of the array belong to the placeholder to be replaced.
			 * The values of the array belong to the value to use as replacement.
			 * Eg: [ '{post_url}' => get_permalink() ]
			 *
			 * @since 4.0.3
			 * @return array
			 */
			$defined_placeholders = apply_filters( 'hustle_global_placeholders_to_replace', $defined_placeholders );

			foreach ( $matches[0] as $placeholder ) {

				if ( key_exists( $placeholder, $defined_placeholders ) ) {
					$replacement = $defined_placeholders[ $placeholder ];

					if ( $replacement !== $placeholder ) {
						// Replace if we found something.
						$string = str_replace( $placeholder, $replacement, $string );
					}
				}
			}
		}

		return $string;
	}
}
