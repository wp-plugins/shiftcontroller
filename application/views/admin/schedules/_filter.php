<?php
$base_link_params = array(
	$this->conf['path'], 'index',
	'start',	$start_date,
	'end',		$end_date,
	'range',	$range,
	'display',	$display
	);
?>
<li class="active">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#">
		<?php if( 'location' == $filter ) : ?>
			<i class="fa fa-home fa-fw"></i> <?php echo $current_location->title(); ?>
		<?php elseif( 'staff' == $filter ) : ?>
			<i class="fa fa-user fa-fw"></i> <?php echo $current_staff->title(); ?>
		<?php else : ?>
			<i class="fa fa-sitemap fa-fw"></i> <?php echo lang('common_overall'); ?>
		<?php endif; ?>
		<span class="caret"></span>
	</a>

	<?php
	$dmenu = array();
	if( $filter != 'all' )
	{
		$dmenu[] = array(
			'href'	=> ci_site_url( $base_link_params ),
			'title'	=> '<i class="fa fa-sitemap"></i> ' . lang('common_overall')
			);
	}

	if( $location_count > 1 )
	{
		$dmenu[] = array(
			'title'	=> lang('location')
			);
		foreach( $locations as $location )
		{
			if( ('location' == $filter) && ($location->id == $current_location->id) )
				continue;

			$link_params = array(
				'filter',	'location',
				'id',		$location->id
				);
			$link_params = array_merge( $base_link_params, $link_params );

			$dmenu[] = array(
				'href'	=> ci_site_url( $link_params ),
				'title'	=> '<i class="fa fa-home"></i> ' . $location->title()
				);
		}
	}

	if( $staff_count > 1 )
	{
		$dmenu[] = array(
			'title'	=> lang('user_level_staff')
			);
		foreach( $working_staff as $staff )
		{
			if( ('staff' == $filter) && ($staff->id == $current_staff->id) )
				continue;

			$link_params = array(
				'filter',	'staff',
				'id',		$staff->id
				);
			$link_params = array_merge( $base_link_params, $link_params );

			$dmenu[] = array(
				'href'	=> ci_site_url( $link_params ),
				'title'	=> '<i class="fa fa-user"></i> ' . $staff->title()
				);
		}
	}
	?>
	<?php echo Hc_html::dropdown_menu($dmenu); ?> 
</li>