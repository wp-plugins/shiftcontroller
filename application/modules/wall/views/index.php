<?php if( ! $dates ) : ?>
	<p>
	<?php echo lang('shifts'); ?>: <?php echo lang('common_none'); ?>
	</p>
	<?php return; ?>
<?php endif; ?>

<?php
$count_by_location = array();

if( ! (($location_id === 0) OR ($location_id === '0')) )
{
	$temp_count_by_location = array();
	foreach( $shifts as $sh )
	{
		if( ! isset($temp_count_by_location[$sh->location_id]) )
		{
			$temp_count_by_location[$sh->location_id] = 0;
		}
		$temp_count_by_location[ $sh->location_id ]++;
	}

	foreach( $locations as $lid => $loc )
	{
		if( isset($temp_count_by_location[$lid]) )
			$count_by_location[ $lid ] = $temp_count_by_location[ $lid ];
	}
}
?>

<?php if( count($count_by_location) > 1 ) : ?>
	<ul class="list-inline list-separated">
	<?php foreach( $count_by_location as $lid => $count ) : ?>
		<li>
			<a href="<?php echo ci_site_url( array($this->conf['path'], 'index', 'location', $lid) ); ?>" class="btn btn-default">
				<i class="fa fa-home"></i> <?php echo $locations[$lid]->name; ?> [<?php echo $count; ?>]
				<?php if( $locations[$lid]->description ) : ?>
					<br>
					<span class="text-muted"><?php echo $locations[$lid]->description; ?></span>
				<?php endif; ?>
			</a>
		</li>
	<?php endforeach; ?>
	</ul>
	<?php return; ?>
<?php endif; ?>

<?php foreach( $dates as $date ) : ?>
	<?php
	$this_day_slot = array(
		'wall/day',
		$date,
//		'location',
//		$location_id
		);
	?>
	<div class="thumbnail">
		<div class="_hc-target" data-src="<?php echo ci_site_url($this_day_slot); ?>">
			<?php echo call_user_func_array( 'Modules::run', $this_day_slot ); ?>
		</div>
	</div>
<?php endforeach; ?>