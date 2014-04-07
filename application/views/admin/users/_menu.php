<?php
$menu = array();
$menu['index'] = array(
	array($this->conf['path']),
	'<i class="fa fa-list"></i>' . ' ' . lang('common_view')
	);

$CI =& ci_get_instance();
$ri = $CI->remote_integration();
if( ! $ri )
{
	$menu['add'] = array(
		array($this->conf['path'], 'add'),
		'<i class="fa fa-plus-square-o"></i>' . ' ' . lang('common_add')
		);
}
else
{
	$menu['add'] = array(
		array($ri, $this->conf['path'], 'add'),
		'<i class="fa fa-plus-square-o"></i>' . ' ' . lang('common_add') . ' [' . ucfirst($ri) . ']'
		);

	$menu['sync'] = array(
		array($ri, $this->conf['path'], 'sync'),
//		'<i class="fa fa-download"></i>' . ' ' . lang('common_sync_from') . ' [' . ucfirst($ri) . ']'
		'<i class="fa fa-download"></i>' . ' ' . lang('common_sync_from')
		);
}
?>
<?php require( NTS_SYSTEM_APPPATH . 'views/_boilerplate/_print_menu.php' ); ?>