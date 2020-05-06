<?php
/** @var $admin Hustle_Entries_Admin */
$count = $admin->filtered_total_entries();
$is_filter_enabled = $admin->is_filter_box_enabled();
$date_range = '';
$date_created = isset( $admin->filters['date_created'] ) ? $admin->filters['date_created'] : '';
if ( is_array( $date_created ) && isset( $date_created[0] ) && isset( $date_created[1] ) ) {
	$date_created[0] = date( 'm/d/Y', strtotime($date_created[0]) );
	$date_created[1] = date( 'm/d/Y', strtotime($date_created[1]) );
	$date_range = implode(' - ', $date_created);
}
$search_email = isset( $admin->filters['search_email'] ) ? $admin->filters['search_email'] : '';
$order_by = isset( $admin->order['order_by'] ) ? $admin->order['order_by'] : '';

$order_by_array = array(
	'entries.entry_id' => esc_html__( 'Id', 'hustle' ),
	'entries.date_created' => esc_html__( 'Date submitted', 'hustle' ),
);
?>

<?php
$limit = $admin->get_per_page();
$page = intval( filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT ) );

$this->render(
	'admin/commons/pagination',
	array(
		'count' => $count,
		'limit' => $limit,
		'page' => $page,
		'show' => ( $count > $limit ),
		'filterclass' => 'hustle-open-dialog-filter',
		'filter' => array(),
	)
);
