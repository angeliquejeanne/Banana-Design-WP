<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">

		<span class="sui-settings-label"><?php esc_html_e( 'Widget', 'hustle' ); ?></span>

		<span class="sui-description"><?php esc_html_e( 'Add a social bar to the sidebars of your website.', 'hustle' ); ?></span>

	</div>

	<div class="sui-box-settings-col-2">

		<div class="sui-form-field">

			<label for="hustle-settings--widget-enable" class="sui-toggle hustle-toggle-with-container" data-toggle-on="widget-enabled">
				<input
					type="checkbox"
					name="widget_enabled"
					data-attribute="widget_enabled"
					id="hustle-settings--widget-enable"
					<?php checked( $is_widget_enabled, '1' ); ?>
				/>
				<span class="sui-toggle-slider"></span>
			</label>

			<label for="hustle-settings--widget-enable"><?php esc_html_e( 'Enable widget module', 'hustle' ); ?></label>

			<div class="sui-toggle-content" data-toggle-content="widget-enabled">
				<span class="sui-description"><?php printf( esc_html__( 'Enabling this will add a new widget named "Hustle" under the Available Widgets list. You can go to %1$sAppearance > %2$s%3$s and configure this widget to show your social bar in the sidebars.', 'hustle' ), '<strong>', '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">Widgets</a>', '</strong>' ); ?></span>
			</div>

		</div>

	</div>

</div>
