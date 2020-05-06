<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">

		<span class="sui-settings-label"><?php esc_html_e( 'Inline Content', 'hustle' ); ?></span>

		<span class="sui-description"><?php esc_html_e( 'Add a social bar inline to your website’s posts and pages.', 'hustle' ); ?></span>

	</div>

	<div class="sui-box-settings-col-2">

		<?php
		// SETTINGS: Enable inline module.
		self::static_render(
			'admin/sshare/display-options/tpl--position-settings',
			array(
				'label'       => esc_html__( 'inline module', 'hustle' ),
				'description' => esc_html__( "By enabling this you can add a social bar above, below or both above and below of your website's posts, pages etc.", 'hustle' ),
				'prefix'      => 'inline',
				'settings'    => $settings,
				'alignment'   => true,
				'positions'   => array(
					'below' => array(
						'label'   => esc_html__( 'Below', 'hustle' ),
						'image1x' => 'social-position/inline-below.png',
						'image2x' => 'social-position/inline-below@2x.png',
					),
					'above' => array(
						'label'   => esc_html__( 'Above', 'hustle' ),
						'image1x' => 'social-position/inline-above.png',
						'image2x' => 'social-position/inline-above@2x.png',
					),
					'both'  => array(
						'label'   => esc_html__( 'Both', 'hustle' ),
						'image1x' => 'social-position/inline-both.png',
						'image2x' => 'social-position/inline-both@2x.png',
					),
				),
			)
		);
		?>

	</div>

</div>
