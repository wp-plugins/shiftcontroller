<ul class="nav nav-tabs">
	<?php require( dirname(__FILE__) . '/_tabs.php' ); ?>

	<li class="pull-right">
		<?php require( dirname(__FILE__) . '/_date_navigation.php' ); ?>
	</li>

	<li class="pull-right">
		<?php require( dirname(__FILE__) . '/_range.php' ); ?>
	</li>
</ul>

<div class="hc-page-status" data-src="<?php echo ci_site_url( array('admin/schedules/status', 'start', $start_date, 'end', $end_date, 'location', $current_location->id) ); ?>">
	<?php 
	echo Modules::run(
		'admin/schedules/status',
		'start', $start_date,
		'end', $end_date,
		'location', $current_location->id
		);
	?>
</div>

<?php
$container_class = 'hc_cal';
switch( $range )
{
	case 'month':
		$per_row = 7;
		$col_class = 'col-sm-1';
		break;
	case 'week':
		$per_row = 4;
		$col_class = 'col-sm-3';
		break;
}
?>

<?php foreach( $month_matrix as $week ) : ?>
	<div class="<?php echo $container_class; ?>">

	<div class="row">

	<?php $in_row_count = 0; ?>

	<?php foreach( $week as $date ) : ?>
		<?php $in_row_count++; ?>

		<?php if( $in_row_count > $per_row ) : ?>
			</div>
			<div class="row">
			<?php $in_row_count = 0; ?>
		<?php endif; ?>

		<div class="<?php echo $col_class; ?>">
			<?php if( ($date >= $start_date) && ($date <= $end_date) ) : ?>
				<div class="thumbnail">
					<div class="hc-target" data-src="<?php echo ci_site_url( 
						array(
							'admin/schedules/day_location',
							'start',	$date,
							'range',	$range,
							'id',		$current_location->id
							)
							); ?>">
						<?php
						echo Modules::run(
							'admin/schedules/day_location',
							'start',	$date,
							'range',	$range,
							'id',		$current_location->id
							);
						?>
					</div>
					<?php
					echo ci_anchor( 
						array(
							'admin/shifts/add',
							'date', $date,
							'location', $current_location->id,
							),
						'<i class="fa fa-plus"></i> ' . lang('shift_add'), 
						'class="btn btn-default btn-sm btn-block" data-return-action="refresh"'
						);
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	</div>
	</div>
<?php endforeach; ?>
