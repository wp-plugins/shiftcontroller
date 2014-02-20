<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Session extends CI_Session {
	/* here we overwrite flash data operations */

	var $my_prefix = 'nts_';

	public function __construct($params = array())
	{
		if( session_id() == '' )
		{
			@session_start();
		}
		parent::__construct( $params );
	}

	function all_userdata()
	{
		$return = array();
		/* get flash data we store in _SESSION */
		foreach( $_SESSION as $key => $v )
		{
			if( ! (substr($key, 0, strlen($this->my_prefix)) == $this->my_prefix) )
				continue;
			$my_key = substr($key, strlen($this->my_prefix) );
			$return[ $my_key ] = $v;
		}

		$parent_return = parent::all_userdata();
		$return = array_merge( $return, $parent_return );
		return $return;
	}

	function userdata($item)
	{
		$my_key = $this->my_prefix . $item;
		if( isset($_SESSION[$my_key]) )
			return $_SESSION[$my_key];
		return parent::userdata($item);
	}

	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}

		$parent_newdata = array();
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				if(  substr($key, 0, strlen($this->flashdata_key)) == $this->flashdata_key )
				{
					$my_key = $this->my_prefix . $key;
					unset($_SESSION[$my_key]);
				}
				else
				{
					$parent_newdata[ $key ] = $val;
				}
			}
		}

		if( $parent_newdata )
		{
			parent::unset_userdata( $parent_newdata );
		}
	}

	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		$parent_newdata = array();
		if (count($newdata) > 0)
		{
			$parent_newdata = array();
			foreach ($newdata as $key => $val)
			{
				if(  substr($key, 0, strlen($this->flashdata_key)) == $this->flashdata_key )
				{
					$my_key = $this->my_prefix . $key;
					$_SESSION[$my_key] = $val;
				}
				else
				{
					$parent_newdata[ $key ] = $val;
				}
			}
		}

		if( $parent_newdata )
		{
			parent::set_userdata( $parent_newdata );
		}
	}
}
