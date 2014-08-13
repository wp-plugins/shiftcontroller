<?php
$CI =& ci_get_instance();
$remote_integration = $CI->remote_integration();
$config = array();

if( $remote_integration )
{
	$config['wall_schedule_display'] = array(
		'default' 	=> USER_MODEL::LEVEL_STAFF,
		'label'		=> lang('everyone_schedule_display_staff'),
		'type'		=> 'dropdown',
		'options'	=> array(
			USER_MODEL::LEVEL_STAFF		=> lang('common_yes'),
			USER_MODEL::LEVEL_ADMIN		=> lang('common_no'),
			),
		);
}
else
{
	$config['wall_schedule_display'] = array(
		'default' 	=> USER_MODEL::LEVEL_STAFF,
		'label'		=> lang('everyone_schedule_display'),
		'type'		=> 'dropdown',
		'options'	=> array(
			0							=> lang('common_everyone'),
			USER_MODEL::LEVEL_STAFF		=> lang('user_level_staff'),
			USER_MODEL::LEVEL_ADMIN		=> lang('common_nobody'),
			),
		);
}
