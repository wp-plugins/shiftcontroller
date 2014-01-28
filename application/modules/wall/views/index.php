<p>
<div class="row-fluid">
	<?php require( dirname(__FILE__) . '/_date_navigation.php' ); ?>
</div>

<p>
<?php foreach( $month_matrix as $week ) : ?>
	<div class="hc_cal">
	<div class="row-fluid">
	<?php foreach( $week as $date ) : ?>
		<div class="span1">
			<?php if( ($date >= $start_date) && ($date <= $end_date) ) : ?>
				<div class="thumbnail">
					<div class="hc-target" data-src="<?php echo ci_site_url( array('wall/schedules/day', $date) ); ?>">
						<?php echo Modules::run('wall/schedules/day', $date); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	</div>
	</div>
<?php endforeach; ?>