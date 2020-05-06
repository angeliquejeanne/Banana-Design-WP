<?php

class Opt_In_Condition_Visitor_Logged_In_Status extends Opt_In_Condition_Abstract {
	public function is_allowed(){
		if ( !empty( $this->args->show_to ) ) {
			$is_user_logged_in = is_user_logged_in();
			if ( 'logged_out' === $this->args->show_to ) {
				return !$is_user_logged_in;
			} elseif ( 'logged_in' === $this->args->show_to ) {
				return $is_user_logged_in;
			}
		}

		return false;
	}

}
