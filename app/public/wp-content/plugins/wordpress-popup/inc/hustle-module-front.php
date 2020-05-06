<?php
class Hustle_Module_Front {

	private $_hustle;

	private $_modules = array();

	/**
	 * Contains the queued modules types as keys, 1 as the value.
	 * Used to queue the required styles only.
	 * @since 4.0.1
	 * @var array
	 */
	private $_module_types_to_display = array();
	private $_non_inline_modules = array();
	private $_inline_modules = array();

	/**
	 * Array with data about the modules.
	 * This is used to conditionally add scripts.
	 *
	 * @since 4.0.4
	 * @var array
	 */
	private $modules_data_for_scripts = array();

	private static $the_content_filter_priority = 20;

	private $_styles;

	const AFTERCONTENT_CSS_CLASS = 'hustle_module_after_content_wrap';
	const WIDGET_CSS_CLASS = 'hustle_module_widget_wrap';
	const SHORTCODE_CSS_CLASS = 'hustle_module_shortcode_wrap';
	const SHORTCODE_TRIGGER_CSS_CLASS = 'hustle_module_shortcode_trigger';
	const SSHARE_WIDGET_CSS_CLASS = 'hustle_sshare_module_widget_wrap';
	const SSHARE_SHORTCODE_CSS_CLASS = 'hustle_sshare_module_shortcode_wrap';

	const SHORTCODE = 'wd_hustle';

	public function __construct( Opt_In $hustle ) {

		$this->_hustle = $hustle;

		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_shortcode( self::SHORTCODE, array( $this, 'shortcode' ) );

		// Legacy custom content support
		add_shortcode(
			'wd_hustle_cc',
			array( $this, 'shortcode' )
		);

		// Legacy social sharing support
		add_shortcode(
			'wd_hustle_ss',
			array( $this, 'shortcode' )
		);

		// Unsubscribe shortcode
		add_shortcode(
			'wd_hustle_unsubscribe',
			array( $this, 'unsubscribe_shortcode' )
		);

		if ( is_admin() ) {
			return;
		}

		add_action(
			'wp_enqueue_scripts',
			array( $this, 'register_scripts' )
		);

		// Enqueue it in the footer to overrider all the css that comes with the popup
		add_action(
			'wp_footer',
			array( $this, 'register_styles' )
		);

		add_action(
			'template_redirect',
			array( $this, 'create_modules' ),
			0
		);

		add_action(
			'wp_footer',
			array( $this, 'render_non_inline_modules' ),
			-1
		);

		add_filter(
			'the_content',
			array( $this, 'show_after_page_post_content' ),
			self::$the_content_filter_priority
		);

		add_filter( 'get_the_excerpt', array( $this, 'remove_the_content_filter' ), 9 );
		add_filter( 'wp_trim_excerpt', array( $this, 'restore_the_content_filter' ) );

		// NextGEN Gallery compat
		add_filter(
			'run_ngg_resource_manager',
			array( $this, 'nextgen_compat' )
		);
	}

	/**
	 * Don't apply the_content filter for excerpts
	 */
	public function remove_the_content_filter( $post_excerpt ) {
		remove_filter( 'the_content', array( $this, 'show_after_page_post_content' ), self::$the_content_filter_priority );

		return $post_excerpt;
	}

	public function restore_the_content_filter( $text ) {
		add_filter( 'the_content', array( $this, 'show_after_page_post_content' ), self::$the_content_filter_priority );

		return $text;
	}

	public function register_widget() {
		register_widget( 'Hustle_Module_Widget' );
		register_widget( 'Hustle_Module_Widget_Legacy' );
	}

