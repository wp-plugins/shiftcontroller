<?php
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
