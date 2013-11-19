<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_conf
{
	protected $saved = array();

	public function __construct()
	{
		$this->load->model('conf/app_conf_model');
		$this->init();
		$this->saved = $this->app_conf_model->get_all();
	}

	function init()
	{
		$this->config->load('settings', TRUE);
		foreach ( $this->config->items('settings') as $fn => $f )
		{
			$f['name'] = $fn;
			$this->config->set_item( $fn, $f, 'settings' );
		}
	}

	public function get( $pname )
	{
		if( isset($this->saved[$pname]) )
		{
			$return = $this->saved[$pname];
		}
		else
		{
			$setting = $this->config->item( $pname, 'settings' );
			$return = isset($setting['default']) ? $setting['default'] : NULL;
		}
	return $return;
	}

	public function set( $pname, $pvalue )
	{
		return $this->app_conf_model->save( $pname, $pvalue );
	}

	public function reset( $pname )
	{
		return $this->app_conf_model->delete( $pname );
	}

	function get_loaded_names()
	{
		$return = array_keys( $this->saved );
		return $return;
	}

	/**
	 * __get
	 *
	 * Enables the use of CI super-global without having to define an extra variable.
	 *
	 * I can't remember where I first saw this, so thank you if you are the original author. -Militis
	 *
	 * @access	public
	 * @param	$var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return ci_get_instance()->$var;
	}
}
