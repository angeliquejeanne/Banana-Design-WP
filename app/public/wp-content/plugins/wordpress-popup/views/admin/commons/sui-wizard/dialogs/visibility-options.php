<?php if ( isset( $smallcaps_singular ) ) {
	$smallcaps_singular = $smallcaps_singular;
} else {
	$smallcaps_singular = esc_html__( 'module', 'hustle' );
}
$post_types = wp_list_pluck( Opt_In_Utils::get_post_types(), 'label', 'name' );
?>

<div id="hustle-dialog--visibility-options" class="sui-dialog sui-dialog-alt" aria-hidden="true" tabindex="-1">

	<div class="sui-dialog-overlay sui-fade-out"></div>

	<div role="dialog"
		class="sui-dialog-content sui-bounce-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription">

		<div class="sui-box" role="document">

			<div class="sui-box-header sui-block-content-center">

				<h3 id="dialogTitle" class="sui-box-title"><?php esc_html_e( 'Choose Conditions', 'hustle' ); ?></h3>

				<button class="hustle-cancel-conditions sui-dialog-close">
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

			</div>

			<div class="sui-box-body sui-box-body-slim sui-block-content-center" style="padding-bottom: 0;">

				<p id="dialogTitle"><small><?php printf( esc_html__( 'Choose the visibility conditions which you want to apply on the %s.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></small></p>

				<?php if ( Opt_In_Utils::is_woocommerce_active() ) { ?>

					<div class="sui-tabs">

						<div role="tablist" class="sui-tabs-menu">

							<button
								type="button"
								role="tab"
								id="hustle-general-conditions"
								class="sui-tab-item active"
								aria-controls="hustle-general-conditions"
								aria-selected="true"
							><?php esc_html_e( 'General', 'hustle' ); ?></button>

							<button
								type="button"
								role="tab"
								id="hustle-wc-conditions"
								class="sui-tab-item"
								aria-controls="hustle-wc-conditions"
								aria-selected="false"
								tabindex="-1"
							><?php esc_html_e( 'Woocommerce', 'hustle' ); ?></button>

						</div>

					</div>

				<?php } ?>

			</div>

			<div class="sui-box-selectors sui-box-selectors-col-2">

				<ul class="sui-spacing-slim">

					<?php
						// devide before CTP and after that
						$first_conditions = [
							'posts' => __( 'Posts', 'hustle' ),
							'pages' => __( 'Pages', 'hustle' ),
						];
						$last_conditions = [
							'categories' => __( 'Categories', 'hustle' ),
							'tags' => __( 'Tags', 'hustle' ),
							'archive_pages' => __( 'Archive Pages', 'hustle' ),
							'wp_conditions' => __( 'Static Pages', 'hustle' ),
							'user_roles' => __( 'User Roles', 'hustle' ),
							'page_templates' => __( 'Page Templates', 'hustle' ),
							'visitor_device' => __( 'Visitor\'s Device', 'hustle' ),
							'on_browser' => __( 'Visitor\'s Browser', 'hustle' ),
							'visitor_logged_in_status' => __( 'Logged in status', 'hustle' ),
							'visitor_country' => __( 'Visitor\'s Country', 'hustle' ),
							'source_of_arrival' => __( 'Source of Arrival', 'hustle' ),
							'from_referrer' => __( 'Referrer', 'hustle' ),
							'on_url' => __( 'Specific URL', 'hustle' ),
							'user_registration' => __( 'After Registration', 'hustle' ),
							'shown_less_than' => __( 'Number of times visitor has seen', 'hustle' ),
							'visitor_commented' => __( 'Visitor Commented Before', 'hustle' ),
						];
						$conditions = array_merge( $first_conditions, $post_types, $last_conditions );

						if ( Opt_In_Utils::is_woocommerce_active() ) {
							// devide before CTP and after that
							$first_wc_conditions = [
								'wc_pages' => __( 'All Woocommerce Pages', 'hustle' ),
							];
							$last_wc_conditions = [
								'wc_categories' => __( 'WooCommerce Categories', 'hustle' ),
								'wc_tags' => __( 'WooCommerce Tags', 'hustle' ),
								'wc_archive_pages' => __( 'WooCommerce Archives', 'hustle' ),
								'wc_static_pages' => __( 'WooCommerce Static Pages', 'hustle' ),
							];
							$conditions = array_merge( $first_wc_conditions, $conditions, $last_wc_conditions );
						}

						/**
						 * Visibility Conditions
						 *
						 * @since 4.1
						 *
						 * @param array $conditions Visibility Conditions.
						 */
						$conditions = apply_filters( 'hustle_visibility_condition_options', $conditions );

						foreach ( $conditions as $key => $label ) {
					?>
						<li class="<?php echo 'wc_' === substr( $key, 0, 3 ) || 'product' === $key ? 'wc' :  'general'; ?>_condition"><label for="hustle-condition--<?php echo esc_attr( $key ); ?>" class="sui-box-selector">
							<input type="checkbox"
								value="<?php echo esc_attr( $key ); ?>"
								name="visibility_options"
								id="hustle-condition--<?php echo esc_attr( $key ); ?>"
								class="hustle-visibility-condition-option" />
							<span><?php echo esc_html( $label ); ?></span>
						</label></li>

					<?php } ?>

				</ul>

			</div>

			<div class="sui-box-footer">

				<button class="sui-button sui-button-ghost hustle-cancel-conditions">
					<?php esc_attr_e( 'Cancel', 'hustle' ); ?>
				</button>

				<button id="hustle-add-conditions" class="sui-button">
					<span class="sui-loading-text"><?php esc_attr_e( 'Add Conditions', 'hustle' ); ?></span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>

			</div>

		</div>

	</div>

</div>
