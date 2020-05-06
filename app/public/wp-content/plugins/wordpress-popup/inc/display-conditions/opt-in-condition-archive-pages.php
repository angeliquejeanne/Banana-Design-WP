<?php

/**
* This functionality has been changed to affect Archive Pages (is_archive, is_tag, is_category).
*/
class Opt_In_Condition_Archive_Pages extends Opt_In_Condition_Abstract {
	public function is_allowed() {

		if ( isset( $this->args->archive_pages ) ) {
			$archive_pages = (array) $this->args->archive_pages;

			if ( is_tag() ) {
				$allowed = in_array( 'is_tag', $archive_pages, true );
			} elseif ( is_category() ) {
				$allowed = in_array( 'is_category', $archive_pages, true );
			} elseif ( is_author() ) {
				$allowed = in_array( 'is_author', $archive_pages, true );
			} elseif ( is_date() ) {
				$allowed = in_array( 'is_date', $archive_pages, true );
			} elseif ( is_post_type_archive() ) {
				$allowed = in_array( 'is_post_type_archive', $archive_pages, true );
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
