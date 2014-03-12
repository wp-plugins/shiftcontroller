<?php
$base_link_params = array(
	$this->conf['path'], 'index',
	'start',	$start_date,
	'range',	$range
	);

$tabs = array();
$tabs['all'] = '<i class="fa fa-sitemap"></i> ' .  lang('common_overall');
?>

<?php foreach( $tabs as $k => $l ) : ?>
	<li<?php if( $k == $display ){echo ' class="active"';}; ?>>
		<?php
		$link_params = array(
			'display',	$k,
			);
		$link_params = array_merge( $base_link_params, $link_params );
		$link = ci_site_url( $link_params );
		?>
		<a href="<?php echo $link; ?>">
			<?php echo $l; ?>
		</a>
	</li>
<?php endforeach; ?>

<?php if( $location_count > 1 ) : ?>
	<li<?php if( 'location' == $display ){echo ' class="active"';}; ?>>
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<i class="fa fa-home fa-fw"></i> 
			<?php if( 'location' == $display ) : ?>
				<?php echo $current_location->title(); ?>
			<?php else : ?>
				- <?php echo lang('location'); ?> - 
			<?php endif; ?>
			<span class="caret"></span>
		</a>

		<?php
		$dmenu = array();
		foreach( $locations as $location )
		{
			if( ('location' == $display) && ($location->id == $current_location->id) )
				continue;

			$link_params = array(
				'display',	'location',
				'id',		$location->id
				);
			$link_params = array_merge( $base_link_params, $link_params );

			$dmenu[] = array(
				'href'	=> ci_site_url( $link_params ),
				'title'	=> '<i class="fa fa-home"></i> ' . $location->title()
				);
		}
		?>
		<?php echo Hc_html::dropdown_menu($dmenu); ?> 
	</li>
<?php endif; ?>

<?php if( $staff_count > 1 ) : ?>
	<li<?php if( 'staff' == $display ){echo ' class="active"';}; ?>>
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<i class="fa fa-user fa-fw"></i> 
			<?php if( 'staff' == $display ) : ?>
				<?php echo $current_staff->title(); ?>
			<?php else : ?>
				- <?php echo lang('user_level_staff'); ?> - 
			<?php endif; ?>
			<span class="caret"></span>
		</a>

		<?php
		$dmenu = array();
		foreach( $staffs as $staff )
		{
			if( ('staff' == $display) && ($staff->id == $current_staff->id) )
				continue;
			if( ! in_array($staff->active, array(USER_MODEL::STATUS_ACTIVE) ) )
				continue;

			$link_params = array(
				'display',	'staff',
				'id',		$staff->id
				);
			$link_params = array_merge( $base_link_params, $link_params );

			$dmenu[] = array(
				'href'	=> ci_site_url( $link_params ),
				'title'	=> '<i class="fa fa-user"></i> ' . $staff->title()
				);
		}
		?>
		<?php echo Hc_html::dropdown_menu($dmenu); ?> 
	</li>
<?php endif; ?>

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