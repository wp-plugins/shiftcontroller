<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Auth_controller extends Front_Controller {
	function __construct()
	{
		$this->conf = array(
			'path'	=> 'wall',
			);
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('form_validation', 'hc_bootstrap');
		$this->load->library( 'hc_form' );

		$app = $this->config->item('nts_app');
		if(
			isset($GLOBALS['NTS_CONFIG'][$app]) &&
			isset($GLOBALS['NTS_CONFIG'][$app]['REMOTE_INTEGRATION']) &&
			$GLOBALS['NTS_CONFIG'][$app]['REMOTE_INTEGRATION']
			)
			{
				$user_id = 0;
				if( $test_user = $this->auth->user() )
				{
					$user_id = $test_user->id;
				}
				if( ! $user_id )
				{
					Modules::run( $GLOBALS['NTS_CONFIG'][$app]['REMOTE_INTEGRATION'] . '/auth/login' );
				}
			}
	}

	function index()
	{
		if( ! $this->auth->check() )
		{
			//redirect them to the login page
			ci_redirect('auth/login', 'refresh');
		}
		return;
	}

	function notallowed()
	{
		$this->data['include'] = 'auth/notallowed';
		$this->load->view( $this->template, $this->data);
	}

	function login()
	{
		$this->data['title'] = "Login";

	//validate form input
		$this->form_validation->set_rules('identity', 'identity', 'required');
		$this->form_validation->set_rules('password', 'lang:common_password', 'required');

		if ($this->form_validation->run() == true)
		{
			//check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->auth->attempt($this->input->post('identity'), $this->input->post('password'), $remember))
			{
				// check if not archived
				if( ! $this->auth->user()->active )
				{
					$this->auth->logout();
					$this->session->set_flashdata('error', lang('login_unsuccessful_archived'));
					ci_redirect('auth/login');
				}
				else
				{
	//				$this->session->set_flashdata('message', lang('login_successful'));
					ci_redirect('');
				}
			}
			else
			{
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('error', lang('login_unsuccessful'));
				ci_redirect('auth/login');
			}
		}
		else
		{
		//the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['auth_message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['identity'] = array(
				'name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
				);
			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				);

			$this->data['include'] = 'auth/login';
			$this->load->view( $this->template, $this->data);
		}
	}

	function logout()
	{
		$logout = $this->auth->logout();
		$this->session->set_flashdata('message', lang('logged_out'));
		ci_redirect('');
	}

	//change password
	function password()
	{
		$min_password_length = 2;
		$max_password_length = 20;
		$new_password = $this->input->post('new');
		$this->form_validation->set_rules('new', lang('common_new_password'), 'required|min_length[' . $min_password_length . ']|max_length[' . $max_password_length . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', lang('common_password_confirm'), 'required');

		if ( ! $this->auth->check())
		{
			ci_redirect('auth/login', 'refresh');
		}

		if ($this->form_validation->run() == false)
		{
			$errors = array();
			if( form_error('new') )
				$errors['new'] = form_error('new');
			if( form_error('new_confirm') )
				$errors['new_confirm'] = form_error('new_confirm');

			$this->hc_form->set_errors( $errors );
			$this->hc_form->set_default( 'new', $this->input->post('new') );
			$this->hc_form->set_default( 'new_confirm', $this->input->post('new_confirm') );

			//display the form
			//set the flash data error message if there is one
			$this->data['email'] = array(
				'name' => 'email',
				'id'   => 'email',
				'type' => 'text',
			);

			$this->data['min_password_length'] = $min_password_length;
			$this->data['old_password'] = array(
				'name' => 'old',
				'id'   => 'old',
				'type' => 'password',
			);
			$this->data['new_password'] = array(
				'label'	=> 'lang:common_new_password',
				'name' => 'new',
				'id'   => 'new',
				'type' => 'password',
			);
			$this->data['new_password_confirm'] = array(
				'label'	=> 'lang:common_password_confirm',
				'name' => 'new_confirm',
				'id'   => 'new_confirm',
				'type' => 'password',
			);

			//render
			$this->data['include'] = 'auth/password';
			$this->data['include_submenu'] = 'auth/password_menu';
			$this->load->view( $this->template, $this->data);
		}
		else
		{
			$msg = array();
			$new_password = $this->input->post('new');
			if( $new_password ){
				$change = $this->auth->change_password($new_password);
				if ($change)
				{
					$msg[] = lang('auth_password_change_successful');
				}
				else
				{
					$msg[] = $this->auth->error;
				}
			}

			$msg = join( '<br/>', $msg );
			$this->session->set_flashdata('message', $msg);
			ci_redirect('auth/profile');
		}
	}

	function profile()
	{
		$email = $this->auth->user()->email;
		$username = $this->auth->user()->username;
		$this->form_validation->set_rules('email', lang('common_email'), 'required|valid_email');

		if ( ! $this->auth->check())
		{
			ci_redirect('auth/login', 'refresh');
		}

		$this->hc_form->set_defaults( array('email' => $email) );

		if ($this->form_validation->run() == false)
		{
			//display the form
			//set the flash data error message if there is one
			$this->data['email'] = array(
				'name' => 'email',
				'id'   => 'email',
				'type' => 'text',
				);

			$this->data['username'] = $username;

			//render
			$this->data['include'] = 'auth/profile';
			$this->data['include_submenu'] = 'auth/profile_menu';
			$this->load->view( $this->template, $this->data);
		}
		else
		{
			$msg = array();
			$new_email = $this->input->post('email');
			if( $new_email != $email ){
				$this->auth->user()->email = $new_email;
				$this->auth->user()->save();
				$msg[] = lang('profile_updated');
				}

			$msg = join( '<br/>', $msg );
			$this->session->set_flashdata('message', $msg);
			ci_redirect('auth/profile');
		}
	}

	//forgot password
	function forgot_password()
	{
		$this->form_validation->set_rules('email', 'Email Address', 'required');
		if ($this->form_validation->run() == false)
		{
			//setup the input
			$this->data['email'] = array('name' => 'email',
				'id' => 'email',
			);

			//set any errors and display the form
			$this->data['auth_message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['include'] = 'auth/forgot_password';
			$this->load->view( $this->template, $this->data);
		}
		else
		{
			$supplied_email = $this->input->post('email');
			//run the forgotten password method to email new one to the user
			$forgotten = $this->auth->forgotten_password($supplied_email);

			if ($forgotten)
			{
				//if there were no errors
				$this->session->set_flashdata('message', lang('auth_forgot_password_successful'));
				ci_redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
			}
			else
			{
				$this->session->set_flashdata('message', $this->auth->error);
				ci_redirect("auth/forgot_password", 'refresh');
			}
		}
	}
}
