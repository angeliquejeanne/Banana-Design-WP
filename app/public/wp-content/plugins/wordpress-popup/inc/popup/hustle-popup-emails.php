<?php

class Hustle_Popup_Emails extends Hustle_Meta {

	/**
	 * Get the defaults for this meta.
	 * 
	 * @since 4.0
	 * @return array
	 */
	public function get_defaults() {
		return $this->get_emails_base_defaults();
	}

}
