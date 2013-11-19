<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require NTS_SYSTEM_APPPATH."third_party/MX/Router.php";

//class MY_Router extends CI_Router {
class MY_Router extends MX_Router {
	function _set_request ($seg = array())
	{
		return parent::_set_request(str_replace('-', '_', $seg));
	}

/* returns the controller's file name */
	function controller_name()
	{
		$suffix = $this->config->item('controller_suffix');
		if ( strstr($this->class, $suffix) )
		{  
			$return = str_replace($suffix, '', $this->class);  
		}
		else
		{
			$return = $this->class;
		}

		$module_prefix = strlen($this->module) ? $this->module . '_' : '';
		if( strlen($module_prefix) )
		{
			$return = substr( $return, strlen($module_prefix) );
		}
		return $return;
	}
}
