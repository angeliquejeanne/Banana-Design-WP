<?php
/**
 * A class to serve static data
 *
 * Class Opt_In_Static
 */
if ( ! class_exists( 'Opt_In_Static', false ) ) {

	class Opt_In_Static {

		/**
		 * Returns animations
		 * Returns Popup Pro animations if it's installed and active
		 *
		 *
		 * @return object
		 */
		public function get_animations() {

			$animations_in = array(
				''                                        => array(
					'' => __( 'No Animation', 'hustle' ),
				),
				__( 'Bouncing Entrances', 'hustle' ) => array(
					'bounceIn'      => __( 'Bounce In', 'hustle' ),
					'bounceInUp'    => __( 'Bounce In Up', 'hustle' ),
					'bounceInRight' => __( 'Bounce In Right', 'hustle' ),
					'bounceInDown'  => __( 'Bounce In Down', 'hustle' ),
					'bounceInLeft'  => __( 'Bounce In Left', 'hustle' ),
				),
				__( 'Fading Entrances', 'hustle' ) => array(
					'fadeIn'      => __( 'Fade In', 'hustle' ),
					'fadeInUp'    => __( 'Fade In Up', 'hustle' ),
					'fadeInRight' => __( 'Fade In Right', 'hustle' ),
					'fadeInDown'  => __( 'Fade In Down', 'hustle' ),
					'fadeInLeft'  => __( 'Fade In Left', 'hustle' ),
				),
				__( 'Falling Entrances', 'hustle' )  => array(
					'fall'     => __( 'Fall In', 'hustle' ), // MISSING
					'sidefall' => __( 'Fade In Side', 'hustle' ), // MISSING
				),
				__( 'Rotating Entrances', 'hustle' ) => array(
					'rotateIn'          => __( 'Rotate In', 'hustle' ),
					'rotateInDownLeft'  => __( 'Rotate In Down Left', 'hustle' ),
					'rotateInDownRight' => __( 'Rotate In Down Right', 'hustle' ),
					'rotateInUpLeft'    => __( 'Rotate In Up Left', 'hustle' ),
					'rotateInUpRight'   => __( 'Rotate In Up Right', 'hustle' ),
				),
				__( 'Sliding Entrances', 'hustle' ) => array(
					'slideInUp'    => __( 'Slide In Up', 'hustle' ),
					'slideInRight' => __( 'Slide In Right', 'hustle' ),
					'slideInDown'  => __( 'Slide In Down', 'hustle' ),
					'slideInLeft'  => __( 'Slide In Left', 'hustle' ),
				),
				__( 'Zoom Entrances', 'hustle' ) => array(
					'zoomIn'      => __( 'Zoom In', 'hustle' ),
					'zoomInUp'    => __( 'Zoom In Up', 'hustle' ),
					'zoomInRight' => __( 'Zoom In Right', 'hustle' ),
					'zoomInDown'  => __( 'Zoom In Down', 'hustle' ),
					'zoomInLeft'  => __( 'Zoom In Left', 'hustle' ),
					'scaled'      => __( 'Super Scaled', 'hustle' ), // MISSING
				),
				__( '3D Entrances', 'hustle' ) => array(
					'sign wpoi-modal'    => __( '3D Sign', 'hustle' ), // MISSING
					'slit wpoi-modal'    => __( '3D Slit', 'hustle' ), // MISSING
					'flipx wpoi-modal'   => __( '3D Flip (Horizontal)', 'hustle' ), // MISSING
					'flipy wpoi-modal'   => __( '3D Flip (Vertical)', 'hustle' ), // MISSING
					'rotatex wpoi-modal' => __( '3D Rotate (Left)', 'hustle' ), // MISSING
					'rotatey wpoi-modal' => __( '3D Rotate (Bottom)', 'hustle' ), // MISSING
				),
				__( 'Special Entrances', 'hustle' ) => array(
					'rollIn'       => __( 'Roll In', 'hustle' ),
					'lightSpeedIn' => __( 'Light Speed In', 'hustle' ),
					'newspaperIn'  => __( 'Newspaper In', 'hustle' ),
				),
			);

			$animations_out = array(
				''                                         => array(
					'' => __( 'No Animation', 'hustle' ),
				),
				__( 'Bouncing Exits', 'hustle' ) => array(
					'bounceOut'      => __( 'Bounce Out', 'hustle' ),
					'bounceOutUp'    => __( 'Bounce Out Up', 'hustle' ),
					'bounceOutRight' => __( 'Bounce Out Right', 'hustle' ),
					'bounceOutDown'  => __( 'Bounce Out Down', 'hustle' ),
					'bounceOutLeft'  => __( 'Bounce Out Left', 'hustle' ),
				),
				__( 'Fading Exits', 'hustle' )  => array(
					'fadeOut'      => __( 'Fade Out', 'hustle' ),
					'fadeOutUp'    => __( 'Fade Out Up', 'hustle' ),
					'fadeOutRight' => __( 'Fade Out Right', 'hustle' ),
					'fadeOutDown'  => __( 'Fade Out Down', 'hustle' ),
					'fadeOutLeft'  => __( 'Fade Out Left', 'hustle' ),
				),
				__( 'Rotating Exits', 'hustle' ) => array(
					'rotateOut'      => __( 'Rotate In', 'hustle' ),
					'rotateOutUp'    => __( 'Rotate In Up', 'hustle' ),
					'rotateOutRight' => __( 'Rotate In Right', 'hustle' ),
					'rotateOutDown'  => __( 'Rotate In Down', 'hustle' ),
					'rotateOutLeft'  => __( 'Rotate In Left', 'hustle' ),
				),
				__( 'Sliding Exits', 'hustle' ) => array(
					'slideOutUp'    => __( 'Slide Out Up', 'hustle' ),
					'slideOutRight' => __( 'Slide Out Left', 'hustle' ),
					'slideOutDown'  => __( 'Slide Out Down', 'hustle' ),
					'slideOutLeft'  => __( 'Slide Out Right', 'hustle' ),
				),
				__( 'Zoom Exits', 'hustle' )    => array(
					'zoomOut'      => __( 'Zoom Out', 'hustle' ),
					'zoomOutUp'    => __( 'Zoom Out Up', 'hustle' ),
					'zoomOutRight' => __( 'Zoom Out Right', 'hustle' ),
					'zoomOutDown'  => __( 'Slide Out Down', 'hustle' ),
					'zoomOutLeft'  => __( 'Slide Out Left', 'hustle' ),
					'scaled'       => __( 'Super Scaled', 'hustle' ), // MISSING
				),
				__( '3D Effects', 'hustle' )    => array(
					'sign wpoi-modal'    => __( '3D Sign', 'hustle' ), // MISSING
					'flipx wpoi-modal'   => __( '3D Flip (Horizontal)', 'hustle' ), // MISSING
					'flipy wpoi-modal'   => __( '3D Flip (Vertical)', 'hustle' ), // MISSING
					'rotatex wpoi-modal' => __( '3D Rotate (Left)', 'hustle' ), // MISSING
					'rotatey wpoi-modal' => __( '3D Rotate (Bottom)', 'hustle' ), // MISSING
				),
				__( 'Special Exits', 'hustle' ) => array(
					'rollOut'       => __( 'Roll Out', 'hustle' ),
					'lightSpeedOut' => __( 'Light Speed Out', 'hustle' ),
					'newspaperOut'  => __( 'Newspaper Out', 'hustle' ),
				),
			);

			return (object) array(
				'in'  => $animations_in,
				'out' => $animations_out,
			);
		}

		/**
		 * Default form filds for a new form
		 *
		 * @since the beginning of time
		 * @since 4.0 is static
		 *
		 */
		public static function default_form_fields() {

			return array(
				'first_name' => array(
					'required'    => 'false',
					'label'       => __( 'First Name', 'hustle' ),
					'name'        => 'first_name',
					'type'        => 'name',
					'placeholder' => 'John',
					'can_delete'  => true,
				),
				'last_name'  => array(
					'required'    => 'false',
					'label'       => __( 'Last Name', 'hustle' ),
					'name'        => 'last_name',
					'type'        => 'name',
					'placeholder' => 'Smith',
					'can_delete'  => true,
				),
				'email'      => array(
					'required'    => 'true',
					'label'       => __( 'Your email', 'hustle' ),
					'name'        => 'email',
					'type'        => 'email',
					'placeholder' => 'johnsmith@example.com',
					'validate'	  => 'true',
					'can_delete'  => false,
				),
				'submit'     => array(
					'required'     => 'true',
					'label'        => __( 'Submit', 'hustle' ),
					'error_message'=> __( 'Something went wrong, please try again.', 'hustle' ),
					'name'         => 'submit',
					'type'         => 'submit',
					'placeholder'  => __( 'Subscribe', 'hustle' ),
					'can_delete'   => false,
				),
			);
		}

		/**
		 * Returns array of countries
		 *
		 * @return array|mixed|null|void
		 */
		public function get_countries() {

			return apply_filters(
				'opt_in-country-list',
				array(
					'AU' => __( 'Australia', 'hustle' ),
					'AF' => __( 'Afghanistan', 'hustle' ),
					'AL' => __( 'Albania', 'hustle' ),
					'DZ' => __( 'Algeria', 'hustle' ),
					'AS' => __( 'American Samoa', 'hustle' ),
					'AD' => __( 'Andorra', 'hustle' ),
					'AO' => __( 'Angola', 'hustle' ),
					'AI' => __( 'Anguilla', 'hustle' ),
					'AQ' => __( 'Antarctica', 'hustle' ),
					'AG' => __( 'Antigua and Barbuda', 'hustle' ),
					'AR' => __( 'Argentina', 'hustle' ),
					'AM' => __( 'Armenia', 'hustle' ),
					'AW' => __( 'Aruba', 'hustle' ),
					'AT' => __( 'Austria', 'hustle' ),
					'AZ' => __( 'Azerbaijan', 'hustle' ),
					'BS' => __( 'Bahamas', 'hustle' ),
					'BH' => __( 'Bahrain', 'hustle' ),
					'BD' => __( 'Bangladesh', 'hustle' ),
					'BB' => __( 'Barbados', 'hustle' ),
					'BY' => __( 'Belarus', 'hustle' ),
					'BE' => __( 'Belgium', 'hustle' ),
					'BZ' => __( 'Belize', 'hustle' ),
					'BJ' => __( 'Benin', 'hustle' ),
					'BM' => __( 'Bermuda', 'hustle' ),
					'BT' => __( 'Bhutan', 'hustle' ),
					'BO' => __( 'Bolivia', 'hustle' ),
					'BA' => __( 'Bosnia and Herzegovina', 'hustle' ),
					'BW' => __( 'Botswana', 'hustle' ),
					'BV' => __( 'Bouvet Island', 'hustle' ),
					'BR' => __( 'Brazil', 'hustle' ),
					'IO' => __( 'British Indian Ocean Territory', 'hustle' ),
					'BN' => __( 'Brunei', 'hustle' ),
					'BG' => __( 'Bulgaria', 'hustle' ),
					'BF' => __( 'Burkina Faso', 'hustle' ),
					'BI' => __( 'Burundi', 'hustle' ),
					'KH' => __( 'Cambodia', 'hustle' ),
					'CM' => __( 'Cameroon', 'hustle' ),
					'CA' => __( 'Canada', 'hustle' ),
					'CV' => __( 'Cape Verde', 'hustle' ),
					'KY' => __( 'Cayman Islands', 'hustle' ),
					'CF' => __( 'Central African Republic', 'hustle' ),
					'TD' => __( 'Chad', 'hustle' ),
					'CL' => __( 'Chile', 'hustle' ),
					'CN' => __( 'China, People\'s Republic of', 'hustle' ),
					'CX' => __( 'Christmas Island', 'hustle' ),
					'CC' => __( 'Cocos Islands', 'hustle' ),
					'CO' => __( 'Colombia', 'hustle' ),
					'KM' => __( 'Comoros', 'hustle' ),
					'CD' => __( 'Congo, Democratic Republic of the', 'hustle' ),
					'CG' => __( 'Congo, Republic of the', 'hustle' ),
					'CK' => __( 'Cook Islands', 'hustle' ),
					'CR' => __( 'Costa Rica', 'hustle' ),
					'CI' => __( 'Côte d\'Ivoire', 'hustle' ),
					'HR' => __( 'Croatia', 'hustle' ),
					'CU' => __( 'Cuba', 'hustle' ),
					'CW' => __( 'Curaçao', 'hustle' ),
					'CY' => __( 'Cyprus', 'hustle' ),
					'CZ' => __( 'Czech Republic', 'hustle' ),
					'DK' => __( 'Denmark', 'hustle' ),
					'DJ' => __( 'Djibouti', 'hustle' ),
					'DM' => __( 'Dominica', 'hustle' ),
					'DO' => __( 'Dominican Republic', 'hustle' ),
					'TL' => __( 'East Timor', 'hustle' ),
					'EC' => __( 'Ecuador', 'hustle' ),
					'EG' => __( 'Egypt', 'hustle' ),
					'SV' => __( 'El Salvador', 'hustle' ),
					'GQ' => __( 'Equatorial Guinea', 'hustle' ),
					'ER' => __( 'Eritrea', 'hustle' ),
					'EE' => __( 'Estonia', 'hustle' ),
					'ET' => __( 'Ethiopia', 'hustle' ),
					'FK' => __( 'Falkland Islands', 'hustle' ),
					'FO' => __( 'Faroe Islands', 'hustle' ),
					'FJ' => __( 'Fiji', 'hustle' ),
					'FI' => __( 'Finland', 'hustle' ),
					'FR' => __( 'France', 'hustle' ),
					'FX' => __( 'France, Metropolitan', 'hustle' ),
					'GF' => __( 'French Guiana', 'hustle' ),
					'PF' => __( 'French Polynesia', 'hustle' ),
					'TF' => __( 'French South Territories', 'hustle' ),
					'GA' => __( 'Gabon', 'hustle' ),
					'GM' => __( 'Gambia', 'hustle' ),
					'GE' => __( 'Georgia', 'hustle' ),
					'DE' => __( 'Germany', 'hustle' ),
					'GH' => __( 'Ghana', 'hustle' ),
					'GI' => __( 'Gibraltar', 'hustle' ),
					'GR' => __( 'Greece', 'hustle' ),
					'GL' => __( 'Greenland', 'hustle' ),
					'GD' => __( 'Grenada', 'hustle' ),
					'GP' => __( 'Guadeloupe', 'hustle' ),
					'GU' => __( 'Guam', 'hustle' ),
					'GT' => __( 'Guatemala', 'hustle' ),
					'GN' => __( 'Guinea', 'hustle' ),
					'GW' => __( 'Guinea-Bissau', 'hustle' ),
					'GY' => __( 'Guyana', 'hustle' ),
					'HT' => __( 'Haiti', 'hustle' ),
					'HM' => __( 'Heard Island And Mcdonald Island', 'hustle' ),
					'HN' => __( 'Honduras', 'hustle' ),
					'HK' => __( 'Hong Kong', 'hustle' ),
					'HU' => __( 'Hungary', 'hustle' ),
					'IS' => __( 'Iceland', 'hustle' ),
					'IN' => __( 'India', 'hustle' ),
					'ID' => __( 'Indonesia', 'hustle' ),
					'IR' => __( 'Iran', 'hustle' ),
					'IQ' => __( 'Iraq', 'hustle' ),
					'IE' => __( 'Ireland', 'hustle' ),
					'IL' => __( 'Israel', 'hustle' ),
					'IT' => __( 'Italy', 'hustle' ),
					'JM' => __( 'Jamaica', 'hustle' ),
					'JP' => __( 'Japan', 'hustle' ),
					'JT' => __( 'Johnston Island', 'hustle' ),
					'JO' => __( 'Jordan', 'hustle' ),
					'KZ' => __( 'Kazakhstan', 'hustle' ),
					'KE' => __( 'Kenya', 'hustle' ),
					'XK' => __( 'Kosovo', 'hustle' ),
					'KI' => __( 'Kiribati', 'hustle' ),
					'KP' => __( 'Korea, Democratic People\'s Republic of', 'hustle' ),
					'KR' => __( 'Korea, Republic of', 'hustle' ),
					'KW' => __( 'Kuwait', 'hustle' ),
					'KG' => __( 'Kyrgyzstan', 'hustle' ),
					'LA' => __( 'Lao People\'s Democratic Republic', 'hustle' ),
					'LV' => __( 'Latvia', 'hustle' ),
					'LB' => __( 'Lebanon', 'hustle' ),
					'LS' => __( 'Lesotho', 'hustle' ),
					'LR' => __( 'Liberia', 'hustle' ),
					'LY' => __( 'Libya', 'hustle' ),
					'LI' => __( 'Liechtenstein', 'hustle' ),
					'LT' => __( 'Lithuania', 'hustle' ),
					'LU' => __( 'Luxembourg', 'hustle' ),
					'MO' => __( 'Macau', 'hustle' ),
					'MK' => __( 'Macedonia', 'hustle' ),
					'MG' => __( 'Madagascar', 'hustle' ),
					'MW' => __( 'Malawi', 'hustle' ),
					'MY' => __( 'Malaysia', 'hustle' ),
					'MV' => __( 'Maldives', 'hustle' ),
					'ML' => __( 'Mali', 'hustle' ),
					'MT' => __( 'Malta', 'hustle' ),
					'MH' => __( 'Marshall Islands', 'hustle' ),
					'MQ' => __( 'Martinique', 'hustle' ),
					'MR' => __( 'Mauritania', 'hustle' ),
					'MU' => __( 'Mauritius', 'hustle' ),
					'YT' => __( 'Mayotte', 'hustle' ),
					'MX' => __( 'Mexico', 'hustle' ),
					'FM' => __( 'Micronesia', 'hustle' ),
					'MD' => __( 'Moldova', 'hustle' ),
					'MC' => __( 'Monaco', 'hustle' ),
					'MN' => __( 'Mongolia', 'hustle' ),
					'ME' => __( 'Montenegro', 'hustle' ),
					'MS' => __( 'Montserrat', 'hustle' ),
					'MA' => __( 'Morocco', 'hustle' ),
					'MZ' => __( 'Mozambique', 'hustle' ),
					'MM' => __( 'Myanmar', 'hustle' ),
					'NA' => __( 'Namibia', 'hustle' ),
					'NR' => __( 'Nauru', 'hustle' ),
					'NP' => __( 'Nepal', 'hustle' ),
					'NL' => __( 'Netherlands', 'hustle' ),
					'AN' => __( 'Netherlands Antilles', 'hustle' ),
					'NC' => __( 'New Caledonia', 'hustle' ),
					'NZ' => __( 'New Zealand', 'hustle' ),
					'NI' => __( 'Nicaragua', 'hustle' ),
					'NE' => __( 'Niger', 'hustle' ),
					'NG' => __( 'Nigeria', 'hustle' ),
					'NU' => __( 'Niue', 'hustle' ),
					'NF' => __( 'Norfolk Island', 'hustle' ),
					'MP' => __( 'Northern Mariana Islands', 'hustle' ),
					'MP' => __( 'Mariana Islands, Northern', 'hustle' ),
					'NO' => __( 'Norway', 'hustle' ),
					'OM' => __( 'Oman', 'hustle' ),
					'PK' => __( 'Pakistan', 'hustle' ),
					'PW' => __( 'Palau', 'hustle' ),
					'PS' => __( 'Palestine, State of', 'hustle' ),
					'PA' => __( 'Panama', 'hustle' ),
					'PG' => __( 'Papua New Guinea', 'hustle' ),
					'PY' => __( 'Paraguay', 'hustle' ),
					'PE' => __( 'Peru', 'hustle' ),
					'PH' => __( 'Philippines', 'hustle' ),
					'PN' => __( 'Pitcairn Islands', 'hustle' ),
					'PL' => __( 'Poland', 'hustle' ),
					'PT' => __( 'Portugal', 'hustle' ),
					'PR' => __( 'Puerto Rico', 'hustle' ),
					'QA' => __( 'Qatar', 'hustle' ),
					'RE' => __( 'Réunion', 'hustle' ),
					'RO' => __( 'Romania', 'hustle' ),
					'RU' => __( 'Russia', 'hustle' ),
					'RW' => __( 'Rwanda', 'hustle' ),
					'SH' => __( 'Saint Helena', 'hustle' ),
					'KN' => __( 'Saint Kitts and Nevis', 'hustle' ),
					'LC' => __( 'Saint Lucia', 'hustle' ),
					'PM' => __( 'Saint Pierre and Miquelon', 'hustle' ),
					'VC' => __( 'Saint Vincent and the Grenadines', 'hustle' ),
					'WS' => __( 'Samoa', 'hustle' ),
					'SM' => __( 'San Marino', 'hustle' ),
					'ST' => __( 'Sao Tome and Principe', 'hustle' ),
					'SA' => __( 'Saudi Arabia', 'hustle' ),
					'SN' => __( 'Senegal', 'hustle' ),
					'CS' => __( 'Serbia', 'hustle' ),
					'SC' => __( 'Seychelles', 'hustle' ),
					'SL' => __( 'Sierra Leone', 'hustle' ),
					'SG' => __( 'Singapore', 'hustle' ),
					'MF' => __( 'Sint Maarten', 'hustle' ),
					'SK' => __( 'Slovakia', 'hustle' ),
					'SI' => __( 'Slovenia', 'hustle' ),
					'SB' => __( 'Solomon Islands', 'hustle' ),
					'SO' => __( 'Somalia', 'hustle' ),
					'ZA' => __( 'South Africa', 'hustle' ),
					'GS' => __( 'South Georgia and the South Sandwich Islands', 'hustle' ),
					'ES' => __( 'Spain', 'hustle' ),
					'LK' => __( 'Sri Lanka', 'hustle' ),
					'XX' => __( 'Stateless Persons', 'hustle' ),
					'SD' => __( 'Sudan', 'hustle' ),
					'SD' => __( 'Sudan, South', 'hustle' ),
					'SR' => __( 'Suriname', 'hustle' ),
					'SJ' => __( 'Svalbard and Jan Mayen', 'hustle' ),
					'SZ' => __( 'Swaziland', 'hustle' ),
					'SE' => __( 'Sweden', 'hustle' ),
					'CH' => __( 'Switzerland', 'hustle' ),
					'SY' => __( 'Syria', 'hustle' ),
					'TW' => __( 'Taiwan, Republic of China', 'hustle' ),
					'TJ' => __( 'Tajikistan', 'hustle' ),
					'TZ' => __( 'Tanzania', 'hustle' ),
					'TH' => __( 'Thailand', 'hustle' ),
					'TG' => __( 'Togo', 'hustle' ),
					'TK' => __( 'Tokelau', 'hustle' ),
					'TO' => __( 'Tonga', 'hustle' ),
					'TT' => __( 'Trinidad and Tobago', 'hustle' ),
					'TN' => __( 'Tunisia', 'hustle' ),
					'TR' => __( 'Turkey', 'hustle' ),
					'TM' => __( 'Turkmenistan', 'hustle' ),
					'TC' => __( 'Turks and Caicos Islands', 'hustle' ),
					'TV' => __( 'Tuvalu', 'hustle' ),
					'UG' => __( 'Uganda', 'hustle' ),
					'UA' => __( 'Ukraine', 'hustle' ),
					'AE' => __( 'United Arab Emirates', 'hustle' ),
					'GB' => __( 'United Kingdom', 'hustle' ),
					'US' => __( 'United States of America (USA)', 'hustle' ),
					'UM' => __( 'US Minor Outlying Islands', 'hustle' ),
					'UY' => __( 'Uruguay', 'hustle' ),
					'UZ' => __( 'Uzbekistan', 'hustle' ),
					'VU' => __( 'Vanuatu', 'hustle' ),
					'VA' => __( 'Vatican City', 'hustle' ),
					'VE' => __( 'Venezuela', 'hustle' ),
					'VN' => __( 'Vietnam', 'hustle' ),
					'VG' => __( 'Virgin Islands, British', 'hustle' ),
					'VI' => __( 'Virgin Islands, U.S.', 'hustle' ),
					'WF' => __( 'Wallis And Futuna', 'hustle' ),
					'EH' => __( 'Western Sahara', 'hustle' ),
					'YE' => __( 'Yemen', 'hustle' ),
					'ZM' => __( 'Zambia', 'hustle' ),
					'ZW' => __( 'Zimbabwe', 'hustle' ),
				)
			);
		}

		/**
		 * Returns array of browsers
		 *
		 * @since 4.1
		 * @return array|mixed|null|void
		 */
		public function get_browsers() {
			return apply_filters(
				'hustle_get_browsers_list',
				array(
					'chrome'	=> __( 'Chrome', 'hustle' ),
					'firefox'	=> __( 'Firefox', 'hustle' ),
					'safari'	=> __( 'Safari', 'hustle' ),
					'edge' 		=> __( 'Edge', 'hustle' ),
					'MSIE'		=> __( 'Internet Explorer', 'hustle' ),
					'opera' 	=> __( 'Opera', 'hustle' ),
				)
			);
		}

	}
}
