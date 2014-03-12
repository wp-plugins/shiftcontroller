<?php
class Logaudit_model extends MY_model
{
	var $table = 'logaudit';
	var $default_order_by = array('action_time' => 'DESC');

	var $has_one = array(
		'user' => array(
			'class'			=> 'user_model',
			'other_field'	=> 'logaudit',
			)
		);

	public function log( $object, $keep_log = array() )
	{
		if( ! $keep_log )
			return;

		$log_changes = array();
		$changes = $object->get_changes();
		reset( $changes );
		foreach( $changes as $property_name => $old_value )
		{
			if( in_array($property_name, $keep_log) )
			{
				$log_changes[ $property_name ] = $old_value;
			}
		}

		if( ! $log_changes )
			return;

		$CI =& ci_get_instance();
		$user_id = (isset($CI->auth) && $CI->auth) ? $CI->auth->check() : -1;

		$defaults = array(
			'user_id'		=> $user_id,
			'action_time'	=> time(),
			'object_class'	=> $object->my_class(),
			'object_id'		=> $object->id,
			);
/*
		if( $log )
		{
			foreach( $log as $k => $v )
				$defaults[$k] = $v;
		}
*/

		/* JUST CREATED */
		if( array_key_exists('id', $log_changes) )
		{
			$this->clear();
			$this->from_array( $defaults );
			$this->property_name = 'id';
			$this->old_value = NULL;
			$this->save();
		}
		else
		{
			foreach( $log_changes as $property_name => $old_value )
			{
				$this->clear();
				$this->from_array( $defaults );
				$this->property_name = $property_name;
				$this->old_value = $old_value;
				$this->save();
			}
		}
		return TRUE;
	}
}