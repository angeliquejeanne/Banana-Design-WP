<?php
if ( ! class_exists( 'Hustle_Admin_Page_Abstract' ) ) :
	/**
	 * Class Hustle_Admin_Page_Abstract
	 * @since 4.0.1
	 */
	abstract class Hustle_Admin_Page_Abstract {

		/**
		 * @var Opt_In
		 */
		protected $_hustle;

		protected $page;

		protected $page_template_path;

		protected $page_title;

		protected $page_menu_title;

		protected $page_capability;

		protected $page_edit;

		protected $page_edit_title;

		protected $page_edit_capability;

		protected $page_edit_template_path;

		/**
		 * The current page that's being requested.
		 * @since 4.0.2
		 * @var string|bool
		 */
		protected $current_page;

		/**
		 * Page slug.
		 * @since 4.0.0
		 */
		protected $page_slug;

		public function __construct( Opt_In $hustle ) {

			$this->_hustle = $hustle;

			$this->current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

			$this->init();

			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		}

		// Extend to setup things on construct.
		abstract protected function init();

		/**
		 * Register the js variables to be localized for this page.
		 * To be overridden.
		 *
		 * @since 4.0.4
		 *
		 * @param array $current_array The already registered js variables.
		 * @return array
		 */
		public function register_current_json( $current_array ) {
			return $current_array;
		}

		/**
		 * Register the admin menus.
		 * @since 4.0.1
		 */
		public function register_admin_menu() {

			$this->page_slug = add_submenu_page( 'hustle', $this->page_title, $this->page_menu_title, $this->page_capability, $this->page,  array( $this, 'render_main_page' ) );

			add_action( 'load-' . $this->page_slug, array( $this, 'current_page_loaded' ) );
		}

		/**
		 * Render the main page.
		 * @since 4.0.1
		 */
		public function render_main_page() {

			$template_args = $this->get_page_template_args();
			$this->_hustle->render( $this->page_template_path, $template_args );
		}

		/**
		 * Perform actions during the 'load-{page}' hook.
		 *
		 * @since 4.0.4
		 */
		public function current_page_loaded() {

			// Register variables for the js side only if this is the requested page.
			add_filter( 'hustle_optin_vars', array( $this, 'register_current_json' ) );

			// Shared plugins notice.
			//if ( Opt_In_Utils::_is_free() ) {
			//	include_once Opt_In::$plugin_path . 'lib/plugin-notice/notice.php';
			//	do_action( 'wpmudev-recommended-plugins-register-notice', Opt_In::$plugin_base_file, __( 'Hustle', 'hustle' ), Hustle_Module_Admin::get_hustle_admin_pages() );
			//}

			$this->run_action_on_page_load();
		}

		/**
		 * Method called when the action 'load-' . $this->page_slug runs.
		 *
		 * @since 4.0.0
		 */
		public function run_action_on_page_load() {}

		/**
		 * Print forminator scripts for preview.
		 * Used by Dashboard, Wizards, and Listings.
		 *
		 * @since 4.0.1
		 */
		public function maybe_print_forminator_scripts() {

			// Add Forminator's front styles and scripts for preview.
			if ( defined( 'FORMINATOR_VERSION' ) ) {
				forminator_print_front_styles( FORMINATOR_VERSION );
				forminator_print_front_scripts( FORMINATOR_VERSION );

			}
		}
	}

endif;
