<?php

class Opt_In_Condition_On_Browser extends Opt_In_Condition_Abstract {
	public function is_allowed() {

		if ( isset( $this->args->browsers ) ) {

			if ( 'except' === $this->args->filter_type ) {
				return ! ( $this->verify_browser( $this->args->browsers ) );
			} elseif ( 'only' === $this->args->filter_type ) {
				return $this->utils()->verify_browser( $this->args->browsers );
			}
		}

		return false;
	}

	/**
	 * Checkes the user agent for known browsers
	 *
	 * @param  array $country_codes List of country codes.
	 * @return bool
	 */
	public function verify_browser( $browsers ) {
		$browser = $this->get_current_user_agent( $_SERVER['HTTP_USER_AGENT'] );
		return in_array( $browser, (array) $browsers, true );
	}

	/**
	 * Returns current actual url, the one seen on browser
	 *
	 * @return string
	 */
	private function get_current_user_agent( $user_agent ) {
		$browser = 'other';

		try {
			// The order matters.
			if ( strpos( $user_agent, 'Opera' ) || strpos( $user_agent, 'OPR/' ) ) {
				throw new Exception( 'opera' );
			}

			if ( strpos( $user_agent, 'Edge' ) ) {
				throw new Exception( 'edge' );
			}

			if ( strpos( $user_agent, 'Firefox' ) ) {
				throw new Exception( 'firefox' );
			}

			if ( strpos( $user_agent, 'MSIE' ) || strpos( $user_agent, 'Trident/7' ) ) {
				throw new Exception( 'MSIE' );
			}

			if ( strpos( $user_agent, 'Chrome' ) ) {
				throw new Exception( 'chrome' );

			} else {

				// Chrome for iOS doesn't display 'Chrome' in the UA.
				preg_match_all( '/^.*(iPhone|iPad).*(OS\s[0-9]).*(CriOS|Version)\/[.0-9]*\sMobile.*$/', $user_agent, $matches );

				// TODO: watch out for old ios versions.
				if ( ! empty( $matches ) && ! empty( $matches[3] ) ) {
					if ( 'CriOS' === $matches[3][0] ) {
						throw new Exception( 'chrome' );
					} else {
						throw new Exception( 'safari' );
					}
				}
			}

			if ( strpos( $user_agent, 'Safari' ) ) {
				throw new Exception( 'safari' );
			}
		} catch ( Exception $e ) {
			$browser = $e->getMessage();
		}

		/**
		 * Filter the current browser based on the user agent
		 *
		 * @since 4.1.0
		 * @param string $browser    Detected browser.
		 * @param string $user_agent Passed user agent.
		 */
		return apply_filters( 'hustle_user_agent_visibility_verify', $browser, $user_agent );
	}
}
