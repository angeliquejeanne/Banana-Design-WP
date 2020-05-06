<?php
$banner_1x = self::$plugin_url . 'assets/images/banner-downgrade.png';
$banner_2x = self::$plugin_url . 'assets/images/banner-downgrade@2x.png';

$new_version     = Opt_In_Utils::_is_free() ? 'v7.0.4' : 'v4.0.4';
$current_version = Opt_In_Utils::_is_free() ? 'v7' . ltrim( Opt_In::VERSION, '4' ) : 'v' . Opt_In::VERSION;

// Click and download Hustle.
$hustle_download_url = Opt_In_Utils::_is_free() ? 'https://downloads.wordpress.org/plugin/wordpress-popup.7.0.4.zip' : 'https://premium.wpmudev.org/download/hustle-pro-4.0.4.zip';

// Link to trigger the restore and deactivate the plugin.
$visibility_restore_url = add_query_arg(
	[
		'page'               => 'hustle_settings',
		'section'            => 'data',
		'hustle-restore-40x' => 'true',
		'nonce'              => wp_create_nonce( 'hustle-restore-40x-visibility' ),
	],
	'admin.php'
);
?>

<div class="sui-modal sui-modal-md">

	<div
		role="dialog"
		id="hustle-dialog--404-downgrade"
		class="sui-modal-content"
		aria-modal="true"
		aria-labelledby="hustle404Downgrade"
		aria-describedby="hustle404DowngradeDesc"
	>

		<div class="sui-box" role="document">

			<div class="sui-box-header sui-flatten sui-content-center">

				<figure class="sui-box-banner" aria-hidden="true">
					<?php echo Opt_In_Utils::render_image_markup( esc_url( $banner_1x ), esc_url( $banner_2x ), 'sui-image sui-image-center' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</figure>

				<button class="sui-button-icon sui-button-float--right" data-modal-close>
					<i class="sui-icon-close sui-md"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog', 'hustle' ); ?></span>
				</button>

				<h3 id="hustle404Downgrade" class="sui-box-title sui-lg">
					<?php /* translators: version to rollback into. */ ?>
					<?php printf( esc_html__( 'Rollback to Hustle %s', 'hustle' ), esc_html( $new_version ) ); ?>
				</h3>

				<p class="sui-description">
					<?php /* translators: version to rollback into. */ ?>
					<?php printf( esc_html__( 'Follow the instructions below to downgrade Hustle to %s.', 'hustle' ), esc_html( $new_version ) ); ?>
				</p>

			</div>

			<div class="sui-box-body">

				<p class="sui-description" style="margin-bottom: 5px;">
				<?php
					printf(
						/* translators: 1. opening 'strong' tag. 2. closing 'strong' tag, 3. version to rollback into, 4. current version */
						esc_html__( '1. The visibility conditions have been migrated to work with the new visibility behavior during the upgrade to %4$s. Click on the "%1$sRollback Database & Deactivate%2$s" button to migrate the visibility conditions back to a format that is supported by %3$s or lower and deactivate Hustle %4$s.', 'hustle' ),
						'<strong style="color: #666;">',
						'</strong>',
						esc_html( $new_version ),
						esc_html( $current_version )
					);
					?>
				</p>
				<p class="sui-description" style="margin-bottom: 5px;">
				<?php
					printf(
						/* translators: 1. version to rollback into, 2. current version */
						esc_html__( '2. Delete Hustle %2$s, so you could install %1$s in the next step.', 'hustle' ),
						esc_html( $new_version ),
						esc_html( $current_version )
					);
					?>
				</p>
				<p class="sui-description">
				<?php
					printf(
						/* translators: 1. first opening 'a' tag 2. second opening 'a' tag, 3. closing 'a' tag, 4. version to rollback into */
						esc_html__( '3. %1$sDownload Hustle %4$s%3$s and install it by manually uploading it on the Plugins page. You can refer to the %2$swp.org guide%3$s on installing a plugin from a zip file.', 'hustle' ),
						'<a href="' . esc_url_raw( $hustle_download_url ) . '">',
						'<a href="https://wordpress.org/support/article/managing-plugins/#manual-upload-via-wordpress-admin" target="_blank">',
						'</a>',
						esc_html( $new_version )
					);
					?>
				</p>

			</div>

			<div class="sui-box-footer sui-content-right">
				<a href="<?php echo esc_url_raw( $visibility_restore_url ); ?>" class="sui-button hustle-load-on-click">
					<span class="sui-loading-text"><?php esc_html_e( 'Rollback Database & Deactivate', 'hustle' ); ?></span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</a>
			</div>

		</div>

		<p style="margin: 0; color: #FFF; font-size: 13px; line-height: 22px; text-align: center;">
		<?php
			printf(
				/* translators: 1. opening 'a' tag, 2. closing 'a' tag. */
				esc_html__( 'Having trouble downgrading? %1$sContact support%2$s', 'hustle' ),
				'<a href="https://premium.wpmudev.org/hub/support/#wpmud-chat-pre-survey-modal" target="_blank" style="color: #FFFFFF; text-decoration: underline;">',
				'</a>'
			);
			?>
		</p>

	</div>

</div>
