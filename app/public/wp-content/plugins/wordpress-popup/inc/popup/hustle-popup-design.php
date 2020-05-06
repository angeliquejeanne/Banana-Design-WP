<?php
class Hustle_Popup_Design extends Hustle_Meta {

	public function get_defaults() {

		$fields_style         = 'flat';
		$fields_border_width  = 2;
		$fields_border_style  = 'solid';
		$fields_border_radius = 5;

		$input_border_static  = '#B0BEC6';
		$input_border_hover   = '#4F5F6B';

		$button_border_static = '#2CAE9F';
		$button_border_hover  = '#39CDBD';

		return array(

			// ========================================|
			// 1. LAYOUT
			// ========================================|

			// Opt-in Layout
			'form_layout'                        => 'one',

			// Informational Layout
			'style'                               => 'minimal',

			// Use "vanilla" theme
			'use_vanilla'						=> '0',

			// ========================================|
			// 2. FEATURE IMAGE                        |
			// ========================================|

			// Position
			'feature_image_position'             => 'left',

			// Fitting
			'feature_image_fit'                  => 'contain',

			// Fitting » Horizontal position
			'feature_image_horizontal'           => 'center',
			'feature_image_horizontal_px'        => '-100',

			// Fitting » Vertical position
			'feature_image_vertical'             => 'center',
			'feature_image_vertical_px'          => '-100',

			// Visibility on mobile
			'feature_image_hide_on_mobile'       => '0',

			// ========================================|
			// 3. FORM DESIGN                          |
			// ========================================|

			'form_fields_style'                  => $fields_style, // Form fields style
			'form_fields_border_radius'          => $fields_border_radius, // Form fields style » Radius
			'form_fields_border_weight'          => $fields_border_width, // Form fields style » Weight
			'form_fields_border_type'            => $fields_border_style, // Form fields style » Border type
			'form_fields_icon'                   => 'static', // Form field icon
			'form_fields_proximity'              => 'joined', // Form fields proximity
			'button_style'                       => $fields_style, // Button style
			'button_border_radius'               => $fields_border_radius, // Button style » Radius
			'button_border_weight'               => $fields_border_width, // Button style » Weight
			'button_border_type'                 => $fields_border_style, // Button style » Border type
			'gdpr_checkbox_style'                => $fields_style, // GDPR checkbox style
			'gdpr_border_radius'                 => $fields_border_radius, // GDPR checkbox style » Radius
			'gdpr_border_weight'                 => $fields_border_width, // GDPR checkbox style » Weight
			'gdpr_border_type'                   => $fields_border_style, // GDPR checkbox style » Border type

			// ========================================|
			// 4. CTA DESIGN                          |
			// ========================================|

			'cta_style'							 => 'flat', // CTA style
			'cta_border_radius'					 => $fields_border_radius, // CTA style » Radius
			'cta_border_weight'					 => $fields_border_width, // CTA style » Weight
			'cta_border_type'					 => $fields_border_style, // CTA style » Border type

			// ========================================|
			// 5. COLORS PALETTE                       |
			// ========================================|

			// Colors palette.
			'color_palette'						 => 'gray_slate',

			// Customize the color palette
			'customize_colors'                   => '0',

			// Colors table » Basic » Main background
			'main_bg_color'                      => '#38454E',

			// Colors table » Basic » Image container
			'image_container_bg'                 => '#35414A',

			// Colors table » Basic » Form area background
			'form_area_bg'                       => '#5D7380',

			// Colors table » Content » Default » Title color
			'title_color'                        => '#FFFFFF', // Used on opt-in modules.
			'title_color_alt'                    => '#ADB5B7', // Used on informational modules.

			// Colors table » Content » Default » Subtitle color
			'subtitle_color'                     => '#FFFFFF', // Used on opt-in modules.
			'subtitle_color_alt'                 => '#ADB5B7', // Used on informational modules.

			// Colors table » Content » Default » Content color
			'content_color'                      => '#ADB5B7',

			// Colors table » Content » Default » OL counter
			'ol_counter'                         => '#ADB5B7',

			// Colors table » Content » Default » UL bullets
			'ul_bullets'                         => '#ADB5B7',

			// Colors table » Content » Default » Blockquote border
			'blockquote_border'                  => '#38C5B5',

			// Colors table » Content » Default » Link color
			'link_static_color'                  => '#38C5B5',

			// Colors table » Content » Hover » Link color
			'link_hover_color'                   => '#49E2D1',

			// Colors table » Content » Active » Link color
			'link_active_color'                  => '#49E2D1',

			// Colors table » Call To Action » Default » Border color
			'cta_button_static_bo'               => $button_border_static,

			// Colors table » Call To Action » Default » Background color
			'cta_button_static_bg'               => '#38C5B5',

			// Colors table » Call To Action » Default » Label color
			'cta_button_static_color'            => '#FFFFFF',

			// Colors table » Call To Action » Hover » Border color
			'cta_button_hover_bo'                => $button_border_hover,

			// Colors table » Call To Action » Hover » Background color
			'cta_button_hover_bg'                => '#49E2D1',

			// Colors table » Call To Action » Hover » Label color
			'cta_button_hover_color'             => '#FFFFFF',

			// Colors table » Call To Action » Active » Border color
			'cta_button_active_bo'               => $button_border_hover,

			// Colors table » Call To Action » Active » Background color
			'cta_button_active_bg'               => '#49E2D1',

			// Colors table » Call To Action » Active » Label color
			'cta_button_active_color'            => '#FFFFFF',

			// Colors table » Inputs » Default » Icon color
			'optin_input_icon'                   => '#AAAAAA',

			// Colors table » Inputs » Default » Border color
			'optin_input_static_bo'              => $input_border_static,

			// Colors table » Inputs » Default » Background color
			'optin_input_static_bg'              => '#FFFFFF',

			// Colors table » Inputs » Default » Text color
			'optin_form_field_text_static_color' => '#5D7380',

			// Colors table » Inputs » Default » Placeholder
			'optin_placeholder_color'            => '#AAAAAA',

			// Colors table » Inputs » Hover » Icon color
			'optin_input_icon_hover'             => '#5D7380',

			// Colors table » Inputs » Hover » Border color
			'optin_input_hover_bo'               => $input_border_hover,

			// Colors table » Inputs » Hover » Background color
			'optin_input_hover_bg'               => '#FFFFFF',

			// Colors table » Inputs » Focus » Icon color
			'optin_input_icon_focus'             => '#5D7380',

			// Colors table » Inputs » Focus » Border color
			'optin_input_active_bo'              => $input_border_hover,

			// Colors table » Inputs » Focus » Background color
			'optin_input_active_bg'              => '#FFFFFF',

			// Colors table » Inputs » Error » Icon color
			'optin_input_icon_error'             => '#D43858',

			// Colors table » Inputs » Error » Border color
			'optin_input_error_border'           => '#D43858',

			// Colors table » Inputs » Error » Background color
			'optin_input_error_background'       => '#FFFFFF',

			// Colors table » Radio and Checkbox » Default » Border color
			'optin_check_radio_bo'               => $input_border_static,

			// Colors table » Radio and Checkbox » Default » Background color
			'optin_check_radio_bg'               => '#FFFFFF',

			// Colors table » Radio and Checkbox » Default » Label color
			'optin_mailchimp_labels_color'       => '#FFFFFF',

			// Colors table » Radio and Checkbox » Checked » Border color
			'optin_check_radio_bo_checked'       => $input_border_hover,

			// Colors table » Radio and Checkbox » Checked » Background color
			'optin_check_radio_bg_checked'       => '#FFFFFF',

			// Colors table » Radio and Checkbox » Checked » Icon color
			'optin_check_radio_tick_color'       => '#38C5B5',

			// Colors table » GDPR Checkbox » Default » Border color
			'gdpr_chechbox_border_static'        => $input_border_static,

			// Colors table » GDPR Checkbox » Default » Background color
			'gdpr_chechbox_background_static'    => '#FFFFFF',

			// Colors table » GDPR Checkbox » Default » Label color
			'gdpr_content'                       => '#FFFFFF',

			// Colors table » GDPR Checkbox » Default » Label link color
			'gdpr_content_link'                  => '#FFFFFF',

			// Colors table » GDPR Checkbox » Checked » Border color
			'gdpr_chechbox_border_active'        => $input_border_hover,

			// Colors table » GDPR Checkbox » Checked » Background color
			'gdpr_checkbox_background_active'    => '#FFFFFF',

			// Colors table » GDPR Checkbox » Checked » Icon color
			'gdpr_checkbox_icon'                 => '#38C5B5',

			// Colors table » GDPR Checkbox » Error » Border color
			'gdpr_checkbox_border_error'         => '#D43858',

			// Colors table » GDPR Checkbox » Error » Background color
			'gdpr_checkbox_background_error'     => '#FFFFFF',

			// Colors table » Select » Default » Border color
			'optin_select_border'                => $input_border_static,

			// Colors table » Select » Default » Background color
			'optin_select_background'            => '#FFFFFF',

			// Colors table » Select » Default » Icon color
			'optin_select_icon'                  => '#38C5B5',

			// Colors table » Select » Default » Label color
			'optin_select_label'                 => '#5D7380',

			// Colors table » Select » Default » Placeholder
			'optin_select_placeholder'           => '#AAAAAA',

			// Colors table » Select » Hover » Border color
			'optin_select_border_hover'          => $input_border_hover,

			// Colors table » Select » Hover » Background color
			'optin_select_background_hover'      => '#FFFFFF',

			// Colors table » Select » Hover » Icon color
			'optin_select_icon_hover'            => '#49E2D1',

			// Colors table » Select » Open » Border color
			'optin_select_border_open'           => $input_border_hover,

			// Colors table » Select » Open » Background color
			'optin_select_background_open'       => '#FFFFFF',

			// Colors table » Select » Open » Icon color
			'optin_select_icon_open'             => '#49E2D1',

			// Colors table » Select » Error » Border color
			'optin_select_border_error'          => '#FFFFFF',

			// Colors table » Select » Error » Background color
			'optin_select_background_error'      => '#FFFFFF',

			// Colors table » Select » Error » Icon color
			'optin_select_icon_error'            => '#D43858',

			// Colors table » Dropdown » Default » Container BG
			'optin_dropdown_background'          => '#FFFFFF',

			// Colors table » Dropdown » Default » Label color
			'optin_dropdown_option_color'        => '#5D7380',

			// Colors table » Dropdown » Hover » Label color
			'optin_dropdown_option_color_hover'  => '#FFFFFF',

			// Colors table » Dropdown » Hover » Label background
			'optin_dropdown_option_bg_hover'     => '#ADB5B7',

			// Colors table » Dropdown » Selected » Label color
			'optin_dropdown_option_color_active' => '#FFFFFF',

			// Colors table » Dropdown » Selected » Label background
			'optin_dropdown_option_bg_active'    => '#38C5B5',

			// Colors table » Calendar » Default » Container background
			'optin_calendar_background'          => '#FFFFFF',

			// Colors table » Calendar » Default » Title color
			'optin_calendar_title'               => '#35414A',

			// Colors table » Calendar » Default » Navigation arrows
			'optin_calendar_arrows'              => '#5D7380',

			// Colors table » Calendar » Default » Table head color
			'optin_calendar_thead'               => '#35414A',

			// Colors table » Calendar » Default » Table cell background
			'optin_calendar_cell_background'     => '#FFFFFF',

			// Colors table » Calendar » Default » Table cell color
			'optin_calendar_cell_color'          => '#5D7380',

			// Colors table » Calendar » Hover » Navigation arrows
			'optin_calendar_arrows_hover'        => '#5D7380',

			// Colors table » Calendar » Hover » Table cell background
			'optin_calendar_cell_bg_hover'       => '#38C5B5',

			// Colors table » Calendar » Hover » Table cell color
			'optin_calendar_cell_color_hover'    => '#FFFFFF',

			// Colors table » Calendar » Active » Navigation arrows
			'optin_calendar_arrows_active'       => '#5D7380',

			// Colors table » Calendar » Active » Table cell background
			'optin_calendar_cell_bg_active'      => '#38C5B5',

			// Colors table » Calendar » Active » Table cell color
			'optin_calendar_cell_color_active'   => '#FFFFFF',

			// Colors table » Submit Button » Default » Border color
			'optin_submit_button_static_bo'      => $button_border_static,

			// Colors table » Submit Button » Default » Background color
			'optin_submit_button_static_bg'      => '#38C5B5',

			// Colors table » Submit Button » Default » Label color
			'optin_submit_button_static_color'   => '#FFFFFF',

			// Colors table » Submit Button » Hover » Border color
			'optin_submit_button_hover_bo'       => $button_border_hover,

			// Colors table » Submit Button » Hover » Background color
			'optin_submit_button_hover_bg'       => '#49E2D1',

			// Colors table » Submit Button » Hover » Label color
			'optin_submit_button_hover_color'    => '#FFFFFF',

			// Colors table » Submit Button » Active » Border color
			'optin_submit_button_active_bo'      => $button_border_hover,

			// Colors table » Submit Button » Active » Background color
			'optin_submit_button_active_bg'      => '#49E2D1',

			// Colors table » Submit Button » Active » Label color
			'optin_submit_button_active_color'   => '#FFFFFF',

			// Colors table » Custom Fields Section » Title color
			'optin_mailchimp_title_color'        => '#FFFFFF',

			// Colors table » Custom Fields Section » Container background
			'custom_section_bg'                  => '#35414A',

			// Colors table » Error Message » Background color
			'optin_error_text_bg'                => '#FFFFFF',

			// Colors table » Error Message » Border color
			'optin_error_text_border'            => '#D43858',

			// Colors table » Error Message » Message color
			'optin_error_text_color'             => '#D43858',

			// Colors table » Success Message » Background color
			'optin_success_background'           => '#38454E',

			// Colors table » Success Message » Icon color
			'optin_success_tick_color'           => '#38C5B5',

			// Colors table » Success Message » Content color
			'optin_success_content_color'        => '#ADB5B7',

			// Colors table » Additional Settings » Default » Pop-up Mask
			'overlay_bg'                         => 'rgba(51,51,51,0.9)',

			// Colors table » Additional Settings » Default » Close button
			'close_button_static_color'          => '#38C5B5',

			// Colors table » Additional Settings » Default » Never see link
			'never_see_link_static'              => '#38C5B5',

			// Colors table » Additional Settings » Default » reCAPTCHA Copy Text
			'recaptcha_copy_text'                => '#FFFFFF',

			// Colors table » Additional Settings » Default » reCAPTCHA Copy Link
			'recaptcha_copy_link_default'        => '#FFFFFF',

			// Colors table » Additional Settings » Hover » Close button
			'close_button_hover_color'           => '#49E2D1',

			// Colors table » Additional Settings » Hover » Never see link
			'never_see_link_hover'               => '#49E2D1',

			// Colors table » Additional Settings » Hover » reCAPTCHA Copy Link
			'recaptcha_copy_link_hover'          => '#FFFFFF',

			// Colors table » Additional Settings » Active » Close button
			'close_button_active_color'          => '#49E2D1',

			// Colors table » Additional Settings » Active » Never see link
			'never_see_link_active'              => '#49E2D1',

			// Colors table » Additional Settings » Active » reCAPTCHA Copy Link
			'recaptcha_copy_link_focus'          => '#FFFFFF',

			// ========================================|
			// 6. BORDER                               |
			// ========================================|

			'border'                             => '0', // Show border
			'border_radius'                      => $fields_border_radius, // Show border » Border radius
			'border_weight'                      => $fields_border_width, // Show border » Border weight
			'border_type'                        => $fields_border_style, // Show border » Border type
			'border_color'                       => '#DADADA', // Show border » Border color

			// ========================================|
			// 7. DROP SHADOW                          |
			// ========================================|

			// Show drop shadow
			'drop_shadow'                        => '0',

			// Show drop shadow » X-offset
			'drop_shadow_x'                      => 0,

			// Show drop shadow » Y-offset
			'drop_shadow_y'                      => 0,

			// Show drop shadow » Blur
			'drop_shadow_blur'                   => 0,

			// Show drop shadow » Spread
			'drop_shadow_spread'                 => 0,

			// Show drop shadow » Color
			'drop_shadow_color'                  => 'rgba(0,0,0,0.4)',

			// ========================================|
			// 8. CUSTOM { MODULE } SIZE               |
			// ========================================|

			// Enable custom size
			'customize_size'                     => '0',

			// Enable custom size » Apply to
			'apply_custom_size_to'               => 'desktop',

			// Enable custom size » Width (px)
			'custom_width'                       => 600,

			// Enable custom size » Height (px)
			'custom_height'                      => 300,

			// ========================================|
			// 9. CUSTOM CSS                           |
			// ========================================|

			// Enable Custom CSS
			'customize_css'                      => 0,

			// Enable Custom CSS » Editor
			'custom_css'                         => '',

		);
	}
}

/*
OLD STUFF THAT NEEDS REVIEW

// Colors Palette: Additional Styles
'optin_success_content_color'		 => '#FDFDFD', // It's using main_content color instead.

// Module Basics: GDPR Checkbox
'gdpr_border_color'					=> '#DADADA', // Uses checkbox/radio settings. It's using mailchimp's settings on migration.

*/
