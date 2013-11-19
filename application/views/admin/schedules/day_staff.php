<?php
$this->hc_time->setDateDb( $date );
$this_weekday = $this->hc_time->getWeekday();

/* group shifts */
$grouped_shifts = array();
foreach( $my_shifts as $sh )
{
	$key = join( '_', array($sh->start, $sh->end) );
	if( ! isset($grouped_shifts[$key]) )
		$grouped_shifts[$key] = array();
	$grouped_shifts[$key][] = $sh;
}
?>

<ul class="nav nav-list nav-list-condensed">
<li>
	<h4>
	<?php echo $this->hc_time->formatWeekdayShort(); ?><br><small><?php echo $this->hc_time->formatDate(); ?></small>
	</h4>
</li>

<li class="divider"></li>

<?php foreach( $my_timeoffs as $to ) : ?>
	<?php 
		$conflicts = $to->conflicts( $this->data['shifts'], $this->data['timeoffs'], $date );
		require( dirname(__FILE__) . '/_timeoff_dropdown.php' );
	?>
<?php endforeach; ?>

<?php foreach( $grouped_shifts as $key => $shs ) : ?>
	<?php
		list( $this_start, $this_end ) = explode( '_', $key );
	?>
	<li class="nav-header">
		<?php echo $this->hc_time->formatTimeOfDay($this_start); ?> - <?php echo $this->hc_time->formatTimeOfDay($this_end); ?>
	</li>

	<?php foreach( $shs as $sh ) : ?>
		<?php require( dirname(__FILE__) . '/_shift_dropdown.php' ); ?>
	<?php endforeach; ?>

	<li class="divider"></li>
<?php endforeach; ?>
</ul>