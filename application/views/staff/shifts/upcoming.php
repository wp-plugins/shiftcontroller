<?php
$date_views = array();
?>
<?php if( $entries ) : ?>
	<ul class="nav nav-list">
	<?php foreach( $entries as $e ) : ?>
	<?php
			$date_view = '';
			if( ! isset($date_views[$e->date]) )
			{
				$date_views[$e->date] = 1;
				$this->hc_time->setDateDb( $e->date );
				$date_view = $this->hc_time->formatDateFull();
			}
	?>
	<?php if( $date_view ) : ?>
	<?php	if( count($date_views) > 1 ) : ?>
			<li>&nbsp;</li>
	<?php endif; ?>
		<li><strong><?php echo $date_view; ?></strong></li>
		<li class="divider"></li>
	<?php endif; ?>

	<li class="nav-header">
		<?php echo $this->hc_time->formatTimeOfDay($e->start); ?> - <?php echo $this->hc_time->formatTimeOfDay($e->end); ?>
	</li>
	<li>
		<?php echo $e->location->get()->title(TRUE); ?>
	</li>
	<li>
		<?php 
		if(
			$e->trade_id && 
			(! in_array($e->trade_status, array(
				TRADE_MODEL::STATUS_DENIED,
				TRADE_MODEL::STATUS_COMPLETED
				)
			))
			) :
		?>
			<?php 
			switch( $e->trade_status )
			{
				case TRADE_MODEL::STATUS_PENDING :
					$class = 'text-error';
					$msg = lang('trade_status_pending');
					break;

				case TRADE_MODEL::STATUS_APPROVED :
					$class = 'text-success';
					$msg = lang('trade_status_approved');
					break;

				case TRADE_MODEL::STATUS_COMPLETED :
					$class = 'text-success';
					$msg = lang('trade_status_approved');
					break;
			}
			?>
				<span class="<?php echo $class; ?>">
				<i class="icon-exchange"></i> <?php echo lang('shift_has_trade'); ?>: <?php echo $msg; ?>
				</span>
		<?php else : ?>
			<a href="<?php echo ci_site_url( array('staff/trades', 'list_trade', $e->id) ); ?>">
				<i class="icon-exchange text-info"></i> <?php echo lang('shift_list_trade'); ?>
			</a>
		<?php endif; ?>
	</li>

	<?php endforeach; ?>
	</ul>
<?php else : ?>
	<p>
	<?php echo lang('common_none'); ?>
<?php endif; ?>