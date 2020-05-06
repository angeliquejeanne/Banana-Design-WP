<?php $cover_image = self::$plugin_url . 'assets/images/review-condition.png'; ?>

<div
	id="hustle-dialog--review_conditions"
	class="sui-dialog sui-dialog-onboard"
	aria-hidden="true"
	tabindex="-1"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_dismiss_notification' ) ); ?>"
>

	<div class="sui-dialog-overlay sui-fade-out" data-a11y-dialog-hide="hustle-dialog--review_conditions"></div>

	<div
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription"
		role="dialog"
	>

		<div class="sui-slider">

			<ul class="sui-slider-content" role="document">

				<li class="sui-current sui-loaded" data-slide="1">

					<div class="sui-box">

						<div class="sui-box-banner" role="banner" aria-hidden="true">
							<?php echo Opt_In_Utils::render_image_markup( esc_url( $cover_image ), '', 'sui-image sui-image-center' ); // WPCS: XSS ok. ?>
						</div>

						<div class="sui-box-header sui-lg sui-block-content-center">

							<h2 id="dialogTitle" class="sui-box-title"><?php esc_html_e( 'We\'ve fixed visibility conditions!', 'hustle' ); ?></h2>

							<span id="dialogDescription" class="sui-description"><?php printf( esc_html__( 'Prior to Hustle %s, the visibility engine would require you to set rules for each and every post type your theme used, not just the ones you specified. We\'ve updated this behaviour to only display modules based on the post types explicitly defined in your conditions.', 'hustle' ), esc_attr( $version ) ); ?></span>

							<button class="sui-dialog-close" data-a11y-dialog-hide="hustle-dialog--review_conditions">
								<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
							</button>
						</div>

						<div class="sui-box-body">
							<h4><?php esc_html_e( 'Examples', 'hustle' ); ?></h4>

							<span class="sui-description"><?php esc_html_e( 'Let\'s take a couple of examples of "Pages" condition to understand how visibility behavior has changed with this bug fix: ', 'hustle' ); ?></span>
							<?php /* translators: 1. opening 'u' tag, 2. closing 'u' tag */ ?>
							<br><b><?php printf( esc_html__( '1. %1$sPages -> Only 2%2$s', 'hustle' ), '<u>', '</u>' ); ?></b>
							<span class="sui-description"><?php printf( esc_html__( 'In %1$s, your module with the above condition will appear on the two selected pages only. Whereas, before %1$s, it would have appeared on the two chosen pages and other post types (such as posts, categories, tags) as well, unless you individually add a condition to not show your module on them.', 'hustle' ), esc_attr( $version ) ); ?></span>
							<?php /* translators: 1. opening 'u' tag, 2. closing 'u' tag */ ?>
							<b><?php printf( esc_html__( '2. %1$sPages -> All except 2%2$s', 'hustle' ), '<u>', '</u>' ); ?></b>
							<span class="sui-description"><?php printf( esc_html__( 'In %1$s, your module will appear on all pages except the two selected pages, and it won\'t appear on other post types such as posts, categories, or tags unless you explicitly add a condition for them. Whereas, before %1$s, this would have appeared across your website except on the two selected pages.', 'hustle' ), esc_attr( $version ) ); ?></span>

						</div>

						<div class="sui-box-body">
							<h4><?php esc_html_e( 'Recommended Actions', 'hustle' ); ?></h4>

							<span class="sui-description"><?php esc_html_e( '1. Review all your active modules\' visibility behavior to ensure that they appear on correct pages.', 'hustle' ); ?></span>
							<span class="sui-description">
							<?php
								printf(
									/* translators: 1. opening 'a' tag to support, 2. closing 'a' tag */
									esc_html__( '2. Unable to make the visibility conditions work correctly? %1$sContact Support%2$s.', 'hustle' ),
									'<a href="' . esc_url( $support_url ) . '" target="_blank">',
									'</a>'
								);
								?>
							</span>
							<span class="sui-description">
							<?php
								printf(
									/* translators: 1. opening 'b' tag, 2. closing 'b' tag, 3. v4.0.4 or v7.0.4 */
									esc_html__( '3. Not yet ready for the new visibility behavior? Go to the Plugins page and use the "%1$sRollback to %3$s%2$s" link below Hustle to downgrade Hustle to %3$s.', 'hustle' ),
									'<b>',
									'</b>',
									Opt_In_Utils::_is_free() ? 'v7.0.4' : 'v4.0.4'
								);
								?>
							</span>
						</div>

						<div class="sui-box-footer">

							<button
								id="getStarted"
								class="sui-button"
								style="float: right;"
								data-a11y-dialog-hide="hustle-dialog--review_conditions"
							>
								<?php esc_html_e( 'Review Modules', 'hustle' ); ?>
							</button><br>

						</div>

					</div>

					<p class="sui-onboard-skip"><a href="#" data-a11y-dialog-hide="hustle-dialog--review_conditions"><?php esc_html_e( 'I\'ll check this later', 'hustle' ); ?></a></p>

				</li>

			</ul>

		</div>

	</div>

</div>
