<?php

/**
 * Class Hustle_Module_Page
 */
abstract class Hustle_Module_Page_Abstract extends Hustle_Admin_Page_Abstract {

	/**
	 * Current module. Only set on wizards when the module exists.
	 * @since 4.0.3
	 * @var integer
	 */
	protected $module = false;

	protected function init() {

		$this->set_page_properties();

		$this->page_menu_title = $this->page_title;

		$this->page = Hustle_Module_Admin::get_listing_page_by_module_type( $this->module_type );

		$this->page_capability = 'hustle_edit_module';

		$this->page_edit = Hustle_Module_Admin::get_wizard_page_by_module_type( $this->module_type );

		$this->page_edit_capability = 'hustle_edit_module';

		$this->page_edit_title = sprintf( esc_html__( 'New %s', 'hustle' ), Opt_In_Utils::get_module_type_display_name( $this->module_type ) );

		add_filter( 'submenu_file', array( $this, 'admin_submenu_file' ), 10, 2 );

		add_action( 'admin_head', array( $this, 'hide_unwanted_submenus' ) );

		// admin-menu-editor compatibility.
		add_action( 'admin_menu_editor-menu_replaced', array( $this, 'hide_unwanted_submenus' ) );

		// Actions to perform when the current page is the listing or the wizard page.
		if ( ! empty( $this->current_page ) && ( $this->current_page === $this->page || $this->current_page === $this->page_edit ) ) {
			$this->on_listing_and_wizard_actions();
		}

	}

	abstract protected function set_page_properties();

	/**
	 * Actions to be performed on Dashboard page.
	 *
	 * @since 4.0.4
	 */
	protected function on_listing_and_wizard_actions() {

		if ( $this->page_edit === $this->current_page ) {
			$this->on_wizard_only_actions();
		}

		// For preview.
		add_action( 'admin_enqueue_scripts', [ 'Hustle_Module_Front', 'add_hui_scripts' ] );
		add_action( 'admin_footer', [ $this, 'maybe_print_forminator_scripts' ] );
	}

	/**
	 * Method called when the action 'load-' . $this->page_slug runs.
	 * That is, Listing page only.
	 *
	 * @since 4.2.0
	 */
	public function run_action_on_page_load() {
		Hustle_Modules_Common_Admin::export();
	}

	/**
	 * Set the current module only if the current page is wizard and the module is valid.
	 *
	 * @since 4.0.3
	 */
	private function on_wizard_only_actions() {

		// Set the current module on Wizards, abort if invalid.
		$module_id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
		$module    = Hustle_Module_Collection::instance()->return_model_from_id( $module_id );

		if ( is_wp_error( $module ) ) {
			// Redirect asap.
			add_action( 'admin_init', [ $this, 'redirect_module_not_found' ] );
			return;
		}

		$this->module = $module;

		// Register variables for the js side only if this is the requested page.
		add_filter( 'hustle_optin_vars', array( $this, 'register_current_json' ) );
	}

	public function register_admin_menu() {

		parent::register_admin_menu();

		add_submenu_page( 'hustle', $this->page_edit_title, $this->page_edit_title, $this->page_edit_capability, $this->page_edit,  array( $this, 'render_edit_page' ) );
	}

