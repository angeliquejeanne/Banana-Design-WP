<?php if ( 0 === count( $providers ) ) : ?>

<div class="sui-notice sui-notice-error">
	<p><?php esc_html_e( "You need at least one active app to send your opt-in's submissions to. If you don't want to use any 3rd party app, you can always use the Local Hustle List to save the submissions.", 'hustle' ); ?></p>
</div>

<?php else : ?>

<table class="sui-table hui-table--apps hui-connected" style="margin-bottom: 10px;">

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

<span class="sui-description"><?php esc_html_e( 'These applications are collecting data of your popup.', 'hustle' ); ?></span>

<?php endif; ?>
