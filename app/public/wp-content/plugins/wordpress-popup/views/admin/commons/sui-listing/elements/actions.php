<?php
$is_embedded_or_social = Hustle_Module_Model::EMBEDDED_MODULE === $module->module_type || Hustle_Module_Model::SOCIAL_SHARING_MODULE === $module->module_type;
$free_limit_reached    = ! Hustle_Module_Admin::can_create_new_module( $module->module_type );

$can_edit   = Opt_In_Utils::is_user_allowed( 'hustle_edit_module', $module->id );
$can_create = current_user_can( 'hustle_create' );

// BUTTON: Open dropdown list ?>
<button class="sui-button-icon sui-dropdown-anchor" aria-expanded="false">
	<span class="sui-loading-text">
		<i class="sui-icon-widget-settings-config" aria-hidden="true"></i>
	</span>
	<span class="sui-screen-reader-text"><?php esc_html_e( 'More options', 'hustle' ); ?></span>
	<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
</button>

<?php // Start dropdown options ?>

<ul>

	<?php
	// Edit module
	if ( ! empty( $dashboard ) && $can_edit ) : ?>

		<li><a href="<?php echo esc_url( $module->decorated->get_edit_url() ); ?>" class="hustle-onload-icon-action">
			<i class="sui-icon-pencil" aria-hidden="true"></i>
			<?php esc_html_e( 'Edit', 'hustle' ); ?>
		</a></li>

	<?php
	endif; ?>

	<?php
	// Preview module
	if ( Hustle_Module_Model::SOCIAL_SHARING_MODULE !== $module->module_type ) : ?>

		<li><button
			class="hustle-preview-module-button hustle-onload-icon-action"
			data-id="<?php echo esc_attr( $module->id ); ?>"
			data-type="<?php echo esc_attr( $module->module_type ); ?>">
			<i class="sui-icon-eye" aria-hidden="true"></i>
			<?php esc_html_e( 'Preview', 'hustle' ); ?>
		</button></li>

	<?php
	endif; ?>

	<?php
	// Copy shortcode
	if ( $is_embedded_or_social ) : ?>

		<li><button
			class="hustle-copy-shortcode-button"
			data-shortcode='[wd_hustle id="<?php echo esc_attr( $module->get_shortcode_id() ); ?>" type="<?php echo esc_attr( $module->module_type ); ?>"/]'>
			<i class="sui-icon-code" aria-hidden="true"></i>
			<?php esc_html_e( 'Copy Shortcode', 'hustle' ); ?>
		</button></li>

	<?php endif; ?>

	<?php
	// Toggle Status button ?>
	<?php if ( $can_edit ) : ?>
		<li><button
			class="hustle-single-module-button-action hustle-onload-icon-action"
			data-module-id="<?php echo esc_attr( $module->id ); ?>"
			data-hustle-action="toggle-status"
		>
			<span class="<?php echo $module->active ? '' : 'sui-hidden'; ?>">
				<i class="sui-icon-unpublish" aria-hidden="true"></i>
				<?php esc_html_e( 'Unpublish', 'hustle' ); ?>
			</span>
			<span class="<?php echo $module->active ? ' sui-hidden' : ''; ?>">
				<i class="sui-icon-web-globe-world" aria-hidden="true"></i>
				<?php esc_html_e( 'Publish', 'hustle' ); ?>
			</span>
		</button></li>
	<?php endif; ?>

<?php
// TODO: FIX INDENTATION.

	// View Email List
if (
		Hustle_Module_Model::SOCIAL_SHARING_MODULE !== $module->module_type
		&& $capability['hustle_access_emails']
		&& 'optin' === $module->module_mode
	) {
	$url = add_query_arg(
		array(
		'page' => Hustle_Module_Admin::ENTRIES_PAGE,
		'module_type' => $module->module_type,
		'module_id' => $module->module_id,
		),
		admin_url( 'admin.php' )
	);
	printf( '<li><a href="%s" class="hustle-onload-icon-action">', esc_url( $url ) );
	echo '<i class="sui-icon-community-people" aria-hidden="true"></i> ';
	esc_html_e( 'View Email List', 'hustle' );
	echo '</a></li>';
}
?>

