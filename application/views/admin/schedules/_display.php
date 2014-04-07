<?php
$base_link_params = array(
	$this->conf['path'], 'index',
	'start',	$start_date,
	'range',	$range,
	'filter',	$filter,
	);
if( $id )
{
	$base_link_params[] = 'id';
	$base_link_params[] = $id;
}
?>
<li<?php if( 'calendar' === $display ){echo ' class="active"';}; ?>>
	<a class="dropdown-toggle" data-toggle="dropdown" href="#">
		<i class="fa fa-calendar fa-fw"></i> 
		<?php if( 'calendar' != $display ) : ?>
			<?php echo lang('time_calendar'); ?>
		<?php else :  ?>
			<?php if( 'month' == $range ) : ?>
				<?php echo lang('time_month'); ?>
			<?php elseif( 'week' == $range ) : ?>
				<?php echo lang('time_week'); ?>
			<?php endif; ?>
		<?php endif; ?>
		<span class="caret"></span>
	</a>

	<?php
	$dmenu = array();
	if( ('calendar' != $display) OR ('month' == $range) )
	{
		$link_params = array(
			'range',	'week',
			);
		$link_params = array_merge( $base_link_params, $link_params );
		$dmenu[] = array(
			'href'	=> ci_site_url( $link_params ),
			'title'	=> '<i class="fa fa-calendar fa-fw"></i> ' . lang('time_week')
			);
	}

	if( ('calendar' != $display) OR ('week' == $range) )
	{
		$link_params = array(
			'range',	'month',
			);
		$link_params = array_merge( $base_link_params, $link_params );
		$dmenu[] = array(
			'href'	=> ci_site_url( $link_params ),
			'title'	=> '<i class="fa fa-calendar fa-fw"></i> ' . lang('time_month')
			);
	}
	?>
	<?php echo Hc_html::dropdown_menu($dmenu); ?> 
</li>

<li<?php if( 'browse' == $display ){echo ' class="active"';}; ?>>
	<?php
	$link_params = array(
		'display',	'browse',
		);
	$link_params = array_merge( $base_link_params, $link_params );
	$link = ci_site_url( $link_params );
	?>
	<a href="<?php echo $link; ?>">
		<i class="fa fa-list"></i> <?php echo lang('common_list'); ?>
	</a>
</li>

<li<?php if( 'stats' == $display ){echo ' class="active"';}; ?>>
	<?php
	$link_params = array(
		'display',	'stats',
		);
	$link_params = array_merge( $base_link_params, $link_params );
	$link = ci_site_url( $link_params );
	?>
	<a href="<?php echo $link; ?>">
		<i class="fa fa-bar-chart-o"></i> <?php echo lang('stats'); ?>
	</a>
</li>