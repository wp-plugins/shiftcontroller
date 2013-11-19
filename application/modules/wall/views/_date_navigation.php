<?php
$this->hc_time->setDateDb( $start_date );
$month_matrix = $this->hc_time->getMonthMatrix( $end_date );

$this->hc_time->setDateDb( $start_date );
$start_view = $this->hc_time->formatDate();
$this->hc_time->modify( '-1 day' );
$prev_date = $this->hc_time->formatDate_Db();

$this->hc_time->setDateDb( $end_date );
$end_view = $this->hc_time->formatDate();
$this->hc_time->modify( '+1 day' );
$next_date = $this->hc_time->formatDate_Db();
?>

<div class="pagination">
	<ul>
	<li><a href="<?php echo ci_site_url(array($this->conf['path'], 'index', $prev_date)); ?>">&lt;&lt;</a></li>
	<li><a href="#"><strong><?php echo $start_view; ?> - <?php echo $end_view; ?></strong></a></li>
	<li><a href="<?php echo ci_site_url(array($this->conf['path'], 'index', $next_date)); ?>">&gt;&gt;</a></li>
	</ul>
</div>
