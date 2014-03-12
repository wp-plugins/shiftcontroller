<div class="page-header">
<h2>
<?php echo $staff->title(); ?>
<br>
<small><?php echo lang('stats'); ?></small>
</h2>
</div>

<?php if( $dates ) : ?>
	<?php
	$tabs = array(
		'week'	=> '' .  lang('time_week'),
		'month'	=> '' .  lang('time_month'),
		);
	?>

	<ul class="nav nav-tabs">
	<?php foreach( $tabs as $k => $l ) : ?>
		<li<?php if( $k == $display ){echo ' class="active"';}; ?>>
			<a href="<?php echo ci_site_url(array($this->conf['path'], 'staff', $staff->id, $k)); ?>">
				<?php echo $l; ?>
			</a>
		</li>
	<?php endforeach; ?>
	</ul>

	<?php
	krsort( $dates );
	?>

	<table class="table table-condensed table-striped">
	<?php foreach( $dates as $dk => $ds ) : ?>
	<?php	list($start, $end) = explode('-', $dk); ?>
	<tr>
		<td>
		<?php
		$this->hc_time->setDateDb( $start );
		switch( $display )
		{
			case 'month':
				echo $this->hc_time->getMonthName();
				echo ' ';
				echo $this->hc_time->getYear();
				break;
			case 'week':
				echo $this->hc_time->formatDate();
				echo ' - ';
				$this->hc_time->setDateDb( $end );
				echo $this->hc_time->formatDate();
				break;
		}
		?>
		</td>
		<td>
		<?php if( $ds['shift_count'] > 0 ): ?>
			[<?php echo $ds['shift_count']; ?>]
			<i class="fa fa-clock-o"></i> <?php echo $this->hc_time->formatPeriodShort($ds['shift_duration'], 'hour'); ?> 
			
		<?php else : ?>
			-
		<?php endif; ?>
		</td>
	</tr>

	<?php endforeach; ?>

	</table>

<?php else : ?>
	<?php echo lang('common_none'); ?>
<?php endif; ?>
