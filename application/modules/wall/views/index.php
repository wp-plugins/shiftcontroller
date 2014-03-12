<div class="page-header">
	<ul class="list-inline">
		<li>
			<?php require( dirname(__FILE__) . '/_range.php' ); ?>
		</li>
		<li>
			<?php require( dirname(__FILE__) . '/_date_navigation.php' ); ?>
		</li>
	</ul>
</div>

<?php
$container_class = 'hc_cal';
switch( $range )
{
	case 'month':
		$per_row = 7;
		$col_class = 'col-sm-1';
		break;
	case 'week':
		$per_row = 4;
		$col_class = 'col-sm-3';

/*
		$per_row = 2;
		$col_class = 'col-sm-6';
*/
		break;
}
?>

<?php foreach( $month_matrix as $week ) : ?>
	<div class="<?php echo $container_class; ?>">
	<div class="row">
	<?php $in_row_count = 0; ?>

	<?php foreach( $week as $date ) : ?>
		<?php $in_row_count++; ?>

		<?php if( $in_row_count > $per_row ) : ?>
			</div>
			<div class="row">
			<?php $in_row_count = 1; ?>
		<?php endif; ?>

		<div class="<?php echo $col_class; ?>">
			<?php if( ($date >= $start_date) && ($date <= $end_date) ) : ?>
				<div class="thumbnail">
					<div class="hc-target" data-src="<?php echo ci_site_url( 
						array(
							'wall/day',
							$date,
							'range',	$range
							)
							); ?>">
						<?php
						echo Modules::run(
							'wall/day',
							$date,
							'range',	$range
							);
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	</div>
	</div>
<?php endforeach; ?>