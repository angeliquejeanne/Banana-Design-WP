<?php
$palettes_settings_url = add_query_arg(
	array(
		'page'    => Hustle_Module_Admin::SETTINGS_PAGE,
		'section' => 'palettes',
	),
	'admin.php'
);
?>

<div class="sui-box-settings-row" data-toggle-content="use-vanilla">
	<?php
		$custom_pallete_url = add_query_arg(
			array(
				'page'    => 'hustle_settings',
				'section' => 'palettes',
			),
			esc_url( admin_url( 'admin.php' ) )
		);
	?>
	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Colors Palette', 'hustle' ); ?></span>
		<span class="sui-description"><?php printf( esc_html__( 'Choose a pre-made palette for your %1$s and further customize it.%2$sYou can also %3$screate custom palettes%4$s and reuse them on your modules.', 'hustle' ), esc_html( $smallcaps_singular ), '<br />&nbsp;<br />', '<a href="' . esc_url( $custom_pallete_url ) . '">', '</a>' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<div class="sui-form-field">

			<label for="hustle-color-palettes-list" id="hustle-color-palettes-list-label" class="sui-label"><?php esc_html_e( 'Select a color palette', 'hustle' ); ?></label>

			<select name="color_palette" id="hustle-color-palettes-list" class="hui-select-palette" data-attribute="color_palette" aria-labelledby="hustle-color-palettes-list-label">

				<?php foreach ( Hustle_Module_Model::get_all_palettes_slug_and_name() as $slug => $name ) : ?>

					<option
						value="<?php echo esc_attr( $slug ); ?>"
						<?php selected( $settings['color_palette'], $slug ); ?>
					>
						<?php echo esc_html( $name ); ?>
					</option>

				<?php endforeach; ?>

			</select>

			<a href="<?php echo esc_url( $palettes_settings_url ); ?>" target="_blank" id="hustle-create-palette-link" class="hui-button">
				<?php esc_html_e( 'Create custom color palettes', 'hustle' ); ?>
			</a>
		</div>

		<div id="hustle-palette-colors" class="sui-form-field">

			<label class="sui-label"><?php esc_html_e( 'Customize the color palette', 'hustle' ); ?></label>

			<div class="sui-side-tabs" style="margin-top: 5px;">

				<div class="sui-tabs-menu">

					<label
						for="hustle-custom-palette--on"
						class="sui-tab-item"
					>
						<input
							type="radio"
							name="customize_colors"
							data-attribute="customize_colors"
							value="1"
							id="hustle-custom-palette--on"
							data-tab-menu="hustle-custom-palette"
							<?php checked( $settings['customize_colors'], '1' ); ?>
						/>
						<?php esc_html_e( 'Customize', 'hustle' ); ?>
					</label>

					<label
						for="hustle-custom-palette--off"
						class="sui-tab-item"
					>
						<input
							type="radio"
							name="customize_colors"
							data-attribute="customize_colors"
							value="0"
							id="hustle-custom-palette--off"
							data-tab-menu="none"
							<?php checked( $settings['customize_colors'], '0' ); ?>
						/>
						<?php esc_html_e( 'Use Default Colors', 'hustle' ); ?>
					</label>

				</div>

				<div class="sui-tabs-content">

					<div
						class="sui-tab-content"
						data-tab-content="hustle-custom-palette"
					>

						<?php
						if ( $is_optin ) {
							self::static_render( 'admin/commons/sui-wizard/elements/palette-optin', [
								'module_type' => $module_type,
								'settings'    => $settings,
							] );
						} else {
							self::static_render( 'admin/commons/sui-wizard/elements/palette-informational', [
								'module_type' => $module_type,
								'settings'    => $settings,
							] );
						}
						?>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>
