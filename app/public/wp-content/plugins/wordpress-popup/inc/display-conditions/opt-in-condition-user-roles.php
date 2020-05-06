<?php

class Opt_In_Condition_User_Roles extends Opt_In_Condition_Abstract {
	public function is_allowed(){
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( !empty( $this->args->filter_type ) ) {
			$user 		 = wp_get_current_user();
			$roles 		 = ( array ) $user->roles;
			$saved_roles = ( array ) $this->args->roles;
			$valid_roles = array_intersect( $roles, $saved_roles );

			if( 'except' === $this->args->filter_type ) {
				return empty( $valid_roles );
			} elseif( 'only' === $this->args->filter_type ) {
				return ! empty( $valid_roles );
			}

		}

		return false;
	}

}
