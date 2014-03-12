<ul class="nav nav-tabs">
	<?php require( dirname(__FILE__) . '/_tabs.php' ); ?>

	<li class="pull-right">
		<?php require( dirname(__FILE__) . '/_date_range.php' ); ?>
	</li>

	<li class="pull-right list-item-hori-separated">
		<div>
			<?php require( dirname(__FILE__) . '/_export.php' ); ?>
		</div>
	</li>
</ul>

<table class="table table-striped">
	<tr>
		<th>
			<?php echo lang('user_level_staff'); ?>
		</th>
		<th>
			<?php echo lang('shift_status_active'); ?>
		</th>
		<th>
			<?php echo lang('shift_status_draft'); ?>
		</th>
	</tr>

	<?php foreach( $stats_shifts as $staff_id => $array ) : ?>
		<?php $staff = $staffs[ $staff_id ]; ?>
		<tr>
			<td>
				<?php echo $staff->title(TRUE); ?>
			</td>
			<td>
				<i class="fa fa-clock-o"></i> <?php echo $this->hc_time->formatPeriodShort($stats_shifts[$staff->id][1], 'hour'); ?>
			</td>
			<td>
				<i class="fa fa-clock-o"></i> <?php echo $this->hc_time->formatPeriodShort($stats_drafts[$staff->id][1], 'hour'); ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>