<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Config extends CI_Config
{
	public function get_module_init( $module )
	{
		$modules = $this->item('modules');
		$return = array();

		$modules = $this->item('modules');
		if( ! is_array($modules) )
		{
			return $return;
		}

		if( isset($modules[$module]) && is_array($modules[$module]) )
		{
			$return = $modules[$module];
		}
		return $return;
	}

	public function get_modules()
	{
		$return = array();

		$modules = $this->item('modules');
		if( ! is_array($modules) )
		{
			return $return;
		}

		reset( $modules );
		foreach( $modules as $name => $value )
		{
			if( ! is_string($name) )
			{
				$name = $value;
			}
			$return[] = $name;
		}
		return $return;
	}
}