	/**
	 * Get the arguments used when rendering the main page.
	 *
	 * @since 4.0.1
	 * @return array
	 */
	protected function get_page_template_args() {

		$entries_per_page = Hustle_Settings_Admin::get_per_page( 'module' );

		$capability = array(
			'hustle_create' => current_user_can( 'hustle_create' ),
			'hustle_access_emails' => current_user_can( 'hustle_access_emails' ),
		);

		//don't use filter_input() here, because of see Hustle_Module_Admin::maybe_remove_paged function
		$paged = ! empty( $_GET['paged'] ) ? (int) $_GET['paged'] : 1; // phpcs:ignore

		$modules = Hustle_Module_Collection::instance()->get_all( null, array(
				'module_type' => $this->module_type,
				'page' => $paged,
				'filter' => [ 'can_edit' => true ],
			), $entries_per_page );

		$total_modules = Hustle_Module_Collection::instance()->get_all( null, array(
				'module_type' => $this->module_type,
				'count_only' => true
			) );

		$active_modules = Hustle_Module_Collection::instance()->get_all( true, array(
				'module_type' => $this->module_type,
				'count_only' => true
			) );

		return array(
			'total' => $total_modules,
			'active' => $active_modules,
			'modules' => $modules,
			'is_free' => Opt_In_Utils::_is_free(),
			'capability'  => $capability,
			'page' => $this->page,
			'paged' => $paged,
			'entries_per_page' => $entries_per_page,
			'message' => filter_input( INPUT_GET, 'message', FILTER_SANITIZE_STRING ),
			'sui' => Opt_In::get_sui_summary_config( 'sui-summary-sm' ),
		);
	}

	/**
	 * Hide module's edit pages from the submenu on dashboard.
	 * @since 4.0.1
	 */
	public function hide_unwanted_submenus() {
		remove_submenu_page( 'hustle', $this->page_edit );
	}

	/**
	 * Highlight submenu's parent on admin page.
	 *
	 * @since 4.0.1
	 *
	 * @param $submenu_file
	 * @param $parent_file
	 *
	 * @return string
	 */
	public function admin_submenu_file( $submenu_file, $parent_file ) {
		global $plugin_page;

		if ( 'hustle' !== $parent_file ) {
			return $submenu_file;
		}

		if ( $this->page_edit === $plugin_page ) {
			$submenu_file = $this->page;
		}

		return $submenu_file;
	}

