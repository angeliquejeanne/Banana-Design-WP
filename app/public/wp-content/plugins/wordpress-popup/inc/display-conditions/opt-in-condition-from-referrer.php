<?php

class Opt_In_Condition_From_Referrer extends Opt_In_Condition_Abstract {
	public function is_allowed() {

		if ( ! isset( $this->args->refs ) ) {
			return false;
		}

		if ( 'true' === $this->args->filter_type ) {
			return $this->utils()->test_referrer( $this->args->refs );
		} else {
			return ! ( $this->utils()->test_referrer( $this->args->refs ) );
		}
	}
}
