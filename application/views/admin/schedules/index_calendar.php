<?php
//$skip_weekdays = array( 0, 6 ); // add weekdays which you do not want to show, 0 - sunday, 1 - monday etc
?>
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

if( isset($skip_weekdays) && $skip_weekdays )
{
	$remain_days = 7 - count($skip_weekdays);
	$per_row = $remain_days;
	switch( $remain_days )
	{
		case 6:
			$col_class = 'col-sm-1';
			$container_class .= ' hc_cal6';
			break;
		case 5:
			$col_class = 'col-sm-1';
			$container_class .= ' hc_cal5';
			break;
		case 4:
			$col_class = 'col-sm-3';
			break;
	}
}
?>

<?php
switch( $filter )
{
	case 'location':
		$day_slot = array(
			'admin/schedules/day_location',
			'range',	$range,
			'id',		$current_location->id
			);
		$add_links = array(
			array(
				array(
					'admin/shifts/add',
					'location', $current_location->id,
					),
				'<i class="fa fa-plus"></i> ' . lang('shift_add')
				)
			);

		break;

	case 'staff':
		$day_slot = array(
			'admin/schedules/day_staff',
			'range',	$range,
			'id',		$current_staff->id
			);
		$add_links = array(
			array(
				array(
					'admin/shifts/add',
					'user', $current_staff->id,
					),
				'<i class="fa fa-fw fa-clock-o"></i> ' . lang('shift')
				),
			array(
				array(
					'admin/timeoffs/add',
					'user', $current_staff->id,
					'status', TIMEOFF_MODEL::STATUS_ACTIVE,
					),
				'<i class="fa fa-fw fa-coffee"></i> ' . lang('timeoff')
				),
			);
		break;

	default:
		$day_slot = array(
			'admin/schedules/day',
			'range',	$range
			);
		$add_links = array(
			array(
				array(
					'admin/shifts/add',
					),
				'<i class="fa fa-plus"></i> ' . lang('shift_add')
				)
			);
		break;
}
?>

<?php foreach( $month_matrix as $week ) : ?>
	<div class="<?php echo $container_class; ?>">
	<div class="row">
	<?php $in_row_count = 0; ?>

	<?php foreach( $week as $week_day => $date ) : ?>
		<?php
		if( isset($skip_weekdays) && $skip_weekdays && in_array($week_day, $skip_weekdays) )
		{
			continue;
		}
		?>
		<?php $in_row_count++; ?>

		<?php if( $in_row_count > $per_row ) : ?>
			</div>
			<div class="row">
			<?php $in_row_count = 0; ?>
		<?php endif; ?>

		<div class="<?php echo $col_class; ?>">
			<?php if( ($date >= $start_date) && ($date <= $end_date) ) : ?>
				<?php
				$this_day_slot = $day_slot;
				$this_day_slot[] = 'start';
				$this_day_slot[] = $date;
				?>
				<div class="thumbnail">
					<div class="hc-target" data-src="<?php echo ci_site_url($this_day_slot); ?>">
						<?php echo call_user_func_array( 'Modules::run', $this_day_slot ); ?>
					</div>

					<?php if( count($add_links) > 1 ) : ?>
						<div class="btn-group btn-block">
							<a href="#" class="btn btn-default btn-sm btn-block dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-plus"></i> <?php echo lang('common_add'); ?> <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<?php foreach( $add_links as $add_link ) : ?>
									<?php
									$this_add_link = $add_link;
									$this_add_link[0][] = 'date';
									$this_add_link[0][] = $date;
									$this_add_link[0] = ci_site_url( $this_add_link[0] );
									?>
									<li>
										<a href="<?php echo $this_add_link[0]; ?>" class="" data-return-action="refresh">
											<?php echo $this_add_link[1]; ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php else : ?>
						<?php
						$this_add_link = $add_links[0];
						$this_add_link[0][] = 'date';
						$this_add_link[0][] = $date;
						$this_add_link[0] = ci_site_url( $this_add_link[0] );
						?>
						<a href="<?php echo $this_add_link[0]; ?>" class="btn btn-default btn-sm btn-block" data-return-action="refresh">
							<?php echo $this_add_link[1]; ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	</div>
	</div>
<?php endforeach; ?>