	public function register_scripts() {

		$modules_deps = $this->modules_data_for_scripts;

		$is_on_upfront_builder = class_exists( 'UpfrontThemeExporter' ) && function_exists( 'upfront_exporter_is_running' ) && upfront_exporter_is_running();
		if ( ! $is_on_upfront_builder ) {
			if ( is_customize_preview() || ! $this->has_modules() || isset( $_REQUEST['fl_builder'] ) ) { // CSRF: ok.
				/**
				 * Check for shortcode wd_hustle_unsubscribe
				 */
				$is_singular = is_singular();
				if ( ! $is_singular ) {
					return;
				}
				global $post;
				if ( ! preg_match( '/wd_hustle_unsubscribe/', $post->post_content ) ) {
					return;
				}
			}
		}

		/**
		 * Register popup requirements
		 */

		//Register popup requirements
		$url_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'debug' : 'min';
		wp_register_script(
			'hustle_front',
			Opt_In::$plugin_url . 'assets/js/front.' . $url_suffix . '.js',
			array( 'jquery', 'underscore' ),
			Opt_In::VERSION,
			true
		);

		wp_register_script(
			'hustle_front_fitie',
			Opt_In::$plugin_url . 'assets/js/vendor/fitie/fitie.js',
			array(),
			Opt_In::VERSION,
			false
		);
		$modules = apply_filters( 'hustle_front_modules', $this->_modules );
		wp_localize_script( 'hustle_front', 'Modules', $modules );

		//force set archive page slug
		global $wp;
		$slug = is_home() && is_front_page() ? 'hustle-front-blog-page' : sanitize_title( $wp->request );

		$vars = apply_filters('hustle_front_vars', array(
			'is_admin'              => is_admin(),
			'native_share_enpoints' => Hustle_Sshare_Model::get_sharing_endpoints( false ),
			'ajaxurl'               => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
			'page_id'               => get_queried_object_id() , // Used in many places to decide whether to show the module and cookies.
			'page_slug'             => $slug, // Used in many places to decide whether to show the module and cookies on archive pages.
			'is_upfront'            => class_exists( 'Upfront' ) && isset( $_GET['editmode'] ) && 'true' === $_GET['editmode'], // Used.
			'script_delay'          => apply_filters( 'hustle_lazy_load_script_delay', 3000 ), //to lazyload script for later on added elements
		) );

		// Datepicker. Add translated strings only if some module has a datepicker.
		if ( ! empty( $modules_deps['datepicker'] ) ) {
			$vars['days_and_months'] = [
				'days_full'    => Opt_In_Utils::get_week_days(),
				'days_short'   => Opt_In_Utils::get_week_days( 'short' ),
				'days_min'     => Opt_In_Utils::get_week_days( 'min' ),
				'months_full'  => Opt_In_Utils::get_months(),
				'months_short' => Opt_In_Utils::get_months( 'short' ),
			];
		}
		wp_localize_script( 'hustle_front', 'incOpt', $vars );

		do_action( 'hustle_register_scripts' );

		// Queue adblocker if a module requires it.
		if ( ! empty( $modules_deps['adblocker'] ) ) {
			wp_enqueue_script(
				'hustle_front_ads',
				Opt_In::$plugin_url . 'assets/js/ads.js',
				array(),
				Opt_In::VERSION,
				true
			);
		}

		// Queue recaptchas if required. Only added if the keys are set.
		if ( ! empty( $modules_deps['recaptcha'] ) ) {
			$this->add_recaptcha_script( $modules_deps['recaptcha']['language'] );
		}

		self::add_hui_scripts();
		wp_enqueue_script( 'hustle_front' );
		wp_enqueue_script( 'hustle_front_fitie' );

		add_filter(
			'script_loader_tag',
			array( 'Hustle_Module_Front', 'handle_specific_script' ),
			10,
			2
		);

		add_filter(
			'style_loader_tag',
			array( 'Hustle_Module_Front', 'handle_specific_style' ),
			10,
			2
		);
	}

	/**
	 * Add Hustle UI scripts.
	 * Used for displaying and previewing modules.
	 *
	 * @since 4.0
	 */
	public static function add_hui_scripts() {

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		// Register Hustle UI functions
		wp_register_script(
			'hui_scripts',
			Opt_In::$plugin_url . 'assets/hustle-ui/js/hustle-ui.min.js',
			array( 'jquery' ),
			Opt_In::VERSION,
			true
		);

		wp_enqueue_script( 'hui_scripts' );
	}

