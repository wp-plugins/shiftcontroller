<ul class="nav nav-pills">
	<li>
		<a><i class="icon-time"></i> <?php echo $this->hc_time->formatPeriodShort($count['duration'], 'hour'); ?></a>
	</li>

<?php if( $count['not_assigned'] ) : ?>
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown">
			<span class="badge badge-important" title="<?php echo lang('shift_not_assigned'); ?>">
				<?php echo $count['not_assigned']; ?>
			</span>
			<?php echo lang('shift_not_assigned'); ?>
		</a>
<?php
/*
?>
		<ul class="dropdown-menu">
			<li>
				<a href="<?php echo ci_site_url( array($this->conf['path'], 'unpublish', $start_date, $end_date) ); ?>">
				<i class="icon-ok text-warning"></i> <?php echo lang('shift_unpublish'); ?>
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
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<span class="badge badge-warning" title="<?php echo lang('shift_status_draft'); ?>">
				<?php echo $count['draft']; ?>
			</span>
			<?php echo lang('shift_status_draft'); ?>
			<b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
			<li>
				<a href="<?php echo ci_site_url( array($this->conf['path'], 'publish', 'start', $start_date, 'end', $end_date, 'staff', $staff_id, 'location', $location_id) ); ?>">
				<i class="icon-ok text-success"></i> <?php echo lang('shift_publish'); ?>
				</a>
			</li>
		</ul>
	</li>
<?php endif; ?>

<?php if( $count['active'] ) : ?>
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<span class="badge badge-success" title="<?php echo lang('shift_status_active'); ?>">
				<?php echo $count['active']; ?>
			</span>
			<?php echo lang('shift_status_active'); ?>
			<b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
			<li>
				<a href="<?php echo ci_site_url( array($this->conf['path'], 'unpublish', 'start', $start_date, 'end', $end_date, 'staff', $staff_id, 'location', $location_id) ); ?>">
				<i class="icon-ok text-warning"></i> <?php echo lang('shift_unpublish'); ?>
				</a>
			</li>
		</ul>
	</li>
<?php endif; ?>

</ul>
