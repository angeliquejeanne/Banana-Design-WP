<?php

class Opt_In_Condition_Visitor_Country extends Opt_In_Condition_Abstract {
	public function is_allowed(){

		if ( isset( $this->args->countries ) ) {

			if ( 'except' === $this->args->filter_type ) {
				return ! ( $this->utils()->test_country( $this->args->countries ) );
			} elseif ( 'only' === $this->args->filter_type ) {
				return $this->utils()->test_country( $this->args->countries );
			}
		}

		return false;
	}
}
