<div class="sui-box" <?php echo 'visibility' !== $section ? 'style="display: none;"' : ''; ?> data-tab="visibility">

	<div class="sui-box-header">

		<h2 class="sui-box-title"><?php esc_html_e( 'Visibility', 'hustle' ); ?></h2>

	</div>

	<div class="sui-box-body">

		<div class="sui-box-settings-row">

			<div class="sui-box-settings-col-1">

				<span class="sui-settings-label"><?php esc_html_e( 'Visibility Rules', 'hustle' ); ?></span>

				<span class="sui-description"><?php printf( esc_html__( 'By default, your %s is set to appear everywhere on your website. Alternately, you can add more specific visibility rules to suit your needs.', 'hustle' ), esc_html( $smallcaps_singular ) ); ?></span>

				<?php if ( isset( $description_line1 ) && '' !== $description_line1 ) { ?>

					<?php if ( isset( $description_line2 ) && '' !== $description_line2 ) {
						$line2 = '<br />&nbsp;<br />' . $description_line2;
					} ?>

					<span class="sui-description"><?php echo esc_attr( $description_line1 ); ?><?php echo $line2; // WPCS: XSS ok. ?></span>

				<?php } ?>

			</div>

			<div id="hustle-conditions-group" class="sui-box-settings-col-2">

				<div id="hustle-visibility-conditions-box">

					<button class="sui-button sui-button-ghost hustle-add-new-visibility-group">
						<i class="sui-icon-plus" aria-hidden="true"></i>
						<?php esc_html_e( 'Add Condition Group', 'hustle' ); ?>
					</button>

				</div>

			</div>

		</div>

	</div>

	<div class="sui-box-footer">

		<button class="sui-button wpmudev-button-navigation" data-direction="prev">
			<i class="sui-icon-arrow-left" aria-hidden="true"></i> <?php echo 'embedded' === $module_type ? esc_html_e( 'Display Options', 'hustle' ) : esc_html_e( 'Appearance', 'hustle' ); ?>
		</button>

		<div class="sui-actions-right">

			<?php if ( 'social_sharing' !== $module_type ) { ?>

				<button class="sui-button sui-button-icon-right wpmudev-button-navigation">
					<?php esc_html_e( 'Behavior', 'hustle' ); ?> <i class="sui-icon-arrow-right" aria-hidden="true"></i>
				</button>

			<?php } else { ?>

				<button
					class="hustle-publish-button sui-button sui-button-blue hustle-action-save"
					data-active="1">
					<span class="sui-loading-text">
						<i class="sui-icon-web-globe-world" aria-hidden="true"></i>
						<span class="button-text"><?php $is_active ? esc_html_e( 'Save changes', 'hustle' ) : esc_html_e( 'Publish', 'hustle' ); ?></span>
					</span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>

			<?php } ?>
		</div>

	</div>

</div>
