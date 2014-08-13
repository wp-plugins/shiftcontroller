<?php require( dirname(__FILE__) . '/_tabs.php' ); ?>

<?php if( ! count($shifts) ) : ?>
	<p>
	<?php echo lang('common_none'); ?>
<?php endif; ?>

<?php if( $display == 'pickup' ) : ?>
	<?php
	$temp_count_by_location = array();
	foreach( $shifts as $sh )
	{
		if( ! isset($temp_count_by_location[$sh->location_id]) )
		{
			$temp_count_by_location[$sh->location_id] = 0;
		}
		$temp_count_by_location[ $sh->location_id ]++;
	}

	$count_by_location = array();
	foreach( $locations as $lid => $loc )
	{
		if( isset($temp_count_by_location[$lid]) )
			$count_by_location[ $lid ] = $temp_count_by_location[ $lid ];
	}
	?>
	<?php if( count($count_by_location) > 1 ) : ?>
		<ul class="list-inline list-separated">
		<?php foreach( $count_by_location as $lid => $count ) : ?>
			<li>
				<a href="<?php echo ci_site_url( array($this->conf['path'], 'index', 'display', 'pickup', 'location', $lid) ); ?>" class="btn btn-default">
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
<?php endif; ?>

<?php
$per_row = 4;
$row_open = FALSE;
$ii = 0;
reset( $shifts );
?>

<?php foreach( $shifts as $sh ) : ?>
	<?php
	$ii++;
	?>

	<?php if( 1 == ($ii % $per_row) ) : ?>
		<div class="row">
		<?php $row_open = TRUE; ?>
	<?php endif; ?>

	<div class="col-sm-3">
		<?php require( dirname(__FILE__) . '/index_child.php' ); ?>
	</div>

	<?php if( ! ($ii % $per_row) ) : ?>
		</div>
		<?php $row_open = FALSE; ?>
	<?php endif; ?>

<?php endforeach; ?>

<?php if( $row_open ) : ?>
	</div>
<?php endif; ?>