<div id="wph-wizard-choose_image" class="sui-upload {{ ( !_.isEmpty( feature_image ) ) ? 'sui-has_file' : '' }}">

	<input type="file"
		name="feature_image"
		value="{{ feature_image }}"
		data-attribute="feature_image"
		readonly="readonly" />

	<div class="sui-upload-image" aria-hidden="true">

		<div class="sui-image-mask"></div>

		<div role="button" class="sui-image-preview wpmudev-feature-image-browse" style="background-image: url({{ feature_image }});"></div>

	</div>

	<button class="sui-upload-button wpmudev-feature-image-browse">
		<i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php esc_html_e( 'Upload image', 'hustle' ); ?>
	</button>

	<div class="sui-upload-file">

		<span>{{ feature_image }}</span>

		<button id="wpmudev-feature-image-clear"
			aria-label="<?php esc_attr_e( 'Clear', 'hustle' ); ?>">
			<i class="sui-icon-close" aria-hidden="true"></i>
		</button>

	</div>

</div>
