<div class="row-fluid">
	<div class="pull-left">
		<?php require( dirname(__FILE__) . '/_tabs.php' ); ?>
	</div>

	<div class="pull-right">
		<?php require( dirname(__FILE__) . '/_date_navigation.php' ); ?>
	</div>
</div>

<div class="hc-page-status" data-src="<?php echo ci_site_url( array('admin/schedules/status', 'start', $start_date, 'end', $end_date) ); ?>">
	<?php echo Modules::run('admin/schedules/status', 'start', $start_date, 'end', $end_date); ?>
</div>

<?php foreach( $month_matrix as $week ) : ?>
	<div class="hc_cal" style="margin-bottom: 1em;">
	<div class="row-fluid">
	<?php foreach( $week as $date ) : ?>
		<div class="span1">
			<?php if( ($date >= $start_date) && ($date <= $end_date) ) : ?>
				<div class="thumbnail">
					<div class="hc-target" data-src="<?php echo ci_site_url( array('admin/schedules/day', $date) ); ?>">
						<?php echo Modules::run('admin/schedules/day', $date); ?>
					</div>

					<?php
					echo ci_anchor( 
						array(
							'admin/shifts/add',
							'date', $date,
							),
						'<i class="icon-plus"></i> ' . lang('shift_add'),
						'class="btn btn-mini btn-block" data-return-action="refresh"'
						);
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	</div>
	</div>
<?php endforeach; ?>