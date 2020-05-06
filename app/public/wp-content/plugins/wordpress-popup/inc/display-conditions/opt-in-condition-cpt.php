<?php

class Opt_In_Condition_Cpt extends Opt_In_Condition_Abstract {
	public function is_allowed(){
		global $post;

		$selected_cpts = !empty( $this->args->selected_cpts ) ? (array)$this->args->selected_cpts : [];
		$filter_type = isset( $this->args->filter_type ) && in_array( $this->args->filter_type, array( 'only', 'except' ), true )
				? $this->args->filter_type : 'except';

		/**
		 * Filter Custop Post Type condition behavior
		 *
		 * @since 4.1
		 *
		 * @param mixed $custom_return  Returned value - is allowed showing module or not
		 * @param object $this Opt_In_Condition_Cpt object
		 */
		$custom_return = apply_filters( 'huste_cpt_condition', null, $this->args->postType, $filter_type, $selected_cpts );
		if ( ! is_null( $custom_return ) ) {
			return $custom_return;
		}

		if ( !isset( $post ) || !( $post instanceof WP_Post ) || empty( $this->args->postType ) || $post->post_type !== $this->args->postType || ! is_single() ) {
			return false;
		}

		switch( $filter_type ){
			case "only":
				return in_array( $post->ID, $selected_cpts );

			case "except":
			default:
				return ! in_array( $post->ID, $selected_cpts );
		}
	}
}
