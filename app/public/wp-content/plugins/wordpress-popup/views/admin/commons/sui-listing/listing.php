<?php
if ( isset( $page_title ) ) {
	$page_title = $page_title;
} else {
	$page_title = esc_html__( 'Module', 'hustle' );
}
$sql_month_start_date = date( 'Y-m-d H:i:s', strtotime( '-30 days midnight' ) );
$tracking_model = Hustle_Tracking_Model::get_instance();
$free_limit_reached = ! Hustle_Module_Admin::can_create_new_module( $module_type );
?>

<main class="<?php echo esc_attr( implode( ' ', apply_filters( 'hustle_sui_wrap_class', null ) ) ); ?>">

	<div class="sui-header">

		<h1 class="sui-title"><?php echo esc_html( $page_title ); ?></h1>

		<?php if ( 0 < $total && $capability['hustle_create'] ) { ?>

			<div class="sui-actions-left">

				<button
					id="hustle-create-new-module"
					class="sui-button sui-button-blue hustle-create-module"
					<?php if ( $free_limit_reached ) echo 'data-enabled="false"'; ?>
				>
					<i class="sui-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Create', 'hustle' ); ?>
				</button>

				<button
					class="sui-button hustle-import-module-button"
					<?php if ( $free_limit_reached ) echo 'data-enabled="false"'; ?>
				>
					<i class="sui-icon-upload-cloud" aria-hidden="true"></i> <?php esc_html_e( 'Import', 'hustle' ); ?>
				</button>

			</div>

		<?php } ?>

		<div class="sui-actions-right">

			<?php if ( false && 0 < count( $modules ) ) { ?>

				<div class="hui-reporting-period">

					<label><?php esc_html_e( 'Reporting Period', 'hustle' ); ?></label>

					<select>
						<option value="7"><?php esc_html_e( 'Last 7 days', 'hustle' ); ?></option>
						<option value="15"><?php esc_html_e( 'Last 15 days', 'hustle' ); ?></option>
						<option value="30" selected><?php esc_html_e( 'Last 30 days', 'hustle' ); ?></option>
					</select>

				</div>

			<?php } ?>

			<?php
			// Waiting for the docs to be completed.
			$hide = true; // apply_filters( 'wpmudev_branding_hide_doc_link', false );
			if ( ! $hide ) {
			?>
					<button class="sui-button sui-button-ghost">
						<i class="sui-icon-academy" aria-hidden="true"></i> <?php esc_html_e( 'View Documentation', 'hustle' ); ?>
					</button>
			<?php } ?>

		</div>

	</div>

	<?php
	if ( 'module-does-not-exists' === $message ) {
		self::static_render(
			'admin/notices/notice-non-exists',
			array(
				'total' => $total,
				'capability' => $capability,
			)
		);
	}
	?>

	<?php if ( 0 < count( $modules ) ) { ?>

		<?php
		// ELEMENT: Summary
		self::static_render(
			'admin/commons/sui-listing/elements/summary',
			array(
				'active_modules_count' => $active,
				'singular' => $capitalize_singular,
				'plural'   => $capitalize_plural,
				'latest_entry_time' => Opt_In_Utils::get_latest_conversion_time( $module_type ),
				'latest_entries_count' => $tracking_model->count_newer_conversions_by_module_type( $module_type, $sql_month_start_date ),
				'sui'      => $sui,
			)
		); ?>

		<?php
		// ELEMENT: Pagination
		self::static_render(
			'admin/commons/sui-listing/elements/pagination',
			array(
				'module_type'      => $module_type,
				'items'            => $modules,
				'total'            => $total,
				'page'             => $page,
				'paged'            => $paged,
				'entries_per_page' => $entries_per_page,
			)
		); ?>

		<div class="hustle-list sui-accordion sui-accordion-block">

            <?php
			foreach ( $modules as $key => $module ) {
			?>

				<?php
				// ELEMENT: Modules
				self::static_render(
					'admin/commons/sui-listing/elements/module',
					array(
						'module'               => $module,
						'module_type'          => $module_type,
						'smallcaps_singular'   => $smallcaps_singular,
						'capitalize_singular'	=> $capitalize_singular,
						'capability'           => $capability,
						'tracking_types'       => $module->get_tracking_types(),
						'can_create'		   => ! $free_limit_reached,
					)
				); ?>

			<?php } ?>

		</div>

		<?php
		// ELEMENT: Pagination
		self::static_render(
			'admin/commons/sui-listing/elements/pagination',
			array(
				'module_type'      => $module_type,
				'items'            => $modules,
				'total'            => $total,
				'page'             => $page,
				'paged'            => $paged,
				'entries_per_page' => $entries_per_page,
				'is_bottom'        => true,
			)
		); ?>

	<?php } else { ?>

		<?php
		// ELEMENT: Empty Message
		self::static_render(
			'admin/commons/sui-listing/elements/empty-message',
			array(
				'count'            => $total,
				'is_free'          => $is_free,
				'capability'       => $capability,
				'message'          => $page_message,
			)
		);
	}

	// ELEMENT: Footer
	self::static_render( 'admin/footer/footer' );

	// DIALOG: Create module
	self::static_render(
		'admin/commons/sui-listing/dialogs/create-module',
		array(
			'module_type'         => $module_type,
			'capitalize_singular' => $capitalize_singular,
			'smallcaps_singular'  => $smallcaps_singular,
		)
	);

	// DIALOG: Import module
	self::static_render(
		'admin/commons/sui-listing/dialogs/import-module',
		array(
			'capitalize_singular' => $capitalize_singular,
			'smallcaps_singular'  => $smallcaps_singular,
			'module_type'         => $module_type,
			'metas_optin'         => Hustle_Module_Model::instance()->get_module_meta_names( $module_type, Hustle_Module_Model::OPTIN_MODE, true ),
			'metas_info'          => Hustle_Module_Model::instance()->get_module_meta_names( $module_type, Hustle_Module_Model::INFORMATIONAL_MODE, true ),
		)
	);

	// DIALOG: Delete module
	self::static_render(
		'admin/commons/sui-listing/dialogs/delete-module',
		array()
	);

	// DIALOG: Manage tracking
	if ( isset( $multiple_charts ) ) {

		self::static_render(
			'admin/commons/sui-listing/dialogs/manage-tracking',
			array(
				'multiple_charts' => isset( $multiple_charts ) ? $multiple_charts : false,
			)
		);
	}

	/**
	 * DIALOG: Reset Tracking Data Confirmation
	 */
	self::static_render( 'admin/commons/sui-listing/dialogs/tracking-reset-data' );

	// DIALOG: Ugrade to pro.
	if ( Opt_In_Utils::_is_free() ) {
		self::static_render( 'admin/commons/sui-listing/dialogs/pro-upgrade' );
	}

	// DIALOG: Preview
	// If embedded, show the preview dialog to embed the module into.
	if ( Hustle_Module_Model::EMBEDDED_MODULE === $module_type ) {
		self::static_render( 'admin/dialogs/preview-dialog' );
	}

	// DIALOG: Dissmiss migrate tracking notice modal confirmation.
	if ( Hustle_Module_Admin::is_show_migrate_tracking_notice() ) {
		self::static_render( 'admin/dashboard/dialogs/migrate-dismiss-confirmation' );
	}
?>
</main>
