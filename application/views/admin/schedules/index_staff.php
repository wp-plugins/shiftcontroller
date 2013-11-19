<div class="row-fluid">
	<div class="pull-left">
		<?php require( dirname(__FILE__) . '/_tabs.php' ); ?>
	</div>
	<div class="pull-right">
		<?php require( dirname(__FILE__) . '/_date_navigation.php' ); ?>
	</div>
</div>

<?php foreach( $staffs as $staff ) : ?>

	<div class="page-header">
		<h4><i class="icon-user"></i> <?php echo $staff->title(); ?></h4>
	</div>

	<div class="hc-page-status" data-src="<?php echo ci_site_url( array('admin/schedules/status', 'start', $start_date, 'end', $end_date, 'staff', $staff->id) ); ?>">
		<?php echo Modules::run('admin/schedules/status', 'start', $start_date, 'end', $end_date, 'staff', $staff->id); ?>
	</div>

	<?php foreach( $month_matrix as $week ) : ?>
		<div class="hc_cal" style="margin-bottom: 1em;">
		<div class="row-fluid">
		<?php foreach( $week as $date ) : ?>
			<div class="span1">
				<?php if( ($date >= $start_date) && ($date <= $end_date) ) : ?>
					<div class="thumbnail">
						<div class="hc-target" data-src="<?php echo ci_site_url( array('admin/schedules/day_staff', $date, $staff->id) ); ?>">
							<?php echo Modules::run('admin/schedules/day_staff', $date, $staff->id); ?>
						</div>

						<div class="btn-group btn-block">
							<a href="#" class="btn btn-mini btn-block dropdown-toggle" data-toggle="dropdown">
								<i class="icon-plus"></i> <?php echo lang('common_add'); ?> <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li>
								<a href="<?php echo ci_site_url(array('admin/shifts/add', 'date', $date, 'user', $staff->id)); ?>" class="" data-return-action="refresh">
									<?php echo lang('shift'); ?>
								</a>
								</li>
								<li>
								<a href="<?php echo ci_site_url(array('admin/timeoffs/add', 'user', $staff->id, 'status', TIMEOFF_MODEL::STATUS_ACTIVE, 'date', $date)); ?>" class="" data-return-action="refresh">
									<?php echo lang('timeoff'); ?>
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

<?php endforeach; ?>
