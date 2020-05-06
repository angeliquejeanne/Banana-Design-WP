<?php
/**
 * @var Opt_In $this
 */
?>

<main class="<?php echo implode( ' ', apply_filters( 'hustle_sui_wrap_class', null ) ); ?>">

	<div class="sui-header">

		<h1 class="sui-header-title"><?php esc_html_e( 'Integrations', 'hustle' ); ?></h1>
		<?php self::static_render( 'admin/commons/view-documentation' ); ?>
	</div>

	<!-- BOX: Summary -->
	<?php self::static_render( 'admin/integrations-page/summary', array( 'sui' => $sui ) ); ?>

	<div class="sui-row">

		<!-- BOX: Connected Apps -->
		<div class="sui-col-md-6">

			<?php self::static_render( 'admin/integrations-page/connected-apps' ); ?>

		</div>

		<!-- BOX: Available Apps -->
		<div class="sui-col-md-6">

			<?php self::static_render( 'admin/integrations-page/available-apps' ); ?>

		</div>

	</div>

	<!-- Integrations modal -->
	<?php self::static_render( 'admin/dialogs/modal-integration' ); ?>

	<!-- Active integration remove modal -->
	<?php self::static_render( 'admin/dialogs/remove-active-integration' ); ?>

	<!-- CTCT integration migration modal -->
	<?php self::static_render( 'admin/dialogs/modal-migrate-ctct' ); ?>

	<!-- Aweber integration migration modal -->
	<?php self::static_render( 'admin/dialogs/modal-migrate-aweber' ); ?>

	<?php
	// Global Footer
	$this->render( 'admin/footer/footer' ); ?>

	<?php
	// DIALOG: Dissmiss migrate tracking notice modal confirmation.
	if ( Hustle_Module_Admin::is_show_migrate_tracking_notice() ) {
		self::static_render( 'admin/dashboard/dialogs/migrate-dismiss-confirmation' );
	}
	?>
</main>
