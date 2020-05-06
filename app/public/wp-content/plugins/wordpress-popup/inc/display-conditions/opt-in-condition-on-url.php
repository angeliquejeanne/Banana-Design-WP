<?php

class Opt_In_Condition_On_Url extends Opt_In_Condition_Abstract {
	public function is_allowed() {
		if ( !isset( $this->args->urls ) || !isset( $this->args->filter_type ) ) {
			return false;
		}

		$is_url = $this->utils()->check_url( preg_split('/\r\n|\r|\n/', $this->args->urls ) );

		if ( 'only' === $this->args->filter_type ) {
			return $is_url;
		} else {
			return ! $is_url;
		}
	}
}
