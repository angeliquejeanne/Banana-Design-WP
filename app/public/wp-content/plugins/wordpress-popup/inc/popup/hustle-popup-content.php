<?php

class Hustle_Popup_Content extends Hustle_Meta {
	
	/**
	 * Get the defaults for this meta.
	 * 
	 * @since 4.0
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'module_name' => '',
			'title' => '',
			'sub_title' => '',
			'main_content' => '',
			'feature_image' => '',
			'show_never_see_link' => '0',
			'never_see_link_text' => __( 'Never see this message again.', 'hustle' ),
			'show_cta' => '0',
			'cta_label' => '',
			'cta_url' => '',
			'cta_target' => 'blank',
		);
	}
}