	/**
	 * Redirect to the listing page when in wizard and the module wasn't found.
	 *
	 * @since 4.0.0
	 */
	public function redirect_module_not_found() {

		// We're on wizard, but the current module isn't valid. Aborting.
		$url = add_query_arg([
			'page'    => $this->page,
			'message' => 'module-does-not-exists',
		], 'admin.php' );

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Add data to the current json array.
	 *
	 * @since 4.0.1
	 *
	 * @param array $current_array Currently registered data.
	 * @return array
	 */
	public function register_current_json( $current_array ) {

		// Wizard page only.
		if ( $this->module ) {

			$data         = $this->module->get_data();
			$module_metas = $this->module->get_module_metas_as_array();

			$current_array = $this->register_visibility_conditions_js_vars( $current_array );
			$current_array = $this->register_fields_js_vars( $current_array );

			$current_array['current'] = array_merge( $module_metas, array(
				'listing_page' => $this->page,
				'wizard_page'  => $this->page_edit,
				'section'      => Hustle_Module_Admin::get_current_section(),
				'data'         => $data,
				'shortcode_id' => $this->module->get_shortcode_id(),
			) );

			$current_array['messages']['settings'] = array(
				'popup'           => __( 'popup', 'hustle' ),
				'slide_in'        => __( 'slide in', 'hustle' ),
				'after_content'   => __( 'after content', 'hustle' ),
				'floating_social' => __( 'floating social', 'hustle' ),
			);

			// Listing page only.
		} elseif ( $this->page === $this->current_page ) {

			$current_array['current'] = array(
				'wizard_page' => $this->page_edit,
				'module_type' => $this->module_type,
			);
		}

		// Both Wizard and Listing pages.
		$current_array['messages']['days_and_months'] = [
			'days_full'    => Opt_In_Utils::get_week_days(),
			'days_short'   => Opt_In_Utils::get_week_days( 'short' ),
			'days_min'     => Opt_In_Utils::get_week_days( 'min' ),
			'months_full'  => Opt_In_Utils::get_months(),
			'months_short' => Opt_In_Utils::get_months( 'short' ),
		];

		return $current_array;
	}

	/**
	 * Include the visibility conditions variables required in js side.
	 * These used to be registered in Hustle_Module_Admin before 4.0.3.
	 *
	 * @since 4.0.3
	 *
	 * @param array $vars
	 * @return array
	 */
	protected function register_visibility_conditions_js_vars( $vars ) {

		$post_ids = array();
		$page_ids = array();
		$tag_ids = array();
		$cat_ids = array();
		$wc_cat_ids = array();
		$wc_tag_ids = array();
		$tags = array();
		$cats = array();
		$wc_cats = array();
		$wc_tags = array();

		$module = Hustle_Module_Model::instance()->get( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ) );
		if ( ! is_wp_error( $module ) ) {
			$settings = $module->get_visibility()->to_array();

			$post_ids = $this->get_conditions_ids( $settings, 'posts' );
			$page_ids = $this->get_conditions_ids( $settings, 'pages' );
			$tag_ids = $this->get_conditions_ids( $settings, 'tags' );
			$cat_ids = $this->get_conditions_ids( $settings, 'categories' );
			if ( Opt_In_Utils::is_woocommerce_active() ) {
				$wc_cat_ids = $this->get_conditions_ids( $settings, 'wc_categories' );
				$wc_tag_ids = $this->get_conditions_ids( $settings, 'wc_tags' );
			}
		}


		if ( $tag_ids ) {
			$tags = array_map( array( $this, 'terms_to_select2_data' ), get_categories( array(
				'hide_empty' => false,
				'include' => $tag_ids,
				'taxonomy' => 'post_tag',
			)));
		}

		if ( $cat_ids ) {
			$cats = array_map( array( $this, 'terms_to_select2_data' ), get_categories( array(
				'include' => $cat_ids,
				'hide_empty' => false,
			)));
		}

		if ( $wc_cat_ids ) {
			$wc_cats = array_map( array( $this, 'terms_to_select2_data' ), get_categories( array(
				'include' => $wc_cat_ids,
				'hide_empty' => false,
				'taxonomy' => 'product_cat',
			)));
		}

		if ( $wc_tag_ids ) {
			$wc_tags = array_map( array( $this, 'terms_to_select2_data' ), get_categories( array(
				'include' => $wc_tag_ids,
				'hide_empty' => false,
				'taxonomy' => 'product_tag',
			)));
		}

		$posts = Opt_In_Utils::get_select2_data( 'post', $post_ids );

		$pages = Opt_In_Utils::get_select2_data( 'page', $page_ids );

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

			$cpt_ids = $this->get_conditions_ids( $settings, $cpt->label );

			$cpt_array['name'] = $cpt->name;
			$cpt_array['label'] = $cpt->label;
			$cpt_array['data'] = Opt_In_Utils::get_select2_data( $cpt->name, $cpt_ids );

			$post_types[ $cpt->name ] = $cpt_array;
		}

		$vars['cats'] = $cats;
		$vars['wc_cats'] = $wc_cats;
		$vars['wc_tags'] = $wc_tags;
		$vars['tags'] = $tags;
		$vars['posts'] = $posts;
		$vars['post_types'] = Opt_In_Utils::get_post_types();
		$vars['pages'] = $pages;

		$vars['countries'] = $this->_hustle->get_countries();
		$vars['roles'] = Opt_In_Utils::get_user_roles();
		$vars['templates'] = Opt_In_Utils::hustle_get_page_templates();

		//module error message
		$vars['messages']['sshare_module_error'] = __( "Couldn't save your module settings because there were some errors on {page} tab(s). Please fix those errors and try again.", 'hustle' );

		$vars['messages']['days_and_months'] = [
			'days_full'    => Opt_In_Utils::get_week_days(),
			'days_short'   => Opt_In_Utils::get_week_days( 'short' ),
			'days_min'     => Opt_In_Utils::get_week_days( 'min' ),
			'months_full'  => Opt_In_Utils::get_months(),
			'months_short' => Opt_In_Utils::get_months( 'short' ),
		];

