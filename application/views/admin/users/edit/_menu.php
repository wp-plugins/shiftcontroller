<?php
$menu = array(
	'_header1'	=> $object->full_name(),
	'edit'		=> ci_anchor( array('admin/users',	'edit', $object->id), '<i class="icon-edit"></i>' . ' ' . lang('common_edit') ),
	'stats'		=> ci_anchor( array('admin/stats/staff', $object->id), '<i class="icon-bar-chart"></i>' . ' ' . lang('stats') ),
	);

$CI =& ci_get_instance();
$ri = $CI->remote_integration();
if( ! $ri )
{
	$menu['password']	= ci_anchor( array('admin/users',	'password', $object->id), '<i class="icon-lock"></i>' . ' ' . lang('common_change_password') );
}
else
{
	$menu[$ri . '_edit']	= ci_anchor( array($ri, 'admin/users', 'edit', $object->id), '<i class="icon-edit"></i>' . ' ' . lang('common_edit') . ' [' . ucfirst($ri) . ']', '' );
}

if( $this->hc_modules->exists('loginlog') )
{
	$menu['loginlog'] = ci_anchor( array('loginlog/admin', 'index', $object->id), '<i class="icon-list"></i>' . ' ' . 'Login Log' );
}

$menu['delete']		= ci_anchor( array('admin/users',	'delete', $object->id), '<i class="icon-remove"></i>' . ' ' . lang('common_delete') );
if( $this->auth->check() == $object->id )
{
	unset( $menu['delete'] );
}

$menu['_header2']	= lang('users');
$menu['index']		= ci_anchor( array('admin/users'), '<i class="icon-list"></i>' . ' ' . lang('common_view') );
//$menu['add']		= ci_anchor( array('admin/users',	'add'), '<i class="icon-plus-sign"></i>' . ' ' . lang('common_add') );
?>

<?php require( NTS_SYSTEM_APPPATH . 'views/_boilerplate/_print_menu.php' ); ?>