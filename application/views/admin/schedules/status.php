<ul class="nav nav-pills">
	<li>
		<a><i class="fa fa-clock-o"></i> <?php echo $this->hc_time->formatPeriodShort($count['duration'], 'hour'); ?></a>
	</li>

<?php if( $count['open'] ) : ?>
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" title="<?php echo lang('shift_status_open_help'); ?>">
			<span class="label label-lg label-danger">
				<?php echo $count['open']; ?>
			</span>
			<?php echo lang('shift_status_open'); ?>
		</a>
<?php
/*
?>
		<ul class="dropdown-menu">
			<li>
				<a href="<?php echo ci_site_url( array($this->conf['path'], 'unpublish', $start_date, $end_date) ); ?>">
				<i class="fa fa-check text-warning"></i> <?php echo lang('shift_unpublish'); ?>
				</a>
			</li>
		</ul>
<?php
*/
?>
	</li>
<?php endif; ?>

<?php if( $count['draft'] ) : ?>
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" title="<?php echo lang('shift_status_draft_help'); ?>">
			<span class="label label-lg label-warning">
				<?php echo $count['draft']; ?>
			</span>
			<?php echo lang('shift_status_draft'); ?>
			<b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
			<li>
				<a href="<?php echo ci_site_url( array($this->conf['path'], 'publishdraft', 'start', $start_date, 'end', $end_date, 'staff', $staff_id, 'location', $location_id) ); ?>">
				<i class="fa fa-check text-success"></i> <?php echo lang('shift_publish'); ?>
				</a>
			</li>
		</ul>
	</li>
<?php endif; ?>

<?php if( $count['pending'] ) : ?>
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" title="<?php echo lang('shift_status_pending_help'); ?>">
			<span class="label label-lg label-info">
				<?php echo $count['pending']; ?>
			</span>
			<?php echo lang('shift_status_pending'); ?>
			<b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
			<li>
				<a href="<?php echo ci_site_url( array($this->conf['path'], 'publish', 'start', $start_date, 'end', $end_date, 'staff', $staff_id, 'location', $location_id) ); ?>">
				<i class="fa fa-check text-success"></i> <?php echo lang('shift_publish'); ?>
				</a>
			</li>
		</ul>
	</li>
<?php endif; ?>

<?php if( $count['active'] ) : ?>
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" title="<?php echo lang('shift_status_active_help'); ?>">
			<span class="label label-lg label-success">
				<?php echo $count['active']; ?>
			</span>
			<?php echo lang('shift_status_active'); ?>
			<b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
			<li>
				<a href="<?php echo ci_site_url( array($this->conf['path'], 'unpublish', 'start', $start_date, 'end', $end_date, 'staff', $staff_id, 'location', $location_id) ); ?>">
				<i class="fa fa-check text-warning"></i> <?php echo lang('shift_unpublish'); ?>
				</a>
			</li>
		</ul>
	</li>
<?php endif; ?>

</ul>