		// Visibility conditions titles, labels and bodies.
		$vars['messages']['conditions'] = array(
			'visitor_logged_in'           => __( "Logged in status", 'hustle' ),
			'shown_less_than'             => __( 'Number of times visitor has seen', 'hustle' ),
			'only_on_mobile'              => __( "Visitor's Device", 'hustle' ),
			'from_specific_ref'           => __( 'Referrer', 'hustle' ),
			'from_search_engine'          => __( 'Source of Arrival', 'hustle' ),
			'on_specific_url'             => __( 'Specific URL', 'hustle' ),
			'on_specific_browser'         => __( 'Visitor\'s Browser', 'hustle' ),
			'visitor_has_never_commented' => __( 'Visitor Commented Before', 'hustle' ),
			'not_in_a_country'            => __( "Visitor's Country", 'hustle' ),
			'on_specific_roles'           => __( "User Roles", 'hustle' ),
			'wp_conditions'               => __( "Static Pages", 'hustle' ),
			'archive_pages'               => __( "Archive Pages", 'hustle' ),
			'on_specific_templates'       => __( "Page Templates", 'hustle' ),
			'user_registration'           => __( 'After Registration', 'hustle' ),
			'page_404'                    => __( '404 page', 'hustle' ),
			'posts'                       => __( 'Posts', 'hustle' ),
			'pages'                       => __( 'Pages', 'hustle' ),
			'categories'                  => __( 'Categories', 'hustle' ),
			'tags'                        => __( 'Tags', 'hustle' ),
			'wc_pages'                    => __( 'WooCommerce Pages', 'hustle' ),
			'wc_categories'               => __( 'WooCommerce Categories', 'hustle' ),
			'wc_tags'                     => __( 'WooCommerce Tags', 'hustle' ),
			'wc_archive_pages'            => __( "WooCommerce Archives", 'hustle' ),
			'wc_static_pages'             => __( "WooCommerce Static Pages", 'hustle' ),
		);

		$vars['messages']['condition_labels'] = array(
			'mobile_only' => __( 'Mobile only', 'hustle' ),
			'desktop_only' => __( 'Desktop only', 'hustle' ),
			'any_conditions' => __( '{number} condition(s)', 'hustle' ),
			'number_views' => '< {number}',
			'number_views_more' => '> {number}',
			'any' => __( 'Any', 'hustle' ),
			'all' => __( 'All', 'hustle' ),
			'no' => __( 'No', 'hustle' ),
			'none' => __( 'None', 'hustle' ),
			'true' => __( 'True', 'hustle' ),
			'false' => __( 'False', 'hustle' ),
			'logged_in' => __( 'Logged in', 'hustle' ),
			'logged_out' => __( 'Logged out', 'hustle' ),
			'only_these' => __( 'Only {number}', 'hustle' ),
			'except_these' => __( 'All except {number}', 'hustle' ),
			'reg_date' => __( 'Day {number} ', 'hustle' ),
			'immediately' => __( 'Immediately', 'hustle' ),
			'forever' => __( 'Forever', 'hustle' ),
		);

