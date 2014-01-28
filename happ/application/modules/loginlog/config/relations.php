<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['user']['has_many']['loginlog'] = array(
	'class'			=> 'loginlog_model',
	'other_field'	=> 'user',
	);
