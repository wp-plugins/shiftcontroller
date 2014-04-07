<?php 
$status_params = array(
	'admin/schedules/status',
	'start', $start_date,
	'end', $end_date
	);
if( $filter == 'staff' )
{
	$status_params[] = 'staff';
	$status_params[] = $current_staff->id;
}
elseif( $filter == 'location' )
{
	$status_params[] = 'location';
	$status_params[] = $current_location->id;
}
?>
<div class="hc-page-status" data-src="<?php echo ci_site_url($status_params); ?>">
	<?php echo call_user_func_array( 'Modules::run', $status_params ); ?>
</div>