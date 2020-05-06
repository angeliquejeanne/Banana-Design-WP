<?php

class Opt_In_Condition_Wc_Pages extends Opt_In_Condition_Abstract {
	public function is_allowed(){
		if ( ! Opt_In_Utils::is_woocommerce_active() ) {
			return false;
		}

		$is_wc = is_woocommerce() || is_checkout() || is_cart();

		$is_all = !isset( $this->args->filter_type ) || 'none' !== $this->args->filter_type;

		if ( $is_all ) {
			return $is_wc;
		} else {
			return ! $is_wc;
		}


	}

}
