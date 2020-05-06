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

<div
	id="hustle-dialog--filter-entries"
	class="sui-dialog sui-dialog-alt"
	aria-hidden="true"
	tabindex="-1"
>

	<div class="sui-dialog-overlay sui-fade-out" data-a11y-dialog-hide="hustle-dialog--filter-entries"></div>

	<div
		role="dialog"
		class="sui-dialog-content sui-fade-out"
		aria-labelledby="dialogTitle"
		aria-describedby="dialogDescription"
	>

		<form
			id="module-mode-step"
			class="sui-box"
		>

			<input type="hidden" name="page" value="hustle_entries" />
			<input type="hidden" name="module_type" value="<?php echo esc_attr( $admin->get_module_type() ); ?>" />
			<input type="hidden" name="module_id" value="<?php echo esc_attr( $admin->get_module_id() ); ?>" />

			<div class="sui-box-header sui-block-content-center">

				<h3 id="dialogTitle" class="sui-box-title"><?php esc_html_e( 'Filters', 'hustle' ); ?></h3>

				<button class="sui-dialog-close hustle-dialog-close">
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'hustle' ); ?></span>
				</button>

			</div>

			<div class="sui-box-body sui-box-body-slim sui-block-content-center">

				<?php
				// FIELD: Keyword ?>
				<div class="sui-form-field">

					<label for="hustle-dialog-filter--keyword" class="sui-label"><?php esc_html_e( 'Email id has keyword', 'hustle' ); ?></label>

					<div class="sui-control-with-icon">

						<input
							type="text"
							name="search_email"
							value="<?php echo esc_attr( $search_email ); ?>"
							placeholder="<?php esc_html_e( 'E.g. gmail', 'hustle' ); ?>"
							id="hustle-dialog-filter--keyword"
							class="sui-form-control"
						/>

						<i class="sui-icon-magnifying-glass-search" aria-hidden="true"></i>

					</div>

				</div>

				<?php
				// FIELD: Sort by ?>
				<div class="sui-form-field">

					<label for="hustle-dialog-filter--sortby" class="sui-label"><?php esc_html_e( 'Sort by', 'hustle' ); ?></label>

					<select name="order_by" id="hustle-dialog-filter--sortby">
						<?php foreach ( $order_by_array as $key => $name ) { ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $order_by ); ?>><?php echo esc_html( $name ); ?></option>
						<?php } ?>
					</select>

				</div>

				<?php
				// FIELD: Date Range ?>
				<div class="sui-form-field">

					<label for="hustle-dialog-filter--date" class="sui-label"><?php esc_html_e( 'Submission date range', 'hustle' ); ?></label>

					<div id="hustle-dialog-filter--date" class="sui-date">

						<i class="sui-icon-calendar" aria-hidden="true"></i>

						<input
							type="text"
							name="date_range"
							value="<?php echo esc_attr( $date_range ); ?>"
							placeholder="<?php esc_html_e( 'Pick a date range', 'hustle' ); ?>"
							class="hustle-entries-filter-date sui-form-control"
						/>

					</div>

				</div>

			</div>

			<div class="sui-box-footer">

				<button type="button" class="sui-button sui-button-ghost hustle-entries-clear-filter">
					<?php esc_html_e( 'Clear Filters', 'hustle' ); ?>
				</button>

				<button class="sui-button">
					<?php esc_html_e( 'Apply', 'hustle' ); ?>
				</button>

			</div>

		</form>

	</div>

</div>
