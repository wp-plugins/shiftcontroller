<?php
$menu = array(
	'_header1'	=> $object->full_name(),
	'edit'		=> array(
		array('admin/users', 'edit', $object->id),
		'<i class="fa fa-edit"></i>' . ' ' . lang('common_edit')
		),
	'stats'		=> array(
		array('admin/stats/staff', $object->id),
		'<i class="fa fa-bar-chart-o"></i>' . ' ' . lang('stats')
		),
	);

$CI =& ci_get_instance();
$ri = $CI->remote_integration();
if( ! $ri )
{
	$menu['password'] = array(
		array('admin/users', 'password', $object->id),
		'<i class="fa fa-lock"></i>' . ' ' . lang('common_change_password')
		);
}
else
{
	$menu[$ri . '_edit'] = array(
		array($ri, 'admin/users', 'edit', $object->id),
		'<i class="fa fa-edit"></i>' . ' ' . lang('common_edit') . ' [' . ucfirst($ri) . ']'
		);
	
}

if( $this->hc_modules->exists('loginlog') )
{
	$menu['loginlog'] = array(
		array('loginlog/admin', 'index', $object->id),
		'<i class="fa fa-list"></i>' . ' ' . 'Login Log'
		);
}

$menu['delete'] = array(
	array('admin/users', 'delete', $object->id),
	'<i class="fa fa-times text-danger"></i>' . ' ' . lang('common_delete')
	);

if( $this->auth->check() == $object->id )
{
	unset( $menu['delete'] );
}

$menu['_divider2']	= '_divider';
$menu['_header2']	= lang('users');
$menu['index'] = array(
	array('admin/users'),
	'<i class="fa fa-list"></i>' . ' ' . lang('common_view')
	);
//$menu['add']		= ci_anchor( array('admin/users',	'add'), '<i class="fa fa-plus-square-o"></i>' . ' ' . lang('common_add') );
?>

<?php require( NTS_SYSTEM_APPPATH . 'views/_boilerplate/_print_menu.php' ); ?>