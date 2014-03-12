<?php
$config['timeoff.after_save'][] = array(
	'file'		=> 'email_notifications/timeoffs.php',
	'class'		=> 'Timeoffs_notify',
	'method'	=> 'save'
	);
$config['shift.after_save'][] = array(
	'file'		=> 'email_notifications/shifts.php',
	'class'		=> 'Shifts_notify',
	'method'	=> 'save'
	);
$config['user.after_login'][] = array(
	'file'		=> 'loginlog/models/loginlog_model.php',
	'class'		=> 'Loginlog_model',
	'method'	=> 'log'
	);
$config['shift.after_save'][] = array(
	'file'		=> 'logaudit/models/logaudit_model.php',
	'class'		=> 'Logaudit_model',
	'method'	=> 'log',
	'attr'		=> array('user_id', 'location_id', 'start', 'end', 'date', 'status', 'id', 'has_trade'),
	);
