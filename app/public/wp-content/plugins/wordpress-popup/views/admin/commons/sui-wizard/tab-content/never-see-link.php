<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( '“Never see this again” Link', 'hustle' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'The visitor will never see this message again, once clicked on this link.', 'hustle' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<label for="hustle-add-never-see-link" class="sui-toggle hustle-toggle-with-container" data-toggle-on="add-never-see-link">
			<input type="checkbox"
				name="show_never_see_link"
				data-attribute="show_never_see_link"
				id="hustle-add-never-see-link"
				<?php checked( $settings['show_never_see_link'], '1' ); ?>
				/>
			<span class="sui-toggle-slider"></span>
		</label>

		<label for="hustle-add-never-see-link"><?php esc_html_e( 'Add “Never see this message again” link', 'hustle' ); ?></label>
		<span class="sui-description sui-toggle-description"><?php esc_html_e( 'Add a “Never see this message again” link at the bottom of the module.', 'hustle' ); ?></span>

		<div class="sui-border-frame sui-toggle-content" data-toggle-content="add-never-see-link">

			<div class="sui-form-field">

				<label for="hustle-never-see-link-text" class="sui-label"><?php esc_html_e( 'Link text', 'hustle' ); ?></label>
				<input type="text"
					name="never_see_link_text"
					data-attribute="never_see_link_text"
					id="hustle-never-see-link-text"
					placeholder="<?php esc_html_e( 'Never see this again', 'hustle' ); ?>"
					value="<?php echo esc_attr( $settings['never_see_link_text'] ); ?>"
					class="sui-form-control" />

			</div>

		</div>

	</div>

</div>
