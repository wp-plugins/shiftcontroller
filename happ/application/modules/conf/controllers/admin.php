<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Conf_admin_controller extends Backend_controller
{
	private $params = array();

	function __construct()
	{
		$this->conf = array(
			'path'	=> 'conf/admin',
			);
		parent::__construct( User_model::LEVEL_ADMIN );

		$defaults = array();
		$fields = $this->config->items('settings');
		foreach( $fields as $fn => $f )
		{
			$defaults[$fn] = $this->app_conf->get($fn);
		}
		$this->hc_form->set_defaults( $defaults );
	}

	function reset( $what )
	{
		// update
		$fields = $this->config->items('settings');
		foreach( $fields as $fn => $f )
		{
			$this->app_conf->reset( $fn );
		}

	// redirect back
		$this->session->set_flashdata( 'message', lang('common_reset') . ': ' . lang('common_ok') );
		ci_redirect( 'conf/admin/' . $what );
	}

	function index()
	{
		$this->form_validation->set_rules( 'submit', 'submit', 'required' );
		$fields = $this->config->items('settings');

		$ri = $this->remote_integration();
		if( $ri )
		{
			unset( $fields['login_with'] );
		}

		reset( $fields );
		foreach( $fields as $fn => $f )
		{
			if( isset($f['rules']) )
			{
				$this->form_validation->set_rules( $fn, $f['label'], $f['rules'] );
			}
		}
		$this->data['fields'] = $fields;

		if( $this->form_validation->run() == false )
		{
			$post = $this->input->post();
			if( $post )
				$this->hc_form->set_defaults( $post );
			$this->hc_form->set_errors( $this->form_validation->error_array() );
		// display the form
			$this->set_include( 'index' );
			$this->load->view( $this->template, $this->data );
		}
		else
		{
		// update
			reset( $fields );
			foreach( $fields as $fn => $f )
			{
				$v = $this->input->post( $fn );
				$this->app_conf->set( $fn, $v );
			}

		// redirect back
			$msg = lang('common_update') . ': ' . lang('common_ok');
			$this->session->set_flashdata( 'message', $msg );

			$to = 'conf/admin';
			ci_redirect( $to );
		}
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */