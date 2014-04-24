<?php
$show_end_time_for_staff = $this->app_conf->get( 'show_end_time_for_staff' );
$this->hc_time->setDateDb( $object->date );
$time_view = $this->hc_time->formatTimeOfDay($object->start);
if( $show_end_time_for_staff )
{
	$time_view .= ' - ' .  $this->hc_time->formatTimeOfDay($object->end);
}
?>
<h2>
<i class="fa fa-clock-o"></i> <?php echo $this->hc_time->formatDateFull(); ?> [<?php echo $time_view; ?>]
</h2>