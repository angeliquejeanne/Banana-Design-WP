<?php if ( 0 === count( $providers ) ) : ?>

	<div class="sui-notice sui-notice-error">
		<p><?php esc_html_e( "You need at least one active app to send your opt-in's submissions to. If you don't want to use any 3rd party app, you can always use the Local Hustle List to save the submissions.", 'hustle' ); ?></p>
	</div>

<?php else : ?>

	<table class="sui-table hui-table--apps">

		<tbody>

			<?php foreach ( $providers as $provider ) : ?>

				<?php self::static_render(
					'admin/integrations/integration-row',
					array(
						'provider' => $provider,
					)
				); ?>

			<?php endforeach; ?>

		</tbody>

	</table>
<?php endif; ?>
