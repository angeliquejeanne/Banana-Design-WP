<div class="sui-box">

	<div class="sui-box-header">

		<h2 class="sui-box-title">
			<i class="sui-icon-<?php echo esc_attr( $widget_type ); ?>" aria-hidden="true"></i>
			<?php echo esc_html( $widget_name ); ?>
		</h2>

	</div>

	<div class="sui-box-body">

		<p><?php echo esc_html( $description ); ?></p>

		<?php if ( count( $modules ) ) { ?>

			</div>

			<table class="sui-table sui-table-flushed hui-table-dashboard">

				<thead>

					<tr>

						<th><?php esc_html_e( 'Name', 'hustle' ); ?></th>
						<th class="hui-status"><?php esc_html_e( 'Status', 'hustle' ); ?></th>

					</tr>

				</thead>

				<tbody>

					<?php foreach ( $modules as $module ) {

						$status_class = 'draft';
						$status_name  = esc_html__( 'Draft', 'hustle' );

						if ( $module->active ) {
							$status_class = 'published';
							$status_name  = esc_html__( 'Published', 'hustle' );
						} ?>

						<tr>

							<td class="sui-table-item-title"><?php echo esc_attr( $module->module_name ); ?></td>

							<td class="hui-status">

								<div class="hui-status-elements">

									<span class="sui-status-dot sui-<?php echo esc_attr( $status_class ); ?> sui-tooltip"
										data-tooltip="<?php echo esc_html( $status_name ); ?>">
										<span aria-hidden="true"></span>
									</span>

									<a href="
										<?php echo esc_url( add_query_arg( array(
											'page' => $module->get_listing_page(),
											'view-stats' => $module->module_id,
										) ), 'admin.php' ); ?>
										" class="sui-button-icon sui-tooltip"
										data-tooltip="<?php esc_html_e( 'View Stats', 'hustle' ); ?>">
										<i class="sui-icon-graph-line" aria-hidden="true"></i>
									</a>

									<div class="sui-dropdown">
										<?php
										// Actions
										self::static_render(
											'admin/commons/sui-listing/elements/actions',
											array(
												'module' => $module,
												'capability' => $capability,
												'dashboard' => true,
												'smallcaps_singular' => $smallcaps_singular,
												'capitalize_singular' => $capitalize_singular,
											)
										); ?>
									</div>

								</div>

							</td>

						</tr>

					<?php } ?>

				</tbody>

			</table>

			<div class="sui-box-footer">

		<?php } ?>

		<?php $query_array = array( 'page' => Hustle_Module_Admin::get_listing_page_by_module_type( $widget_type ) ); ?>

		<?php if ( $capability['hustle_create'] ) { ?>
			<a
				href="
					<?php
					if ( Hustle_Module_Admin::can_create_new_module( $widget_type ) ) {
						$args = array_merge( $query_array, ['create-module' => 'true'] );
					} else {
						$args = array_merge( $query_array, ['requires-pro' => 'true'] );
					}
					echo esc_url( add_query_arg( $args, 'admin.php' ) );
					?>"
				class="sui-button sui-button-blue"
			>
				<i class="sui-icon-plus" aria-hidden="true"></i>
				<?php esc_html_e( 'Create', 'hustle' ); ?>
			</a>
		<?php } ?>

		<?php if ( count( $modules ) ) : ?>

			<div class="sui-actions-right">
				<p><small><strong>
					<a href="<?php echo esc_url( add_query_arg( $query_array, 'admin.php' ) ); ?>" style="color: #888888;">
						<?php printf( esc_html__( 'View all %s', 'hustle' ), esc_html( strtolower( $widget_name ) ) ); ?>
					</a>
				</strong></small></p>
			</div>

		<?php endif; ?>

	</div>

</div>
