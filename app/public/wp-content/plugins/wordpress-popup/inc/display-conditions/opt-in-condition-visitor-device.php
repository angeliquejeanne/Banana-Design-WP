<?php

class Opt_In_Condition_Visitor_Device extends Opt_In_Condition_Abstract {
	public function is_allowed() {

		if ( 'mobile' === $this->args->filter_type ) {
			return wp_is_mobile();
		} else {
			return ! wp_is_mobile();
		}
	}
}
