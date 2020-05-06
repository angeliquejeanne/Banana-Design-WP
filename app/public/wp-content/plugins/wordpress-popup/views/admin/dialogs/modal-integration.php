<script type="text/template" id="hustle-integration-dialog-tpl">

	<div class="sui-dialog sui-dialog-alt sui-dialog-sm" id="hustle-integration-dialog">

		<div class="sui-dialog-overlay sui-fade-in" tabindex="-1" data-a11y-dialog-hide=""></div>

		<div class="sui-dialog-content sui-bounce-in" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">

			<div class="sui-box" role="document">

			<!-- content -->

				<form style="margin: 0;">

					<div class="sui-box-header sui-dialog-with-image sui-block-content-center" style="padding-bottom: 0;">

						<button class="sui-dialog-back hustle-provider-back" aria-label="Back" style="display: none;"></button>

						<button class="sui-dialog-close hustle-provider-close" aria-label="<?php esc_html_e( 'Close', 'hustle' ); ?>"></button>

						<div class="sui-dialog-image" aria-hidden="true">
							<img
								src="{{ image }}"
								alt="{{ title }}"
								class="sui-image sui-image-center"
							/>
						</div>

						<div class="sui-box-content integration-header"></div>

					</div>

					<div class="sui-box-body"></div>

				</form>

				<div class="sui-box-footer" style="padding-top: 0;"></div>

			<!-- /content -->

			</div>

		</div>

	</div>

</script>

<script type="text/template" id="hustle-integration-dialog-content-tpl">

	<div class="sui-box-header">

		<button class="sui-dialog-back hustle-provider-back" aria-label="Back" style="display: none;"></button>

		<div class="sui-box-image" aria-hidden="true">

			<img src="{{ image }}" alt="{{title}}"
					class="sui-image sui-image-center" />

		</div>

		<button class="sui-dialog-close hustle-provider-close" aria-label="<?php esc_html_e( 'Close', 'hustle' ); ?>"></button>

		<div class="sui-box-content integration-header"></div>

	</div>

	<div class="sui-box-body"></div>

	<div class="sui-box-footer sui-align-unset"></div>

</script>

<script type="text/template" id="hustle-dialog-tpl">

	<div class="sui-dialog" id="hustle-dialog" aria-hidden="true">

		<div class="sui-dialog-overlay sui-fade-in" tabindex="-1" data-a11y-dialog-hide=""></div>

		<div class="sui-dialog-content sui-bounce-in" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">

			<div class="sui-box" role="document"></div>

		</div>

	</div>

</script>

<script type="text/template" id="hustle-dialog-header-tpl">

	<div class="sui-box-header">

		<h3 class="sui-box-title" id="dialogTitle">{{ title }}</h3>

		<div class="sui-actions-right">

			<button data-a11y-dialog-hide="" class="sui-dialog-close" aria-label=""></button>

		</div>

	</div>

</script>

<script type="text/template" id="hustle-dialog-loader-tpl">

	<p class="fui-loading-dialog" aria-label="Loading content">

		<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

	</p>

</script>