	/**
	 * Enqueue the recaptcha script if recaptcha is globally configured.
	 * @since 4.0
	 * @since 4.0.3 param $recaptcha_versions and $is_preview added
	 *
	 * @param string $language reCAPTCHA language.
	 * @param bool $is_preview if it's preview.
	 */
	public static function add_recaptcha_script( $language = '', $is_preview = false, $is_return = false ) {

		$recaptcha_settings = Hustle_Settings_Admin::get_recaptcha_settings();

		if ( empty( $language ) || 'automatic' === $language ) {
			$language = ! empty( $recaptcha_settings['language'] ) && 'automatic' !== $recaptcha_settings['language']
				? $recaptcha_settings['language'] : determine_locale();
		}
		$script_url = 'https://www.google.com/recaptcha/api.js?render=explicit&hl=' . $language;

		if ( ! $is_return ) {
			wp_enqueue_script( 'recaptcha', $script_url, [], false, true );

		} elseif ( $is_preview ) {
			return $script_url;
		}
	}

	/**
	 * Handling specific scripts for each scenario
	 *
	 */
	public static function handle_specific_script( $tag, $handle ) {
		if ( 'hustle_front_fitie' === $handle ) {
			if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
			} else {
				return $tag;
			}

			$is_ie = (
				// IE 10 or older
				false !== stripos( $user_agent, 'MSIE' ) ||
				// IE 11
				false !== stripos( $user_agent, 'Trident' ) ||
				// Edge (IE 12+)
				false !== stripos( $user_agent, 'Edge' )
			);
			if ( ! $is_ie ) {
				$tag = '';
			}
		}
		return $tag;
	}

	/**
	 * Handling specific style for each scenario
	 *
	 */
	public static function handle_specific_style( $tag, $handle ) {
		if ( 'hustle_front_ie' === $handle ) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$is_ie = (
				// IE 10 or older
				false !== stripos( $user_agent, 'MSIE' ) ||
				// IE 11
				false !== stripos( $user_agent, 'Trident' ) ||
				// Edge (IE 12+)
				false !== stripos( $user_agent, 'Edge' )
			);
			if ( ! $is_ie ) {
				$tag = '';
			}
		}
		return $tag;
	}

	/**
	 * Registeres front styles and fonts
	 */
	public function register_styles() {
		$is_on_upfront_builder = class_exists( 'UpfrontThemeExporter' ) && function_exists( 'upfront_exporter_is_running' ) && upfront_exporter_is_running();

		if ( ! $is_on_upfront_builder ) {
			if ( ! $this->has_modules() || isset( $_REQUEST['fl_builder'] ) ) { // CSRF ok.
				return;
			}
		}

		$module_types_to_display = array_keys( $this->_module_types_to_display );

		self::print_front_styles( $module_types_to_display );
		self::print_front_fonts( $this->_hustle );
	}

	/**
	 * Register and enqueue the required styles according to the given module's types.
	 * The accepted module's types are:
	 * popup, slidein, embedded, social_sharing, optin, informational, floating (ssharing), inline (ssharing).
	 *
	 * @since 4.0
	 * @since 4.0.1 enequeues only the given module's types.
	 *
	 * @param array $module_types_to_display Array with the module's type to be displayed.
	 */
	public static function print_front_styles( $module_types_to_display = array() ) {

		wp_register_style(
			'hustle_icons',
			Opt_In::$plugin_url . 'assets/hustle-ui/css/hustle-icons.min.css',
			array(),
			Opt_In::VERSION
		);
		wp_enqueue_style( 'hustle_icons' );

		wp_register_style(
			'hustle_global',
			Opt_In::$plugin_url . 'assets/hustle-ui/css/hustle-global.min.css',
			array(),
			Opt_In::VERSION
		);
		wp_enqueue_style( 'hustle_global' );

		// Informational mode.
		if ( ! $module_types_to_display || in_array( Hustle_Module_Model::INFORMATIONAL_MODE, $module_types_to_display, true ) ) {

			wp_register_style(
				'hustle_info',
				Opt_In::$plugin_url . 'assets/hustle-ui/css/hustle-info.min.css',
				array(),
				Opt_In::VERSION
			);
			wp_enqueue_style( 'hustle_info' );
		}

		// Optin mode.
		if ( ! $module_types_to_display || in_array( Hustle_Module_Model::OPTIN_MODE, $module_types_to_display, true ) ) {

			wp_register_style(
				'hustle_optin',
				Opt_In::$plugin_url . 'assets/hustle-ui/css/hustle-optin.min.css',
				array(),
				Opt_In::VERSION
			);
			wp_enqueue_style( 'hustle_optin' );
		}

		// Popup type.
		if ( ! $module_types_to_display || in_array( Hustle_Module_Model::POPUP_MODULE, $module_types_to_display, true ) ) {

			wp_register_style(
				'hustle_popup',
				Opt_In::$plugin_url . 'assets/hustle-ui/css/hustle-popup.min.css',
				array(),
				Opt_In::VERSION
			);
			wp_enqueue_style( 'hustle_popup' );
		}


		// Slidein type.
		if ( ! $module_types_to_display || in_array( Hustle_Module_Model::SLIDEIN_MODULE, $module_types_to_display, true ) ) {

			wp_register_style(
				'hustle_slidein',
				Opt_In::$plugin_url . 'assets/hustle-ui/css/hustle-slidein.min.css',
				array(),
				Opt_In::VERSION
			);
			wp_enqueue_style( 'hustle_slidein' );
		}

		// SSharing type.
		if ( ! $module_types_to_display || in_array( Hustle_Module_Model::SOCIAL_SHARING_MODULE, $module_types_to_display, true ) ) {

			wp_register_style(
				'hustle_social',
				Opt_In::$plugin_url . 'assets/hustle-ui/css/hustle-social.min.css',
				array(),
				Opt_In::VERSION
			);
			wp_enqueue_style( 'hustle_social' );

			// Inline display.
			if ( ! $module_types_to_display || in_array( Hustle_SShare_Model::INLINE_MODULE, $module_types_to_display, true ) ) {

				wp_register_style(
					'hustle_inline',
					Opt_In::$plugin_url . 'assets/hustle-ui/css/hustle-inline.min.css',
					array(),
					Opt_In::VERSION
				);
				wp_enqueue_style( 'hustle_inline' );
			}

			// Floating display.
			if ( ! $module_types_to_display || in_array( Hustle_SShare_Model::FLOAT_MODULE, $module_types_to_display, true ) ) {

				wp_register_style(
					'hustle_float',
					Opt_In::$plugin_url . 'assets/hustle-ui/css/hustle-float.min.css',
					array(),
					Opt_In::VERSION
				);
				wp_enqueue_style( 'hustle_float' );
			}

		}
	}

	/**
	 * Handles Preview Enqueue for module styles
	 *
	 * @since 4.0.1
	 *
	 * @param string $url 		Assets url
	 * @param string $version 	Asset version
	 * @param string $screen  	Admin screen
	 */
	public static function print_preview_styles( $url, $version, $screen ){

		//switch case for module type
		switch ( $screen ) {
			case 'hustle_page_hustle_popup':

				//enqueue popupcss once
				if( ! wp_script_is( 'hustle_popup', 'enqueued' ) ){
					wp_register_style(
						'hustle_popup',
						$url . 'assets/hustle-ui/css/hustle-popup.min.css',
						array(),
						$version
					);

					wp_enqueue_style( 'hustle_popup' );
				}

				//check for module mode
				if( ! wp_script_is( 'hustle_optin', 'enqueued' ) ){
					// load only if optin module exists
					wp_register_style(
						'hustle_optin',
						$url . 'assets/hustle-ui/css/hustle-optin.min.css',
						array(),
						$version
					);

					wp_enqueue_style( 'hustle_optin' );


				}

				if( ! wp_script_is( 'hustle_info', 'enqueued' ) ){
					// load only if info module exists
					wp_register_style(
						'hustle_info',
						$url . 'assets/hustle-ui/css/hustle-info.min.css',
						array(),
						$version
					);

					wp_enqueue_style( 'hustle_info' );

				}

				break;

			case 'hustle_page_hustle_slidein':

				//enqueue slidein css once
				if( ! wp_script_is( 'hustle_slidein', 'enqueued' ) ){
					wp_register_style(
						'hustle_slidein',
						$url . 'assets/hustle-ui/css/hustle-slidein.min.css',
						array(),
						$version
					);

					wp_enqueue_style( 'hustle_slidein' );
				}

				//check for module mode
				if( ! wp_script_is( 'hustle_optin', 'enqueued' ) ){
					// load only if optin module exists
					wp_register_style(
						'hustle_optin',
						$url . 'assets/hustle-ui/css/hustle-optin.min.css',
						array(),
						$version
					);

					wp_enqueue_style( 'hustle_optin' );


				}

				if( ! wp_script_is( 'hustle_info', 'enqueued' ) ){
					// load only if info module exists
					wp_register_style(
						'hustle_info',
						$url . 'assets/hustle-ui/css/hustle-info.min.css',
						array(),
						$version
					);

					wp_enqueue_style( 'hustle_info' );

				}
				break;

			case 'hustle_page_hustle_embedded':

				//check for module mode
				if( ! wp_script_is( 'hustle_optin', 'enqueued' ) ){
					// load only if optin module exists
					wp_register_style(
						'hustle_optin',
						$url . 'assets/hustle-ui/css/hustle-optin.min.css',
						array(),
						$version
					);

					wp_enqueue_style( 'hustle_optin' );


				}

				if( ! wp_script_is( 'hustle_info', 'enqueued' ) ){
					// load only if info module exists
					wp_register_style(
						'hustle_info',
						$url . 'assets/hustle-ui/css/hustle-info.min.css',
						array(),
						$version
					);

					wp_enqueue_style( 'hustle_info' );

				}
				break;

			case 'hustle_page_hustle_sshare':

				//enqueue social_sharing css once
				if( ! wp_script_is( 'hustle_social', 'enqueued' ) ){

					wp_register_style(
						'hustle_social',
						$url . 'assets/hustle-ui/css/hustle-social.min.css',
						array(),
						$version
					);

					wp_enqueue_style( 'hustle_social' );
				}


				if( ! wp_script_is( 'hustle_inline', 'enqueued' ) ){
					// load only if inline module exists
					wp_register_style(
						'hustle_inline',
						$url . 'assets/hustle-ui/css/hustle-inline.min.css',
						array(),
						$version
					);
					wp_enqueue_style( 'hustle_inline' );
				}

				if( ! wp_script_is( 'hustle_float', 'enqueued' ) ){

					// load only if floating module exists
					wp_register_style(
						'hustle_float',
						$url . 'assets/hustle-ui/css/hustle-float.min.css',
						array(),
						$version
					);
					wp_enqueue_style( 'hustle_float' );

				}

				break;

			default:
				break;
		}

	}

	public static function print_front_fonts( $hustle ) {

		$load_google_fonts = apply_filters( 'hustle_load_google_fonts', true );
		if ( ! $load_google_fonts ) {
			return;
		}
		wp_register_style(
			'hstl-roboto',
			'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:300,300i,400,400i,500,500i,700,700i&display=swap',
			array(),
			Opt_In::VERSION
		);
		wp_register_style(
			'hstl-opensans',
			'https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i&display=swap',
			array(),
			Opt_In::VERSION
		);
		wp_register_style(
			'hstl-source-code-pro',
			'https://fonts.googleapis.com/css?family=Source+Code+Pro&display=swap',
			array(),
			Opt_In::VERSION
		);

		wp_enqueue_style( 'hstl-roboto' );
		wp_enqueue_style( 'hstl-opensans' );
		wp_enqueue_style( 'hstl-source-code-pro' );

	}

	/**
	 * Enqueue modules to be displayed on Frontend.
	 */
	public function create_modules() {

		// Retrieve all active modules.
		$modules = apply_filters( 'hustle_sort_modules', Hustle_Module_Collection::instance()->get_all( true ) );
		$datepicker_found = false;
		$recaptcha_found = false;
		$recaptcha_language = '';
		$enqueue_adblock = false;

		foreach ( $modules as $module ) {

			if ( ! $module instanceof Hustle_Module_Model ) {
				continue;
			}

			$is_non_inline_module = ( Hustle_Module_Model::POPUP_MODULE === $module->module_type || Hustle_Module_Model::SLIDEIN_MODULE === $module->module_type );

			if ( ! $module->is_allowed_to_display( $module->module_type ) ) {

				// If shortcode is enabled for inline modules, don't abort.
				// Shortcodes shouldn't follow the visibility conditions.
				if ( ! $is_non_inline_module ) {
					$display_options = $module->get_display()->to_array();
					if ( '1' !== $display_options['shortcode_enabled'] ) {
						continue;
					}
				} else {
					continue;
				}
			}

			// Setting up stuff for all modules except social sharing.
			if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE !== $module->module_type ) {

				if ( 'optin' === $module->module_mode ) {

					if ( ! $datepicker_found || empty( $recaptcha_language ) ) {
						$form_fields = $module->get_form_fields();

						// Datepicker.
						// Check if the module has a datepicker unless we already found one in other modules.
						// We'll localize some variables if the modules have a datepicker.
						if ( ! $datepicker_found ) {
							$field_types      = wp_list_pluck( $form_fields, 'type', true );
							$datepicker_found = in_array( 'datepicker', $field_types, true );
						}

						// Recaptcha.
						// Check if the module has a recaptcha to enqueue scripts unless we already found one.
						// We'll queue the script afterwards.
						if ( ! empty( $form_fields['recaptcha'] ) && empty( $recaptcha_language ) ) {

							$recaptcha_found = true;

							$recaptcha_field = $form_fields['recaptcha'];
							// Get only first recaptcha language. Skip if not set or it's "automatic".
							if ( ! empty( $recaptcha_field['recaptcha_language'] ) && 'automatic' !== $recaptcha_field['recaptcha_language'] ) {
								$recaptcha_language = $recaptcha_field['recaptcha_language'];
							}
						}
					}
				}

				// For popups and slideins.
				if ( $is_non_inline_module ) {
					$this->_non_inline_modules[] = $module;

					if ( ! $enqueue_adblock ) {

						$settings = $module->get_settings()->to_array();
						if (
							// If Trigger exists.
							! empty( $settings['triggers']['trigger'] )
							// If trigger is adblock.
							&& 'adblock' === $settings['triggers']['trigger']
							// If on_adblock toggle is enabled.
							&& ! empty( $settings['triggers']['on_adblock'] )
						) {
							$enqueue_adblock = true;
						}
					}
				} elseif ( Hustle_Module_Model::EMBEDDED_MODULE === $module->module_type ) {
					$this->_inline_modules[] = $module;

				}
			} else { // Social sharing modules.
				$this->_inline_modules[]     = $module;
				$this->_non_inline_modules[] = $module;
			}

			$this->log_module_type_to_load_styles( $module );

			$this->_modules[] = $module->get_module_data_to_display();

		} // End looping through the modules.

		// Set flag for scripts: datepicker field.
		if ( $datepicker_found ) {
			$this->modules_data_for_scripts['datepicker'] = true;
		}

		// Set flag for scripts: adblocker.
		if ( $enqueue_adblock ) {
			$this->modules_data_for_scripts['adblocker'] = true;
		}

		// Set flag for scripts: recaptcha field.
		if ( $recaptcha_found ) {
			$this->modules_data_for_scripts['recaptcha'] = [ 'language' => $recaptcha_language ];
		}
	}

	/**
	 * Store the modules' types to be displayed in order to enqueue
	 * their required styles.
	 * Called within self::create_modules() method.
	 *
	 * @since 4.0.1
	 *
	 * @param Hustle_Module_Model $module
	 */
	private function log_module_type_to_load_styles( Hustle_Module_Model $module ) {

		// Keep track of the of the modules types and modes to display
		// in order to queue the required styles only.
		$this->_module_types_to_display[ $module->module_type ] = 1;

		// Register the module mode for non SSharing modules.
		if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE !== $module->module_type ) {
			$this->_module_types_to_display[ $module->module_mode ] = 1;

		} else { // Register the module display type for SSharing modules.

			// Floating display.
			if (
				$module->is_display_type_active( Hustle_SShare_Model::FLOAT_MOBILE ) ||
				$module->is_display_type_active( Hustle_SShare_Model::FLOAT_DESKTOP )
			) {
				$this->_module_types_to_display[ Hustle_SShare_Model::FLOAT_MODULE ] = 1;
			}

			// Inline display.
			if (
				$module->is_display_type_active( Hustle_SShare_Model::INLINE_MODULE ) ||
				$module->is_display_type_active( Hustle_SShare_Model::WIDGET_MODULE ) ||
				$module->is_display_type_active( Hustle_SShare_Model::SHORTCODE_MODULE )
			) {
				$this->_module_types_to_display[ Hustle_SShare_Model::INLINE_MODULE ] = 1;
			}
		}
	}

	/**
	 * Check if current page has renderable opt-ins.
	 **/
	public function has_modules() {
		$has_modules = ! empty( $this->_non_inline_modules ) || ! empty( $this->_inline_modules );
		return apply_filters( 'hustle_front_handler', $has_modules );
	}

	/**
	 * By-pass NextGEN Gallery resource manager
	 *
	 * @return false
	 */
	public function nextgen_compat() {
		return false;
	}

	public function render_non_inline_modules() {

		foreach ( $this->_non_inline_modules as $module ) {

			if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE !== $module->module_type ) {
				$module->display();

			} elseif ( $module->is_display_type_active( Hustle_SShare_Model::FLOAT_DESKTOP ) || $module->is_display_type_active( Hustle_SShare_Model::FLOAT_MOBILE ) ) {
				$module->display( Hustle_SShare_Model::FLOAT_MODULE );
			}
		}
	}

	/**
	 * Handles the data for the unsubscribe shortcode
	 *
	 * @since 3.0.5
	 * @param array $atts The values passed through the shortcode attributes
	 * @return string The content to be rendered within the shortcode.
	 */
	public function unsubscribe_shortcode( $atts ) {
		$messages = Hustle_Settings_Admin::get_unsubscribe_messages();
		if ( isset( $_GET['token'] ) && isset( $_GET['email'] ) ) { // WPCS: CSRF ok.
			$error_message = $messages['invalid_data'];
			$sanitized_data = Opt_In_Utils::validate_and_sanitize_fields( $_GET ); // WPCS: CSRF ok.
			$email = $sanitized_data['email'];
			$nonce = $sanitized_data['token'];
			// checking if email is valid
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				return $error_message;
			}
			$entry = new Hustle_Entry_Model();
			$unsubscribed = $entry->unsubscribe_email( $email, $nonce );
			if ( $unsubscribed ) {
				return $messages['successful_unsubscription'];
			} else {
				return $error_message;
			}
		}
		// Show all modules' lists by default.
		$attributes = shortcode_atts( array( 'id' => '-1' ), $atts );
		$params = array(
			'ajax_step' => false,
			'shortcode_attr_id' => $attributes['id'],
			'messages' => $messages,
			);
		$html = $this->_hustle->render( 'general/unsubscribe-form', $params, true );
		apply_filters( 'hustle_render_unsubscribe_form_html', $html, $params );
		return $html;
	}

	/**
	 * Render the modules' wrapper to render the actual module using their shortcodes.
	 *
	 * @since the beginning of time.
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public function shortcode( $atts, $content ) {
		$atts = shortcode_atts( array(
			'id' => '',
			'type' => 'embedded',
			'css_class' => '',
		), $atts, self::SHORTCODE );

		if ( empty( $atts['id'] ) ) {
			return '';
		}

		$type = $atts['type'];

		// If shortcode type is not embed or sshare.
		if ( 'embedded' !== $type && 'social_sharing' !== $type ) {
			// Do not enforce embedded/social_sharing type.
			$enforce_type = false;
		} else {
			// Enforce embedded/social_sharing type.
			$enforce_type = true;
		}

		// Get the module data.
		$module = Hustle_Module_Model::instance()->get_by_shortcode( $atts['id'], $enforce_type );
		if ( is_wp_error( $module ) ) {
			return '';
		}
		if ( ! $module || ! $module->active ) {
			return '';
		}

		$module = Hustle_Module_Collection::instance()->return_model_from_id( $module->module_id );

		if ( is_wp_error( $module ) || ! $module->is_display_type_active( Hustle_Module_Model::SHORTCODE_MODULE ) ) {
			return '';
		}

		$custom_classes = esc_attr( $atts['css_class'] );

		// Maybe display trigger link (For popups and slideins).
		if ( ! empty( $content ) && ( 'popup' === $type || 'slidein' === $type ) ) {

			// If shortcode click trigger is disabled, print nothing.
			$settings = $module->get_settings()->to_array();
			if ( ! isset( $settings['triggers']['enable_on_click_shortcode'] ) || '0' === $settings['triggers']['enable_on_click_shortcode'] ) {
				return '';
			}

			return sprintf(
				'<a href="#" class="%s hustle_module_%s %s" data-id="%s" data-type="%s">%s</a>',
				self::SHORTCODE_TRIGGER_CSS_CLASS,
				esc_attr( $module->id ),
				esc_attr( $custom_classes ),
				esc_attr( $module->id ),
				esc_attr( $type ),
				wp_kses_post( $content )
			);
		}

		$preview = Hustle_Renderer_Abstract::$is_preview;

		// Display the module.
		ob_start();

		$module->display( Hustle_Module_Model::SHORTCODE_MODULE, $custom_classes, $preview );

		if ( $preview ) {
			$view = $module->get_renderer();
			$view->module = $module->load();
			$view->print_styles( $preview );
		}

		return ob_get_clean();
	}

	/**
	 * Display inline modules.
	 * Embedded and Social Sharing modules only.
	 *
	 * @since the beginning of time.
	 *
	 * @param $content
	 * @return string
	 */
	public function show_after_page_post_content( $content ) {

		// Return the content immediately if there are no modules or the page doesn't have a content to embed into.
		if ( ! count( $this->_inline_modules ) || isset( $_REQUEST['fl_builder'] ) || is_home() || is_archive() ) { // CSRF: ok.
			return $content;
		}

		$modules = apply_filters( 'hustle_inline_modules_to_display', $this->_inline_modules );

		foreach ( $modules as $module ) {

			// Skip if "inline" display is disabled.
			if ( ! $module->is_display_type_active( Hustle_Module_Model::INLINE_MODULE ) ) {
				continue;
			}

			$custom_classes = apply_filters( 'hustle_inline_module_custom_classes', '', $module );

			ob_start();
			$module->display( Hustle_Module_Model::INLINE_MODULE, $custom_classes );
			$module_markup = ob_get_clean();

			$display = $module->get_display()->to_array();
			$display_position = $display['inline_position'];

			if ( 'both' === $display_position ) {
				$content = $module_markup . $content . $module_markup;

			} elseif ( 'above' === $display_position ) {
				$content = $module_markup . $content;

			} else { // For "below".
				$content .= $module_markup;

			}
		}

		remove_filter( 'the_content', array( $this, 'show_after_page_post_content' ), self::$the_content_filter_priority );

		return $content;
	}
}
