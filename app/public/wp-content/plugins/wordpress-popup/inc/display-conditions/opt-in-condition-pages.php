<?php

class Opt_In_Condition_Pages extends Opt_In_Condition_Abstract {
	public function is_allowed(){
		global $post;

		$all = false;
		$none = false;
		$pages = !empty( $this->args->pages ) ? (array)$this->args->pages : [];
		$filter_type = isset( $this->args->filter_type ) && in_array( $this->args->filter_type, array( 'only', 'except' ), true )
				? $this->args->filter_type : 'except';


		if( !isset( $post ) || !( $post instanceof WP_Post ) || "page" !== $post->post_type || ! is_page() ) {
			return false;
		}
		if ( empty( $pages ) ) {
			if ( "except" === $filter_type ) {
				$all = true;
			} else {
				$none = true;
			}
		}
		if ( $none ) {
			return false;
		}

		$page_id = class_exists('woocommerce') && is_shop() ? wc_get_page_id('shop') : $post->ID;
		switch( $filter_type ){
			case "only":
				return $all || in_array( $page_id, $pages );

			case "except":
			default:
				return $all || !in_array( $page_id, $pages );
		}
	}

}
