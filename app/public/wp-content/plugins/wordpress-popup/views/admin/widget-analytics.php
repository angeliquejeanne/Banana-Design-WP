<?php
/**
 * Dashboard Hustle analytics widget: Displayed on site dashboards with stats.
 *
 * @since  4.1
 */
$analytics_stats = Hustle_Tracking_Model::analytics_stats( 30 );
// temporary var_dump for @leigh
// var_dump( $analytics_stats );

$array_days_ago = Opt_In_Utils::get_analytic_ranges();
$tab = filter_input( INPUT_GET, 'tab' );
$days_ago = ( isset( $_REQUEST['analytics_range'] ) && in_array( $_REQUEST['analytics_range'], array_keys( $array_days_ago ) ) ) ? absint( $_REQUEST['analytics_range'] ) : 7;
?>
<div class="wpmudui-analytics">

	<div class="wpmudui-tabs">

			<div class="wpmudui-analytics-tabs" data-tabs>
				<a data-tab="total"<?php echo ( ( is_null( $tab ) || 'total' === $tab ) ? ' class="wpmudui-current"' : '' ); ?>><?php esc_html_e( 'Total', 'hustle' ); ?></a>
				<a data-tab="floating"<?php echo ( 'floating' === $tab ? ' class="wpmudui-current"' : '' ); ?>><?php esc_html_e( 'Floating', 'hustle' ); ?></a>
				<a data-tab="inline"<?php echo ( 'inline' === $tab ? ' class="wpmudui-current"' : '' ); ?>><?php esc_html_e( 'Inline', 'hustle' ); ?></a>
				<a data-tab="widget"<?php echo ( 'widget' === $tab ? ' class="wpmudui-current"' : '' ); ?>><?php esc_html_e( 'Widget', 'hustle' ); ?></a>
				<a data-tab="shortcode"<?php echo ( 'shortcode' === $tab ? ' class="wpmudui-current"' : '' ); ?>><?php esc_html_e( 'Shortcode', 'hustle' ); ?></a>
			</div>

			<div data-pane="posts" class="">

				<div class="wpmudui-search-form">
					<label class="wpmudui-label" for="wpmudui-analytics-posts-type"><?php esc_html_e( 'Show', 'hustle' ); ?></label>
					<select id="wpmudui-analytics-posts-type" class="wpmudui-select wpmudui-analytics-column-filter">
						<option value="views"><?php esc_html_e( 'Views', 'hustle' ); ?></option>
						<option value="conversions" selected><?php esc_html_e( 'Conversions', 'hustle' ); ?></option>
						<option value="conversion_rate"><?php esc_html_e( 'Conversion Rate', 'hustle' ); ?></option>
					</select>
					<label class="wpmudui-label" for="wpmudui-analytics-posts-range"><?php esc_html_e( 'data for', 'hustle' ); ?></label>
					<select id="wpmudui-analytics-posts-range" class="wpmudui-select wpmudui-analytics-range">
						<?php foreach ( $array_days_ago as $val => $title ) { ?>
							<option value="<?php echo esc_attr( $val ); ?>"<?php selected( $val, $days_ago ); ?>><?php echo esc_html( $title ); ?></option>
						<?php } ?>
					</select>
				</div>

				<div class="wpmudui-analytics-chart">
					<div class="wpmudui-analytics-chart-empty">
						<p class="wpmudui-analytics-chart-title"><?php esc_html_e( "We haven't collected enough data yet.", 'hustle' ); ?></p>
						<p><?php esc_html_e( "You will start viewing the performance statistics of your Hustle modules shortly. So feel free to check back soon.", 'hustle' ); ?></p>
					</div>
					<canvas id="hustle-analytics-graph"></canvas>
				</div>

				<div class="wpmudui-chart-options">

					<button data-type="overall" class="wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'Overall', 'hustle' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Overall', 'hustle' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="popups" class="wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'Pop-ups', 'hustle' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Pop-ups', 'hustle' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="slideins" class="wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'Slide-ins', 'hustle' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Slide-ins', 'hustle' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="embeds" class="wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'Embeds', 'hustle' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Embeds', 'hustle' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="social_sharing" class="wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'Social Sharing', 'hustle' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_attr_e( 'Social Sharing', 'hustle' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

				</div>

			</div>

	</div>

</div>
