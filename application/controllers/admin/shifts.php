<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shifts_controller extends Backend_controller_crud
{
	function __construct()
	{
		$this->conf = array(
			'model'			=> 'Shift_model',
			'path'			=> 'admin/shifts',
			'entity'		=> 'shift',
			'after_save'	=> 'shift',
			);
		parent::__construct( User_model::LEVEL_MANAGER );

	/* check how many locations do we have */
		$lm = new Location_Model;
		$location_count = $lm->count();
		$this->data['location_count'] = $location_count;
	}

	function after_delete()
	{
		$redirect_to = array('admin/schedules');
		return $redirect_to;
	}

	function publish( $id )
	{
		if( $this->{$this->model}->id != $id )
		{
			$this->{$this->model}->get_by_id($id);
		}
		if( ! $this->{$this->model}->exists() )
		{
			$this->session->set_flashdata( 'message', sprintf(lang('not_found'), $id) );
			$redirect_to = $this->after_save();
			$this->redirect( $redirect_to );
			return;
		}

		if( $this->{$this->model}->status == SHIFT_MODEL::STATUS_ACTIVE )
		{
			$msg = lang('shift_unpublish');
			if( $this->{$this->model}->unpublish() )
			{
				$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );
			}
			else
			{
				$error = $this->{$this->model}->error->string;
				$this->session->set_flashdata( 'error', $msg . ': ' . $error );
			}
		}
		else
		{
			$msg = lang('shift_publish');
			if( $this->{$this->model}->publish() )
			{
				$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );
			}
			else
			{
				$error = $this->{$this->model}->error->string;
				$this->session->set_flashdata( 'error', $msg . ': ' . $error );
			}
		}

		$redirect_to = $this->after_save();
		$this->redirect( $redirect_to );
		return;
	}

	function add()
	{
	// shift templates
		$stm = new Shift_template_model;
		$this->data['shift_templates'] = $stm->get()->all;

		$args = func_get_args();
		call_user_func_array( array($this, 'parent::add'), $args );
	}

	function edit( $id, $view = 'edit' )
	{
	// shift templates
		$templates = new Shift_template_model;
		$this->data['shift_templates'] = $templates->get()->all;

	// group count
		if( ! $this->_load($id) )
			return;
		$this->data['group_count'] = $this->{$this->model}
			->where( 'group_id', $this->{$this->model}->group_id )
			->count()
			;

		$args = func_get_args();
		call_user_func_array( array($this, 'parent::edit'), $args );
		return;
	}

	function assign( $id )
	{
		if( ! $this->_load($id) )
			return;

		$this->data['object'] = $this->{$this->model};

		$this->set_include( 'edit/assign' );
		$this->load->view( $this->template, $this->data);
	}

	function save()
	{
		$args = func_get_args();
		$id = count($args) ? $args[0] : 0;

		if( $id )
		{
			call_user_func_array( array($this, 'parent::save'), $args );
			return;
		}

	/* new one */
		$post = call_user_func_array( array($this, 'save_prepare'), $args );
		$post['count'] = $this->input->post('count');
		$post['assign'] = $this->input->post('assign');

		$this->hc_form->set_defaults( $post );

		$relations = $this->save_prepare_relations();

		$dates = array( $this->{$this->model}->date );
		$more_post = array();
		if( $this->hc_modules->exists('shift_groups') )
		{
			$date = $this->{$this->model}->date;
			list( $this->{$this->model}, $this->hc_form ) = Modules::run('shift_groups/admin/save', $this->{$this->model}, $this->hc_form );
			$dates = $this->{$this->model}->date;
			$this->{$this->model}->date = $date;
		}

		if( $post['assign'] == 'now' )
		{
			$this->{$this->model}->validation['user'] = array(
				'label'	=> lang('user_level_staff'),
				'rules'	=> array('required'),
				);
		}
		else
		{
			$this->{$this->model}->count = $post['count'];
			$this->{$this->model}->validation['count'] = array(
				'label'	=> lang('shift_staff_count'),
				'rules'	=> array('required', 'trim', 'is_natural_no_zero'),
				);
		}

		$this->{$this->model}->validate($relations);
		if( ! $this->{$this->model}->valid  )
		{
			$this->hc_form->set_errors( $this->{$this->model}->error->all );

			if( $this->{$this->model}->id )
				$this->edit( $this->{$this->model}->id );
			else
			{
				array_shift( $args );
				call_user_func_array( array($this, 'add'), $args );
			}
			return;
		}

		if( isset($post['user']) )
		{
			$assigned = TRUE;
			if( is_array($post['user']) )
			{
				$create_qty = count($post['user']);
				$users = $relations['user'];
			}
			else
			{
				$create_qty = 1;
				$users = $relations['user'];
			}
			unset($relations['user']);
		}
		else
		{
			$assigned = FALSE;
			$create_qty = $post['count'];
		}

		$result_count = 0;
		reset( $dates );
		foreach( $dates as $date )
		{
			for( $cc = 0; $cc < $create_qty; $cc++ )
			{
				$this->{$this->model}->id = 0;
				$this->{$this->model}->date = $date;
				if( $assigned )
				{
					$relations['user'] = is_object($users) ? $users : $users[$cc];
				}
				if( $this->{$this->model}->save($relations) )
				{
					$result_count++;
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
			}
		}

	// redirect to list
		$msg = lang('common_add') . ': ' . $result_count . ' ' . lang('shifts');
		$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );

		$redirect_to = $this->after_save();
		$this->redirect( $redirect_to );
		return;
	}

	protected function after_save()
	{
		$this->load->library('user_agent');
		$schedule_view = $this->session->userdata('schedule_view');
		if( is_array($schedule_view) )
		{
			$return = array(
				'admin/schedules/index'
				);
			foreach( $schedule_view as $k => $v )
			{
				$return[] = $k;
				$return[] = $v;
			}
		}
		elseif( $this->agent->is_referral() )
		{
			$return = $this->agent->referrer();
		}
		else
		{
			$return = array( 'admin/schedules/index', 'start', $this->{$this->model}->date );
		}
		return $return;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */