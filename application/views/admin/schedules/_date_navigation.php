<?php
$base_link_params = array(
	$this->conf['path'], 'index',
	'display',	$display,
	'range',	$range,
	'filter',	$filter,
	'id',		$id,
	);

switch( $display )
{
	case 'staff':
		$base_link_params[] = 'id';
		$base_link_params[] = $current_staff->id;
		break;
	case 'location':
		$base_link_params[] = 'id';
		$base_link_params[] = $current_location->id;
		break;
}

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
		$nav_title = $this->hc_time->formatDateRange( $start_date, $end_date );
		break;
	case 'month':
		$nav_title = $month_view;
		break;
}
?>

<ul class="pagination">
	<li>
		<?php
		$link_params = array(
			'start', $prev_date
			);
		$link_params = array_merge( $base_link_params, $link_params );
		?>
		<a href="<?php echo ci_site_url($link_params); ?>">
			&lt;&lt;
		</a>
	</li>
	<li class="active">
		<?php
		$link_params = array(
			'start', $start_date
			);
		$link_params = array_merge( $base_link_params, $link_params );
		?>
		<a href="<?php echo ci_site_url($link_params); ?>">
			<?php echo $nav_title; ?>
		</a>
	</li>
	<li>
		<?php
		$link_params = array(
			'start', $next_date
			);
		$link_params = array_merge( $base_link_params, $link_params );
		?>
		<a href="<?php echo ci_site_url($link_params); ?>">
			&gt;&gt;
		</a>
	</li>
</ul>