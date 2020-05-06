<?php

class Opt_In_Condition_Visitor_Commented extends Opt_In_Condition_Abstract {
	public function is_allowed() {

		if ( 'true' === $this->args->filter_type ) {
			return $this->utils()->has_user_commented();
		} else {
			return ! ( $this->utils()->has_user_commented() );
		}
	}
}
