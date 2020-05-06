<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Vanilla Theme', 'hustle' ); ?></span>
		<span class="sui-description"><?php printf( esc_html__( 'Enable this option if you don’t want to use the styling Hustle adds to your %s.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<label for="hustle-module-use-vanilla" class="sui-toggle hustle-toggle-with-container" data-toggle-on="use-vanilla-on" data-toggle-off="use-vanilla">
			<input type="checkbox"
				name="use_vanilla"
				data-attribute="use_vanilla"
				id="hustle-module-use-vanilla"
				<?php checked( $settings['use_vanilla'], '1' ); ?> />
			<span class="sui-toggle-slider"></span>
		</label>

		<label for="hustle-module-use-vanilla"><?php printf( esc_html__( 'Enable vanilla theme for this %s', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></label>

		<div class="sui-toggle-content" data-toggle-content="use-vanilla-on" style="margin-top: 4px;">

			<?php
			self::static_render(
				'admin/elements/notice-inline',
				[
					'content' => array(
						/* translators: module type display name */
						sprintf( esc_html__( "You have opted for no stylesheet to be enqueued. The %s will inherit styles from your theme's CSS.", 'hustle' ), esc_html( $smallcaps_singular ) ),
					),
				]
			);
			?>

		</div>

	</div>

</div>
