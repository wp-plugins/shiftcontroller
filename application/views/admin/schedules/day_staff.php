<?php
if( $range == 'week' )
	$wide_view = TRUE;

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

<ul class="nav nav-stacked">
<li>
	<h4>
		<?php echo $this->hc_time->formatWeekdayShort(); ?> 
		<small><?php echo $this->hc_time->formatDate(); ?></small>
	</h4>
</li>

<li class="divider"></li>

<?php foreach( $my_timeoffs as $to ) : ?>
	<li>
		<?php 
		$conflicts = $to->conflicts( $this->data['shifts'], $this->data['timeoffs'], $date );
		require( dirname(__FILE__) . '/_timeoff_dropdown.php' );
		?>
	</li>
<?php endforeach; ?>

<?php foreach( $grouped_shifts as $key => $shs ) : ?>
	<?php
	list( $this_start, $this_end ) = explode( '_', $key );
	?>

	<?php foreach( $shs as $sh ) : ?>
		<li>
			<?php 
			$titles = array();
			if( $location_count > 1 )
				$titles[] = 'location';
			$titles[] = 'time';
			if( $range == 'week' )
				$wide_view = TRUE;
			require( dirname(__FILE__) . '/_shift_dropdown.php' );
			?>
		</li>
	<?php endforeach; ?>

	<li class="divider"></li>
<?php endforeach; ?>
</ul>