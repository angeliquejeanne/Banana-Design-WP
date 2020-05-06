<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">

		<span class="sui-settings-label"><?php esc_html_e( 'Feature Image', 'hustle' ); ?></span>

		<span class="sui-description"><?php esc_html_e( 'We recommend adding a feature image related clearly to your offering to convert more visitors.', 'hustle' ); ?></span>

	</div>

	<div class="sui-box-settings-col-2">

		<div class="sui-form-field">

			<label class="sui-label"><?php esc_html_e( 'Upload Feature Image (optional)', 'hustle' ); ?></label>

			<div id="wph-wizard-choose_image" class="sui-upload <?php echo empty( $feature_image ) ? '' : 'sui-has_file'; ?>">

				<input type="file"
					name="feature_image"
					value="<?php echo esc_attr( $feature_image ); ?>"
					data-attribute="feature_image"
					readonly="readonly" />

				<div class="sui-upload-image" aria-hidden="true">

					<div class="sui-image-mask"></div>

					<div
						role="button"
						class="sui-image-preview wpmudev-feature-image-browse" 
						style="background-image: url(<?php echo esc_url( $feature_image ); ?>);"
					>
					</div>

				</div>

				<button class="sui-upload-button wpmudev-feature-image-browse">
					<i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php esc_html_e( 'Upload image', 'hustle' ); ?>
				</button>

				<div class="sui-upload-file">

					<span><?php echo esc_url( $feature_image ); ?></span>

					<button id="wpmudev-feature-image-clear"
						aria-label="<?php esc_attr_e( 'Clear', 'hustle' ); ?>">
						<i class="sui-icon-close" aria-hidden="true"></i>
					</button>

				</div>

			</div>

		</div>

	</div>

</div>
