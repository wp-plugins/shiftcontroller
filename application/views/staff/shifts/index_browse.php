<?php require( dirname(__FILE__) . '/_tabs.php' ); ?>

<p>
<?php require( dirname(__FILE__) . '/_date_range.php' ); ?>
</p>

<?php if( ! $shifts ) : ?>
	<?php echo lang('common_none'); ?>
	<?php return; ?>
<?php endif; ?>

<?php
// totals
$total_shifts = 0;
$total_duration = 0;
reset( $shifts );
foreach( $shifts as $sh )
{
	if( $sh->date < $start_date )
		continue;
	if( $sh->date > $end_date )
		continue;
	$total_shifts += 1;
	$total_duration += $sh->get_duration();
}
?>

<ul class="list-inline list-separated">
	<li>
	</li>
	<li>
		<?php echo $total_shifts; ?> <?php echo ( $total_shifts > 1 ) ? lang('shifts') : lang('shift'); ?>
	</li>
	<li>
		<i class="fa fa-clock-o"></i> 
		<?php echo $this->hc_time->formatPeriodShort($total_duration, 'hour'); ?>
	</li>
</ul>


<table class="table table-striped">
<tr>
	<th><?php echo lang('time_date'); ?></th>
	<th><?php echo lang('time'); ?></th>
	<th><?php echo lang('time_duration'); ?></th>
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
		<?php if( $location_count > 1 ) : ?>
			<td>
				<i class="fa fa-home"></i> <?php echo $sh->location_name; ?>
			</td>
		<?php endif; ?>
	</tr>
<?php endforeach; ?>
</table>