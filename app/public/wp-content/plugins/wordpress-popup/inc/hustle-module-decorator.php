<?php

/**
 * Class Hustle_Module_Decorator
 *
 * @property string $mail_service_label
 */
class Hustle_Module_Decorator extends Opt_In {

	private $_module;

	private $design;

	public function __construct( Hustle_Module_Model $module ) {
		$this->_module = $module;
	}

	/**
	 * Implements getter magic method
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $field
	 * @return mixed
	 */
	public function __get( $field ) {

		if ( method_exists( $this, 'get_' . $field ) ) {
			return $this->{ 'get_' . $field }();
		}

		if ( ! empty( $this->_module ) && isset( $this->_module->{$field} ) ) {
			return $this->_module->{$field};
		}

	}

	public function get_module_styles( $module_type, $is_preview = false ) {

		$this->design = ! $is_preview ? $this->_module->get_design()->to_array() : (array) $this->_module->design;

		$styles = '';

		if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE !== $module_type ) {

			$use_vanilla = '1' === $this->design['use_vanilla'];
			if ( ! $use_vanilla ) {
				$styles = $this->_get_common_styles( $is_preview );
			}

			$styles .= $this->_get_custom_css();

		} else {
			$styles = $this->_get_social_sharing_styles( $is_preview );

		}