		return $vars;
	}

	/**
	 * Include the form fields variables required in js side.
	 * These used to be registered in Hustle_Module_Admin before 4.0.3.
	 *
	 * @since 4.0.3
	 *
	 * @param array $vars
	 * @return array
	 */
	protected function register_fields_js_vars( $vars ) {

		$vars['messages']['form_fields'] = array(
			'errors' => array(
				'no_fileds_info' => '<div class="sui-notice"><p>' . __( 'You don\'t have any {field_type} field in your opt-in form.', 'hustle' ) . '</p></div>',
				'custom_field_not_supported' => __( 'Custom fields are not supported by the active provider', 'hustle' ),
			),
			'label' => array(
				'placeholder'            => __( 'Enter placeholder here', 'hustle' ),
				'name_label'             => __( 'Name', 'hustle' ),
				'name_placeholder'       => __( 'E.g. John', 'hustle' ),
				'email_label'            => __( 'Email Address', 'hustle' ),
				'enail_placeholder'      => __( 'E.g. john@doe.com', 'hustle' ),
				'phone_label'            => __( 'Phone Number', 'hustle' ),
				'phone_placeholder'      => __( 'E.g. +1 300 400 500', 'hustle' ),
				'address_label'          => __( 'Address', 'hustle' ),
				'address_placeholder'    => '',
				'hidden_label'           => __( 'Hidden Field', 'hustle' ),
				'hidden_placeholder'     => '',
				'url_label'              => __( 'Website', 'hustle' ),
				'url_placeholder'        => __( 'E.g. https://example.com', 'hustle' ),
				'text_label'             => __( 'Text', 'hustle' ),
				'text_placeholder'       => __( 'E.g. Enter your nick name', 'hustle' ),
				'number_label'           => __( 'Number', 'hustle' ),
				'number_placeholder'     => __( 'E.g. 1', 'hustle' ),
				'datepicker_label'       => __( 'Date', 'hustle' ),
				'datepicker_placeholder' => __( 'Choose date', 'hustle' ),
				'timepicker_label'       => __( 'Time', 'hustle' ),
				'timepicker_placeholder' => '',
				'recaptcha_label'        => 'reCAPTCHA',
				'recaptcha_placeholder'  => '',
				'gdpr_label'             => __( 'GDPR', 'hustle' ),
			),
			'recaptcha_badge_replacement' => sprintf(
				/* translators: 1: closing 'a' tag, 2: opening privacy 'a' tag, 3: opening terms 'a' tag */
				esc_html__( 'This site is protected by reCAPTCHA and the Google %2$sPrivacy Policy%1$s and %3$sTerms of Service%1$s apply.', 'hustle' ),
				'</a>',
				'<a href="https://policies.google.com/privacy" target="_blank">',
				'<a href="https://policies.google.com/terms" target="_blank">'
			),
			'recaptcha_error_message'     => esc_html__( 'reCAPTCHA verification failed. Please try again.', 'hustle' ),
			'gdpr_message'                => sprintf( __( 'I\'ve read and accept the %1$sterms & conditions%2$s', 'hustle' ), '<a href="#">', '</a>' ),
		);

		return $vars;
	}

	/**
	 *
	 * @since 3.0.7
	 * @since 4.0.3 moved from Hustle_Modules_Admin to here.
	 *
	 * @param array $settings Display settings
	 * @param string $type posts|pages|tags|categories|{cpt}
	 * @return array
	 */
	private function get_conditions_ids( $settings, $type ) {
		$ids = array();
		if ( ! empty( $settings['conditions'] ) ) {
			foreach ( $settings['conditions'] as $conditions ) {
				if ( ! empty( $conditions[ $type ] )
					&& ( ! empty( $conditions[ $type ][ $type ] )
					|| ! empty( $conditions[ $type ]['selected_cpts'] ) ) ) {
					$new_ids = ! empty( $conditions[ $type ][ $type ] )
					? $conditions[ $type ][ $type ]
					: $conditions[ $type ]['selected_cpts'];

					$ids = array_merge( $ids, $new_ids );
				}
			}
		}

		return array_unique( $ids );
	}

	/**
	 * Converts term object to usable object for select2
	 * @since 4.0.3 moved from Hustle_Modules_Admin to here.
	 * @param $term Term
	 * @return stdClass
	 */
	public static function terms_to_select2_data( $term ) {
		$obj = new stdClass();
		$obj->id = $term->term_id;
		$obj->text = $term->name;
		return $obj;
	}

	/**
	 * Render the module's wizard page.
	 * @since 4.0.1
	 */
	public function render_edit_page() {

		$template_args = $this->get_page_edit_template_args();
		$allowed = Opt_In_Utils::is_user_allowed( 'hustle_edit_module', $template_args['module_id'] );
		if ( ! $allowed  ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.' ), 403 );
		}

		if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE !== $this->module_type ) {
			wp_enqueue_editor();
		}

		$template_args = $this->get_page_edit_template_args();
		$this->_hustle->render( $this->page_edit_template_path, $template_args );
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
			'section' => ( ! $current_section ) ? 'content' : $current_section,
			'module_id' => $this->module->module_id,
			'module' => $this->module,
			'is_active' => (bool) $this->module->active,
			'is_optin' => ( 'optin' === $this->module->module_mode ),
		);
	}
}
