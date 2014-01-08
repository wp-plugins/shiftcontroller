<?php
$tabs = array();
$tabs['all']		= '<i class="icon-sitemap"></i> ' .  lang('common_overall');

if( $location_count > 1 )
{
	$tabs['location']	= '<i class="icon-home"></i> ' .  lang('schedule_by_location');
}
$tabs['staff']		= '<i class="icon-user"></i> ' .  lang('schedule_by_staff');
$tabs['browse']		= '<i class="icon-list"></i> ' .  lang('common_list');
?>
<ul class="nav nav-tabs">
<?php foreach( $tabs as $k => $l ) : ?>
	<li<?php if( $k == $display ){echo ' class="active"';}; ?>>
		<a href="<?php echo ci_site_url(array($this->conf['path'], 'index', $k, $start_date)); ?>">
			<?php echo $l; ?>
		</a>
	</li>
<?php endforeach; ?>
</ul>