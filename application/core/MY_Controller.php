<?php
class MY_Controller extends MY_BaseController 
{
	function __construct()
	{
		parent::__construct();
		$this->load->library(
			'shiftexec_access_manager', 
			array(
				'auth'	=> $this->auth,
				),
			'access_manager'
			);
	}
}