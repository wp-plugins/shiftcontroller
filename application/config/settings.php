<?php
$config['date_format'] = array(
	'default' 	=> 'j M Y',
	'label'		=> lang('conf_date_format'),
	'type'		=> 'dropdown',
	'options'	=> array(
		'd/m/Y'	=> date('d/m/Y'),
		'd-m-Y'	=> date('d-m-Y'),
		'n/j/Y'	=> date('n/j/Y'),
		'Y/m/d'	=> date('Y/m/d'),
		'd.m.Y'	=> date('d.m.Y'),
		'j M Y'	=> date('j M Y')
		),
	);

$config['time_format'] = array(
	'default' 	=> 'g:ia',
	'label'		=> lang('conf_time_format'),
	'type'		=> 'dropdown',
	'options'	=> array(
		'g:ia'	=> date('g:ia'),
		'g:i A'	=> date('g:i A'),
		'H:i'	=> date('H:i'),
		),
	);

$config['week_starts'] = array(
	'default' 	=> 0,
	'label'		=> lang('conf_week_starts'),
	'type'		=> 'dropdown',
	'options'	=> array(
		1	=> lang('time_monday'),
		0	=> lang('time_sunday'),
		),
	);

$config['email_from'] = array(
	'default' 	=> '',
	'label'		=> lang('conf_email_from'),
	'type'		=> 'text',
	'rules'		=> 'trim|required|valid_email'
	);

$config['email_from_name'] = array(
	'default' 	=> '',
	'label'		=> lang('conf_email_from_name'),
	'type'		=> 'text',
	'rules'		=> 'trim|required'
	);

$config['csv_separator'] = array(
	'default' 	=> ',',
	'label'		=> lang('conf_csv_separator'),
	'type'		=> 'dropdown',
	'options'	=> array(
		','	=> ',',
		';'	=> ';',
		),
	);
