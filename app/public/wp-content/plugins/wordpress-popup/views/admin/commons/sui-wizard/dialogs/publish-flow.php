<?php
$image_1x = self::$plugin_url . 'assets/images/hustle-summary.png';
$image_2x = self::$plugin_url . 'assets/images/hustle-summary@2x.png';
?>

<div
	id="hustle-dialog--publish-flow"
	class="sui-dialog sui-dialog-sm sui-dialog-alt"
	tabindex="-1"
	aria-hidden="true"
>

	<div class="sui-dialog-overlay sui-fade-out"></div>

	<div
		role="dialog"
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription"
	>

		<div
			class="sui-box"
			role="document"
			data-loading-icon="loader"
			data-loading-title="<?php printf( esc_html__( 'Publishing %s', 'hustle' ), esc_html( $capitalize_singular ) ); ?>"
			data-loading-desc="<?php printf( esc_html__( 'Great work! Please hold tight a few moments while we publish your %s to the world.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?>"
			data-ready-icon="check"
			data-ready-title="<?php esc_html_e( 'Ready to go!', 'hustle' ); ?>"
			data-ready-desc="<?php printf( esc_html__( 'Your %s is now published and will start appearing on your site based on the visibility conditions you’ve defined.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?>"
		>

			<div class="sui-box-header sui-block-content-center">

				<i id="dialogIcon" class="sui-lg" aria-hidden="true"></i>

				<h3 id="dialogTitle" class="sui-box-title"></h3>

				<button
					class="sui-dialog-close"
					style="display: none;"
				>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

			</div>

			<div class="sui-box-body sui-box-body-slim sui-block-content-center">

				<p id="dialogDescription" class="sui-description"></p>

			</div>

			<?php echo Opt_In_Utils::render_image_markup( esc_url( $image_1x ), esc_url( $image_2x ), 'sui-image sui-image-center', 'auto', '120px' ); // WPCS: XSS ok. ?>

		</div>

	</div>

</div>