		return $styles;
	}

	private function _get_common_styles( $is_preview = false ) {

		$prefix = '.hustle-ui.module_id_' . $this->_module->module_id . ' ';

		$styles = '';
		$stylable_elements = $this->_get_popup_stylable_elements();

		if ( ! $is_preview ) {
			$content = $this->_module->get_content()->to_array();
			$emails = $this->_module->get_emails()->to_array();
		} else {
			$content = (array) $this->_module->content;
			$emails = (array) $this->_module->emails;
		}

		$design			= $this->design;
		$layout_style      = $design['style'];
		$form_layout       = $design['form_layout'];
		$is_optin		   = ( 'optin' === $this->_module->module_mode );

		// COMMON STYLES
		$colors = $design;

		/**
		 * Implement styles
		 *
		 * @since 1.0
		 */
		if ( isset( $design['color_palette'] ) ) {

			$palette = Hustle_Module_Model::get_palette_array( $design['color_palette'] );

			if ( '1' === $design['customize_colors'] || empty( $palette ) ) {
				$colors = array_merge( $palette, $colors );
			} else {
				$colors = $palette;
			}
		}

		/**
		 * Form Design
		 * Works for opt-in modules only.
		 *
		 * @since 4.0
		 */

		// Form fields style
		if ( $is_optin ) {

			if ( isset( $design['form_fields_style'] ) && 'outlined' === $design['form_fields_style'] ) {

				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['form_input'] . ', ';
				$styles .= $prefix . $stylable_elements['form_radio'] . ', ';
				$styles .= $prefix . $stylable_elements['form_checkbox'] . ' {';
					$styles .= 'border-width: ' . $design['form_fields_border_weight'] . 'px;';
					$styles .= 'border-style: ' . $design['form_fields_border_type'] . ';';
				$styles .= '}';

				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['form_input'] . ', ';
				$styles .= $prefix . $stylable_elements['form_checkbox'] . ' {';
					$styles .= 'border-radius: ' . $design['form_fields_border_radius'] . 'px;';
					$styles .= '-moz-border-radius: ' . $design['form_fields_border_radius'] . 'px;';
					$styles .= '-webkit-border-radius: ' . $design['form_fields_border_radius'] . 'px;';
				$styles .= '}';

			} else {

				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['form_input'] . ', ';
				$styles .= $prefix . $stylable_elements['form_radio'] . ', ';
				$styles .= $prefix . $stylable_elements['form_checkbox'] . ' {';
					$styles .= 'border-width: 0;';
					$styles .= 'border-style: none;';
				$styles .= '}';

				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['form_input'] . ', ';
				$styles .= $prefix . $stylable_elements['form_checkbox'] . ' {';
					$styles .= 'border-radius: 0;';
					$styles .= '-moz-border-radius: 0;';
					$styles .= '-webkit-border-radius: 0;';
				$styles .= '}';

			}
		}

		// Submit button style
		if ( $is_optin ) {

			if ( isset( $design['button_style'] ) && 'outlined' === $design['button_style'] ) {

				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['button_submit'] . ' {';
					$styles .= 'border-width: ' . $design['button_border_weight'] . 'px;';
					$styles .= 'border-style: ' . $design['button_border_type'] . ';';
					$styles .= 'border-radius: ' . $design['button_border_radius'] . 'px;';
					$styles .= '-moz-border-radius: ' . $design['button_border_radius'] . 'px;';
					$styles .= '-webkit-border-radius: ' . $design['button_border_radius'] . 'px;';
					$styles .= 'line-height: ' . (32 - ($design['button_border_weight']) * 2) . 'px;';
				$styles .= '}';

			} else {

				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['button_submit'] . ' {';
					$styles .= 'border-width: 0;';
					$styles .= 'border-style: none;';
					$styles .= 'border-radius: 0;';
					$styles .= '-moz-border-radius: 0;';
					$styles .= '-webkit-border-radius: 0;';
				$styles .= '}';

			}
		}

		// GDPR field style
		if ( $is_optin ) {

			if ( isset( $design['gdpr_checkbox_style'] ) && 'outlined' === $design['gdpr_checkbox_style'] ) {

				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['gdpr_checkbox'] . ' {';
					$styles .= 'border-width: ' . $design['gdpr_border_weight'] . 'px;';
					$styles .= 'border-style: ' . $design['gdpr_border_type'] . ';';
					$styles .= 'border-radius: ' . $design['gdpr_border_radius'] . 'px;';
					$styles .= '-moz-border-radius: ' . $design['gdpr_border_radius'] . 'px;';
					$styles .= '-webkit-border-radius: ' . $design['gdpr_border_radius'] . 'px;';
				$styles .= '}';

			} else {

				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['gdpr_checkbox'] . ' {';
					$styles .= 'border-width: 0;';
					$styles .= 'border-style: none;';
					$styles .= 'border-radius: 0;';
					$styles .= '-moz-border-radius: 0;';
					$styles .= '-webkit-border-radius: 0;';
				$styles .= '}';

			}
		}

		/**
		 * CTA Design
		 *
		 * @since 4.0
		 */
		if ( isset( $design['cta_style'] ) && 'outlined' === $design['cta_style'] ) {

			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ' {';
				$styles .= 'border-width: ' . $design['cta_border_weight'] . 'px;';
				$styles .= 'border-style: ' . $design['cta_border_type'] . ';';
				$styles .= 'border-radius: ' . $design['cta_border_radius'] . 'px;';
				$styles .= '-moz-border-radius: ' . $design['cta_border_radius'] . 'px;';
				$styles .= '-webkit-border-radius: ' . $design['cta_border_radius'] . 'px;';
				$styles .= 'line-height: ' . (32 - ($design['cta_border_weight']) * 2) . 'px;';
			$styles .= '}';

		} else {

			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ' {';
				$styles .= 'border-width: 0;';
				$styles .= 'border-style: none;';
				$styles .= 'border-radius: 0;';
				$styles .= '-moz-border-radius: 0;';
				$styles .= '-webkit-border-radius: 0;';
			$styles .= '}';

		}

		/**
		 * Colors Palette.
		 * Works for opt-in and informational modules.
		 *
		 * @since 4.0
		 */

		// ========================================|
		// 1. BASIC                                |
		// ========================================|

		// Main background
		if ( $is_optin ) {

			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_body'] . ' {';
				$styles .= 'background-color: ' . $colors['main_bg_color'];
			$styles .= '}';

		} else {

			if ( 'cabriolet' === $layout_style ) {
				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['layout_body'] . ' {';
					$styles .= 'background-color: ' . $colors['main_bg_color'];
				$styles .= '}';
			} else {
				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['layout'] . ' {';
					$styles .= 'background-color: ' . $colors['main_bg_color'];
				$styles .= '}';
			}
		}

		// Image container BG
		if ( '' !== $content['feature_image'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_image'] . ' {';
				$styles .= 'background-color: ' . $colors['image_container_bg'];
			$styles .= '}';
		}

		// Form area background
		if ( $is_optin ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_form'] . ' {';
				$styles .= 'background-color: ' . $colors['form_area_bg'];
			$styles .= '}';
		}

		// ========================================|
		// 2. CONTENT                              |
		// ========================================|

		// ****************************************
		// 2.1. DEFAULT

		// Title color
		if ( $is_optin ) {

			if ( '' !== $content['title'] ) {
				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['layout_title'] . ' {';
					$styles .= 'color: ' . $colors['title_color'];
				$styles .= '}';
			}
		} else {

			if ( '' !== $content['title'] ) {
				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['layout_title'] . ' {';
					$styles .= 'color: ' . $colors['title_color_alt'];
				$styles .= '}';
			}
		}

		// Subtitle color
		if ( $is_optin ) {

			if ( '' !== $content['sub_title'] ) {
				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['layout_subtitle'] . ' {';
					$styles .= 'color: ' . $colors['subtitle_color'];
				$styles .= '}';
			}
		} else {

			if ( '' !== $content['sub_title'] ) {
				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['layout_subtitle'] . ' {';
					$styles .= 'color: ' . $colors['subtitle_color_alt'];
				$styles .= '}';
			}
		}

		// Content color
		if ( '' !== $content['main_content'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_content'] . ' {';
				$styles .= 'color: ' . $colors['content_color'];
			$styles .= '}';
		}

		// OL counter
		if ( '' !== $content['main_content'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_content'] . ' ol li:before {';
				$styles .= 'color: ' . $colors['ol_counter'];
			$styles .= '}';
		}

		if ( $is_optin && '' !== $emails['success_message'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['success_container'] . ' ol li:before  {';
				$styles .= 'color: ' . $colors['content_color'];
			$styles .= '}';
		}

		// UL bullets
		if ( '' !== $content['main_content'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_content'] . ' ul li:before {';
				$styles .= 'background-color: ' . $colors['ul_bullets'];
			$styles .= '}';
		}

		if ( $is_optin && '' !== $emails['success_message'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['success_container'] . ' ul li:before  {';
				$styles .= 'color: ' . $colors['content_color'];
			$styles .= '}';
		}

		// Blockquote border
		if ( '' !== $content['main_content'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_content'] . ' blockquote {';
				$styles .= 'border-left-color: ' . $colors['blockquote_border'] . ';';
			$styles .= '}';
		}

		if ( $is_optin && '' !== $emails['success_message'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['success_container'] . ' blockquote  {';
				$styles .= 'color: ' . $colors['content_color'];
			$styles .= '}';
		}

		// Link color
		if ( '' !== $content['main_content'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_content'] . ' a,';
			$styles .= $prefix . $stylable_elements['layout_content'] . ' a:visited {';
				$styles .= 'color: ' . $colors['link_static_color'];
			$styles .= '}';
		}

		if ( $is_optin && '' !== $emails['success_message'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['success_container'] . ' a,';
			$styles .= $prefix . $stylable_elements['success_container'] . ' a:visited {';
				$styles .= 'color: ' . $colors['content_color'];
			$styles .= '}';
		}

		// ****************************************
		// 2.2. HOVER

		// Link color
		if ( '' !== $content['main_content'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_content'] . ' a:hover {';
				$styles .= 'color: ' . $colors['link_hover_color'];
			$styles .= '}';
		}

		if ( $is_optin && '' !== $emails['success_message'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['success_container'] . ' a:hover {';
				$styles .= 'color: ' . $colors['link_hover_color'];
			$styles .= '}';
		}

		// ****************************************
		// 2.3. ACTIVE

		// Link color
		if ( '' !== $content['main_content'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['layout_content'] . ' a:active {';
				$styles .= 'color: ' . $colors['link_active_color'];
			$styles .= '}';
		}

		if ( $is_optin && '' !== $emails['success_message'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['success_container'] . ' a:active {';
				$styles .= 'color: ' . $colors['link_active_color'];
			$styles .= '}';
		}

		// ========================================|
		// 3. CALL TO ACTION                       |
		// ========================================|

		// ****************************************
		// 3.1. DEFAULT

		// Border color
		if ( (int) $content['show_cta'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ',';
			$styles .= $prefix . $stylable_elements['button_cta'] . ':visited {';
				$styles .= 'border-color: ' . $colors['cta_button_static_bo'];
			$styles .= '}';
		}

		// Background color
		if ( (int) $content['show_cta'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ',';
			$styles .= $prefix . $stylable_elements['button_cta'] . ':visited {';
				$styles .= 'background-color: ' . $colors['cta_button_static_bg'];
			$styles .= '}';
		}

		// Label color
		if ( (int) $content['show_cta'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ',';
			$styles .= $prefix . $stylable_elements['button_cta'] . ':visited {';
				$styles .= 'color: ' . $colors['cta_button_static_color'];
			$styles .= '}';
		}

		// ****************************************
		// 3.2. HOVER

		// Border color
		if ( (int) $content['show_cta'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ':hover {';
				$styles .= 'border-color: ' . $colors['cta_button_hover_bo'];
			$styles .= '}';
		}

		// Background color
		if ( (int) $content['show_cta'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ':hover {';
				$styles .= 'background-color: ' . $colors['cta_button_hover_bg'];
			$styles .= '}';
		}

		// Label color
		if ( (int) $content['show_cta'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ':hover {';
				$styles .= 'color: ' . $colors['cta_button_hover_color'];
			$styles .= '}';
		}

		// ****************************************
		// 3.3. ACTIVE

		// Border color
		if ( (int) $content['show_cta'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ':active {';
				$styles .= 'border-color: ' . $colors['cta_button_active_bo'];
			$styles .= '}';
		}

		// Background color
		if ( (int) $content['show_cta'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ':active {';
				$styles .= 'background-color: ' . $colors['cta_button_active_bg'];
			$styles .= '}';
		}

		// Label color
		if ( (int) $content['show_cta'] ) {
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_cta'] . ':active {';
				$styles .= 'color: ' . $colors['cta_button_active_color'];
			$styles .= '}';
		}

		// ========================================|
		// 4. INPUTS                               |
		// ========================================|

		if ( $is_optin ) {

			// ****************************************
			// 4.1. DEFAULT

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input_icon'] . ' {';
				$styles .= 'color: ' . $colors['optin_input_icon'];
			$styles .= '}';

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input'] . ' {';
				$styles .= 'border-color: ' . $colors['optin_input_static_bo'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input'] . ' {';
				$styles .= 'background-color: ' . $colors['optin_input_static_bg'];
			$styles .= '}';

			// Text color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input'] . ' {';
				$styles .= 'color: ' . $colors['optin_form_field_text_static_color'];
			$styles .= '}';

			// Placeholder
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input_placeholder'] . ' {';
				$styles .= 'color: ' . $colors['optin_placeholder_color'];
			$styles .= '}';

			// ****************************************
			// 4.2. HOVER

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input_icon_hover'] . ' {';
				$styles .= 'color: ' . $colors['optin_input_icon_hover'];
			$styles .= '}';

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input'] . ':hover {';
				$styles .= 'border-color: ' . $colors['optin_input_hover_bo'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input'] . ':hover {';
				$styles .= 'background-color: ' . $colors['optin_input_hover_bg'];
			$styles .= '}';

			// ****************************************
			// 4.3. FOCUS

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input_icon_focus'] . ' {';
				$styles .= 'color: ' . $colors['optin_input_icon_focus'];
			$styles .= '}';

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input'] . ':focus {';
				$styles .= 'border-color: ' . $colors['optin_input_active_bo'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input'] . ':focus {';
				$styles .= 'background-color: ' . $colors['optin_input_active_bg'];
			$styles .= '}';

			// ****************************************
			// 4.4. ERROR

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input_icon_error'] . ' {';
				$styles .= 'color: ' . $colors['optin_input_icon_error'] . ' !important';
			$styles .= '}';

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input_error'] . ' {';
				$styles .= 'border-color: ' . $colors['optin_input_error_border'] . ' !important';
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_input_error'] . ' {';
				$styles .= 'background-color: ' . $colors['optin_input_error_background'] . ' !important';
			$styles .= '}';
		}

		// ========================================|
		// 5. RADIO AND CHECKBOX                   |
		// ========================================|

		if ( $is_optin ) {

			// ****************************************
			// 5.1. DEFAULT

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_radio'] . ', ';
			$styles .= $prefix . $stylable_elements['form_checkbox'] . ' {';
				$styles .= 'border-color: ' . $colors['optin_check_radio_bo'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_radio'] . ', ';
			$styles .= $prefix . $stylable_elements['form_checkbox'] . ' {';
				$styles .= 'background-color: ' . $colors['optin_check_radio_bg'];
			$styles .= '}';

			// Label color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_radio_label'] . ', ';
			$styles .= $prefix . $stylable_elements['form_checkbox_label'] . ' {';
				$styles .= 'color: ' . $colors['optin_mailchimp_labels_color'];
			$styles .= '}';

			// ****************************************
			// 5.2. CHECKED

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_radio_checked'] . ', ';
			$styles .= $prefix . $stylable_elements['form_checkbox_checked'] . ' {';
				$styles .= 'border-color: ' . $colors['optin_check_radio_bo_checked'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_radio_checked'] . ', ';
			$styles .= $prefix . $stylable_elements['form_checkbox_checked'] . ' {';
				$styles .= 'background-color: ' . $colors['optin_check_radio_bg_checked'];
			$styles .= '}';

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_radio_icon'] . ' {';
				$styles .= 'background-color: ' . $colors['optin_check_radio_tick_color'];
			$styles .= '}';
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_checkbox_icon'] . ' {';
				$styles .= 'color: ' . $colors['optin_check_radio_tick_color'];
			$styles .= '}';

		}

		// ========================================|
		// 6. GDPR CHECKBOX                        |
		// ========================================|

		if ( $is_optin ) {

			// ****************************************
			// 6.1. DEFAULT

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox'] . ' {';
				$styles .= 'border-color: ' . $colors['gdpr_chechbox_border_static'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox'] . ' {';
				$styles .= 'background-color: ' . $colors['gdpr_chechbox_background_static'];
			$styles .= '}';

			// Label color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_label'] . ' {';
				$styles .= 'color: ' . $colors['gdpr_content'];
			$styles .= '}';

			// Label link color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_link'] . ',';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_link'] . ':hover,';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_link'] . ':focus,';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_link'] . ':active,';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_link'] . ':visited {';
				$styles .= 'color: ' . $colors['gdpr_content_link'];
			$styles .= '}';

			// ****************************************
			// 6.2. CHECKED

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_checked'] . ' {';
				$styles .= 'border-color: ' . $colors['gdpr_chechbox_border_active'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_checked'] . ' {';
				$styles .= 'background-color: ' . $colors['gdpr_checkbox_background_active'];
			$styles .= '}';

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_icon'] . ' {';
				$styles .= 'color: ' . $colors['gdpr_checkbox_icon'];
			$styles .= '}';

			// ****************************************
			// 6.3. ERROR

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_error'] . ' {';
				$styles .= 'border-color: ' . $colors['gdpr_checkbox_border_error'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['gdpr_checkbox_error'] . ' {';
				$styles .= 'background-color: ' . $colors['gdpr_checkbox_background_error'];
			$styles .= '}';

		}

		// ========================================|
		// 6. SELECT                               |
		// ========================================|

		if ( $is_optin ) {

			// ****************************************
			// 6.1. DEFAULT

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . ' .select2-selection--single {';
				$styles .= 'border-color: ' . $colors['optin_select_border'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . ' .select2-selection--single {';
				$styles .= 'background-color: ' . $colors['optin_select_background'];
			$styles .= '}';

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . ' .select2-selection--single .select2-selection__arrow {';
				$styles .= 'color: ' . $colors['optin_select_icon'];
			$styles .= '}';

			// Label color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . ' .select2-selection--single .select2-selection__rendered {';
				$styles .= 'color: ' . $colors['optin_select_label'];
			$styles .= '}';

			// Placeholder
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . ' .select2-selection--single .select2-selection__rendered .select2-selection__placeholder {';
				$styles .= 'color: ' . $colors['optin_select_placeholder'];
			$styles .= '}';

			// ****************************************
			// 6.2. HOVER

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . ':hover .select2-selection--single {';
				$styles .= 'border-color: ' . $colors['optin_select_border_hover'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . ':hover .select2-selection--single {';
				$styles .= 'background-color: ' . $colors['optin_select_background_hover'];
			$styles .= '}';

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . ':hover .select2-selection--single .select2-selection__arrow {';
				$styles .= 'color: ' . $colors['optin_select_icon_hover'];
			$styles .= '}';

			// ****************************************
			// 6.3. OPEN

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . '.select2-container--open .select2-selection--single {';
				$styles .= 'border-color: ' . $colors['optin_select_border_open'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . '.select2-container--open .select2-selection--single {';
				$styles .= 'background-color: ' . $colors['optin_select_background_open'];
			$styles .= '}';

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2'] . '.select2-container--open .select2-selection--single .select2-selection__arrow {';
				$styles .= 'color: ' . $colors['optin_select_icon_open'];
			$styles .= '}';

			// ****************************************
			// 6.4. ERROR

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2_error'] . ' .select2-selection--single {';
				$styles .= 'border-color: ' . $colors['optin_select_border_error'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2_error'] . ' .select2-selection--single {';
				$styles .= 'background-color: ' . $colors['optin_select_background_error'];
			$styles .= '}';

			// Icon color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['form_select2_error'] . ' .select2-selection--single .select2-selection__arrow {';
				$styles .= 'color: ' . $colors['optin_select_icon_error'];
			$styles .= '}';
		}

		// ========================================|
		// 7. DROPDOWN LIST                        |
		// ========================================|

		if ( $is_optin ) {

			// ****************************************
			// 7.1. DEFAULT

			// Background color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-dropdown,';
			$styles .= $prefix . ' .hustle-timepicker .ui-timepicker {';
				$styles .= 'background-color: ' . $colors['optin_dropdown_background'] . ';';
			$styles .= '}';

			// Label color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-dropdown .select2-results .select2-results__options .select2-results__option,';
			$styles .= $prefix . ' .hustle-timepicker .ui-timepicker .ui-timepicker-viewport a {';
				$styles .= 'color: ' . $colors['optin_dropdown_option_color'] . ';';
			$styles .= '}';

			// ****************************************
			// 7.2. HOVER

			// Label color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-dropdown .select2-results .select2-results__options .select2-results__option.select2-results__option--highlighted,';
			$styles .= $prefix . ' .hustle-timepicker .ui-timepicker .ui-timepicker-viewport a:hover,';
			$styles .= $prefix . ' .hustle-timepicker .ui-timepicker .ui-timepicker-viewport a:active {';
				$styles .= 'color: ' . $colors['optin_dropdown_option_color_hover'] . ';';
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-dropdown .select2-results .select2-results__options .select2-results__option.select2-results__option--highlighted,';
			$styles .= $prefix . ' .hustle-timepicker .ui-timepicker .ui-timepicker-viewport a:hover,';
			$styles .= $prefix . ' .hustle-timepicker .ui-timepicker .ui-timepicker-viewport a:active {';
				$styles .= 'background-color: ' . $colors['optin_dropdown_option_bg_hover'] . ';';
			$styles .= '}';

			// ****************************************
			// 7.3. SELECTED

			// Label color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-dropdown .select2-results .select2-results__options .select2-results__option[aria-selected="true"] {';
				$styles .= 'color: ' . $colors['optin_dropdown_option_color_active'] . ';';
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-dropdown .select2-results .select2-results__options .select2-results__option[aria-selected="true"] {';
				$styles .= 'background-color: ' . $colors['optin_dropdown_option_bg_active'] . ';';
			$styles .= '}';
		}

		// ========================================|
		// 8. CALENDAR                             |
		// ========================================|

		if ( $is_optin ) {

			// ****************************************
			// 8.1. DEFAULT

			// Background color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar:before {';
				$styles .= 'background-color: ' . $colors['optin_calendar_background'] . ';';
			$styles .= '}';

			// Title color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-header .ui-datepicker-title {';
				$styles .= 'color: ' . $colors['optin_calendar_title'] . ';';
			$styles .= '}';

			// Navigation arrows
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-header .ui-corner-all,';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-header .ui-corner-all:visited {';
				$styles .= 'color: ' . $colors['optin_calendar_arrows'] . ';';
			$styles .= '}';

			// Table head color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-calendar thead th {';
				$styles .= 'color: ' . $colors['optin_calendar_thead'] . ';';
			$styles .= '}';

			// Table cell background
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-calendar tbody tr td a,';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-calendar tbody tr td a:visited {';
				$styles .= 'background-color: ' . $colors['optin_calendar_cell_background'] . ';';
			$styles .= '}';

			// Table cell color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-calendar tbody tr td a,';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-calendar tbody tr td a:visited {';
				$styles .= 'color: ' . $colors['optin_calendar_cell_color'] . ';';
			$styles .= '}';

			// ****************************************
			// 8.2. HOVER

			// Navigation arrows
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-header .ui-corner-all:hover {';
				$styles .= 'color: ' . $colors['optin_calendar_arrows_hover'] . ';';
			$styles .= '}';

			// Table cell background
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-calendar tbody tr td a:hover {';
				$styles .= 'background-color: ' . $colors['optin_calendar_cell_bg_hover'] . ';';
			$styles .= '}';

			// Table cell color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-calendar tbody tr td a:hover {';
				$styles .= 'color: ' . $colors['optin_calendar_cell_color_hover'] . ';';
			$styles .= '}';

			// ****************************************
			// 8.3. ACTIVE

			// Navigation arrows
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-header .ui-corner-all:active {';
				$styles .= 'color: ' . $colors['optin_calendar_arrows_active'] . ';';
			$styles .= '}';

			// Table cell background
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-calendar tbody tr td a:active {';
				$styles .= 'background-color: ' . $colors['optin_calendar_cell_bg_active'] . ';';
			$styles .= '}';

			// Table cell color
			$styles .= ' ';
			$styles .= '.hustle-module-' . $this->_module->module_id . '.hustle-calendar .ui-datepicker-calendar tbody tr td a:active {';
				$styles .= 'color: ' . $colors['optin_calendar_cell_color_active'] . ';';
			$styles .= '}';
		}

		// ========================================|
		// 6. SUBMIT BUTTON                        |
		// ========================================|

		if ( $is_optin ) {

			// ****************************************
			// 6.1. DEFAULT

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_submit'] . ' {';
				$styles .= 'border-color: ' . $colors['optin_submit_button_static_bo'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_submit'] . ' {';
				$styles .= 'background-color: ' . $colors['optin_submit_button_static_bg'];
			$styles .= '}';

			// Label color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_submit'] . ' {';
				$styles .= 'color: ' . $colors['optin_submit_button_static_color'];
			$styles .= '}';

			// ****************************************
			// 6.2. HOVER

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_submit'] . ':hover {';
				$styles .= 'border-color: ' . $colors['optin_submit_button_hover_bo'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_submit'] . ':hover {';
				$styles .= 'background-color: ' . $colors['optin_submit_button_hover_bg'];
			$styles .= '}';

			// Label color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_submit'] . ':hover {';
				$styles .= 'color: ' . $colors['optin_submit_button_hover_color'];
			$styles .= '}';

			// ****************************************
			// 6.3. ACTIVE

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_submit'] . ':active {';
				$styles .= 'border-color: ' . $colors['optin_submit_button_active_bo'];
			$styles .= '}';

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_submit'] . ':active {';
				$styles .= 'background-color: ' . $colors['optin_submit_button_active_bg'];
			$styles .= '}';

			// Label color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['button_submit'] . ':active {';
				$styles .= 'color: ' . $colors['optin_submit_button_active_color'];
			$styles .= '}';
		}

		// ========================================|
		// 7. CUSTOM FIELDS SECTION                |
		// ========================================|

		if ( $is_optin ) {

			// Title color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['custom_section_title'] . ' {';
				$styles .= 'color: ' . $colors['optin_mailchimp_title_color'];
			$styles .= '}';

			// Container background
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['custom_section'] . ' {';
				$styles .= 'background-color: ' . $colors['custom_section_bg'];
			$styles .= '}';
		}

		// ========================================|
		// 8. ERROR MESSAGE                        |
		// ========================================|

		if ( $is_optin ) {

			// Background color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['error_message'] . ' {';
				$styles .= 'background-color: ' . $colors['optin_error_text_bg'];
			$styles .= '}';

			// Border color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['error_message'] . ' {';
				$styles .= 'box-shadow: inset 4px 0 0 0 ' . $colors['optin_error_text_border'] . ';';
				$styles .= '-moz-box-shadow: inset 4px 0 0 0 ' . $colors['optin_error_text_border'] . ';';
				$styles .= '-webkit-box-shadow: inset 4px 0 0 0 ' . $colors['optin_error_text_border'] . ';';
			$styles .= '}';

			// Message color
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['error_message_text'] . ' {';
				$styles .= 'color: ' . $colors['optin_error_text_color'];
			$styles .= '}';
		}

		// ========================================|
		// 9. ERROR MESSAGE                        |
		// ========================================|

		if ( $is_optin ) {

			// Success background.
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['success_container'] . ' {';
				$styles .= 'background-color: ' . $colors['optin_success_background'];
			$styles .= '}';

			// Success icon.
			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['success_icon'] . ' {';
				$styles .= 'color: ' . $colors['optin_success_tick_color'];
			$styles .= '}';

			// Success content.
			if ( '' !== $emails['success_message'] ) {
				$styles .= ' ';
				$styles .= $prefix . $stylable_elements['success_container'] . ' .hustle-success-content {';
					$styles .= 'color: ' . $colors['optin_success_content_color'];
				$styles .= '}';
			}
		}

		// ========================================|
		// 10. ADDITIONAL SETTINGS                 |
		// ========================================|

		// ****************************************
		// 10.1. DEFAULT

		// Pop-up mask.
		$styles .= ' ';
		$styles .= $prefix . $stylable_elements['overlay'] . ' {';
			$styles .= 'background-color: ' . $colors['overlay_bg'];
		$styles .= '}';

		// Close button.
		$styles .= ' ';
		$styles .= $prefix . $stylable_elements['button_close'] . ' {';
			$styles .= 'color: ' . $colors['close_button_static_color'];
		$styles .= '}';

		// Never See Link.
		$styles .= ' ';
		$styles .= $prefix . $stylable_elements['never_see_link'] . ',';
		$styles .= $prefix . $stylable_elements['never_see_link'] . ':visited {';
			$styles .= 'color: ' . $colors['never_see_link_static'];
		$styles .= '}';

		// reCAPTCHA copy content.
		if ( $is_optin ) {

			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['recaptcha_copy'] . ',';
			$styles .= $prefix . $stylable_elements['recaptcha_copy'] . ' p {';
				$styles .= 'color: ' . $colors['recaptcha_copy_text'];
			$styles .= '}';

			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['recaptcha_copy'] . ' a,';
			$styles .= $prefix . $stylable_elements['recaptcha_copy'] . ' a:visited {';
				$styles .= 'color: ' . $colors['recaptcha_copy_link_default'];
			$styles .= '}';

		}

		// ****************************************
		// 10.2. HOVER

		// Close button.
		$styles .= ' ';
		$styles .= $prefix . $stylable_elements['button_close'] . ':hover {';
			$styles .= 'color: ' . $colors['close_button_hover_color'];
		$styles .= '}';

		// Never See Link.
		$styles .= ' ';
		$styles .= $prefix . $stylable_elements['never_see_link'] . ':hover {';
			$styles .= 'color: ' . $colors['never_see_link_hover'];
		$styles .= '}';

		// reCAPTCHA copy content.
		if ( $is_optin ) {

			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['recaptcha_copy'] . ' a:hover {';
				$styles .= 'color: ' . $colors['recaptcha_copy_link_hover'];
			$styles .= '}';

		}

		// ****************************************
		// 10.3. ACTIVE

		// Close button.
		$styles .= ' ';
		$styles .= $prefix . $stylable_elements['button_close'] . ':active {';
			$styles .= 'color: ' . $colors['close_button_active_color'];
		$styles .= '}';

		// Never See Link.
		$styles .= ' ';
		$styles .= $prefix . $stylable_elements['never_see_link'] . ':active {';
			$styles .= 'color: ' . $colors['never_see_link_active'];
		$styles .= '}';

		// reCAPTCHA copy content.
		if ( $is_optin ) {

			$styles .= ' ';
			$styles .= $prefix . $stylable_elements['recaptcha_copy'] . ' a:focus,';
			$styles .= $prefix . $stylable_elements['recaptcha_copy'] . ' a:active {';
				$styles .= 'color: ' . $colors['recaptcha_copy_link_focus'];
			$styles .= '}';

		}

		/**
		 * Border
		 * This will add a customizable border to the main container.
		 * Works for both opt-in and informational modules.
		 *
		 * @since 4.0
		 */
		if ( (int) $design['border'] ) {

			if ( $is_optin ) {

				// Default
				if ( 'one' === $form_layout ) {
					$styles .= ' ';
					$styles .= $prefix . $stylable_elements['success_container'] . ',';
					$styles .= $prefix . ' .hustle-optin--default .hustle-layout .hustle-layout-body {';
						$styles .= 'overflow: hidden;';
						$styles .= 'border-width: ' . $design['border_weight'] . 'px;';
						$styles .= 'border-style: ' . $design['border_type'] . ';';
						$styles .= 'border-color: ' . $design['border_color'] . ';';
						$styles .= 'border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-moz-border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-webkit-border-radius: ' . $design['border_radius'] . 'px;';
					$styles .= '}';
				}

				// Compact
				if ( 'two' === $form_layout ) {
					$styles .= ' ';
					$styles .= $prefix . $stylable_elements['success_container'] . ',';
					$styles .= $prefix . ' .hustle-optin--compact .hustle-layout .hustle-layout-body {';
						$styles .= 'overflow: hidden;';
						$styles .= 'border-width: ' . $design['border_weight'] . 'px;';
						$styles .= 'border-style: ' . $design['border_type'] . ';';
						$styles .= 'border-color: ' . $design['border_color'] . ';';
						$styles .= 'border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-moz-border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-webkit-border-radius: ' . $design['border_radius'] . 'px;';
					$styles .= '}';
				}

				// Focus Opt-in
				if ( 'three' === $form_layout ) {
					$styles .= ' ';
					$styles .= $prefix . $stylable_elements['success_container'] . ',';
					$styles .= $prefix . ' .hustle-optin--focus-optin .hustle-layout .hustle-layout-body {';
						$styles .= 'overflow: hidden;';
						$styles .= 'border-width: ' . $design['border_weight'] . 'px;';
						$styles .= 'border-style: ' . $design['border_type'] . ';';
						$styles .= 'border-color: ' . $design['border_color'] . ';';
						$styles .= 'border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-moz-border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-webkit-border-radius: ' . $design['border_radius'] . 'px;';
					$styles .= '}';
				}

				// Focus Content
				if ( 'four' === $form_layout ) {
					$styles .= ' ';
					$styles .= $prefix . $stylable_elements['success_container'] . ',';
					$styles .= $prefix . ' .hustle-optin--focus-content .hustle-layout .hustle-layout-body {';
						$styles .= 'overflow: hidden;';
						$styles .= 'border-width: ' . $design['border_weight'] . 'px;';
						$styles .= 'border-style: ' . $design['border_type'] . ';';
						$styles .= 'border-color: ' . $design['border_color'] . ';';
						$styles .= 'border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-moz-border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-webkit-border-radius: ' . $design['border_radius'] . 'px;';
					$styles .= '}';
				}
			} else {

				// Default
				if ( 'minimal' === $layout_style ) {
					$styles .= ' ';
					$styles .= $prefix . ' .hustle-info--default .hustle-layout {';
						$styles .= 'overflow: hidden;';
						$styles .= 'border-width: ' . $design['border_weight'] . 'px;';
						$styles .= 'border-style: ' . $design['border_type'] . ';';
						$styles .= 'border-color: ' . $design['border_color'] . ';';
						$styles .= 'border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-moz-border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-webkit-border-radius: ' . $design['border_radius'] . 'px;';
					$styles .= '}';
				}

				// Compact
				if ( 'simple' === $layout_style ) {
					$styles .= ' ';
					$styles .= $prefix . ' .hustle-info--compact .hustle-layout {';
						$styles .= 'overflow: hidden;';
						$styles .= 'border-width: ' . $design['border_weight'] . 'px;';
						$styles .= 'border-style: ' . $design['border_type'] . ';';
						$styles .= 'border-color: ' . $design['border_color'] . ';';
						$styles .= 'border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-moz-border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-webkit-border-radius: ' . $design['border_radius'] . 'px;';
					$styles .= '}';
				}

				// Stacked
				if ( 'cabriolet' === $layout_style ) {
					$styles .= ' ';
					$styles .= $prefix . ' .hustle-info--stacked .hustle-layout-body {';
						$styles .= 'overflow: hidden;';
						$styles .= 'border-width: ' . $design['border_weight'] . 'px;';
						$styles .= 'border-style: ' . $design['border_type'] . ';';
						$styles .= 'border-color: ' . $design['border_color'] . ';';
						$styles .= 'border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-moz-border-radius: ' . $design['border_radius'] . 'px;';
						$styles .= '-webkit-border-radius: ' . $design['border_radius'] . 'px;';
					$styles .= '}';
				}
			}
		}

		/**
		 * Drop Shadow
		 * Works for both opt-in and informational modules.
		 *
		 * @since 4.0
		 */
		if ( (int) $design['drop_shadow'] ) {

			if ( $is_optin ) {

				if ( Hustle_Module_Model::SLIDEIN_MODULE !== $this->_module->module_type ) {

					// Default
					if ( 'one' === $form_layout ) {
						$styles .= ' ';
						$styles .= $prefix . $stylable_elements['success_container'] . ',';
						$styles .= $prefix . ' .hustle-optin--default .hustle-layout .hustle-layout-body {';
							$styles .= 'box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-moz-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-webkit-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '}';
					}

					// Compact
					if ( 'two' === $form_layout ) {
						$styles .= ' ';
						$styles .= $prefix . $stylable_elements['success_container'] . ',';
						$styles .= $prefix . ' .hustle-optin--compact .hustle-layout .hustle-layout-body {';
							$styles .= 'box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-moz-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-webkit-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '}';
					}

					// Focus Opt-in
					if ( 'three' === $form_layout ) {
						$styles .= ' ';
						$styles .= $prefix . $stylable_elements['success_container'] . ',';
						$styles .= $prefix . ' .hustle-optin--focus-optin .hustle-layout .hustle-layout-body {';
							$styles .= 'box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-moz-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-webkit-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '}';
					}

					// Focus Content
					if ( 'four' === $form_layout ) {
						$styles .= ' ';
						$styles .= $prefix . $stylable_elements['success_container'] . ',';
						$styles .= $prefix . ' .hustle-optin--focus-content .hustle-layout .hustle-layout-body {';
							$styles .= 'box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-moz-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-webkit-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '}';
					}
				} else {

					$styles .= ' ';
					$styles .= $prefix . ' .hustle-slidein-content {';
						$styles .= 'background-color: ' . $design['drop_shadow_color'] . ';';
						$styles .= 'box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '-moz-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '-webkit-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
					$styles .= '}';
				}
			} else {

				if ( Hustle_Module_Model::SLIDEIN_MODULE !== $this->_module->module_type ) {

					// Default
					if ( 'minimal' === $layout_style ) {
						$styles .= ' ';
						$styles .= $prefix . ' .hustle-info--default .hustle-layout {';
							$styles .= 'box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-moz-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-webkit-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '}';
					}

					// Compact
					if ( 'simple' === $layout_style ) {
						$styles .= ' ';
						$styles .= $prefix . ' .hustle-info--compact .hustle-layout {';
							$styles .= 'box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-moz-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-webkit-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '}';
					}

					// Stacked
					if ( 'cabriolet' === $layout_style ) {
						$styles .= ' ';
						$styles .= $prefix . ' .hustle-info--stacked .hustle-layout-body {';
							$styles .= 'box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-moz-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
							$styles .= '-webkit-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '}';
					}
				} else {

					$styles .= ' ';
					$styles .= $prefix . ' .hustle-slidein-content {';
						$styles .= 'background-color: ' . $design['drop_shadow_color'] . ';';
						$styles .= 'box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '-moz-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
						$styles .= '-webkit-box-shadow: ' . $design['drop_shadow_x'] . 'px ' . $design['drop_shadow_y'] . 'px ' . $design['drop_shadow_blur'] . 'px ' . $design['drop_shadow_spread'] . 'px ' . $design['drop_shadow_color'] . ';';
					$styles .= '}';
				}
			}
		}

		/**
		 * Custom Size
		 * Works for both opt-in and informational modules.
		 *
		 * @since 4.0
		 */
		if ( (int) $design['customize_size'] ) {

			$customize_size_styles = '';

			if ( Hustle_Module_Model::SLIDEIN_MODULE === $this->_module->module_type ) {
				$customize_size_styles .= $prefix . ' .hustle-slidein-content > [class*="hustle-"] {';
				$customize_size_styles .= 'max-width: ' . $design['custom_width'] . 'px;';
				$customize_size_styles .= 'height: calc(' . $design['custom_height'] . 'px - 30px);';
				$customize_size_styles .= '}';
				$customize_size_styles .= $prefix . ' .hustle-slidein-content > [class*="hustle-"] .hustle-layout {';
				$customize_size_styles .= 'height: 100%';
				$customize_size_styles .= '}';
			} else {
				$customize_size_styles .= $prefix . ' .hustle-inline-content, ';
				$customize_size_styles .= $prefix . ' .hustle-popup-content {';
				$customize_size_styles .= 'max-width: ' . $design['custom_width'] . 'px;';
				$customize_size_styles .= 'height: ' . $design['custom_height'] . 'px;';
				$customize_size_styles .= 'overflow-y: auto;';
				$customize_size_styles .= '}';
				$customize_size_styles .= $prefix . ' .hustle-inline-content .hustle-info,';
				$customize_size_styles .= $prefix . ' .hustle-popup-content .hustle-info {';
				$customize_size_styles .= 'padding: 0;';
				$customize_size_styles .= 'height: calc( 100% - 30px )';
				$customize_size_styles .= '}';
				$customize_size_styles .= $prefix . ' .hustle-inline-content .hustle-layout,';
				$customize_size_styles .= $prefix . ' .hustle-popup-content .hustle-layout {';
				$customize_size_styles .= 'height: 100%';
				$customize_size_styles .= '}';
			}

			if ( 'all' !== $design['apply_custom_size_to'] ) {
				$customize_size_styles =  '@media (min-width: 783px) {' . $customize_size_styles . '}';
			}

			$styles .= ' ' . $customize_size_styles;
		}

		return $styles;
	}

	/**
	 * Get the module's custom CSS.
	 *
	 * @since 4.0.3
	 * @return string
	 */
	private function _get_custom_css() {

		$styles = '';

		/**
		 * Custom CSS
		 * Works for both opt-in and informational modules.
		 *
		 * @since 4.0
		 */
		if ( (int) $this->design['customize_css'] ) {

			if ( ! empty( $this->design['custom_css'] ) ) {

				$prefix_mode = 'informational' === $this->_module->module_mode ? 'info' : 'optin';
				$prefix_alt = '.hustle-ui[data-id="' . $this->_module->module_id . '"] .hustle-' . $prefix_mode . ' ';

				$styles .= Opt_In::prepare_css( $this->design['custom_css'], $prefix_alt, false, true );
			}
		}

		return $styles;
	}

	private function _get_social_sharing_styles( $preview = false ) {

		$prefix = '.hustle-ui[data-id="' . $this->_module->module_id . '"]';

		$styles  = '';

		$module    = $this->_module;
		$module_id = $module->id;

		if ( $preview ) {
			$content   = (array)$module->content;
			$display   = (array)$module->display;
			$design    = (array)$module->design;
		} else {
			$content   = $module->get_content()->to_array();
			$display   = $module->get_display()->to_array();
			$design    = $module->get_design()->to_array();
		}

		/**
		 * Floating Social
		 *
		 * @since 1.0
		 */
		if ( (bool) $display['float_desktop_enabled'] || (bool) $display['float_mobile_enabled'] ) {

			$bp_desktop = 783;
			$bp_mobiles = 782;

			$box_shadow = sprintf(
				'%spx %spx %spx %spx %s',
				$design['floating_drop_shadow_x'],
				$design['floating_drop_shadow_y'],
				$design['floating_drop_shadow_blur'],
				$design['floating_drop_shadow_spread'],
				$design['floating_drop_shadow_color']
			);

			// Custom position for desktops
			if ( (bool) $display['float_desktop_enabled'] ) {

				$desktop_x_offset = ( ! empty( $display['float_desktop_offset_x'] ) ) ? $display['float_desktop_offset_x'] : '0';
				$desktop_y_offset = ( ! empty( $display['float_desktop_offset_y'] ) ) ? $display['float_desktop_offset_y'] : '0';

				$styles .= '@media screen and (min-width: ' . esc_attr( $bp_desktop ) . 'px) {';

					if ( 'center' !== $display['float_desktop_position'] ) {

						$styles .= sprintf(
							$prefix . '.hustle-float[data-desktop="true"] { %s: %spx }',
							esc_html( $display['float_desktop_position'] ),
							esc_attr( $desktop_x_offset )
						);
					}

					$styles .= sprintf(
						$prefix . '.hustle-float[data-desktop="true"] { %s: %spx }',
						esc_html( $display['float_desktop_position_y'] ),
						esc_attr( $desktop_y_offset )
					);

				$styles .= '}';
			}

			// Custom position for mobiles
			if ( (bool) $display['float_mobile_enabled'] ) {

				$mobile_x_offset = ( ! empty( $display['float_mobile_offset_x'] ) ) ? $display['float_mobile_offset_x'] : '0';
				$mobile_y_offset = ( ! empty( $display['float_mobile_offset_y'] ) ) ? $display['float_mobile_offset_y'] : '0';

				$styles .= '@media screen and (max-width: ' . esc_attr( $bp_mobiles ) . 'px) {';

					if ( 'center' !== $display['float_mobile_position'] ) {

						$styles .= sprintf(
							$prefix . '.hustle-float[data-mobiles="true"] { %s: %spx }',
							esc_html( $display['float_mobile_position'] ),
							esc_attr( $mobile_x_offset )
						);
					}

					$styles .= sprintf(
						$prefix . '.hustle-float[data-mobiles="true"] { %s: %spx }',
						esc_html( $display['float_mobile_position_y'] ),
						esc_attr( $mobile_y_offset )
					);

				$styles .= '}';
			}

			// Main background
			$styles .= sprintf(
				$prefix . '.hustle-float .hustle-social { background-color: %s; }',
				$design['floating_bg_color']
			);

			// Container shadow
			if ( (bool) $design['floating_drop_shadow'] ) {

				$styles .= sprintf(
					$prefix . '.hustle-float .hustle-social { box-shadow: %s; -moz-box-shadow: %s; -webkit-box-shadow: %s; }',
					$box_shadow,
					$box_shadow,
					$box_shadow
				);
			}

			// Counter colors
			if ( (bool) $content['counter_enabled'] ) {

				// Counter text
				$styles .= sprintf(
					$prefix . '.hustle-float .hustle-social .hustle-counter { color: %s; }',
					$design['floating_counter_color']
				);

				// DESIGN: Default
				if ( 'flat' === $design['icon_style'] ) {

					// Counter border
					$styles .= sprintf(
						$prefix . '.hustle-float .hustle-social.hustle-social--default[data-custom="true"] ul:not(.hustle-counter--none) a[class*="hustle-share-"] { border-color: %s; }',
						$design['floating_counter_border']
					);
				}

				// DESIGN: Rounded
				if ( 'rounded' === $design['icon_style'] ) {

					// Counter border
					$styles .= sprintf(
						$prefix . '.hustle-float .hustle-social.hustle-social--rounded[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
						$design['floating_counter_border']
					);
				}

				// DESIGN: Squared
				if ( 'squared' === $design['icon_style'] ) {

					// Counter border
					$styles .= sprintf(
						$prefix . '.hustle-float .hustle-social.hustle-social--squared[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
						$design['floating_counter_border']
					);
				}
			} else {

				// Icons custom color
				if ( (bool) $design['floating_customize_colors'] ) {

					// DESIGN: Default
					if ( 'flat' === $design['icon_style'] ) {

						// Element border
						$styles .= sprintf(
							$prefix . '.hustle-float .hustle-social.hustle-social--default[data-custom="true"] ul.hustle-counter--none a[class*="hustle-share-"] { border-color: %s; }',
							'transparent'
						);
					}

					// DESIGN: Rounded
					if ( 'rounded' === $design['icon_style'] ) {

						// Element border
						$styles .= sprintf(
							$prefix . '.hustle-float .hustle-social.hustle-social--rounded[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
							$design['floating_icon_bg_color']
						);
					}

					// DESIGN: Squared
					if ( 'squared' === $design['icon_style'] ) {

						// Element border
						$styles .= sprintf(
							$prefix . '.hustle-float .hustle-social.hustle-social--squared[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
							$design['floating_icon_bg_color']
						);
					}
				}
			}

			// Icons custom color
			if ( (bool) $design['floating_customize_colors'] ) {

				// DESIGN: Default
				if ( 'flat' === $design['icon_style'] ) {

					$styles .= sprintf(
						$prefix . '.hustle-float .hustle-social.hustle-social--default[data-custom="true"] a[class*="hustle-share-"] [class*="hustle-icon-social-"] { color: %s; }',
						$design['floating_icon_color']
					);
				}

				// DESIGN: Outlined
				if ( 'outline' === $design['icon_style'] ) {

					$styles .= sprintf(
						$prefix . '.hustle-float .hustle-social.hustle-social--outlined[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
						$design['floating_icon_bg_color']
					);

					$styles .= sprintf(
						$prefix . '.hustle-float .hustle-social.hustle-social--outlined[data-custom="true"] a[class*="hustle-share-"] [class*="hustle-icon-social-"] { color: %s; }',
						$design['floating_icon_color']
					);
				}

				// DESIGN: Rounded
				if ( 'rounded' === $design['icon_style'] ) {

					$styles .= sprintf(
						$prefix . '.hustle-float .hustle-social.hustle-social--rounded[data-custom="true"] a[class*="hustle-share-"] [class*="hustle-icon-social-"] { background-color: %s; color: %s; }',
						$design['floating_icon_bg_color'],
						$design['floating_icon_color']
					);
				}

				// DESIGN: Squared
				if ( 'squared' === $design['icon_style'] ) {

					$styles .= sprintf(
						$prefix . '.hustle-float .hustle-social.hustle-social--squared[data-custom="true"] a[class*="hustle-share-"] [class*="hustle-icon-social-"] { background-color: %s; color: %s; }',
						$design['floating_icon_bg_color'],
						$design['floating_icon_color']
					);
				}
			}
		}

		/**
		 * Inline Social
		 *
		 * @since 1.0
		 */
		if ( (bool) $display['inline_enabled'] || (bool) $display['widget_enabled'] || (bool) $display['shortcode_enabled'] ) {

			$box_shadow = sprintf(
				'%spx %spx %spx %spx %s',
				$design['widget_drop_shadow_x'],
				$design['widget_drop_shadow_y'],
				$design['widget_drop_shadow_blur'],
				$design['widget_drop_shadow_spread'],
				$design['widget_drop_shadow_color']
			);

			// Main background
			$styles .= sprintf(
				$prefix . '.hustle-inline .hustle-social { background-color: %s; }',
				$design['widget_bg_color']
			);

			// Container shadow
			if ( (bool) $design['widget_drop_shadow'] ) {

				$styles .= sprintf(
					$prefix . '.hustle-inline .hustle-social { box-shadow: %s; -moz-box-shadow: %s; -webkit-box-shadow: %s; }',
					$box_shadow,
					$box_shadow,
					$box_shadow
				);
			}

			// Counter colors
			if ( (bool) $content['counter_enabled'] ) {

				// Counter text
				$styles .= sprintf(
					$prefix . '.hustle-inline .hustle-social .hustle-counter { color: %s; }',
					$design['widget_counter_color']
				);

				// DESIGN: Default
				if ( 'flat' === $design['icon_style'] ) {

					// Counter border
					$styles .= sprintf(
						$prefix . '.hustle-inline .hustle-social.hustle-social--default[data-custom="true"] ul:not(.hustle-counter--none) a[class*="hustle-share-"] { border-color: %s; }',
						$design['widget_counter_border']
					);
				}

				// DESIGN: Rounded
				if ( 'rounded' === $design['icon_style'] ) {

					// Counter border
					$styles .= sprintf(
						$prefix . '.hustle-inline .hustle-social.hustle-social--rounded[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
						$design['widget_counter_border']
					);
				}

				// DESIGN: Squared
				if ( 'squared' === $design['icon_style'] ) {

					// Counter border
					$styles .= sprintf(
						$prefix . '.hustle-inline .hustle-social.hustle-social--squared[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
						$design['widget_counter_border']
					);
				}
			} else {

				// Icons custom color
				if ( (bool) $design['widget_customize_colors'] ) {

					// DESIGN: Default
					if ( 'flat' === $design['icon_style'] ) {

						// Element border
						$styles .= sprintf(
							$prefix . '.hustle-inline .hustle-social.hustle-social--default[data-custom="true"] ul.hustle-counter--none a[class*="hustle-share-"] { border-color: %s; }',
							'transparent'
						);
					}

					// DESIGN: Rounded
					if ( 'rounded' === $design['icon_style'] ) {

						// Element border
						$styles .= sprintf(
							$prefix . '.hustle-inline .hustle-social.hustle-social--rounded[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
							$design['widget_icon_bg_color']
						);
					}

					// DESIGN: Squared
					if ( 'squared' === $design['icon_style'] ) {

						// Element border
						$styles .= sprintf(
							$prefix . '.hustle-inline .hustle-social.hustle-social--squared[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
							$design['widget_icon_bg_color']
						);
					}
				}
			}

			// Icons custom color
			if ( (bool) $design['widget_customize_colors'] ) {

				// DESIGN: Default
				if ( 'flat' === $design['icon_style'] ) {

					$styles .= sprintf(
						$prefix . '.hustle-inline .hustle-social.hustle-social--default[data-custom="true"] a[class*="hustle-share-"] [class*="hustle-icon-social-"] { color: %s; }',
						$design['widget_icon_color']
					);
				}

				// DESIGN: Outlined
				if ( 'outline' === $design['icon_style'] ) {

					$styles .= sprintf(
						$prefix . '.hustle-inline .hustle-social.hustle-social--outlined[data-custom="true"] a[class*="hustle-share-"] { border-color: %s; }',
						$design['widget_icon_bg_color']
					);

					$styles .= sprintf(
						$prefix . '.hustle-inline .hustle-social.hustle-social--outlined[data-custom="true"] a[class*="hustle-share-"] [class*="hustle-icon-social-"] { color: %s; }',
						$design['widget_icon_color']
					);
				}

				// DESIGN: Rounded
				if ( 'rounded' === $design['icon_style'] ) {

					$styles .= sprintf(
						$prefix . '.hustle-inline .hustle-social.hustle-social--rounded[data-custom="true"] a[class*="hustle-share-"] [class*="hustle-icon-social-"] { background-color: %s; color: %s; }',
						$design['widget_icon_bg_color'],
						$design['widget_icon_color']
					);
				}

				// DESIGN: Squared
				if ( 'squared' === $design['icon_style'] ) {

					$styles .= sprintf(
						$prefix . '.hustle-inline .hustle-social.hustle-social--squared[data-custom="true"] a[class*="hustle-share-"] [class*="hustle-icon-social-"] { background-color: %s; color: %s; }',
						$design['widget_icon_bg_color'],
						$design['widget_icon_color']
					);
				}
			}
		}

		return $styles;

	}

	private function _get_popup_stylable_elements() {

		return array(
			'overlay'                => '.hustle-popup-mask',
			'layout'                 => '.hustle-layout',
			'layout_body'            => '.hustle-layout .hustle-layout-body',
			'layout_image'           => '.hustle-layout .hustle-image',
			'layout_form'            => '.hustle-layout .hustle-layout-form',
			'layout_title'           => '.hustle-layout .hustle-title',
			'layout_subtitle'        => '.hustle-layout .hustle-subtitle',
			'layout_content'         => '.hustle-layout .hustle-group-content',
			'button_cta'             => '.hustle-layout .hustle-button-cta',
			'button_submit'          => '.hustle-layout .hustle-button-submit',
			'button_close'           => '.hustle-button-close',
			'form_input'             => '.hustle-layout .hustle-field .hustle-input',
			'form_input_error'       => '.hustle-layout .hustle-field.hustle-field-error .hustle-input',
			'form_input_icon'        => '.hustle-layout .hustle-field .hustle-input-label [class*="hustle-icon-"]',
			'form_input_icon_hover'  => '.hustle-layout .hustle-field:hover .hustle-input-label [class*="hustle-icon-"]',
			'form_input_icon_focus'  => '.hustle-layout .hustle-field .hustle-input:focus + .hustle-input-label [class*="hustle-icon-"]',
			'form_input_icon_error'  => '.hustle-layout .hustle-field.hustle-field-error .hustle-input-label [class*="hustle-icon-"]',
			'form_input_placeholder' => '.hustle-layout .hustle-field .hustle-input-label span',
			'form_radio'             => '.hustle-layout .hustle-radio span[aria-hidden]',
			'form_radio_icon'        => '.hustle-layout .hustle-radio span[aria-hidden]:before',
			'form_radio_label'       => '.hustle-layout .hustle-radio span:not([aria-hidden])',
			'form_radio_checked'     => '.hustle-layout .hustle-radio input:checked + span[aria-hidden]',
			'form_radio_error'       => '.hustle-layout .hustle-radio.hustle-field-error span[aria-hidden]',
			'form_checkbox'          => '.hustle-layout .hustle-checkbox:not(.hustle-gdpr) span[aria-hidden]',
			'form_checkbox_icon'     => '.hustle-layout .hustle-checkbox:not(.hustle-gdpr) span[aria-hidden]:before',
			'form_checkbox_label'    => '.hustle-layout .hustle-checkbox:not(.hustle-gdpr) span:not([aria-hidden])',
			'form_checkbox_checked'  => '.hustle-layout .hustle-checkbox:not(.hustle-gdpr) input:checked + span[aria-hidden]',
			'form_checkbox_error'    => '.hustle-layout .hustle-checkbox:not(.hustle-gdpr).hustle-field-error span[aria-hidden]',
			'form_select2'           => '.hustle-layout .hustle-select2 + .select2',
			'form_select2_error'     => '.hustle-layout .hustle-select2.hustle-field-error + .select2',
			'custom_section'         => '.hustle-layout .hustle-form-options',
			'custom_section_title'   => '.hustle-layout .hustle-form-options .hustle-group-title',
			'error_message'          => '.hustle-layout .hustle-error-message',
			'error_message_text'     => '.hustle-layout .hustle-error-message p',
			'recaptcha_copy'         => '.hustle-layout .hustle-recaptcha-copy',
			'never_see_link'         => '.hustle-nsa-link a',
			'success_container'      => '.hustle-success',
			'success_icon'           => '.hustle-success [class*="hustle-icon-"]',
			'gdpr_checkbox'          => '.hustle-layout .hustle-checkbox.hustle-gdpr span[aria-hidden]',
			'gdpr_checkbox_icon'     => '.hustle-layout .hustle-checkbox.hustle-gdpr span[aria-hidden]:before',
			'gdpr_checkbox_label'    => '.hustle-layout .hustle-checkbox.hustle-gdpr span:not([aria-hidden])',
			'gdpr_checkbox_link'     => '.hustle-layout .hustle-checkbox.hustle-gdpr span:not([aria-hidden]) a',
			'gdpr_checkbox_checked'  => '.hustle-layout .hustle-checkbox.hustle-gdpr input:checked + span[aria-hidden]',
			'gdpr_checkbox_error'    => '.hustle-layout .hustle-checkbox.hustle-gdpr.hustle-field-error span[aria-hidden]',
		);
	}

	/**
	 * Gets provider name from id/slug
	 *
	 * @param $slug
	 * @return bool | string
	 */
	public function get_service_name_from_id( $slug ) {
		$registered_providers = $this->get_providers();
		foreach( $registered_providers as $provider ) {
			if( $provider['slug'] === $slug ) {
				return $provider['title'];
			}
		}

		return false;
	}

	/**
	 * Returns link to edit page on specific section
	 *
	 * @param $section
	 * @return string
	 */
	public function get_edit_url( $section = '' ) {

		$page = $this->_module->get_wizard_page();

		if ( empty( $section ) ) {
			$url = admin_url( 'admin.php?page=' . $page . '&id=' . $this->_module->id );
		} else {
			$url = admin_url( 'admin.php?page=' . $page . '&id=' . $this->_module->id . '&section=' . $section );
		}

		return esc_url( $url );
	}

}
