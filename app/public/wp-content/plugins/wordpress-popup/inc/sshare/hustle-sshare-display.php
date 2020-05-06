<?php

class Hustle_SShare_Display extends Hustle_Meta {
	public $defaults = array(
		'inline_enabled' => '0',
		'inline_position' => 'below',
		'widget_enabled' => '1',
		'shortcode_enabled' => '1',
		'inline_align' => 'left',

		'float_desktop_enabled' => '1',
		'float_desktop_position' => 'right',
		'float_desktop_offset' => 'screen',
		'float_desktop_offset_x' => '0',
		'float_desktop_position_y' => 'top',
		'float_desktop_offset_y' => '0',
		'float_desktop_css_selector' => '',

		'float_mobile_enabled' => '1',
		'float_mobile_position' => 'left',
		'float_mobile_offset' => 'screen',
		'float_mobile_position_x' => 'left',
		'float_mobile_offset_x' => '0',
		'float_mobile_position_y' => 'top',
		'float_mobile_offset_y' => '0',
		'float_mobile_css_selector' => '',
	);
}