<?php
// Duplicate
?>
<?php if ( empty( $dashboard ) && $can_create ) : ?>
	<li><button
		class="<?php echo ! $free_limit_reached ? 'hustle-single-module-button-action hustle-onload-icon-action' : 'hustle-upgrade-modal-button'; ?>"
		data-module-id="<?php echo esc_attr( $module->id ); ?>"
		data-hustle-action="clone"
	>
		<i class="sui-icon-copy" aria-hidden="true"></i>
		<?php esc_html_e( 'Duplicate', 'hustle' ); ?>
	</button></li>
<?php endif; ?>

<?php
// Tracking
?>
<?php if ( empty( $dashboard ) && $can_edit ) : ?>

	<li>
		<?php
		if ( ! $is_embedded_or_social ) :

			$is_tracking_enabled = $module->is_tracking_enabled( $module->module_type );
			?>

			<button
				class="hustle-single-module-button-action hustle-onload-icon-action"
				data-module-id="<?php echo esc_attr( $module->id ); ?>"
				data-hustle-action="toggle-tracking"
			>
				<span class="<?php echo $is_tracking_enabled ? '' : 'sui-hidden'; ?>">
					<i class="sui-icon-tracking-disabled" aria-hidden="true"></i>
					<?php esc_html_e( 'Disable Tracking', 'hustle' ); ?>
				</span>
				<span class="<?php echo $is_tracking_enabled ? ' sui-hidden' : ''; ?>">
					<i class="sui-icon-graph-line" aria-hidden="true"></i>
					<?php esc_html_e( 'Enable Tracking', 'hustle' ); ?>
				</span>
			</button>
		<?php
		else :

			$trackings = $module->get_tracking_types();
			$enabled_trackings = $trackings ? implode( ',', array_keys( $trackings ) ) : '';
			?>
			<button
				class="hustle-manage-tracking-button"
				data-module-id="<?php echo esc_attr( $module->id ); ?>"
				data-tracking-types="<?php echo esc_attr( $enabled_trackings ); ?>"
			>
				<i class="sui-icon-graph-line" aria-hidden="true"></i>
				<?php esc_html_e( 'Manage Tracking', 'hustle' ); ?>
			</button>
		<?php endif; ?>
	</li>

	<li>
		<button class="hustle-module-tracking-reset-button"
				data-module-id="<?php echo esc_attr( $module->id ); ?>"
				data-title="<?php esc_attr_e( 'Reset Tracking Data', 'hustle' ); ?>"
				data-description="<?php esc_attr_e( 'Are you sure you wish reset the tracking data of this module?', 'hustle' ); ?>"
			>
			<i class="sui-icon-undo" aria-hidden="true"></i> <?php esc_html_e( 'Reset Tracking Data', 'hustle' ); ?>
		</button>
	</li>

<?php endif; ?>

	<?php // Export ?>
	<li>
		<form method="post">
			<input type="hidden" name="hustle_action" value="export">
			<input type="hidden" name="id" value="<?php echo esc_attr( $module->id ); ?>">
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'hustle_module_export' ) ); ?>">
			<button>
				<i class="sui-icon-cloud-migration" aria-hidden="true"></i>
				<?php esc_html_e( 'Export', 'hustle' ); ?>
			</button>
		</form>
	</li>

	<?php
	// Import.
	if ( empty( $dashboard ) && $can_edit ) :
		?>

		<li><button
			class="hustle-import-module-button"
			data-module-id="<?php echo esc_attr( $module->id ); ?>"
			data-module-mode="<?php echo esc_attr( $module->module_mode ); ?>"
		>
			<span>
				<i class="sui-icon-upload-cloud" aria-hidden="true"></i>
				<?php esc_html_e( 'Import', 'hustle' ); ?>
			</span>
		</button></li>

	<?php
	endif; ?>

	<?php
	// Delete module ?>
	<?php if ( $can_create ) : ?>
		<li><button class="sui-option-red hustle-delete-module-button"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_listing_request' ) ); ?>"
			data-type="<?php echo esc_attr( $module->module_type ); ?>"
			data-id="<?php echo esc_attr( $module->id ); ?>"
			data-title="<?php printf( esc_attr__( 'Delete %s', 'hustle' ), esc_attr( $capitalize_singular ) ); ?>"
			data-description="<?php printf( esc_attr__( 'Are you sure you wish to permanently delete this %s? Its additional data, like subscriptions and tracking data, will be deleted as well.', 'hustle' ), esc_attr( $smallcaps_singular ) ); ?>"
		>
			<i class="sui-icon-trash" aria-hidden="true"></i> <?php esc_html_e( 'Delete', 'hustle' ); ?>
		</button></li>
	<?php endif; ?>

</ul>
