<?php require( dirname(__FILE__) . '/_control.php' ); ?>
<?php require( dirname(__FILE__) . '/_status.php' ); ?>

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
							'admin/schedules/day_staff',
							'start',	$date,
							'range',	$range,
							'id',		$current_staff->id
							)
							); ?>">
						<?php 
						echo Modules::run(
							'admin/schedules/day_staff',
							'start',	$date,
							'range',	$range,
							'id',		$current_staff->id
							);
						?>
					</div>

					<div class="btn-group btn-block">
						<a href="#" class="btn btn-default btn-sm btn-block dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-plus"></i> <?php echo lang('common_add'); ?> <span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li>
							<a href="<?php echo ci_site_url(array('admin/shifts/add', 'date', $date, 'user', $current_staff->id)); ?>" class="" data-return-action="refresh">
								<i class="fa fa-fw fa-clock-o"></i> <?php echo lang('shift'); ?>
							</a>
							</li>
							<li>
							<a href="<?php echo ci_site_url(array('admin/timeoffs/add', 'user', $current_staff->id, 'status', TIMEOFF_MODEL::STATUS_ACTIVE, 'date', $date)); ?>" class="" data-return-action="refresh">
								<i class="fa fa-fw fa-coffee"></i> <?php echo lang('timeoff'); ?>
							</a>
							</li>
						</ul>
					</div>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	</div>
	</div>
<?php endforeach; ?>