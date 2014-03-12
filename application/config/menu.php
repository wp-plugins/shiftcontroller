<?php defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& ci_get_instance();
$config = array();

$config = array(
	USER_MODEL::LEVEL_ADMIN . '/schedules' =>
		array(
			'title'	=> '<i class="fa fa-calendar"></i> ' . lang('schedules'),
			'link'	=> 'admin/schedules',
			),
	USER_MODEL::LEVEL_ADMIN . '/users' =>
		array(
			'title'	=> '<i class="fa fa-user"></i> ' . lang('users'),
			'link'	=> 'admin/users',
			),
	USER_MODEL::LEVEL_ADMIN . '/timeoffs' => 
		array(
			'title'	=> '<i class="fa fa-coffee"></i> ' . lang('timeoffs'),
			'link'	=> 'admin/timeoffs',
			),
	USER_MODEL::LEVEL_ADMIN . '/conf' => 
		array(
			'title'	=> '<i class="fa fa-cog"></i> ' . lang('menu_conf'),
			'link'	=> '',
			'order'	=> 100,
			),
		USER_MODEL::LEVEL_ADMIN . '/conf/locations'	=> array( 
			'title'	=> '<i class="fa fa-home fa-fw"></i> ' . lang('locations'),
			'link'	=> 'admin/locations',
			),
		USER_MODEL::LEVEL_ADMIN . '/conf/templates'	=> array( 
			'title'	=> '<i class="fa fa-clock-o fa-fw"></i> ' . lang('shift_templates'),
			'link'	=> 'admin/shift-templates',
			),
		USER_MODEL::LEVEL_ADMIN . '/conf/settings'	=> array( 
			'title'	=> '<i class="fa fa-cogs fa-fw"></i> ' . lang('menu_conf_settings'),
			'link'	=> 'conf/admin',
			'order'	=> 100
			),
	);

$config[ USER_MODEL::LEVEL_MANAGER . '/schedules' ] = array(
	'title'	=> '<i class="fa fa-calendar"></i> ' . lang('schedules'),
	'link'	=> 'admin/schedules',
	);
$config[ USER_MODEL::LEVEL_MANAGER . '/timeoffs' ] = array(
	'title'	=> '<i class="fa fa-coffee"></i> ' . lang('timeoffs'),
	'link'	=> 'admin/timeoffs',
	);

$config[ USER_MODEL::LEVEL_STAFF . '/schedules' ] = array(
	'title'	=> '<i class="fa fa-calendar-o"></i> ' . lang('my_schedule'),
	'link'	=> '/staff/shifts',
	);
$config[ USER_MODEL::LEVEL_STAFF . '/timeoffs' ] = array(
	'title'	=> '<i class="fa fa-coffee"></i> ' . lang('my_timeoffs'),
	'link'	=> '/staff/timeoffs',
	);

$promo_url = $CI->config->item( 'nts_promo_url' );
if( $promo_url )
{
	$promo_title = $CI->config->item( 'nts_promo_title' );
	$config[USER_MODEL::LEVEL_ADMIN . '/promo'] = array(
		'title'	=> $promo_title,
		'link'	=> $promo_url,
		'external'	=> TRUE,
		'order'	=> 200
		);
}
/* End of file menu.php */
/* Location: ./application/config/menu.php */