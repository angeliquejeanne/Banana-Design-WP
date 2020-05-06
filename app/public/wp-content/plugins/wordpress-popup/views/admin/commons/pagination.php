<?php
if ( ! $show ) {

	if ( isset( $filterclass ) && ! empty( $filterclass ) ) {

		$filterbtn_icon = '<i class="sui-icon-filter" aria-hidden="true"></i>';
		$filterbtn_aria = '<span class="sui-screen-reader-text">' . esc_html__( 'Filter results', 'hustle' ) . '</span>';

		echo '<div class="sui-pagination-wrap">';

			echo '<span class="sui-pagination-results">';
				printf( _n( '%d result', '%d results', esc_attr( $count ), 'hustle' ), esc_attr( $count ) ); // phpcs:ignore
			echo '</span>';

			printf(
				'<button class="sui-button-icon sui-button-outlined %s">%s%s</button>',
				esc_attr( $filterclass ),
				$filterbtn_icon, // phpcs:ignore
				$filterbtn_aria // phpcs:ignore
			);

		echo '</div>';
	}

	return;
}

/**
 * Pagination helper
 *
 * @since 4.0.0
 *
 * @param string $url URL for link.
 * @param string $content Content of the link.
 * @param boolean $enabled Is link enabled?
 * @param string $class Class of the link.
 *
 */
if ( ! function_exists( 'hustle_pagination_one' ) ) {

	function hustle_pagination_one( $url, $content = '', $enabled = true, $class = '' ) {

		// Handle icons
		switch ( $content ) {

			case 'sui-icon-arrow-skip-end' :
			case 'sui-icon-arrow-skip-start' :
			case 'sui-icon-chevron-left' :
			case 'sui-icon-chevron-right' :
				$content = sprintf(
					'<i class="%s" aria-hidden="true"></i>',
					$content
				);
				break;
			default:
				break;
		}

		printf(
			'<li><a href="%1$s"%3$s%4$s>%2$s</a></li>',
			esc_url( $url ),
			$content,
			$enabled? '':' disabled="disabled"',
			empty( $class ) ? '' : sprintf( ' class="%s"', esc_attr( $class ) ) // phpcs:ignore
		);
	}
}

/**
 * Setup pager values
 *
 * @since 4.0.0
 */
$args = array();

// Add the current section to the pagination URL. For example, for Settings -> permissions.
if ( ! empty( $section ) ) {
	$args['section'] = $section;
}

foreach ( $filter as $key => $value ) {

	if ( empty( $value ) ) {
		continue;
	}

	if ( is_array( $value ) ) {
		$value = implode( ',', $value );
	}

	$args[ 'filter['.$key.']' ] = $value;
}

$base_url = add_query_arg( $args, remove_query_arg( 'paged' ) );
$page     = max( 1, $page );
$start    = max( 0, $page - 3 );
$max      = intval( ceil( $count / $limit ) );
$end      = min( $max, $page + 2 );

/**
 * Show skips when $max extends:
 *
 * @since 4.0.0
 *
 */
$show_skip = 9 < $max;

/**
 * Show arrows when $max extends:
 *
 * @since 4.0.0
 *
 */
$show_arrow = 3 < $max;

/**
 * Pager markup
 *
 * @since 4.0.0
 *
 */
$url = $base_url; ?>

<div class="sui-pagination-wrap">

	<span class="sui-pagination-results"><?php printf( _n( '%d result', '%d results', esc_attr( $count ), 'hustle' ), esc_attr( $count ) ); // phpcs:ignore ?></span>

    <ul class="sui-pagination">

		<?php
		if ( $show_skip ) {
			$enabled = 1 < $page;
			hustle_pagination_one( $url, 'sui-icon-arrow-skip-start', $enabled );
		}

		if ( $show_arrow ) {
			if ( 1 < $page ) {
				$url = add_query_arg( 'paged', $page - 1, $base_url );
			}
			$enabled = 1 < $page;
			hustle_pagination_one( $url, 'sui-icon-chevron-left', $enabled );
		}

		if ( 0 < $start ) {
			hustle_pagination_one( '', '...', false );
		}

		for ( $i = $start; $i < $end ; ) {
			$i++;
			if ( 1 < $i ) {
				$url = add_query_arg( 'paged', $i, $base_url );
			} else {
				$url = $base_url;
			}
			$enabled = $i !== $page;
			$class = $i === $page? 'sui-active':'';
			hustle_pagination_one( $url, $i, $enabled, $class );
		}

		if ( $max != $end ) {
			hustle_pagination_one( '', '...', false );
		}

		if ( $show_arrow ) {

			if ( $max - 1 > $page ) {
				$url = add_query_arg( 'paged', $page + 1, $base_url );
			}

			$enabled = $max !== $page;
			hustle_pagination_one( $url, 'sui-icon-chevron-right', $enabled );
		}

		if ( $show_skip ) {
			$url = add_query_arg( 'paged', $max, $base_url );
			$enabled = $page !== $max;
			hustle_pagination_one( $url, 'sui-icon-arrow-skip-end', $enabled );
		}
		?>
    </ul>

	<?php
	if ( isset( $filterclass ) && ! empty( $filterclass ) ) {
		printf(
			'<button class="sui-button-icon sui-button-outlined %s"><i class="sui-icon-filter" aria-hidden="true"></i></button>',
			esc_attr( $filterclass )
		);
	}
	?>

</div>
