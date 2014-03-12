<?php
$config = array();

$CI =& ci_get_instance();
$wall_schedule_display = $CI->app_conf->get('wall_schedule_display');

if( 
	(
	$CI->auth && 
	$CI->auth->user() &&
	($CI->auth->user()->level >= $wall_schedule_display)
	)
)
{
	$config[ USER_MODEL::LEVEL_STAFF . '/wall' ] = array(
		'title'	=> '<i class="fa fa-calendar"></i> ' . lang('everyone_schedule'),
		'link'	=> 'wall',
		'order'	=> 50
		);
}
?>