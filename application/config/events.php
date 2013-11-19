<?php
$config['timeoff.after_save'][] = array(
	'file'		=> 'email_notifications/timeoffs.php',
	'class'		=> 'Timeoffs_notify',
	'method'	=> 'save'
	);
/*
$config['timeoff.after_delete'][] = array(
	'file'		=> 'email_notifications/timeoffs.php',
	'class'		=> 'Timeoffs_notify',
	'method'	=> 'delete'
	);
*/
$config['shift.after_save'][] = array(
	'file'		=> 'email_notifications/shifts.php',
	'class'		=> 'Shifts_notify',
	'method'	=> 'save'
	);
