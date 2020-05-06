<?php
$args = array(
	'role'  => 'alert'
);

// Set unique identifier to notice.
if ( isset( $id ) && '' !== $id ) {
	$args['id'] = esc_attr( $id );
}

// Set default class.
$args['class'] = 'sui-notice-top';

// Set type of notice.
if ( isset( $type ) && '' !== $type ) {
	$args['class'] .= ' sui-notice-' . esc_attr( $type );
}

// Make notice dismissable.
if ( isset( $dismiss ) && true === $dismiss ) {
	$args['class'] .= ' sui-can-dismiss';
}

// Set custom class(es).
if ( isset( $class ) && '' !== $class ) {
	$args['class'] .= ' sui-notice-' . esc_attr( $class );
}

// Hide notice on load.
$args['style'] = 'display: none;';

foreach ( $args as $key => $value ) {
	$attrs[] = $key . '="' . $value . '"';
}
?>

<div <?php echo  wp_kses_post( implode( ' ', $attrs ) ); ?>>

	<?php if ( isset( $content ) ) { ?>

		<div class="sui-notice-content">

			<?php foreach ( $content as $text ) {

				if ( '' !== $text ) {
					echo '<p>' . $text . '</p>'; // phpcs:disable
				}
			} ?>

		</div>

	<?php } ?>

	<?php if ( isset( $dismiss ) && true === $dismiss ) { ?>

		<span class="sui-notice-dismiss">
			<a role="button" href="#" aria-label="Dismiss" class="sui-icon-check"></a>
		</span>

	<?php } ?>

</div>
