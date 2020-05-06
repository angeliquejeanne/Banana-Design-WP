<table class="sui-table hui-table--apps">

	<tbody>

		<?php foreach ( $providers as $provider ) : ?>

			<?php self::static_render(
				'admin/integrations/integration-row',
				array(
					'provider' => $provider
				)
			); ?>

		<?php endforeach; ?>

	</tbody>

</table>
