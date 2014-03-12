<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logaudit_admin_controller extends Backend_controller_crud
{
	function __construct()
	{
		$this->conf = array(
			'model'		=> 'Logaudit_model',
			'path'		=> 'logaudit/admin',
			'entity'	=> 'logaudit',
			);
		parent::__construct( USER_MODEL::LEVEL_MANAGER );
	}

	function index( $object )
	{
	/* load */
		$this->{$this->model}
			->where( 'object_class', $object->my_class() )
			->where( 'object_id', $object->id )
			;

		$entries = $this->{$this->model}->get()->all;

		$new_ones = array();
		/* fill in new values */
		for( $ii = 0; $ii < count($entries); $ii++ )
		{
			if( array_key_exists($entries[$ii]->property_name, $new_ones) )
				$entries[$ii]->new_value = $new_ones[ $entries[$ii]->property_name ];
			else
				$entries[$ii]->new_value = $object->{$entries[$ii]->property_name};
			$new_ones[ $entries[$ii]->property_name ] = $entries[$ii]->old_value;
		}

		$this->data['object'] = $object;
		$this->data['entries'] = $entries;

		$this->set_include( 'index' );
		$this->load->view( $this->template, $this->data);
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */