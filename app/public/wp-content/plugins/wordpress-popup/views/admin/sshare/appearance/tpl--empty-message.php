<div id="hustle-appearance-empty-message" class="sui-message"<?php echo ! $is_empty ? ' style="display: none;"' : ''; ?>>

	<?php
	echo Opt_In_Utils::render_image_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_url( self::$plugin_url . 'assets/images/hustle-empty-message.png' ),
		esc_url( self::$plugin_url . 'assets/images/hustle-empty-message@2x.png' ),
		'sui-image'
	);
	?>

	<div class="sui-message-content">

		<h2><?php esc_html_e( 'No Display Option Enabled', 'hustle' ); ?></h2>

		<p>
			<?php
			printf(
				/* translators: 1: opening a tag, 2: closing a tag */
				esc_html__( 'Whoops, you need to choose where you want the social widget to show up first. Jump back to %1$sDisplay Options%2$s and enable a module.', 'hustle' ),
				'<a href="#" data-tab="display" class="hustle-go-to-tab">',
				'</a>'
			);
			?>
		</p>

	</div>

</div>
