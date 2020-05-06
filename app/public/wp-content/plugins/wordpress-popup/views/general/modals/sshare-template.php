<div class="hustle-shares-<?php echo $is_inline ? 'widget' : 'floating'; ?> <?php if ( $animate_icons ) echo 'hustle-shares-animated'; ?>">

	<div class="hustle-shares-wrap">

		<?php foreach( $social_icons as $name => $data ) : ?>

			<?php $class = sprintf( 'hustle-social-icon hustle-social-icon-%s hustle-social-icon-counter-%s hustle-icon-%s %s %s %s',
				$counter_enabled ? 'native' : 'custom',
				$data['type'],
				$icon_style,
				$customize_colors ? 'hustle-icon-' . $name : '',
				( 'flat' === $icon_style && $counter_enabled ) ? 'has-counter' : '',
				( $counter_enabled && '1' === $inline_count ) ? 'hustle-social-inline' : ''
			);?>
			
			<a 
				data-social="<?php echo esc_attr( $name ); ?>"
				data-service-type="<?php echo $counter_enabled ? esc_attr( $data['type'] ) : 'custom'; ?>"
				href="<?php echo $counter_enabled ? '#' : esc_url( $data['link'] ); ?>" 
				<?php if ( ! $counter_enabled ) echo 'target="_blank"'; ?> 
				class="<?php echo esc_attr( $class ); ?>" 
				aria-label="<?php printf( esc_attr__( 'Share on %s', 'hustle' ), esc_attr( $data['label'] ) ); ?>"
			>

				<div class="hustle-icon-container" aria-hidden="true">

					<?php self::static_render( 'general/icons/social/' . $name ); ?>

				</div>

				<?php if ( $counter_enabled ) : ?>

					<div class="hustle-shares-counter"><span><?php echo ! empty( $data['counter'] ) ? esc_attr( $data['counter'] ) : '0'; ?></span></div>

				<?php endif; ?>

			</a>

		<?php endforeach; ?>

	</div>

</div>
