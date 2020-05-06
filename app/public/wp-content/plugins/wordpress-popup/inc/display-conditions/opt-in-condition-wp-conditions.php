<?php

/**
* This functionality has been changed to affect Static Pages (is_front_page, is_404, is_search).
*/
class Opt_In_Condition_Wp_Conditions extends Opt_In_Condition_Abstract {
	public function is_allowed() {

		if ( isset( $this->args->wp_conditions ) ) {
			$conditions = (array) $this->args->wp_conditions;

			if ( is_404() ) {
				$allowed = in_array( 'is_404', $conditions, true );
			} elseif ( is_front_page() ) {
				$allowed = in_array( 'is_front_page', $conditions, true );
			} elseif ( is_search() ) {
				$allowed = in_array( 'is_search', $conditions, true );
			}

			if ( ! isset( $allowed ) ) {
				return false;
			}

			if ( 'except' === $this->args->filter_type ) {
				return ! $allowed;
			} elseif ( 'only' === $this->args->filter_type ) {
				return $allowed;
			}
		}
		return false;
	}

}
