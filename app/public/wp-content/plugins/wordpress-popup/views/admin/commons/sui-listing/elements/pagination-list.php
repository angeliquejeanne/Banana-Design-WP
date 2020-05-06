<?php
if ( $total < $entries_per_page ) {
	return;
}
$url = add_query_arg(
	array(
		'page' => $page,
	),
	get_admin_url( get_current_blog_id(), 'admin.php' )
);
?>
<ul class="sui-pagination">
<?php
if ( 1 < $paged ) {
	/**
	 * ELEMENT: Skip to first page
	 *
	 * Conditions:
	 * 1. Show this button if there are 10 pages or more.
	 * 2. Show this button if user is on page #5 or later.
	 *
	 */
	?>
	<li class="sui-pagination--start">
		<a href="<?php echo esc_url( $url ); ?>">
			<i class="sui-icon-arrow-skip-start" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Go to first page', 'hustle' ); ?></span>
		</a>
	</li>

<?php
	/**
	 * ELEMENT: Go to next page
	 *
	 * Conditions:
	 * 1. Show this button if there are 5 pages or more.
	 * 2. Hide this button if first page is current page.
	 *
	 */
	$u = add_query_arg( 'paged', $paged - 1, $url );
	?>
	<li class="sui-pagination--next">
		<a href="<?php echo esc_url( $u ); ?>">
			<i class="sui-icon-chevron-left" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Go to previous page', 'hustle' ); ?></span>
		</a>
	</li>
<?php
}

	/**
	 * ELEMENT: List of pages
	 *
	 * 1. Use "sui-active" class to determine current page.
	 *
	 */
if (  $total > $entries_per_page ) {
	$i = 1;
	do {
		$u = $url;
		if ( 1 < $i ) {
			$u = add_query_arg( 'paged', $i, $u );
		}
		printf(
			'<li><a class="%s" href="%s">%d</a></li>',
			esc_attr( $paged === $i? 'sui-active' : '' ),
			esc_url( $u ),
			esc_html( $i++ )
		);
	} while ( ( $i - 1 ) * $entries_per_page < $total );
}

	/**
	 * ELEMENT: Go to previous page
	 *
	 * Conditions:
	 * 1. Show this button if there are 5 pages or more.
	 * 2. Hide this button if last page is current page.
	 *
	 */
if ( $paged < $total / $entries_per_page ) {
	$u = add_query_arg( 'paged', $paged + 1, $url );
	?>
	<li class="sui-pagination--next">
		<a href="<?php echo esc_url( $u ); ?>">
			<i class="sui-icon-chevron-right" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Go to next page', 'hustle' ); ?></span>
		</a>
	</li>
<?php

	/**
	 * ELEMENT: Skip to last page
	 *
	 * Conditions:
	 * 1. Show this button if there are 10 pages or more.
	 * 2. Hide this button if user is on page #7.
	 *
	 */
	$u = add_query_arg( 'paged', ceil( $total / $entries_per_page ), $url );
	?>
	<li class="sui-pagination--end">
		<a href="<?php echo esc_url( $u ); ?>">
			<i class="sui-icon-arrow-skip-end" aria-hidden="true"></i>
			<span class="sui-screen-reader-text"><?php esc_html_e( 'Go to last page', 'hustle' ); ?></span>
		</a>
	</li>
<?php
}
?>
</ul>
