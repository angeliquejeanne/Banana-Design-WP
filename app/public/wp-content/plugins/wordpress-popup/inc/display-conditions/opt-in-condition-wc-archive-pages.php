<?php

/**
* This functionality has been changed to affect WooCommerce Archive Pages (is_shop, is_product_tag, is_product_category).
*/
class Opt_In_Condition_Wc_Archive_Pages extends Opt_In_Condition_Abstract {
	public function is_allowed() {
		if ( ! Opt_In_Utils::is_woocommerce_active() ) {
			return false;
		}

		if ( isset( $this->args->wc_archive_pages ) ) {
			$archive_pages = (array) $this->args->wc_archive_pages;

			if ( is_product_tag() ) {
				$allowed = in_array( 'is_product_tag', $archive_pages, true );
			} elseif ( is_product_category() ) {
				$allowed = in_array( 'is_product_category', $archive_pages, true );
			} elseif ( is_shop() ) {
				$allowed = in_array( 'is_shop', $archive_pages, true );
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
