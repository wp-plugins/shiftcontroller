<?php
$base_link_params = array(
	$this->conf['path'], 'index',
	'start',	$start_date,
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
?>
<ul class="pagination">
<?php
$range_options = array(
	'month'	=> lang('time_month'),
	'week'	=> lang('time_week'),
	);
?>
<?php foreach( $range_options as $r => $r_label) : ?>
	<?php
	if( $r == $range )
		continue;

	$link_params = array(
		'range', $r
		);
	$link_params = array_merge( $base_link_params, $link_params );
	?>
	<li>
		<a href="<?php echo ci_site_url($link_params); ?>">
			<i class="fa fa-calendar"></i> <?php echo $r_label; ?>
		</a>
	</li>
<?php endforeach; ?>
</ul>