<?php require( dirname(__FILE__) . '/_control.php' ); ?>

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