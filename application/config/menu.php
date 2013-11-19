<?php defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& ci_get_instance();

$config[User_model::LEVEL_ADMIN]['m1'] = array( 
	lang('schedules'),
	'admin/schedules',
	10
	);
$config[User_model::LEVEL_ADMIN]['m2'] = array(
	lang('users'),
	'admin/users',
	20
	);
$config[User_model::LEVEL_ADMIN]['m3'] = array(
	lang('timeoffs'),
	'admin/timeoffs',
	30
	);

$config[User_model::LEVEL_ADMIN]['conf'] = array( 
	lang('menu_conf'),
	'',
	100,
	'locations'	=> array( 
		lang('locations'),
		'admin/locations',
		10
		),
	'templates'	=> array( 
		lang('shift_templates'),
		'admin/shift-templates',
		20
		),
	'settings'	=> array( 
		lang('menu_conf_settings'),
		'conf/admin',
		100
		),
	);

$config[User_model::LEVEL_STAFF][] = array( 
	lang('my_schedule'),
	'staff/shifts',
	10
	);
$config[User_model::LEVEL_STAFF][] = array( 
	lang('my_timeoffs'),
	'staff/timeoffs',
	20
	);

$promo_url = $CI->config->item( 'nts_promo_url' );
if( $promo_url )
{
	$promo_title = $CI->config->item( 'nts_promo_title' );
	$config[User_model::LEVEL_ADMIN]['promo'] = array( 
		$promo_title,
		$promo_url,
		200
		);
}
/* End of file menu.php */
/* Location: ./application/config/menu.php */