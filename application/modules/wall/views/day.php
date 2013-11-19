<?php
$this->hc_time->setDateDb( $date );
$this_weekday = $this->hc_time->getWeekday();

/* group shifts */
$grouped_shifts = array();
foreach( $my_shifts as $shift )
{
	$key = join( '_', array($shift->start, $shift->end) );
	$key2 = $shift->location_id;
	if( ! isset($grouped_shifts[$key]) )
		$grouped_shifts[$key] = array();
	if( ! isset($grouped_shifts[$key][$key2]) )
		$grouped_shifts[$key][$key2] = array();

	$grouped_shifts[$key][$key2][] = $shift;
}
?>

<ul class="nav nav-list nav-list-condensed">
<li>
	<h4>
	<?php echo $this->hc_time->formatWeekdayShort(); ?><br><small><?php echo $this->hc_time->formatDate(); ?></small>
	</h4>
</li>

<li class="divider"></li>

<?php foreach( $grouped_shifts as $key => $sha ) : ?>
	<?php
		list( $this_start, $this_end ) = explode( '_', $key );
	?>
	<li class="nav-header">
		<?php echo $this->hc_time->formatTimeOfDay($this_start); ?> - <?php echo $this->hc_time->formatTimeOfDay($this_end); ?>
	</li>

	<?php foreach( $sha as $lid => $shs ) : ?>
		<li>
			<i class="icon-home"></i> <?php echo $shs[0]->location_name; ?>
		</li>

		<?php foreach( $shs as $sh ) : ?>
			<li class="alert alert-condensed alert-success">
				<i class="icon-user"></i> <?php echo $staffs[$sh->user_id]->full_name(); ?>
			</li>
		<?php endforeach; ?>

	<?php endforeach; ?>

	<li class="divider"></li>
<?php endforeach; ?>
</ul>