<?php require( dirname(__FILE__) . '/_control.php' ); ?>
<?php require( dirname(__FILE__) . '/_status.php' ); ?>

<table class="table table-striped">
<tr>
	<th><?php echo lang('time_date'); ?></th>
	<th><?php echo lang('time'); ?></th>
	<th><?php echo lang('time_duration'); ?></th>
	<th><?php echo lang('user_level_staff'); ?></th>
	<?php if( $location_count > 1 ) : ?>
		<th><?php echo lang('location'); ?></th>
	<?php endif; ?>
</tr>

<?php foreach( $shifts as $sh ) : ?>
	<?php
	if( $sh->date < $start_date )
		continue;
	if( $sh->date > $end_date )
		continue;
	if( ($filter == 'location') && ($sh->location_id != $current_location->id) )
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
			<ul class="nav nav-list nav-condensed">
			<li>
				<?php 
				$titles = array('staff');
				require( dirname(__FILE__) . '/_shift_dropdown.php' );
				?>
			</li>
			</ul>
		</td>
		<?php if( $location_count > 1 ) : ?>
			<td>
				<i class="fa fa-home"></i> <?php echo $sh->location_name; ?>
			</td>
		<?php endif; ?>
	</tr>
<?php endforeach; ?>
</table>