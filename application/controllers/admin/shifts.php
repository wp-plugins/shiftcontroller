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
			ci_redirect( $this->conf['path'] );
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

		$redirect_to = method_exists($this, 'after_save') ? $this->after_save() : array($this->conf['path']);
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

		$result_count = 0;
		reset( $dates );
		foreach( $dates as $date )
		{
			$this->{$this->model}->id = 0;
			$this->{$this->model}->date = $date;
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

	// redirect to list
		$msg = lang('common_add') . ': ' . $result_count . ' ' . lang('shifts');
		$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );

//		$redirect_to = method_exists($this, 'after_save') ? $this->after_save() : array($this->conf['path']);
		$date_return = $dates ? $dates[0] : $this->{$this->model}->date;
		$redirect_to = array( 'admin/schedules/index/all', $date_return );
		$this->redirect( $redirect_to );
		return;
	}

	protected function after_save()
	{
		$this->load->library('user_agent');
		if ($this->agent->is_referral())
			$return = $this->agent->referrer();
		else
			$return = array( 'admin/schedules/index', $this->{$this->model}->date );
		return $return;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */