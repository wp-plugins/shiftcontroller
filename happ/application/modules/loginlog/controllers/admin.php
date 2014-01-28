<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Loginlog_admin_controller extends Backend_controller_crud
{
	function __construct()
	{
		$this->conf = array(
			'model'		=> 'Loginlog_model',
			'path'		=> 'loginlog/admin',
			'entity'	=> 'loginlog',
			);
		parent::__construct( USER_MODEL::LEVEL_MANAGER );
	}

	function index( $user_id = 0 )
	{
	/* load */
		$this->{$this->model}
			->include_related( 'user',	'email' )
			->include_related( 'user',	'first_name' )
			->include_related( 'user',	'last_name' )
			;

		if( $user_id )
		{
			$this->{$this->model}
				->where_related( 'user', 'id', $user_id )
				;
			$user = new User_Model;
			$user->get_by_id( $user_id );
			$this->data['object'] = $user;
		}

		$this->data['user_id'] = $user_id;
		$this->data['entries'] = $this->{$this->model}->get()->all;

		if( $user_id )
		{
			$this->inherit_views('admin/users/edit');
		}
		$this->set_include( 'loginlog' );
		$this->load->view( $this->template, $this->data);
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */