<?php

class Opt_In_Condition_Shown_Less_Than extends Opt_In_Condition_Abstract {

	public function is_allowed(){
		$module = $this->module;

		if( !isset( $this->args->less_than ) )
			return false;

		$cookie_key = $this->get_cookie_key($module->module_type) . $module->id;

		$show_count = isset( $_COOKIE[ $cookie_key ] ) ?  (int) $_COOKIE[ $cookie_key ] : 0;

		$is_less = empty( $this->args->less_or_more ) || 'more_than' !== $this->args->less_or_more;

		if ( empty( $this->args->less_than ) ) {
			return true;
		} else if ( $is_less ) {
			return $show_count < (int) $this->args->less_than;
		} else {
			return $show_count > (int) $this->args->less_than;
		}
	}

	public function get_cookie_key( $module_type ) {
		return 'hustle_module_show_count-' . $module_type . '-';
	}
}
