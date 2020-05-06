<?php
if ( ! class_exists( 'Hustle_Module_Admin' ) ) :

	/**
 * Class Hustle_Module_Admin
 */
	class Hustle_Module_Admin {

		const ADMIN_PAGE = 'hustle';
		const DASHBOARD_PAGE = 'hustle_dashboard';
		const POPUP_LISTING_PAGE = 'hustle_popup_listing';
		const POPUP_WIZARD_PAGE = 'hustle_popup';
		const SLIDEIN_LISTING_PAGE = 'hustle_slidein_listing';
		const SLIDEIN_WIZARD_PAGE = 'hustle_slidein';
		const EMBEDDED_LISTING_PAGE = 'hustle_embedded_listing';
		const EMBEDDED_WIZARD_PAGE = 'hustle_embedded';
		const SOCIAL_SHARING_LISTING_PAGE = 'hustle_sshare_listing';
		const SOCIAL_SHARING_WIZARD_PAGE = 'hustle_sshare';
		const INTEGRATIONS_PAGE = 'hustle_integrations';
		const ENTRIES_PAGE = 'hustle_entries';
		const SETTINGS_PAGE = 'hustle_settings';
		const UPGRADE_MODAL_PARAM = 'requires-pro';

		private $_hustle;

		public function __construct( Opt_In $hustle ) {

			$this->_hustle = $hustle;

			add_action( 'admin_init', array( $this, 'init' ) );
			add_action( 'current_screen', array( $this, 'set_proper_current_screen' ) );

			add_action( 'wp_ajax_hustle_dismiss_notification', array( $this, 'dismiss_notification' ) );
			add_action( 'wp_ajax_hustle_dismiss_m2_notification', array( $this, 'dismiss_m2_notification' ) );

			if ( Opt_In_Utils::_is_free() && ! file_exists( WP_PLUGIN_DIR . '/hustle/opt-in.php' ) ) {
				add_action( 'wp_ajax_hustle_dismiss_admin_notice', array( $this, 'dismiss_admin_notice' ) );
			}

			if ( $this->_is_admin_module() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'sui_scripts' ), 99 );
				add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 99 );
				add_action( 'admin_print_styles', array( $this, 'register_styles' ) );
				add_filter( 'admin_body_class', array( $this, 'admin_body_class' ), 99 );
				add_filter( 'user_can_richedit', '__return_true' ); // allow rich editor in
				add_filter( 'tiny_mce_before_init', array( $this, 'set_tinymce_settings' ), 10, 2 );
				add_filter( 'wp_default_editor', array( $this, 'set_editor_to_tinymce' ) );
				add_filter( 'tiny_mce_plugins', array( $this, 'remove_despised_editor_plugins' ) );
				add_filter( 'mce_external_plugins', array( $this, 'remove_all_mce_external_plugins' ), -1 );
				add_filter( 'mce_buttons', array( $this, 'register_buttons' ) );

				$this->load_notices();

				add_filter( 'removable_query_args', array( $this, 'maybe_remove_paged' ) );

				//geodirectory plugin compatibility.
				add_action( 'wp_super_duper_widget_init', array( $this, 'geo_directory_compat' ), 10, 2 );

				// remove Get params for notices
				add_filter( 'removable_query_args', array( $this, 'remove_notice_params' ) );
			}

			add_filter( 'w3tc_save_options', array( $this, 'filter_w3tc_save_options' ), 10, 1 );
			add_filter( 'plugin_action_links', array( $this, 'add_plugin_action_links' ), 10, 4 );
			add_filter( 'network_admin_plugin_action_links', array( $this, 'add_plugin_action_links' ), 10, 4 );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
		}

		/**
		 * Remove paged get attribute if there isn't a module and it's not the first page
		 *
		 * @param array $removable_query_args
		 * @return array
		 */
		public function maybe_remove_paged( $removable_query_args ) {
			$paged = filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT );
			$module_type = $this->get_modyle_type_by_page();

			if ( $paged && 1 !== $paged && $module_type ) {
				$args = array(
					'module_type' => $module_type,
					'page' => $paged,
				);
				$entries_per_page = Hustle_Settings_Admin::get_per_page( 'module' );
				$modules = Hustle_Module_Collection::instance()->get_all( null, $args, $entries_per_page );
				if ( empty( $modules ) ) {
					$_SERVER['REQUEST_URI'] = remove_query_arg( 'paged' );
					$removable_query_args[] = 'paged';
					unset( $_GET['paged'] );
				}
			}

			return $removable_query_args;
		}

		/**
		 * Remove Get parameters for Hustle notices
		 *
		 * @param array $vars
		 * @return array
		 */
		public function remove_notice_params( $vars ){
			$vars[] = 'show-notice';
			$vars[] = 'notice';

			return $vars;
		}

		/**
		* Removing all MCE external plugins which often break our pages
		*
		* @since 3.0.8
		* @param array $external_plugins External plugins
		* @return array
		*/
		public function remove_all_mce_external_plugins( $external_plugins ) {
			remove_all_filters( 'mce_external_plugins' );

			$external_plugins = array();
			$external_plugins['hustle'] = Opt_In::$plugin_url . 'assets/js/vendor/tiny-mce-button.js';
			add_action( 'admin_footer', array( $this, 'add_tinymce_variables' ) );

			return $external_plugins;
		}

		/**
		 * Queue the admin notices.
		 * @since 4.0
		 */
		private function load_notices() {

			// Show upgrade notice only if this is free, and Hustle Pro is not already installed.
			if ( Opt_In_Utils::_is_free() && ! file_exists( WP_PLUGIN_DIR . '/hustle/opt-in.php' ) ) {
				add_action( 'admin_notices', array( $this, 'show_hustle_pro_available_notice' ) );
			}

			if ( Hustle_Migration::check_tracking_needs_migration() ) {
				add_action( 'admin_notices', array( $this, 'show_migrate_tracking_notice' ) );
			}

			if ( /*! Hustle_Settings_Admin::was_notification_dismissed( '40_custom_style_review' ) &&*/ Hustle_Migration::did_hustle_exist() ) {
				add_action( 'admin_notices', array( $this, 'show_review_css_after_migration_notice' ) );
			}

			if ( Hustle_Migration::is_migrated( 'hustle_40_migrated' ) ) {
				add_action( 'admin_notices', array( $this, 'show_visibility_behavior_update' ) );
			}

			add_action( 'admin_notices', array( $this, 'show_sendgrid_update_notice' ) );

			add_action( 'admin_notices', array( $this, 'show_provider_migration_notice' ) );

			add_action( 'admin_notices', array( $this, 'show_depricating_m2_conditions' ) );

		}

		/**
		 * Display a notice for Deprecating Membership 2 visibility condition
		 *
		 * @since 4.1
		 */
		public function show_depricating_m2_conditions() {
			$count_m2_modules = count( get_option( 'hustle_notice_stop_support_m2', [] ) );
			if ( ! $count_m2_modules ) {
				return;
			}

			$current_user = wp_get_current_user();
			$username = ! empty( $current_user->user_firstname ) ? $current_user->user_firstname : $current_user->user_login;
			?>
			<div id="hustle-m2-notice" class="notice notice-error">
				<p><b><?php esc_html_e( 'Hustle - Deprecating Membership 2 visibility condition', 'hustle' ); ?></b></p>
				<p>
				<?php
					printf( esc_html__( 'Hey %1$s, we have deprecated the membership visibility condition for the %2$sMembership 2%4$s plugin. Since you were using the condition on %5$s modules, you can install a mu-plugin to continue using it. Read our mu-plugin %3$sinstallation guide%4$s.', 'hustle' )
						, esc_html( $username )
						, '<a href="https://github.com/wpmudev/membership-2" target="_blank">'
						, '<a href="https://premium.wpmudev.org/manuals/wpmu-manual-2/using-mu-plugins/" target="_blank">'
						, '</a>'
						, (int)$count_m2_modules
					); ?></p>
				<p>
					<a href="https://gist.github.com/wpmudev-sls/84544541eddd5cd7c7c60c5ef0406597" class="button-primary" target="_blank"><?php esc_html_e( 'Membership 2 condition mu-plugin', 'hustle' ); ?></a>
					&nbsp;&nbsp;
					<span id="hustle-dismiss-m2-notice" data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_dismiss_notification' ) ); ?>" style="cursor: pointer;"><?php esc_html_e( 'Dismiss', 'hustle' ); ?></span>
				</p>
			</div>
			<?php
		}

		/**
		 * Display a notice for updating Marketing Campaings via Sendgrid.
		 * @since 4.0.4
		 */
		public function show_sendgrid_update_notice() {
			// check if the notification is already dismissed
			if ( Hustle_Settings_Admin::was_notification_dismissed( 'hustle_sendgrid_update_showed' ) ) {
				return;
			}
			// check if there is no Sendgrid intagration
			if ( ! $this->is_provider_integrated( 'sendgrid' ) ) {
				Hustle_Settings_Admin::add_dismissed_notification( 'hustle_sendgrid_update_showed' );

				return;
			}

			$integrations_url = add_query_arg( array(
				'page' => self::INTEGRATIONS_PAGE,
			), 'admin.php' );

			$current_user = wp_get_current_user();
			$username = ! empty( $current_user->user_firstname ) ? $current_user->user_firstname : $current_user->user_login;
			?>
			<div id="hustle-sendgrid-update-notice" class="notice notice-warning is-dismissible" data-name="hustle_sendgrid_update_showed" data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_dismiss_notification' ) ); ?>">
				<p>
				<?php
					printf( esc_html__( 'Hey %1$s, we have updated our %4$sSendGrid%5$s integration to support the %2$snew Marketing Campaigns%3$s. You need to review your existing SendGrid integration(s) and select the Marketing Campaigns version (new or legacy) you are using to avoid failed API calls.', 'hustle' )
						, esc_html( $username )
						, '<a href="https://sendgrid.com/blog/new-era-marketing-campaigns/" target="_blank">'
						, '</a>'
						, '<b>'
						, '</b>'
					); ?></p>
				<p><a href="<?php echo esc_url( $integrations_url ); ?>" class="button-primary"><?php esc_html_e( 'Review Integrations', 'hustle' ); ?></a></p>
			</div>
			<?php
		}

		/**
		 * Check is $provider integrated or not
		 *
		 * @param string $provider
		 * @since 4.0.4
		 * @return bool
		 */
		private function is_provider_integrated( $provider ) {
			$providers = Hustle_Provider_Utils::get_registered_addons_grouped_by_connected();
			$connected = wp_list_pluck( $providers['connected'], 'slug' );

			return in_array( $provider, $connected, true );
		}

		/**
		 * Display a notice for reviewing visibility conditions after updating.
		 * @since 4.1
		 */
		public function show_visibility_behavior_update() {
			if ( Hustle_Settings_Admin::was_notification_dismissed( '41_visibility_behavior_update' ) ) {
				return;
			}
			$url_params = [
				'page' => self::ADMIN_PAGE,
				'review-conditions' => 'true',
			];
			$url = add_query_arg( $url_params, 'admin.php' );
			$link = lib3()->html->element( [
				'type' => 'html_link',
				'value' => esc_html__( 'Check conditions', 'hustle' ),
				'url' => $url,
				'class' => 'button-primary',
			], true );
			$version = Opt_In_Utils::_is_free() ? '7.1' : '4.1';
			?>
			<div class="hustle-notice notice notice-warning" data-name="41_visibility_behavior_update" data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_dismiss_notification' ) ); ?>">
				<p><b><?php esc_html_e( 'Hustle - Module visibility behaviour update', 'hustle' ); ?></b></p>
				<p>
					<?php printf( esc_html__( 'Hustle %s fixes a visibility bug which may affect the visibility behavior of your popups and other modules. Please review the visibility conditions of each of your modules to ensure they will appear as you expect.', 'hustle' ), esc_attr( $version ) ); ?>
				</p>
				<p>
					<?php echo $link; // WPCS: XSS ok. ?>&nbsp;&nbsp;&nbsp;
					<label class="sui-label"><span class="sui-label-link dismiss-notice" role="button"><?php esc_html_e( 'Dismiss', 'hustle' ); ?></span></label>
				</p>

			</div>
			<?php

		}

		/**
		 * Display a notice for reviewing the modules' custom css after migration.
		 * @since 4.0
		 */
		public function show_review_css_after_migration_notice() {
			if ( Hustle_Settings_Admin::was_notification_dismissed( '40_custom_style_review' ) ) {
				return;
			}

			$current_user = wp_get_current_user();
			$username = ! empty( $current_user->user_firstname ) ? $current_user->user_firstname : $current_user->user_login;
			?>
			<div class="hustle-notice notice notice-warning is-dismissible" data-name="40_custom_style_review" data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_dismiss_notification' ) ); ?>">
				<p>
				<?php printf(
					esc_html__( "Hey %s, we have improved Hustle’s front-end code in this update, which included modifying some CSS classes. Any custom CSS you were using may have been affected. We recommend reviewing the modules (which were using custom CSS) to ensure they don't need any adjustments.", 'hustle' ),
					esc_html( $username )
				); ?>
				</p>
				<p><a href="#" class="dismiss-notice"><?php esc_html_e( 'Dismiss this notice', 'hustle' ); ?></a></p>
			</div>
			<?php
		}

		/**
		 * Display the notice to migrate tracking and subscriptions data.
		 * @since 4.0
		 */
		public function show_migrate_tracking_notice() {

			if ( ! self::is_show_migrate_tracking_notice() ) {
				return;
			}

			$migrate_url = add_query_arg( array(
				'page' => self::ADMIN_PAGE,
				'show-migrate' => 'true',
			), 'admin.php' );

			$current_user = wp_get_current_user();
			$username = ! empty( $current_user->user_firstname ) ? $current_user->user_firstname : $current_user->user_login;
			?>
			<div id="hustle-tracking-migration-notice" class="notice notice-warning">
				<p><?php printf( esc_html__( 'Hey %s, nice work on updating the Hustle! However, you need to migrate the data of your existing modules such as tracking data and email list manually.', 'hustle' ), esc_html( $username ) ); ?></p>
				<p><a href="<?php echo esc_url( $migrate_url ); ?>" class="button-primary"><?php esc_html_e( 'Migrate Data', 'hustle' ); ?></a><a href="#" class="hustle-notice-dismiss" style="margin-left:20px;">Dismiss</a></p>
			</div>
			<?php
		}

		public static function is_show_migrate_tracking_notice() {

			if ( ! Hustle_Migration::check_tracking_needs_migration() ) {
				return false;
			}

			$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
			$show_modal = filter_input( INPUT_GET, 'show-migrate', FILTER_VALIDATE_BOOLEAN );

			if ( $show_modal || ( self::ADMIN_PAGE === $page && ! Hustle_Settings_Admin::was_notification_dismissed( Hustle_Dashboard_Admin::MIGRATE_MODAL_NAME ) ) ) {
				return false;
			}

			return true;
		}

		public function dismiss_m2_notification() {
			Opt_In_Utils::validate_ajax_call( 'hustle_dismiss_notification' );
			delete_option( 'hustle_notice_stop_support_m2' );

			wp_send_json_success();
		}

		/**
		 * Dismiss the given notification.
		 * @since 4.0
		 */
		public function dismiss_notification() {
			Opt_In_Utils::validate_ajax_call( 'hustle_dismiss_notification' );
			$notification_name = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_STRING );

			if ( Hustle_Dashboard_Admin::MIGRATE_NOTICE_NAME !== $notification_name ) {
				Hustle_Settings_Admin::add_dismissed_notification( $notification_name );
			} else {
				Hustle_Migration::mark_tracking_migration_as_completed();
			}

			wp_send_json_success();
		}

		public function register_buttons( $buttons ) {
			array_unshift( $buttons, 'hustlefields' );
			return $buttons;
		}

		public function add_tinymce_variables() {

			$var_button = array();

			$module = Hustle_Module_Model::instance()->get( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ) );
			if ( ! is_wp_error( $module ) ) {

				$saved_fields = $module->get_form_fields();

				if ( is_array( $saved_fields ) && ! empty( $saved_fields ) ) {
					$fields = array();
					$ignored_fields = Hustle_Entry_Model::ignored_fields();

					foreach( $saved_fields as $field_name => $data ) {
						if ( ! in_array( $data['type'], $ignored_fields, true ) ) {
							$fields[ $field_name ] = $data['label'];
						}
					}

					$available_editors = array( 'success_message', 'email_body' );

					/**
					 * Print JS details for the custom TinyMCE "Insert Variable" button
					 *
					 * @see assets/js/vendor/tiny-mce-button.js
					 */
					$var_button = array(
						'button_title' => __( 'Add Hustle Fields', 'hustle' ),
						'fields' => $fields,
						'available_editors' => $available_editors,
					);
				}
			}

			printf(
				'<script>window.hustleData = %s;</script>',
				wp_json_encode( $var_button )
			);
		}

		// force reject minify for hustle js and css
		public function filter_w3tc_save_options( $config ) {

			// reject js
			$defined_rejected_js = $config['new_config']->get( 'minify.reject.files.js' );
			$reject_js = array(
				Opt_In::$plugin_url . 'assets/js/admin.min.js',
				Opt_In::$plugin_url . 'assets/js/ad.js',
				Opt_In::$plugin_url . 'assets/js/front.min.js',
			);
			foreach ( $reject_js as $r_js ) {
				if ( ! in_array( $r_js, $defined_rejected_js, true ) ) {
					array_push( $defined_rejected_js, $r_js );
				}
			}
			$config['new_config']->set( 'minify.reject.files.js', $defined_rejected_js );

			// reject css
			$defined_rejected_css = $config['new_config']->get( 'minify.reject.files.css' );
			$reject_css = array(
				Opt_In::$plugin_url . 'assets/css/front.min.css',
			);
			foreach ( $reject_css as $r_css ) {
				if ( ! in_array( $r_css, $defined_rejected_css, true ) ) {
					array_push( $defined_rejected_css, $r_css );
				}
			}
			$config['new_config']->set( 'minify.reject.files.css', $defined_rejected_css );

			return $config;
		}

		/**
	 * Removes unnecessary editor plugins
	 *
	 * @param $plugins
	 * @return mixed
	 */
		public function remove_despised_editor_plugins( $plugins ) {
			$k = array_search( 'fullscreen', $plugins, true );
			if ( false !== $k ) {
				unset( $plugins[ $k ] );
			}
			$plugins[] = 'paste';
			return $plugins;
		}

		/**
	 * Sets default editor to tinymce for opt-in admin
	 *
	 * @param $editor_type
	 * @return string
	 */
		public function set_editor_to_tinymce( $editor_type ) {
			return 'tinymce';
		}

		/**
	 * Inits admin
	 *
	 * @since 3.0
	 */
		public function init() {
			$this->add_privacy_message();
		}

		/**
		 * Register scripts for the admin page
		 *
		 * @since 1.0
		 */
		public function register_scripts( $page_slug ) {

			/**
			 * Register popup requirements
			 */
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_media();
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'jquery-ui-sortable' );

			wp_register_script(
				'optin_admin_ace',
				Opt_In::$plugin_url . 'assets/js/vendor/ace/ace.js',
				array(),
				Opt_In::VERSION,
				true
			);
			wp_register_script(
				'optin_admin_fitie',
				Opt_In::$plugin_url . 'assets/js/vendor/fitie/fitie.js',
				array(),
				Opt_In::VERSION,
				true
			);

			wp_enqueue_script( 'optin_admin_ace' );
			wp_enqueue_script( 'optin_admin_popup' );
			wp_enqueue_script( 'optin_admin_select2' );

			wp_enqueue_script( 'optin_admin_fitie' );
			add_filter( 'script_loader_tag', array( $this, 'handle_specific_script' ), 10, 2 );
			add_filter( 'style_loader_tag', array( $this, 'handle_specific_style' ), 10, 2 );

			$optin_vars = array(
				'single_module_action_nonce' => wp_create_nonce( 'hustle_single_action' ),
				'settings_palettes_action_nonce' => wp_create_nonce( 'hustle_palette_action' ), // This is only used in the global "Settings" page.
				'providers_action_nonce' => wp_create_nonce( 'hustle_provider_action' ),
				'palettes' => Hustle_Module_Model::get_all_palettes(),
				'current' => array(),
				'fetching_list' => __( 'Fetching integration list…', 'hustle' ),
				'daterangepicker' => array( // Used only in entries page
					'daysOfWeek' => Opt_In_Utils::get_short_days_names(),
					'monthNames' => Opt_In_Utils::get_months_names(),
				),
				'module_name' => array(
					'popup'           => __( 'Pop-up', 'hustle' ),
					'slidein'         => __( 'Slide-in', 'hustle' ),
					'embedded'        => __( 'Embed', 'hustle' ),
					'social_sharing'  => __( 'Social Sharing', 'hustle' ),
				),
				'module_page' => array(
					'popup'           => self::POPUP_LISTING_PAGE,
					'slidein'         => self::SLIDEIN_LISTING_PAGE,
					'embedded'        => self::EMBEDDED_LISTING_PAGE,
					'social_sharing'  => self::SOCIAL_SHARING_LISTING_PAGE,
				),
				'labels' => array(
					'submissions' => __( '%d Conversions', 'hustle' ),
					'views' => __( '%d Views', 'hustle' ),
				),
				'wp_conditions' => [
					'is_front_page' => __( 'Front page', 'hustle' ),
					'is_404' => __( '404 page', 'hustle' ),
					'is_search' => __( 'Search results', 'hustle' ),
				],
				'wc_static_pages' => [
					'is_cart' => __( 'Cart', 'hustle' ),
					'is_checkout' => __( 'Checkout', 'hustle' ),
					'is_order_received' => __( 'Order Received', 'hustle' ),
					'is_account_page' => __( 'My account', 'hustle' ),
				],
				'archive_pages' => [
					'is_category' => __( 'Category archive', 'hustle' ),
					'is_tag' => __( 'Tag archive', 'hustle' ),
					'is_author' => __( 'Author archive', 'hustle' ),
					'is_date' => __( 'Date archive', 'hustle' ),
					'is_post_type_archive' => __( 'Custom post archive', 'hustle' ),
				],
				'wc_archive_pages' => [
					'is_shop' => __( 'Shop', 'hustle' ),
					'is_product_category' => __( 'Product Category', 'hustle' ),
					'is_product_tag' => __( 'Product Tag', 'hustle' ),
				],
				'messages' => array(
					'required_error_message' => __( 'Your {field} is required.', 'hustle' ),
					'is_required' => __( '{field} is required.', 'hustle' ),
					'cant_empty' => __( 'This field can\'t be empty.', 'hustle' ),
					'url_required_error_message' => __( 'Your website url is required.', 'hustle' ),
					'gdpr_required_error_message' => __( 'Please accept the terms and try again.', 'hustle' ),
					'validation_message' => __( 'Please enter a valid {field}.', 'hustle' ),
					'date_validation_message' => __( 'Please enter a valid date.', 'hustle' ),
					'time_validation_message' => __( 'Please enter a valid time.', 'hustle' ),
					'recaptcha_validation_message' => __( 'reCAPTCHA verification failed. Please try again.', 'hustle' ),
					'settings_rows_updated' => __( ' number of IPs removed from database successfully.', 'hustle' ),
					'settings_saved' => __( 'Settings saved.' , 'hustle' ),
					'dont_navigate_away' => __( 'Changes are not saved, are you sure you want to navigate away?', 'hustle' ),
					'ok' => __( 'Ok', 'hustle' ),
					'something_went_wrong' => '<label class="wpmudev-label--notice"><span>' . __( 'Something went wrong. Please try again.', 'hustle' ) . '</span></label>',
					'aweber_migration_success' => sprintf( esc_html__( "%s integration successfully migrated to the oAuth 2.0.", 'hustle' ), '<strong>' . esc_html__( 'Aweber', 'hustle' ) . '</strong>' ),
					'settings_was_reset' => '<label class="wpmudev-label--notice"><span>' . __( 'Plugin was successfully reset.', 'hustle' ) . '</span></label>',
					'integraiton_required' => '<label class="wpmudev-label--notice"><span>' . __( 'An integration is required on opt-in module.', 'hustle' ) . '</span></label>',
					'media_uploader' => array(
						'select_or_upload' => __( 'Select or Upload Image', 'hustle' ),
						'use_this_image' => __( 'Use this image', 'hustle' ),
					),
					'dashboard' => array(
						'not_enough_data' => __( 'There is no enough data yet, please try again later.', 'hustle' ),
					),
					'commons' => array(
						'published' => __( 'Published', 'hustle' ),
						'draft' => __( 'Draft', 'hustle' ),
						'unpublish' => __( 'Unpublish', 'hustle' ),
						'save_changes' => __( 'Save changes', 'hustle' ),
						'save_draft' => __( 'Save draft', 'hustle' ),
						'publish' => __( 'Publish', 'hustle' ),
						'dismiss' => __( 'Dismiss', 'hustle' ),
						'module_created' => __( '{type_name} created successfully. Get started by adding content to your new {type_name} below.', 'hustle' ),
						'generic_ajax_error' => __( 'Something went wrong with the request. Please reload the page and try again.', 'hustle' ),
						'module_imported' => __( 'Module successfully imported.', 'hustle' ),
						'module_duplicated' => __( 'Module successfully duplicated.', 'hustle' ),
						'module_tracking_reset' => __( "Module's tracking data successfully reset.", 'hustle' ),
						'module_deleted' => __( 'Module successfully deleted.', 'hustle' ),
						'shortcode_copied' => __( 'Shortcode copied successfully.', 'hustle' ),
					),
				),
			);

			$optin_vars['browsers'] = $this->_hustle->get_browsers();
			$optin_vars['countries'] = $this->_hustle->get_countries();
			$optin_vars['roles'] = Opt_In_Utils::get_user_roles();
			$optin_vars['templates'] = Opt_In_Utils::hustle_get_page_templates();
			$optin_vars['urlParams'] = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification

			/**
			 * The variables specific to each page are added via this hook.
			 */
			$optin_vars = apply_filters( 'hustle_optin_vars', $optin_vars );

			$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
			if ( 'hustle' === $page ) {
				wp_enqueue_script( 'jquery-sortable' );
			}
			if ( !is_null( $page ) && 'hustle' !== $page ) {
				wp_enqueue_script( 'wp-color-picker-alpha', Opt_In::$plugin_url . 'assets/js/vendor/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '1.2.2', true );
			}
			if ( 'hustle_entries' === $page ) {
				$this->enqueue_entries_scripts();
			}
			$url_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'debug' : 'min';
			wp_register_script(
				'optin_admin_scripts',
				Opt_In::$plugin_url . 'assets/js/admin.' . $url_suffix . '.js',
				array( 'jquery', 'backbone', 'jquery-effects-core' ),
				Opt_In::VERSION,
				true
			);
			wp_localize_script( 'optin_admin_scripts', 'optinVars', $optin_vars );
			wp_enqueue_script( 'optin_admin_scripts' );
		}

		/**
		 * Custom scripts that only used on submissions page
		 *
		 * @since 4.0
		 */
		public function enqueue_entries_scripts() {
			wp_enqueue_script( 'hustle-entries-moment',
							   Opt_In::$plugin_url . 'assets/js/vendor/moment.min.js',
							   array( 'jquery' ),
							   Opt_In::VERSION,
							   true );
			wp_enqueue_script( 'hustle-entries-datepicker-range',
							   Opt_In::$plugin_url . 'assets/js/vendor/daterangepicker.min.js',
							   array( 'hustle-entries-moment' ),
							   Opt_In::VERSION,
							   true );
			wp_enqueue_style( 'hustle-entries-datepicker-range',
							  Opt_In::$plugin_url . 'assets/css/daterangepicker.min.css',
							  array(),
							  Opt_In::VERSION );

			// use inline script to allow hooking into this
			$daterangepicker_ranges
				= sprintf(
				"
				var hustle_entries_datepicker_ranges = {
					'%s': [moment(), moment()],
					'%s': [moment().subtract(1,'days'), moment().subtract(1,'days')],
					'%s': [moment().subtract(6,'days'), moment()],
					'%s': [moment().subtract(29,'days'), moment()],
					'%s': [moment().startOf('month'), moment().endOf('month')],
					'%s': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
				};",
				__( 'Today', 'hustle' ),
				__( 'Yesterday', 'hustle' ),
				__( 'Last 7 Days', 'hustle' ),
				__( 'Last 30 Days', 'hustle' ),
				__( 'This Month', 'hustle' ),
				__( 'Last Month', 'hustle' )
			);

			/**
			 * Filter ranges to be used on submissions date range
			 *
			 * @since 4.0
			 *
			 * @param string $daterangepicker_ranges
			 */
			$daterangepicker_ranges = apply_filters( 'hustle_entries_datepicker_ranges', $daterangepicker_ranges );

			wp_add_inline_script( 'hustle-entries-datepicker-range', $daterangepicker_ranges );

		}

		/**
		 * Register shared-ui scripts
		 *
		 * @since 4.0.0
		 */
		public function sui_scripts() {

			$sanitize_version = str_replace( '.', '-', HUSTLE_SUI_VERSION );
			$sui_body_class   = "sui-$sanitize_version";

			wp_enqueue_script(
				'shared-ui',
				Opt_In::$plugin_url . 'assets/js/shared-ui.min.js',
				array( 'jquery' ),
				$sui_body_class,
				true
			);

			wp_enqueue_script(
				'chartjs',
				Opt_In::$plugin_url . 'assets/js/vendor/chartjs/Chart.bundle.min.js',
				'2.7.2',
				true
			);
		}

		/**
	 * Determine what admin section for Pop-up module
	 *
     * @since 3.0.0.
     *
	 * @param boolean/string $default Default value.
	 *
	 * @return mixed, string or boolean
	 */
		public static function get_current_section( $default = false ) {
			$section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );
			return ( is_null( $section ) || empty( $section ) )
			? $default
			: $section;
		}

		/**
	 * Handling specific scripts for each scenario
	 *
	 */
		public function handle_specific_script( $tag, $handle ) {
			if ( 'optin_admin_fitie' === $handle ) {
				$tag = "<!--[if IE]>$tag<![endif]-->";
			}
			return $tag;
		}

		/**
	 * Handling specific style for each scenario
	 *
	 */
		public function handle_specific_style( $tag, $handle ) {
			if ( 'hustle_admin_ie' === $handle ) {
				$tag = '<!--[if IE]>'. $tag .'<![endif]-->';
			}
			return $tag;
		}

		public function set_proper_current_screen( $current ) {
			global $current_screen;
			if ( ! Opt_In_Utils::_is_free() ) {
				$current_screen->id = Opt_In_Utils::clean_current_screen( $current_screen->id );
			}
		}

		/**
	 * Registers styles for the admin
	 *
	 *
	 */
		public function register_styles( $page_slug ) {

			$sanitize_version = str_replace( '.', '-', HUSTLE_SUI_VERSION );
			$sui_body_class   = "sui-$sanitize_version";

			wp_enqueue_style( 'thickbox' );

			wp_register_style(
				'hstl-roboto',
				'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:300,300i,400,400i,500,500i,700,700i',
				array(),
				Opt_In::VERSION
			);
			wp_register_style(
				'hstl-opensans',
				'https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i',
				array(),
				Opt_In::VERSION
			);
			wp_register_style(
				'hstl-source',
				'https://fonts.googleapis.com/css?family=Source+Code+Pro',
				array(),
				Opt_In::VERSION
			);

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wdev_ui' );
			wp_enqueue_style( 'wdev_notice' );
			wp_enqueue_style( 'hstl-roboto' );
			wp_enqueue_style( 'hstl-opensans' );
			wp_enqueue_style( 'hstl-source' );

			wp_enqueue_style(
				'sui_styles',
				Opt_In::$plugin_url . 'assets/css/shared-ui.min.css',
				array(),
				$sui_body_class
			);

			$is_page_with_render = ! preg_match( '/hustle_(integrations|entries|settings)/', $page_slug );
			if ( $is_page_with_render ) {
				// TODO: pass the array with the required module's types only.
				Hustle_Module_Front::print_front_styles();
			}

		}

	/**
	 * Checks if it's module admin page
	 *
	 * @return bool
	 */
		private function _is_admin_module() {
			$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
			return in_array( $page, [
				self::ADMIN_PAGE,
				self::DASHBOARD_PAGE,
				self::POPUP_LISTING_PAGE,
				self::POPUP_WIZARD_PAGE,
				self::SLIDEIN_LISTING_PAGE,
				self::SLIDEIN_WIZARD_PAGE,
				self::EMBEDDED_LISTING_PAGE,
				self::EMBEDDED_WIZARD_PAGE,
				self::SOCIAL_SHARING_LISTING_PAGE,
				self::SOCIAL_SHARING_WIZARD_PAGE,
				self::INTEGRATIONS_PAGE,
				self::ENTRIES_PAGE,
				self::SETTINGS_PAGE,
			], true );

		}

		/**
		 * Return an array with the slugs of the admin pages
		 *
		 * @since 4.1.1
		 * @return array
		 */
		public static function get_hustle_admin_pages() {
			return [
				'toplevel_page_hustle',
				'hustle_page_' . self::ADMIN_PAGE,
				'hustle_page_' . self::DASHBOARD_PAGE,
				'hustle_page_' . self::POPUP_LISTING_PAGE,
				'hustle_page_' . self::POPUP_WIZARD_PAGE,
				'hustle_page_' . self::SLIDEIN_LISTING_PAGE,
				'hustle_page_' . self::SLIDEIN_WIZARD_PAGE,
				'hustle_page_' . self::EMBEDDED_LISTING_PAGE,
				'hustle_page_' . self::EMBEDDED_WIZARD_PAGE,
				'hustle_page_' . self::SOCIAL_SHARING_LISTING_PAGE,
				'hustle_page_' . self::SOCIAL_SHARING_WIZARD_PAGE,
				'hustle_page_' . self::INTEGRATIONS_PAGE,
				'hustle_page_' . self::ENTRIES_PAGE,
				'hustle_page_' . self::SETTINGS_PAGE,
			];
		}

		/**
		 * Get module type by page
		 *
		 * @param string $page
		 * @return string
		 */
		private function get_modyle_type_by_page() {
			$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
			switch ( $page ) {
				case self::POPUP_LISTING_PAGE:
					$module_type = Hustle_Model::POPUP_MODULE;
					break;

				case self::SLIDEIN_LISTING_PAGE:
					$module_type = Hustle_Model::SLIDEIN_MODULE;
					break;

				case self::EMBEDDED_LISTING_PAGE:
					$module_type = Hustle_Model::EMBEDDED_MODULE;
					break;

				case self::SOCIAL_SHARING_LISTING_PAGE:
					$module_type = Hustle_Model::SOCIAL_SHARING_MODULE;
					break;

				default:
					$module_type = '';
					break;
			}

			return $module_type;
		}

		/**
		 * Sets an user meta to prevent admin notice from showing up again after dismissed.
		 *
		 * @since 3.0.6
		 */
		public function dismiss_admin_notice() {
			$user_id = get_current_user_id();
			$notice = filter_input( INPUT_POST, 'dismissed_notice', FILTER_SANITIZE_STRING );

			$dismissed_notices = get_user_meta( $user_id, 'hustle_dismissed_admin_notices', true );
			$dismissed_notices = array_filter( explode( ',', (string) $dismissed_notices ) );

			if ( $notice && ! in_array( $notice, $dismissed_notices, true ) ) {
				$dismissed_notices[] = $notice;
				$to_store = implode( ',', $dismissed_notices );
				update_user_meta( $user_id, 'hustle_dismissed_admin_notices', $to_store );
			}

			wp_send_json_success();
		}

		/**
	 * Modify admin body class to our own advantage!
	 *
	 * @param $classes
	 * @return mixed
	 */
		public function admin_body_class( $classes ) {

			$sanitize_version = str_replace( '.', '-', HUSTLE_SUI_VERSION );
			$sui_body_class   = "sui-$sanitize_version";

			$screen = get_current_screen();

			$classes = '';

			// Do nothing if not a hustle page
			if ( strpos( $screen->base, '_page_hustle' ) === false ) {
				return $classes;
			}

			$classes .= $sui_body_class;

			return $classes;

		}

		/**
	 * Modify tinymce editor settings
	 *
	 * @param $settings
	 * @param $editor_id
	 */
		public function set_tinymce_settings( $settings, $editor_id ) {
			$settings['paste_as_text'] = 'true';

			return $settings;
		}

		/**
	 * Add Privacy Messages
	 *
	 * @since 3.0.6
	 */
		public function add_privacy_message() {
			if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
				$external_integrations_list = '';
				$external_integrations_privacy_url_list = '';
				$params = array(
				'external_integrations_list' => apply_filters( 'hustle_privacy_external_integrations_list', $external_integrations_list ),
				'external_integrations_privacy_url_list' => apply_filters( 'hustle_privacy_url_external_integrations_list', $external_integrations_privacy_url_list ),
				);
				// TODO: get the name from a variable instead
				$content = $this->_hustle->render( 'general/policy-text', $params, true );
				wp_add_privacy_policy_content( 'Hustle', wp_kses_post( $content ) );
			}
		}

		/**
	 * Adds custom links on plugin page
	 *
	 */
		public function add_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			static $plugin;

			if ( ! isset( $plugin ) ) {
				$plugin = Opt_In::$plugin_base_file; }

			if ( $plugin === $plugin_file ) {
				if ( is_network_admin() ) {
					$admin_url = network_admin_url( 'admin.php' );
				} else {
					$admin_url = admin_url( 'admin.php' );
				}
				$settings_url = add_query_arg( 'page', 'hustle_settings', $admin_url );
				$links = [
					'settings' => '<a href="'. $settings_url .'">' . esc_html__( 'Settings', 'hustle' ) . '</a>',
					'docs' => '<a href="https://premium.wpmudev.org/project/hustle/#wpmud-hg-project-documentation?utm_source=hustle&utm_medium=plugin&utm_campaign=hustle_pluginlist_docs" target="_blank">' . esc_html__( 'Docs', 'hustle' ) . '</a>',
				];

				// Upgrade link.
				if ( Opt_In_Utils::_is_free() ) {
					if ( ! lib3()->is_member() ) {
						$url = 'https://premium.wpmudev.org/?utm_source=hustle&utm_medium=plugin&utm_campaign=hustle_pluginlist_upgrade';
					} else {
						$url = lib3()->get_link( 'hustle', 'install_plugin', '' );
					}
					if ( is_network_admin() || ! is_multisite() ) {
						$links['upgrade'] = '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( __( 'Upgrade to Hustle Pro', 'hustle' ) ) . '" target="_blank" style="color: #8D00B1;">' . esc_html__( 'Upgrade', 'hustle' ) . '</a>';
					}
				} else {
					if ( ! lib3()->is_member() ) {
						$links['renew'] = '<a href="https://premium.wpmudev.org/?utm_source=hustle&utm_medium=plugin&utm_campaign=hustle_pluginlist_renew" target="_blank" style="color: #8D00B1;">' . esc_html__( 'Renew Membership', 'hustle' ) . '</a>';
					}
				}

				// Display only on site's plugins page, not network's.
				if ( current_user_can( 'activate_plugins' ) && ( ! is_network_admin() || ! is_multisite() ) ) {

					$migration      = Hustle_Migration::get_instance();
					$has_404_backup = $migration->migration_410->is_backup_created();

					// Add a "Rollback to 404" link if we have its backup.
					if ( $has_404_backup ) {
						$args    = [
							'page'                => self::SETTINGS_PAGE,
							'404-downgrade-modal' => 'true',
						];
						$url     = add_query_arg( $args, 'admin.php' );
						$version = Opt_In_Utils::_is_free() ? 'v7.0.4' : 'v4.0.4';

						$links['rollback_404'] = '<a href="' . esc_url_raw( $url ) . '">' . sprintf( esc_html( 'Rollback to %s', 'hustle' ), $version ) . '</a>';
					}
					$actions = array_merge( $links, $actions );
				}
			}

			return $actions;
		}

		/**
		 * Links next to version number
		 *
		 * @param array $plugin_meta
		 * @param string $plugin_file
		 * @return array
		 */
		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( Opt_In::$plugin_base_file === $plugin_file ) {
				if ( Opt_In_Utils::_is_free() ) {
					$row_meta = array(
						'rate' => '<a href="https://wordpress.org/support/plugin/wordpress-popup/reviews/#new-post" target="_blank">' . esc_html__( 'Rate Hustle', 'hustle' ) . '</a>',
						'support' => '<a href="https://wordpress.org/support/plugin/wordpress-popup/" target="_blank">' . esc_html__( 'Support', 'hustle' ) . '</a>',
					);
				} else {
					$row_meta = array(
						'support' => '<a href="https://premium.wpmudev.org/hub/support/#wpmud-chat-pre-survey-modal" target="_blank">' . esc_html__( 'Premium Support', 'hustle' ) . '</a>',
					);
				}

				$row_meta['roadmap'] = '<a href="https://premium.wpmudev.org/roadmap/" target="_blank">' . esc_html__( 'Roadmap', 'hustle' ) . '</a>';

				$plugin_meta = array_merge( $plugin_meta, $row_meta );
			}

			return $plugin_meta;
		}

		/**
	 * Displays an admin notice when the user is an active member and doesn't have Hustle Pro installed
	 *
	 * @since 3.0.6
	 */
		public function show_hustle_pro_available_notice() {
			// Show the notice only to super admins who are members.
			if ( ! is_super_admin() || ! lib3()->is_member() ) {
				return;
			}

			// The notice was already dismissed.
			$dismissed_notices = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'hustle_dismissed_admin_notices', true ) ) );
			if ( in_array( 'hustle_pro_is_available', $dismissed_notices, true ) ) {
				return;
			}

			$link = lib3()->html->element( array(
				'type' => 'html_link',
				'value' => esc_html__( 'Upgrade' ),
				'url' => esc_url( lib3()->get_link( 'hustle', 'install_plugin', '' ) ),
				'class' => 'button-primary',
			), true );

			$profile = get_option( 'wdp_un_profile_data', '' );
			$name = ! empty( $profile ) ? $profile['profile']['name'] : 'Hey';

			$message = esc_html( sprintf( __( '%s, it appears you have an active WPMU DEV membership but haven\'t upgraded Hustle to the pro version. You won\'t lose an any settings upgrading, go for it!', 'hustle' ), $name ) );

			$html = '<div id="hustle-notice-pro-is-available" class="notice notice-info is-dismissible"><p>' . $message . '</p><p>' . $link . '</p></div>';

			echo $html; // WPCS: XSS ok.

		}

		/**
		 * Get the listing page by the module type.
		 *
		 * @since 4.0
		 *
		 * @param string $module_type
		 * @return string
		 */
		public static function get_listing_page_by_module_type( $module_type ) {

			switch ( $module_type ) {
				case Hustle_Module_Model::POPUP_MODULE:
					return self::POPUP_LISTING_PAGE;

				case Hustle_Module_Model::SLIDEIN_MODULE:
					return self::SLIDEIN_LISTING_PAGE;

				case Hustle_Module_Model::EMBEDDED_MODULE:
					return self::EMBEDDED_LISTING_PAGE;

				case Hustle_Module_Model::SOCIAL_SHARING_MODULE:
					return self::SOCIAL_SHARING_LISTING_PAGE;

				default:
					return self::POPUP_LISTING_PAGE;
			}
		}

		/**
		 * Get the wizard page by the module type.
		 *
		 * @since 4.0
		 *
		 * @param string $module_type
		 * @return string
		 */
		public static function get_wizard_page_by_module_type( $module_type ) {

			switch ( $module_type ) {
				case Hustle_Module_Model::POPUP_MODULE:
					return self::POPUP_WIZARD_PAGE;

				case Hustle_Module_Model::SLIDEIN_MODULE:
					return self::SLIDEIN_WIZARD_PAGE;

				case Hustle_Module_Model::EMBEDDED_MODULE:
					return self::EMBEDDED_WIZARD_PAGE;

				case Hustle_Module_Model::SOCIAL_SHARING_MODULE:
					return self::SOCIAL_SHARING_WIZARD_PAGE;

				default:
					return self::POPUP_WIZARD_PAGE;
			}
		}

		/**
		 * Check whether a new module of this type can be created.
		 * If it's free and there's already 3 modules of this type, then it's a nope.
		 *
		 * @since 4.0
		 *
		 * @param string $module_type
		 * @return boolean
		 */
		public static function can_create_new_module( $module_type ) {

			// If it's Pro, the sky's the limit.
			if ( ! Opt_In_Utils::_is_free() ) {
				return true;
			}

			// Check the Module's type is valid.
			if ( ! in_array( $module_type, Hustle_Module_Model::get_module_types(), true ) ) {
				return false;
			}

			$collection_args = array(
				'module_type' => $module_type,
				'count_only' => true,
			);
			$total_modules = Hustle_Module_Collection::instance()->get_all( null, $collection_args );

			// If we have less than 3 modules of this type, can create another one.
			if ( $total_modules >= 3 ) {
				return false;
			} else {
				return true;
			}
		}

		/**
		 * Geodirectory compatibility issues.
		 *
		 * @since 4.0.1
		 *
		 * @param array $options
		 * @param object $class WP_Super_Duper class instance
		 */
		public function geo_directory_compat( $options, $class ){
			remove_action( 'media_buttons', array( $class, 'shortcode_insert_button' ) );
		}

		public function show_provider_migration_notice() {
			//if( '1.0' === get_option( 'hustle_provider_constantcontact_version' ) ){
			//	$this->get_provider_migration_notice_html( 'constantcontact' );
			//}
			$aweber_instances = get_option( 'hustle_provider_aweber_settings' );
			if( ! empty( $aweber_instances ) ){
				foreach( $aweber_instances as $key => $instance  ){
					if( ! array_key_exists( 'access_oauth2_token', $instance ) || empty( $instance['access_oauth2_token'] ) ){
						$provider_data = array(
							'name' => $instance['name'],
							'id'   => $key
						);
						$this->get_provider_migration_notice_html( 'aweber', $provider_data );
					}
				}
			}
		}

		public function get_provider_migration_notice_html( $provider, $provider_data = array() ){
			$current_user = wp_get_current_user();

			$username = ! empty( $current_user->user_firstname ) ? $current_user->user_firstname : $current_user->user_login;

			$migrate_url = add_query_arg(
				array(
					'page' => self::INTEGRATIONS_PAGE,
					'show_provider_migration' => $provider,
					'integration_id' => isset( $provider_data['id'] ) ? $provider_data['id'] : '',
				),
				'admin.php'
			);
			$provided_id = isset( $provider_data['id'] ) ? $provider . '_' . $provider_data['id'] : $provider;
			?>
			<div id='<?php echo esc_attr( "hustle_migration_notice__$provided_id" ); ?>' class="hustle-notice notice notice-warning hustle-provider-notice <?php echo esc_attr( "hustle_migration_notice__$provider" ); ?>" data-name="<?php echo esc_attr( $provider ); ?>" data-id="<?php echo isset( $provider_data['id'] ) ? $provider_data['id']  : ''; ?>" style="display: none">
				<p>
				<?php $this->get_provider_migration_content( $provider, $username, $provider_data['name'] ); ?>
				</p>
				<p><a href="<?php echo esc_url( $migrate_url ); ?>" class="button-primary"><?php esc_html_e( 'Migrate Data', 'hustle' ); ?></a><a style="margin-left:20px; text-decoration: none;" href="#" class="dismiss-provider-migration-notice" data-name="<?php echo esc_attr( $provider ); ?>"><?php esc_html_e( 'Remind me later', 'hustle' ); ?></a></p>
			</div>
			<?php
		}

		public function get_provider_migration_content( $provider, $username = '', $identifier = '' ) {
			switch ( $provider ) {
				case 'constantcontact':
					$msg = sprintf( esc_html__( "Hey %s, we have updated our Constant Contact integration to support the latest v3.0 API. Since you are connected to the old API version, we recommend you to migrate your integration to the latest API version as we'll cease to support the deprecated API at some point.", 'hustle' ), $username );
					break;
				case 'infusionsoft':
					$msg = sprintf( esc_html__( "Hey %s, we have updated our InfusionSoft integration to support the latest REST API. Since you are connected to the old API version, we recommend you to migrate your integration to the latest API version as we'll cease to support the deprecated API at some point.", 'hustle' ), $username );
					break;
				case 'aweber':
					$msg = sprintf( esc_html__( "Hey %1\$s, we have updated our AWeber integration to support the oAuth 2.0. Since you are connected via oAuth 1.0, we recommend you to migrate your %2\$s integration to the latest authorization method as we'll cease to support the deprecated oAuth method at some point.", 'hustle' ), $username, $identifier );
					break;

				default:
					$msg = '';
					break;
			}

			echo esc_html( $msg );
		}


	}

endif;
