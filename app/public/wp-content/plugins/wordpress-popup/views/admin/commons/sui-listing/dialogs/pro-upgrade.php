<div
	id="hustle-dialog--upgrade-to-pro"
	class="sui-dialog sui-dialog-alt sui-dialog-sm"
	aria-hidden="true"
	tabindex="-1"
>

	<div class="sui-dialog-overlay sui-fade-out" data-a11y-dialog-hide="hustle-dialog--upgrade-to-pro"></div>

	<div
		role="dialog"
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription"
	>

		<div class="sui-box" role="document">

			<div class="sui-box-header sui-block-content-center">

				<h3 id="dialogTitle" class="sui-box-title"><?php esc_html_e( 'Upgrade to Pro', 'hustle' ); ?></h3>

				<button class="sui-dialog-close" data-a11y-dialog-hide="hustle-dialog--import">
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

			</div>

			<div class="sui-box-body sui-box-body-slim sui-block-content-center">

				<p id="dialogDescription" class="sui-description"><?php esc_html_e( 'Get unlimited Popups, Slide-ins, Embeds and social sharing widgets with the Pro version of Hustle. Get it as part of a WPMU DEV membership including Smush Pro, Hummingbird Pro and other popular professional plugins.', 'hustle' ); ?></p>

				<a
					target="_blank"
					href="https://premium.wpmudev.org/project/hustle/?utm_source=hustle&utm_medium=plugin&utm_campaign=hustle_modal_upsell_notice"
					class="sui-button sui-button-green"
				>
					<?php esc_html_e( 'Learn more', 'hustle' ); ?>
				</a>

			</div>

			<img
				src="<?php echo esc_url( self::$plugin_url . 'assets/images/hustle-upsell.png' ); ?>"
				srcset="<?php echo esc_url( self::$plugin_url . 'assets/images/hustle-upsell.png' ); ?> 1x, <?php echo esc_url( self::$plugin_url . 'assets/images/hustle-upsell@2x.png' ); ?> 2x"
				alt="<?php esc_html_e( 'Upgrade to Hustle Pro!', 'hustle' ); ?>"
				class="sui-image sui-image-center"
				style="max-width: 128px;"
				aria-hidden="true"
			/>

		</div>

	</div>

</div>
