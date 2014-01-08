<p>
<?php require( dirname(__FILE__) . '/_date_navigation.php' ); ?>
<p>
<?php require( dirname(__FILE__) . '/_tabs.php' ); ?>

<?php foreach( $locations as $location ) : ?>

	<div class="page-header">
		<h4><i class="icon-home"></i> <?php echo $location->title(); ?></h4>
	</div>

	<div class="hc-page-status" data-src="<?php echo ci_site_url( array('admin/schedules/status', 'start', $start_date, 'end', $end_date, 'location', $location->id) ); ?>">
		<?php echo Modules::run('admin/schedules/status', 'start', $start_date, 'end', $end_date, 'location', $location->id); ?>
	</div>

	<?php foreach( $month_matrix as $week ) : ?>
		<div class="hc_cal" style="margin-bottom: 1em;">
		<div class="row-fluid">
		<?php foreach( $week as $date ) : ?>
			<div class="span1">
				<?php if( ($date >= $start_date) && ($date <= $end_date) ) : ?>
					<div class="thumbnail">
						<div class="hc-target" data-src="<?php echo ci_site_url( array('admin/schedules/day_location', $date, $location->id) ); ?>">
							<?php echo Modules::run('admin/schedules/day_location', $date, $location->id); ?>
						</div>

						<?php
						echo ci_anchor( 
							array(
								'admin/shifts/add',
								'date', $date,
								'location', $location->id,
								),
							'<i class="icon-plus"></i> ' . lang('shift_add'),
							'class="btn btn-mini btn-block" data-return-action="refresh"'
							);
						?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		</div>
		</div>
	<?php endforeach; ?>

<?php endforeach; ?>
