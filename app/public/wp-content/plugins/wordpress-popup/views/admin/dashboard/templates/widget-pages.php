<?php
$sshare_count = Hustle_Module_Collection::instance()->get_all( null, array(
	'module_type' => array( Hustle_Module_Model::SOCIAL_SHARING_MODULE ),
	'count_only' => true,
));
?>
<div class="sui-box">

	<div class="sui-box-header">

		<h2 class="sui-box-title">
			<i class="sui-icon-<?php echo esc_attr( $widget_type ); ?>" aria-hidden="true"></i>
			<?php echo esc_html( $widget_name ); ?>
		</h2>

	</div>

	<div class="sui-box-body">

		<p><?php echo esc_html( $description ); ?></p>

		<?php if ( $sshare_count && count( $sshare_per_page_data ) ) { ?>

			<table class="sui-table sui-table-flushed hui-table-dashboard">

				<thead>

					<tr>

						<th><?php esc_html_e( 'Page Name', 'hustle' ); ?></th>
						<th><?php esc_html_e( 'Total Shares', 'hustle' ); ?></th>

					</tr>

				</thead>

				<tbody>

					<?php foreach ( $sshare_per_page_data as $page_data ) {
						?>

						<tr>

							<td class="sui-table-item-title"><a href="<?php echo esc_url( $page_data['url'] ); ?>" target="_blank">
								<?php echo esc_html( $page_data['title'] ); ?>
							</a></td>

							<td><?php echo esc_html( $page_data['count'] ); ?></td>

						</tr>

					<?php } ?>

				</tbody>

			</table>

		<?php } ?>

	</div>

	<div class="sui-box-footer"<?php echo ( $sshare_count && count( $sshare_per_page_data ) ) ? '' : ' style="padding-top: 0; border-top: 0;"'; ?>>

		<?php $query_array = array( 'page' => Hustle_Module_Admin::get_listing_page_by_module_type( $widget_type ) ); ?>

		<?php if ( $capability['hustle_create'] ) { ?>
			<a
				href="
					<?php
					$query_array = array( 'page' => Hustle_Module_Admin::get_listing_page_by_module_type( $widget_type ) );
					if ( Hustle_Module_Admin::can_create_new_module( $widget_type ) ) {
						$args = array_merge( $query_array, ['create-module' => 'true'] );
					} else {
						$args = array_merge( $query_array, ['requires-pro' => 'true'] );
					}
					echo esc_url( add_query_arg( $args, 'admin.php' ) );
					?>
				"
				class="sui-button sui-button-blue"
			>
				<i class="sui-icon-plus" aria-hidden="true"></i>
				<?php esc_html_e( 'Create', 'hustle' ); ?>
			</a>
		<?php } ?>

		<?php if ( $sshare_count ) : ?>

			<div class="sui-actions-right">
				<p><small><strong><a href="<?php echo esc_url( add_query_arg( $query_array, 'admin.php' ) ); ?>" style="color: #888888;"><?php printf( esc_html__( 'View all %s', 'hustle' ), esc_html( strtolower( $widget_name ) ) ); ?></a></strong></small></p>
			</div>

		<?php endif; ?>

	</div>

</div>
