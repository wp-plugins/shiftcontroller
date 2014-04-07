<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Timeoffs_controller extends Backend_controller_crud
{
	function __construct()
	{
		$this->conf = array(
			'model'		=> 'Timeoff_model',
			'path'		=> 'staff/timeoffs',
			'entity'	=> 'timeoff',
			);
		parent::__construct( USER_MODEL::LEVEL_STAFF );
		$this->{$this->model} = $this->auth->user()->timeoff;
		$this->{$this->model}->user = $this->auth->user();
		$this->data['fields'] = $this->process_fields();
	}

	function add()
	{
		$this->hc_time->setNow();
		$this->{$this->model}->date = $this->hc_time->formatDate_Db();

		$args = func_get_args();
		call_user_func_array( array($this, 'parent::add'), $args );
	}

	function edit( $id, $view = 'edit' )
	{
		$this->data['fixed'] = array('user');
		return parent::edit( $id, $view );
	}

	function index( $status = 0 )
	{
		$model = $this->{$this->model};
		if( ! $status )
			$status = TIMEOFF_MODEL::STATUS_PENDING;

	/* all statuses counts */
		$statuses = array();
		$res = $this->{$this->model}
			->select('status')
			->select_func('COUNT', '@id', 'count')
			->group_by('status')
			->order_by('status', 'ASC')
			->get();

		foreach( $res as $r )
		{
			if( $r->count > 0 )
				$statuses[ $r->status ] = $r->count;
		}

		if( isset($statuses[TIMEOFF_MODEL::STATUS_ACTIVE]) && ($statuses[TIMEOFF_MODEL::STATUS_ACTIVE] > 0) )
		{
			$this->hc_time->setNow();
		/* if this week then not yet expired */
			$this->hc_time->setStartWeek();
			$check_with = $this->hc_time->formatDate_Db();

		// count archive
			$archived_count = $this->{$this->model}
				->where('status', TIMEOFF_MODEL::STATUS_ACTIVE)
				->where('date_end <', $check_with)
				->count();

			if( $archived_count > 0 )
			{
				$statuses[TIMEOFF_MODEL::STATUS_ARCHIVE] = $archived_count;
				$statuses[TIMEOFF_MODEL::STATUS_ACTIVE] = $statuses[TIMEOFF_MODEL::STATUS_ACTIVE] - $archived_count;
				if( $statuses[TIMEOFF_MODEL::STATUS_ACTIVE] <= 0 )
					unset( $statuses[TIMEOFF_MODEL::STATUS_ACTIVE] );
			}
		}

	/* no timeoffs so far */
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

		switch( $status )
		{
			case TIMEOFF_MODEL::STATUS_ARCHIVE:
				$this->{$this->model}->where('status', TIMEOFF_MODEL::STATUS_ACTIVE);
				$this->{$this->model}->where('date <', $check_with);
				break;
			case TIMEOFF_MODEL::STATUS_ACTIVE:
				$this->{$this->model}->where('status', $status);
				$this->{$this->model}->where('date >=', $check_with);
				break;
			default:
				$this->{$this->model}->where('status', $status);
				break;
		}
		$this->data['entries'] = $this->{$this->model}->get()->all;

		$this->data['status'] = $status;
		$this->set_include( 'index' );
		$this->data['index_child'] = $this->get_view('index_child');

		$this->load->view( $this->template, $this->data);
	}

	function save()
	{
		$args = func_get_args();
		$id = count($args) ? $args[0] : 0;

		if( $id )
		{
			/* set it to pending */
			$this->_load( $id );
			$current = $this->{$this->model}->to_array();
			$post = call_user_func_array( array($this, 'save_prepare'), $args );

			$changed = FALSE;
			reset( $post );
			foreach( $post as $k => $v )
			{
				if( $v != $current[$k] )
				{
					$changed = TRUE;
					break;
				}
			}

			if( $changed )
			{
				$this->{$this->model}->status = TIMEOFF_MODEL::STATUS_PENDING;
			}

			call_user_func_array( array($this, 'parent::save'), $args );
			return;
		}

	/* new one */
		$post = call_user_func_array( array($this, 'save_prepare'), $args );
		$relations = $this->save_prepare_relations();

		$also_get = array(
			'repeat',
			'date_start',
			);
		reset( $also_get );
		foreach( $also_get as $ag )
		{
			$post[ $ag ] = $this->input->post( $ag );
		}

	/* check repeat */
		$repeat = $this->input->post( 'repeat' );

	/* add validation */
		switch( $repeat )
		{
			case 'multiple':
				$this->{$this->model}->date_start = $post['date_start'];
				$this->{$this->model}->date = $this->{$this->model}->date_start;
				$this->{$this->model}->start = 0;
				$this->{$this->model}->end = 24*60*60;
				$this->{$this->model}->validation['date_start'] = $this->{$this->model}->validation['date'];
				unset( $this->{$this->model}->validation['date'] );
				unset( $this->{$this->model}->validation['start'] );
				break;

			case 'single':
			default:
				$this->{$this->model}->date_end = $this->{$this->model}->date;
				$_POST['date_end'] = $this->{$this->model}->date_end;
				break;
		}

		$this->{$this->model}->status = TIMEOFF_MODEL::STATUS_PENDING;
		$this->{$this->model}->validate($relations);
		if( ! $this->{$this->model}->valid  )
		{
			$this->hc_form->set_errors( $this->{$this->model}->error->all );
			$this->hc_form->set_defaults( $post );

			if( $this->{$this->model}->id )
				$this->edit( $this->{$this->model}->id );
			else
			{
				array_shift( $args );
				call_user_func_array( array($this, 'add'), $args );
			}
			return;
		}

//		$this->{$this->model}->user_id = $this->auth->user()->id;
//		unset( $relations['user'] );
		unset( $this->{$this->model}->user );

		if( $this->{$this->model}->save($relations) )
		{
			$notes = $this->input->post( 'notes' );
			if( strlen($notes) > 0 )
			{
				$note = new Note_Model;
				$note->content = $notes;
				$note->save(
					array(
						'author'	=> $this->auth->user(),
						'timeoff'	=> $this->{$this->model}
						)
					);
			}
		}
		else
		{
			$this->hc_form->set_errors( $this->{$this->model}->error->all );
			$this->hc_form->set_defaults( $post );

			if( $this->{$this->model}->id )
				$this->edit( $this->{$this->model}->id );
			else
			{
				array_shift( $args );
				call_user_func_array( array($this, 'add'), $args );
			}
			return;
		}

	// redirect to list
		$msg = lang('timeoff') . ': ' . lang('common_add');
		$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );

		$redirect_to = method_exists($this, 'after_save') ? $this->after_save() : array($this->conf['path']);
		$this->redirect( $redirect_to );
//		$this->_save_complete( TRUE, $id, $post, $args );
		return;
	}

	function process_fields()
	{
		$skip = array( 'user', 'status', 'admin_notes' );
		$fields = array();
		$all_fields = $this->{$this->model}->get_form_fields();
		
		foreach( $all_fields as $n => $f )
		{
			if( in_array($f['name'], $skip) )
				continue;
			if( $f['name'] == 'staff_notes' )
			{
				$f['label'] = 'lang:timeoff_staff_my_notes';
			}
			$fields[ $n ] = $f;
		}
		return $fields;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */