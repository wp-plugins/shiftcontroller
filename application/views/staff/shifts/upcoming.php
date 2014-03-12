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
			<?php if( count($date_views) > 1 ) : ?>
				<li>&nbsp;</li>
			<?php endif; ?>
			<li><strong><?php echo $date_view; ?></strong></li>
			<li class="divider"></li>
		<?php endif; ?>

		<li class="nav-header">
			<?php echo $this->hc_time->formatTimeOfDay($e->start); ?> - <?php echo $this->hc_time->formatTimeOfDay($e->end); ?>
		</li>

		<?php if( $location_count > 1 ) : ?>
			<li>
				<?php echo $e->location->get()->title(TRUE); ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
<?php else : ?>
	<p>
	<?php echo lang('common_none'); ?>
<?php endif; ?>