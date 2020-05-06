<div class="sui-dialog sui-dialog-alt sui-dialog-reduced" aria-hidden="true" tabindex="-1" id="hustle-dialog--delete-ips">

	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>
	<div class="sui-dialog-content" aria-labelledby="dialog-delete--title" aria-describedby="dialog-delete--description" role="dialog">

		<div class="sui-box" role="document">

			<div class="sui-box-header sui-block-content-center">

				<h3 id="dialog-delete--title" class="sui-box-title"><?php esc_html_e( 'Delete IP Addresses', 'hustle' ); ?></h3>

				<p id="dialog-delete--description" class="sui-description" style="margin-bottom: 0;"><?php esc_html_e( 'Choose the IP addresses you want to delete from your database permanently. Note that this will only remove the IP addresses from the database leaving rest of the tracking data intact.', 'hustle' ); ?></p>

				<div class="sui-actions-right">
					<button class="sui-dialog-close" aria-label="<?php esc_html_e( 'Close dialog', 'hustle' ); ?>" data-a11y-dialog-hide></button>
				</div>

			</div>

			<form id="hustle-delete-ip-form" class="sui-box-body sui-box-body-slim">

				<label class="sui-label" style="margin-bottom: 5px;"><?php esc_html_e( 'Delete IP Addresses', 'hustle' ); ?></label>

				<div class="sui-side-tabs">

					<div class="sui-tabs-menu">

						<label for="hustle-remove-ips--all" class="sui-tab-item active">
							<input type="radio"
								name="range"
								id="hustle-remove-ips--all"
								value="all"
								checked
							/>
							<?php esc_html_e( 'All IPs', 'hustle' ); ?>
						</label>

						<label for="hustle-remove-ips--range" class="sui-tab-item">
							<input type="radio"
							name="range"
							id="hustle-remove-ips--range"
							value="range"
							data-tab-menu="only-ips"
						/>
							<?php esc_html_e( 'Specific IPs Only', 'hustle' ); ?>
						</label>

					</div>

					<div class="sui-tabs-content">

						<div class="sui-tab-boxed"
							data-tab-content="only-ips">

							<label for="hustle-remove-specific-ips" class="sui-label"><?php esc_html_e( 'Delete Specific IPs', 'hustle' ); ?></label>

							<textarea name="ips"
								rows="16"
								placeholder="<?php esc_html_e( 'Enter your IP addresses here...', 'hustle' ); ?>"
								id="hustle-remove-specific-ips"
								class="sui-form-control"></textarea>

							<span class="sui-description" style="margin-bottom: 20px;"><?php esc_html_e( 'Type one IP address per line. Both IPv4 and IPv6 are supported. IP ranges are also accepted in format xxx.xxx.xxx.xxx-xxx.xxx.xxx.xxx.', 'hustle' ); ?></span>

						</div>

					</div>

				</div>

			</form>

			<div class="sui-box-footer sui-outlined">
				<button class="sui-button sui-button-ghost" data-a11y-dialog-hide><?php esc_html_e( 'Cancel', 'hustle' ); ?></button>
				<button
					id="hustle-delete-ips-submit"
					class="sui-button sui-button-red sui-button-ghost hustle-delete"
					data-nonce="<?php echo esc_attr( wp_create_nonce( 'hustle_remove_ips' ) ); ?>"
					data-form-id="hustle-delete-ip-form"
				>
					<span class="sui-loading-text">
						<i class="sui-icon-trash" aria-hidden="true"></i> <?php esc_html_e( 'Delete IP Addresses', 'hustle' ); ?>
					</span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>

			</div>

		</div>

	</div>

</div>
