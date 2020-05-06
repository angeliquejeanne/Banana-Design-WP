<?php
/**
 * Class Hustle_Settings_Page
 *
 */
class Hustle_Settings_Page extends Hustle_Admin_Page_Abstract {

	/**
	 * Key of the Hustle's settings in wp_options.
	 * @since 4.0
	 */
	const SETTINGS_OPTION_KEY = 'hustle_settings';

	const DISMISSED_USER_META = 'hustle_dismissed_notifications';

	public function init() {

		$this->page = 'hustle_settings';

		$this->page_title = __( 'Hustle Settings', 'hustle' );

		$this->page_menu_title = __( 'Settings', 'hustle' );

		$this->page_capability = 'hustle_edit_settings';

		$this->page_template_path = 'admin/settings';

		/**
		 * Add visual settings classes
		 */
		add_filter( 'hustle_sui_wrap_class', array( $this, 'sui_wrap_class' ) );
	}

	/**
	 * Actions to be performed on Settings page.
	 *
	 * @since 4.1.0
	 */
	public function run_action_on_page_load() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_hui_scripts' ] );

		$restore = filter_input( INPUT_GET, 'hustle-restore-40x', FILTER_VALIDATE_BOOLEAN );
		if ( $restore ) {
			$this->trigger_restore_40x_visibility();
		}
	}

	/**
	 * Enqueue HUI scripts
	 * Used to render the recaptcha.
	 *
	 * @since 4.1.0
	 */
	public function enqueue_hui_scripts() {

		// Register Hustle UI functions.
		wp_enqueue_script(
			'hui_scripts',
			Opt_In::$plugin_url . 'assets/hustle-ui/js/hustle-ui.min.js',
			array( 'jquery' ),
			Opt_In::VERSION,
			true
		);
	}

	public function get_page_template_args() {
		$current_user     = wp_get_current_user();
		$general_settings = Hustle_Settings_Admin::get_general_settings();
		$migration        = Hustle_Migration::get_instance();

		return array(
			'user_name'               => ucfirst( $current_user->display_name ),
			'email_name'              => $general_settings['sender_email_name'],
			'email_address'           => $general_settings['sender_email_address'],
			'unsubscription_messages' => Hustle_Settings_Admin::get_unsubscribe_messages(),
			'unsubscription_email'    => Hustle_Settings_Admin::get_unsubscribe_email_settings(),
			'hustle_settings'         => Hustle_Settings_Admin::get_hustle_settings(),
			'section'                 => Hustle_Module_Admin::get_current_section( 'general' ),
			'has_40x_backup'          => $migration->migration_410->is_backup_created(),
		);
	}

	/**
	 * Add data to the current json array.
	 *
	 * @since 4.0.4
	 *
	 * @param array $current_array Registered variables.
	 * @return array
	 */
	public function register_current_json( $current_array ) {

		// Error messages for 4.0.x restoring.
		$current_array['messages']['restricted_access']  = __( "You can't perform this action", 'hustle' );
		$current_array['messages']['restore_40x_failed'] = __( "The restore failed. It could be that there's no data to restore. Please check the logs.", 'hustle' );

		$saved_id = filter_input( INPUT_GET, 'saved-id', FILTER_SANITIZE_STRING );
		if ( $saved_id ) {

			$saved_palettes = Hustle_Module_Model::get_all_palettes_slug_and_name();
			if ( ! empty( $saved_palettes[ $saved_id ] ) ) {

				$saved_name = '<span style="color:#333;"><strong>' . $saved_palettes[ $saved_id ] . '</strong></span>';
				/* translators: %s: palette name */
				$current_array['messages']['palette_saved'] = sprintf( __( '%s - Palette saved successfully.', 'hustle' ), $saved_name );
			}
		}

		$deleted_name = filter_input( INPUT_GET, 'deleted-name', FILTER_SANITIZE_STRING );
		if ( $deleted_name ) {

			$deleted_name = '<span style="color:#333;"><strong>' . $deleted_name . '</strong></span>';
			/* translators: %s: palette name */
			$current_array['messages']['palette_deleted'] = sprintf( __( '%s - Palette deleted successfully.', 'hustle' ), $deleted_name );
		}

		$palettes = array();
		$args     = array( 'except_types' => array( Hustle_Module_Model::SOCIAL_SHARING_MODULE ) );
		$modules  = Hustle_Module_Collection::instance()->get_all( null, $args );

		foreach ( $modules as $module ) {
			$palettes[ $module->module_type ][ $module->module_id ] = $module->module_name;
		}
		$current_array['current']                        = $palettes;
		$current_array['current']['save_settings_nonce'] = wp_create_nonce( 'hustle_settings_save' );

		return $current_array;
	}

	/**
	 * Triggers the restore if the request is valid.
	 *
	 * Checks for nonce and capabilities before triggering the restore.
	 * It also handles the response of the restore.
	 *
	 * @since 4.1.0
	 */
	private function trigger_restore_40x_visibility() {

		$error_base_args = [
			'page'        => $this->page,
			'section'     => 'data',
			'show-notice' => 'error',
		];

		try {

			// Checking nonce and capabilities.
			$nonce        = filter_input( INPUT_GET, 'nonce', FILTER_SANITIZE_STRING );
			$valid_nonce  = wp_verify_nonce( $nonce, 'hustle-restore-40x-visibility' );
			$user_allowed = current_user_can( 'hustle_edit_settings' );

			if ( ! $valid_nonce || ! $user_allowed ) {
				$error_base_args['notice'] = 'restricted_access';

				$url = add_query_arg( $error_base_args, 'admin.php' );
				throw new Exception( $url );
			}

			// Do the restore.
			$migration_401 = new Hustle_410_Migration();
			$success       = $migration_401->restore();

			// The restoring failed. Display a message and abort.
			if ( ! $success ) {

				// This could be because there was nothing to restore, or the restoring per se failed.
				$error_base_args['notice'] = 'restore_40x_failed';

				$url = add_query_arg( $error_base_args, 'admin.php' );
				throw new Exception( $url );
			}

			// All good. Deactivate the plugin.
			deactivate_plugins( Opt_In::$plugin_base_file );

			// Redirecting to site's plugins pages. In MU, non-super admins can't install plugins.
			throw new Exception( admin_url( 'plugins.php' ) );

		} catch ( Exception $e ) {

			$url = esc_url_raw( $e->getMessage() );
			if ( wp_safe_redirect( $url ) ) {
				exit;
			}
		}
	}

		/**
		 * Handle SUI wrapper container classes.
		 *
		 * @since 4.0.06
		 */
    public function sui_wrap_class( $classes ) {
        if ( is_string( $classes ) ) {
            $classes = array( $classes );
        }
        if ( ! is_array( $classes ) ) {
            $classes = array();
        }
        $classes[] = 'sui-wrap';
        $classes[] = 'sui-wrap-hustle';
        /**
         * Add high contrast mode.
         */
        $accessibility = Hustle_Settings_Admin::get_hustle_settings( 'accessibility' );
        $is_high_contrast_mode = !empty( $accessibility['accessibility_color'] );
        if ( $is_high_contrast_mode ) {
            $classes[] = 'sui-color-accessible';
        }
        /**
         * Set hide branding
         *
         * @since 4.0.0
         */
        $hide_branding = apply_filters( 'wpmudev_branding_hide_branding', false );
        if ( $hide_branding ) {
            $classes[] = 'no-hustle';
        }
        /**
         * hero image
         *
         * @since 4.0.0
         */
        $image = apply_filters( 'wpmudev_branding_hero_image', 'hustle-default' );
        if ( empty( $image ) ) {
            $classes[] = 'no-hustle-hero';
        }
        return $classes;
    }
}
