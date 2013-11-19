<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Locations_controller extends Backend_controller_crud
{
	function __construct()
	{
		$this->conf = array(
			'model'		=> 'Location_model',
			'path'		=> 'admin/locations',
			'entity'	=> 'location',
			);
		parent::__construct();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */