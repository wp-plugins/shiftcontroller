<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shift_templates_controller extends Backend_controller_crud
{
	function __construct()
	{
		$this->conf = array(
			'model'		=> 'Shift_template_model',
			'path'		=> 'admin/shift_templates',
			'entity'	=> 'shift_template',
			);
		parent::__construct( User_model::LEVEL_ADMIN );
	}

	function save()
	{
		$args = func_get_args();
		$id = count($args) ? $args[0] : 0;
		$post = call_user_func_array( array($this, 'save_prepare'), $args );
		$relations = $this->save_prepare_relations();
		$this->{$this->model}->weekday = 'mmm';

		if( $this->{$this->model}->save($relations) )
		{
		// redirect to list
			$msg = $id ? lang('common_update') : lang('common_add');
			$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );

			$redirect_to = method_exists($this, 'after_save') ? $this->after_save() : array($this->conf['path']);
			$this->redirect( $redirect_to );
			return;
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
		}
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */