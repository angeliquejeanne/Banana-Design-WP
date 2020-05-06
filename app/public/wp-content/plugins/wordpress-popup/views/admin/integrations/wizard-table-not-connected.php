<?php if ( 0 === count( $providers ) ) : ?>

	<?php
		$module_type = Hustle_Module_Model::instance()->get_module_type_by_module_id( $module_id );
		$display_type_name = Opt_In_Utils::get_module_type_display_name( $module_type );

		if ( current_user_can( 'hustle_edit_integrations' ) ) {
			$integrations_url = add_query_arg( 'page', Hustle_Module_Admin::INTEGRATIONS_PAGE, 'admin.php' );
			$empty_providers_msg = sprintf(
				esc_html__( 'Connect to more third party apps via %1$sIntegrations%2$s page and activate them to collect the data of this %3$s here.', 'hustle' ),
				'<a href="' . esc_url( $integrations_url ) . '">',
				'</a>',
				esc_html( $display_type_name )
			);
		} else {
			$empty_providers_msg = sprintf(
				/* translators: module type in small caps and singular */
				esc_html__( 'Ask your site admin to connect more third-party apps to activate them for this %s', 'hustle' ),
				esc_html( $display_type_name )
			);
		}
	?>
	<div class="sui-notice">
		<p><?php echo $empty_providers_msg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	</div>

<?php else : ?>

	<table class="sui-table hui-table--apps" style="margin-bottom: 10px;">

		<tbody>

			<?php foreach ( $providers as $provider ) : ?>

				<?php self::static_render(
					'admin/integrations/integration-row',
					array(
						'provider' => $provider,
						'module_id' => $module_id,
					)
				); ?>

			<?php endforeach; ?>

		</tbody>

	</table>

	<span class="sui-description"><?php esc_html_e( 'You are connected to these applications via their APIs.', 'hustle' ); ?></span>

<?php endif; ?>

