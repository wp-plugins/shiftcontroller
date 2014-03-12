<?php
if( $range == 'week' )
	$wide_view = TRUE;

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

<ul class="nav nav-stacked">
<li>
	<h4>
		<?php echo $this->hc_time->formatWeekdayShort(); ?> 
		<small><?php echo $this->hc_time->formatDate(); ?></small>
	</h4>
</li>

<li class="divider"></li>

<?php foreach( $grouped_shifts as $key => $sha ) : ?>
	<?php
	list( $this_start, $this_end ) = explode( '_', $key );
	?>

	<?php foreach( $sha as $lid => $shs ) : ?>

		<?php foreach( $shs as $sh ) : ?>
			<?php
			$delete_url = ci_site_url( 
				array(
					'admin/shifts/delete/' . $sh->id,
					)
				);
			?>
			<li>
				<?php 
				$titles = array();
				if( $location_count > 1 )
					$titles[] = 'location';
				$titles[] = 'staff';
				$titles[] = 'time';
				require( dirname(__FILE__) . '/_shift_dropdown.php' );
				?>
			</li>
		<?php endforeach; ?>

	<?php endforeach; ?>

	<li class="divider"></li>
<?php endforeach; ?>
</ul>