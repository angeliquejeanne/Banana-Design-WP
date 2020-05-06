<?php // VIEW: Mobiles only. ?>
<div class="hui-pagination hui-pagination-mobile">

	<div class="sui-pagination-wrap">

		<span class="sui-pagination-results"><?php printf( esc_html( _n( '%d result', '%d results', $total, 'hustle' ) ),  esc_html( $total ) ); ?></span>

		<?php
		self::static_render(
			'admin/commons/sui-listing/elements/pagination-list',
			array(
				'page'             => $page,
				'paged'            => intval( $paged ),
				'total'            => $total,
				'entries_per_page' => $entries_per_page,
			)
		);
		?>

	</div>

</div>

<div class="sui-pagination-box">

	<?php // VIEW: Desktop only. ?>
	<div class="sui-box sui-pagination-desktop">

		<?php
		self::static_render(
			'admin/commons/sui-listing/elements/bulk-actions',
			array(
				'module_type' => $module_type,
				'is_bottom'   => ! empty( $is_bottom ),
			)
		);
		?>

		<div class="sui-pagination-wrap">

			<span class="sui-pagination-results"><?php echo esc_html( sprintf( _n( '%d result', '%d results', $total, 'hustle' ), esc_html( $total ) ) ); ?></span>

			<?php
			self::static_render(
				'admin/commons/sui-listing/elements/pagination-list',
				array(
					'total'            => $total,
					'page'             => $page,
					'paged'            => intval( $paged ),
					'entries_per_page' => $entries_per_page,
				)
			);
			?>

		</div>

	</div>

</div>
