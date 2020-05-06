<?php

class Opt_In_Condition_Posts extends Opt_In_Condition_Abstract {
	public function is_allowed(){
		global $post;

		$all = false;
		$none = false;
		$posts = !empty( $this->args->posts ) ? (array)$this->args->posts : [];
		$filter_type = isset( $this->args->filter_type ) && in_array( $this->args->filter_type, array( 'only', 'except' ), true )
				? $this->args->filter_type : 'except';

		if ( !isset( $post ) || !( $post instanceof WP_Post ) || "post" !== $post->post_type || ! is_single() ) {
			return false;
		}
		if ( empty( $posts ) ) {
			if ( "except" === $filter_type ) {
				$all = true;
			} else {
				$none = true;
			}
		}

		if ( $none ) {
			return false;
		}

		switch( $filter_type ){
			case "only":
				return $all || in_array( $post->ID, $posts );

			case "except":
			default:
				return $all || !in_array( $post->ID, $posts );
		}
	}
}
