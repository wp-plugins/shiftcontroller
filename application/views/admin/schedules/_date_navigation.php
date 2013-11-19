<?php
$this->hc_time->setDateDb( $start_date );
$month_matrix = $this->hc_time->getMonthMatrix( $end_date );

$this->hc_time->setDateDb( $start_date );
$start_view = $this->hc_time->formatDate();
$month_view = $this->hc_time->getMonthName() . ' ' . $this->hc_time->getYear();
$this->hc_time->modify( '-1 day' );
$prev_date = $this->hc_time->formatDate_Db();

$this->hc_time->setDateDb( $end_date );
$end_view = $this->hc_time->formatDate();
$this->hc_time->modify( '+1 day' );
$next_date = $this->hc_time->formatDate_Db();

$nav_title = '';
switch( $range )
{
	case 'week':
		$nav_title = $start_view . ' - ' . $end_view;
		break;
	case 'month':
		$nav_title = $month_view;
		break;
}
?>

<div class="pagination">
	<ul>
	<li><a href="<?php echo ci_site_url(array($this->conf['path'], 'index', $display, $prev_date)); ?>">&lt;&lt;</a></li>
	<li><a href="#"><strong><?php echo $nav_title; ?></strong></a></li>
	<li><a href="<?php echo ci_site_url(array($this->conf['path'], 'index', $display, $next_date)); ?>">&gt;&gt;</a></li>
	</ul>
</div>
