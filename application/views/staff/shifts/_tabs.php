<?php
$tabs = array(
	'my'	=> lang('shift_next'),
	);
$tabs['list'] = lang('stats');

if( $this->hc_modules->exists('shift_trades') )
{
	$tabs['pickup'] = lang('shift_pick_up');
}
?>
<ul class="nav nav-tabs">
<?php foreach( $tabs as $k => $l ) : ?>
	<li<?php if( $k == $display ){echo ' class="active"';}; ?>>
		<a href="<?php echo ci_site_url(array($this->conf['path'], 'index', 'display', $k)); ?>">
			<?php echo $l; ?>
		</a>
	</li>
<?php endforeach; ?>
</ul>