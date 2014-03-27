<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config = array();

/* this file defines model relationships runtime if needed */
$CI =& ci_get_instance();
if( $CI->hc_modules->exists('notes') )
{
	$config['note']['has_one']['timeoff'] = array(
		'class'			=> 'timeoff_model',
		'other_field'	=> 'note',
		);
	$config['note']['has_one']['shift'] = array(
		'class'			=> 'shift_model',
		'other_field'	=> 'note',
		);
	$config['timeoff']['has_many']['note'] = array(
		'class'			=> 'note_model',
		'other_field'	=> 'timeoff',
		);
	$config['shift']['has_many']['note'] = array(
		'class'			=> 'note_model',
		'other_field'	=> 'shift',
		);
	$config['user']['has_many']['note'] = array(
		'class'			=> 'note_model',
		'other_field'	=> 'author',
		);
}