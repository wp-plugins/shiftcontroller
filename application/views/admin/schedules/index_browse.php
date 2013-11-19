<div class="row-fluid">
	<div class="pull-left">
		<?php require( dirname(__FILE__) . '/_tabs.php' ); ?>
	</div>
	<div class="pull-right">
		<?php require( dirname(__FILE__) . '/_date_range.php' ); ?>
	</div>
	<div class="pull-right" style="margin-right: 1em;">
		<?php require( dirname(__FILE__) . '/_export.php' ); ?>
	</div>
</div>

<div class="hc-page-status" data-src="<?php echo ci_site_url( array('admin/schedules/status', 'start', $start_date, 'end', $end_date) ); ?>">
	<?php echo Modules::run('admin/schedules/status', 'start', $start_date, 'end', $end_date); ?>
</div>

<table class="table table-striped">
<tr>
	<th><?php echo lang('time_date'); ?></th>
	<th><?php echo lang('time'); ?></th>
	<th><?php echo lang('time_duration'); ?></th>
	<th><?php echo lang('user_level_staff'); ?></th>
	<th><?php echo lang('location'); ?></th>
</tr>

<?php foreach( $shifts as $sh ) : ?>
	<?php
		if( $sh->date < $start_date )
			continue;
		if( $sh->date > $end_date )
			continue;
	?>
	<tr>
		<td>
			<?php
				$this->hc_time->setDateDb( $sh->date );
				$date_view = '';
				$date_view .= $this->hc_time->formatWeekdayShort();
				$date_view .= ', ';
				$date_view .= $this->hc_time->formatDate();
			?>
			<?php echo $date_view; ?>
		</td>
		<td>
			<?php
				$time_view = hc_format_time_of_day($sh->start) . ' - ' . hc_format_time_of_day($sh->end);
			?>
			<?php echo $time_view; ?>
		</td>
		<td>
			<?php echo $this->hc_time->formatPeriodShort($sh->get_duration(), 'hour'); ?>
		</td>
		<td>
			<ul class="nav nav-list nav-list-condensed">
			<?php 
				$display_as = 'location';
				require( dirname(__FILE__) . '/_shift_dropdown.php' );
			?>
			</ul>
		</td>
		<td>
			<?php echo $sh->location_name; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>