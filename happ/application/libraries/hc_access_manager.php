<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Hc_Access_Manager
{
	var $auth;
	var $user_id = 0;
	var $user_level = 0;

	function __construct( $params = array() )
	{
		$this->auth = $params['auth'];
		if( $this->auth && $this->auth->user() )
		{
			$this->user_id = $this->auth->user()->id;
			$this->user_level = $this->auth->user()->level;
		}
	}

	function access_levels( $what )
	{
		return array();
	}

	function can_see( $what )
	{
		return TRUE;
	}

	function can_edit( $what )
	{
		return TRUE;
	}


}