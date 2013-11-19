<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Hc_modules
{
	var $modules = array();

	function __construct()
	{
		$CI =& ci_get_instance(); 
		$this->modules = $CI->config->item('modules');
	}

	function exists( $path )
	{
		$return = in_array($path, $this->modules) && Modules::exists($path);
		return $return;
	}
}
