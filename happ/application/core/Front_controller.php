<?php
class Front_controller extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('migration');
		if ( ! $this->migration->current()){
			show_error($this->migration->error_string());
			return false;
			}
		$this->load->library( 'conf/app_conf' );
	}
}