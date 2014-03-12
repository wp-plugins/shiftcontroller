<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['user']['has_many']['logaudit'] = array(
	'class'			=> 'logaudit_model',
	'other_field'	=> 'user',
	);
