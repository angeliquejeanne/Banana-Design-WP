<?php
/**
 * @var Opt_In $this
 */
$sections = array(
	'general' => array(
		'label' => __( 'General', 'hustle' ),
		'data' => array(
			'settings' => Hustle_Settings_Admin::get_general_settings(),
		),
	),
	'palettes' => array(
		'label' => __( 'Color Palettes', 'hustle' ),
		'data' => array(
			'palettes' => Hustle_Settings_Admin::get_custom_color_palettes(),
		),
	),
	//'analytics' => array(
	//	'label' => __( 'Dashboard Analytics', 'hustle' ),
	//),
	'data' => array(
		'label' => __( 'Data', 'hustle' ),
	),
	'privacy' => array(
		'label' => __( 'Viewer\'s Privacy', 'hustle' ),
	),
	'permissions' => array(
		'label' => __( 'Permissions', 'hustle' ),
	),
	'recaptcha' => array(
		'label' => __( 'reCAPTCHA', 'hustle' ),
		'data' => array(
			'settings' => Hustle_Settings_Admin::get_recaptcha_settings(),
		),
	),
	'accessibility' => array(
		'label' => __( 'Accessibility', 'hustle' ),
		'data' => array(
			'settings' => Hustle_Settings_Admin::get_hustle_settings( 'accessibility' ),
		),
	),
	'metrics' => array(
		'label' => __( 'Top Metrics', 'hustle' ),
		'data' => array(
			'stored_metrics' => Hustle_Settings_Admin::get_top_metrics_settings(),
		),
	),
	'unsubscribe' => array(
		'label' => __( 'Unsubscribe', 'hustle' ),
	),
);
?>

<main class="<?php echo esc_attr( implode( ' ', apply_filters( 'hustle_sui_wrap_class', null ) ) ); ?>">

	<div class="sui-header">
		<h1 class="sui-header-title"><?php esc_html_e( 'Settings', 'hustle' ); ?></h1>
		<?php $this->render( 'admin/commons/view-documentation' ); ?>
	</div>

	<div class="sui-row-with-sidenav">

		<div class="sui-sidenav">

			<ul class="sui-vertical-tabs sui-sidenav-hide-md">
				<?php
				foreach ( $sections as $key => $value ) {

					$classes = array(
						'sui-vertical-tab',
					);

					if ( $section === $key ) {
						$classes[] = 'current';
					}

					printf(
						'<li class="%s"><a href="#" data-tab="%s">%s</a></li>',
						esc_attr( implode( ' ', $classes ) ),
						esc_attr( $key ),
						esc_html( $value['label'] )
					);
				}
				?>
			</ul>

			<div class="sui-sidenav-hide-lg">

				<select class="sui-mobile-nav" style="display: none;">
					<?php
					foreach ( $sections as $key => $value ) {

						printf(
							'<option value="%1$s" %2$s>%3$s</option>',
							esc_attr( $key ),
							selected( $section, $key, false ),
							esc_html( $value['label'] )
						);

					}
					?>
				</select>

			</div>

		</div>

		<?php
		foreach ( $sections as $key => $value ) {

			if ( ! empty( $value['status'] ) && 'hide' === $value['status'] ) {
				continue;
			}

			$data = isset( $value['data'] )? $value['data']:array();
			$data['section'] = $section;
			$template = sprintf( 'admin/settings/tab-%s', esc_attr( $key ) );

			$this->render( $template, $data );
		}
		?>

	</div>

	<?php
	// Global Footer
	$this->render( 'admin/footer/footer' );

	// DIALOG: Delete.
	$this->render( 'admin/commons/sui-listing/dialogs/delete-module' );

	// DIALOG: Delete All IPs.
	$this->render( 'admin/settings/privacy/dialog-ip-delete' );

	// DIALOG: Dissmiss migrate tracking notice modal confirmation.
	if ( Hustle_Module_Admin::is_show_migrate_tracking_notice() ) {
		$this->render( 'admin/dashboard/dialogs/migrate-dismiss-confirmation' );
	}

	// DIALOG: Data -> Reset plugin.
	$this->render( 'admin/settings/data/reset-data-dialog' );

	// DIALOG: Palettes -> Edit palette.
	$this->render(
		'admin/dialogs/modal-settings-edit-palette',
		array(
			'palettes' => Hustle_Module_Model::get_all_palettes_slug_and_name(),
		)
	);

	// DIALOG: Downgrade to 4.0.4.
	if ( $has_40x_backup ) {
		$this->render( 'admin/settings/dialogs/modal-404-downgrade' );
	}
	?>

</main>
