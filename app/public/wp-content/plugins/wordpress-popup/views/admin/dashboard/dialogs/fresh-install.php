<?php
$slide_one_1x = self::$plugin_url . 'assets/images/onboard-welcome.png';
$slide_one_2x = self::$plugin_url . 'assets/images/onboard-welcome@2x.png';

$slide_two_1x = self::$plugin_url . 'assets/images/onboard-welcome.png';
$slide_two_2x = self::$plugin_url . 'assets/images/onboard-welcome@2x.png';
?>

<div
	id="hustle-dialog--welcome"
	class="sui-dialog sui-dialog-onboard"
	aria-hidden="true"
	tabindex="-1"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_dismiss_notification' ) ); ?>"
>

	<div class="sui-dialog-overlay sui-fade-out" data-a11y-dialog-hide="hustle-dialog--welcome"></div>

	<div
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription"
		role="dialog"
	>

		<div class="sui-slider">

			<ul class="sui-slider-content" role="document">

				<?php
				// SLIDE 1: Welcome ?>
				<li class="sui-current sui-loaded" data-slide="1">

					<div class="sui-box">

						<div class="sui-box-banner" role="banner" aria-hidden="true">
							<?php echo Opt_In_Utils::render_image_markup( esc_url( $slide_one_1x ), esc_url( $slide_one_2x ), 'sui-image sui-image-center' ); // WPCS: XSS ok. ?>
						</div>

						<div class="sui-box-header sui-lg sui-block-content-center">

							<h2 id="dialogTitle" class="sui-box-title"><?php printf( esc_html__( 'Hey, %s', 'hustle' ), esc_html( $username ) ); ?></h2>

							<span id="dialogDescription" class="sui-description"><?php esc_html_e( "Welcome to Hustle, the only plugin you'll ever need to turn your visitors into loyal subscribers, leads and customers.", 'hustle' ); ?></span>

							<button class="sui-dialog-close" data-a11y-dialog-hide="hustle-dialog--welcome">
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
							</button>

						</div>

						<div class="sui-box-body sui-lg sui-block-content-center">

							<button
								id="getStarted"
								class="sui-button sui-button-blue sui-button-icon-right"
								data-a11y-dialog-tour-next
							>
								<?php esc_html_e( 'Get Started', 'hustle' ); ?>
								<i class="sui-icon-chevron-right" aria-hidden="true"></i>
							</button>

						</div>

					</div>

					<p class="sui-onboard-skip"><a href="#" data-a11y-dialog-hide="hustle-dialog--welcome"><?php esc_html_e( 'Skip this, I know my way around', 'hustle' ); ?></a></p>

				</li>

				<?php
				// SLIDE 2: Create ?>
				<li data-slide="2">

					<div class="sui-box">

						<div class="sui-box-banner" role="banner" aria-hidden="true">
							<?php echo Opt_In_Utils::render_image_markup( esc_url( $slide_two_1x ), esc_url( $slide_two_2x ), 'sui-image sui-image-center' ); // WPCS: XSS ok. ?>
						</div>

						<div class="sui-box-header sui-lg sui-block-content-center">

							<h2 id="dialogTitle" class="sui-box-title"><?php esc_html_e( 'Create Module', 'hustle' ); ?></h2>

							<span id="dialogDescription" class="sui-description"><?php esc_html_e( 'Choose a module to get started on converting your visitors into subscribers, generate more leads and grow your social following.', 'hustle' ); ?></span>

							<button class="sui-dialog-back" data-a11y-dialog-tour-back>
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Return to previous slide', 'hustle' ); ?></span>
							</button>

							<button class="sui-dialog-close" data-a11y-dialog-hide="hustle-dialog--welcome">
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
							</button>

						</div>

						<div class="sui-box-selectors sui-box-selectors-col-2">

							<ul>
								<?php
									$module_types = array(
										'popup' => array(
											'name' => __( 'Pop-up', 'hustle' ),
											'icon' => 'popup',
										),
										'slidein'         => array(
											'name' => __( 'Slide-in', 'hustle' ),
											'icon' => 'slide-in',
										),
										'embedded'        => array(
											'name' => __( 'Embed', 'hustle' ),
											'icon' => 'embed',
										),
										'social_sharing'  => array(
											'name' => __( 'Social Share', 'hustle' ),
											'icon' => 'share',
										),
									);

									foreach ( $module_types as $key => $attr ) {
										?>

								<li><label for="hustle-create-<?php echo esc_attr( $key ); ?>" class="sui-box-selector">
									<input type="radio" name="hustle-create-welcome" id="hustle-create-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>">
									<span>
										<i class="sui-icon-<?php echo esc_attr( $attr['icon'] ); ?>" aria-hidden="true"></i>
										<?php echo esc_html( $attr['name'] ); ?>
									</span>
								</label></li>

										<?php
									}
								?>

							</ul>

						</div>

						<div class="sui-box-body sui-lg sui-block-content-center">

							<button id="hustle-new-create-module" class="sui-button sui-button-blue sui-button-icon-right" disabled="disabled">
								<span class="sui-loading-text"><?php esc_html_e( 'Create', 'hustle' ); ?></span>
								<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
							</button>

						</div>

					</div>

					<p class="sui-onboard-skip"><a href="#" data-a11y-dialog-hide="hustle-dialog--welcome"><?php esc_html_e( "Skip this, I'll create a module later", 'hustle' ); ?></a></p>

				</li>

			</ul>

		</div>

	</div>

</div>
