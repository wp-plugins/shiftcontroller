<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_controller extends Backend_controller_crud
{
	function __construct()
	{
		$this->conf = array(
			'model'		=> 'User_model',
			'path'		=> 'admin/users',
			'entity'	=> 'user',
			'export'	=> 'users',
			);
		parent::__construct( User_model::LEVEL_ADMIN );

		$CI =& ci_get_instance();
		if( $CI->app_conf->get('login_with') != 'username' )
		{
			unset( $this->{$this->model}->validation['username'] );
		}
	}

	function index( $status = 1 )
	{
	/* all statuses counts */
		$statuses = array();
		$res = $this->{$this->model}
			->select('active')
			->select_func('COUNT', '@id', 'count')
			->group_by('active')
			->order_by('active', 'DESC')
			->get();

		foreach( $res as $r )
		{
			if( $r->count > 0 )
				$statuses[ $r->active ] = $r->count;
		}

	/* no users so far */
		if( ! $statuses )
		{
			ci_redirect( $this->conf['path'] . '/add' );
			return;
		}
		$this->data['statuses'] = $statuses;

	/* load */
		if( ! isset($statuses[$status]) )
		{
			$all_statuses = array_keys( $statuses );
			$status = $all_statuses[0];
		}

		$this->{$this->model}->where('active', $status);
		$this->data['entries'] = $this->{$this->model}->get();
		$this->data['status'] = $status;
		$this->set_include( 'index' );
		$this->load->view( $this->template, $this->data);
	}
	
	function disable( $id )
	{
		if( ! $this->{$this->model}->id )
			$this->{$this->model}->get_by_id($id);
		if( $this->{$this->model}->active )
			$this->{$this->model}->active = 0;
		else
			$this->{$this->model}->active = 1;

		if( $this->{$this->model}->save() )
		{
			$msg = $this->{$this->model}->active ? lang('user_restore') : lang('user_archive');
			$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );
		}
		else
		{
			$this->session->set_flashdata( 'error', $this->{$this->model}->error->string );
		}
		$redirect_to = array( $this->conf['path'] );
		ci_redirect( $redirect_to );
		return;
	}

	function shifts( $id )
	{
		if( ! $this->_load($id) )
			return;
		$this->data['shifts'] = $this->{$this->model}->shift->get()->all;

		$this->data['dates'] = array();
		reset( $this->data['shifts'] );
		foreach( $this->data['shifts'] as $sh )
		{
			$this->data['dates'][ $sh->date ] = $sh->date;
		}
		parent::edit( $id, 'shifts' );
	}

	function edit( $id )
	{
		$fields = array_values( array_filter( 
			$this->{$this->model}->get_form_fields(),
			create_function(
				'$a',
				'return (isset($a["type"]) && ("password" == $a["type"])) ? FALSE : TRUE;'
				) 
			));

	/* can't change own level */
		if( $id == $this->auth->check() )
		{
			for( $ii = 0; $ii < count($fields); $ii++ )
			{
				if( $fields[$ii]['name'] == 'level' )
				{
					$fields[$ii]['extra']['disabled'] = 'disabled';
				}
			}
		}

	/* can't edit remotely integrated accounts */
		$remote_integration = $this->remote_integration();
		if( $remote_integration )
		{
			for( $ii = 0; $ii < count($fields); $ii++ )
			{
				if( ! in_array($fields[$ii]['name'], array('level')) )
				{
					$fields[$ii]['extra']['disabled'] = 'disabled';
				}
			}
		}

		$this->data['fields'] = $fields;
		return parent::edit( $id );
	}

	function password( $id )
	{
		$fields = array_values( array_filter( 
			$this->{$this->model}->get_form_fields(),
			create_function(
				'$a',
				'return (isset($a["type"]) && ("password" == $a["type"])) ? TRUE : FALSE;'
				)
			));
		$this->data['fields'] = $fields;
		if( ! $this->hc_form->is_set_default('password') )
			$this->hc_form->set_default( 'password', '' );
		return parent::edit( $id, 'password' );
	}

	function savepassword( $id )
	{
		if( ! $this->{$this->model}->id )
		{
			$this->{$this->model}->get_by_id($id);
		}
		if( ! $this->{$this->model}->exists() )
		{
			$this->session->set_flashdata( 'message', sprintf(lang('common_not_found'), get_class($this->{$this->model}), $id) );
			ci_redirect( $this->conf['path'] );
			return;
		}

		$post = array();
		foreach( array('password', 'confirm_password') as $fname )
		{
			$supplied = $this->input->post($fname);
			if( $supplied !== FALSE )
			{
				$post[$fname] = $supplied;
				$this->{$this->model}->{$fname} = $supplied;
			}
		}

		if( $this->{$this->model}->save() )
		{
		// redirect to list
			$msg = lang('common_change_password');
			$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );
			$redirect_to = array( $this->conf['path'] );
			ci_redirect( $redirect_to );
			return;
		}
		else
		{
			$this->hc_form->set_errors( $this->{$this->model}->error->all );
			$this->hc_form->set_defaults( $post );
			return $this->password( $id );
		}
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */