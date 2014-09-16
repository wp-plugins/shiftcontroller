<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Hc_auth
{
	var $user = NULL;
	var $error = NULL;

	function __construct()
	{
		$this->load->library( array('email', 'session') );
		$this->load->helper('cookie');
		$this->load->model('User_model', 'auth_model');
	}

	public function __get($var)
	{
		return ci_get_instance()->$var;
	}

	public function check()
	{
		$user_id = $this->session->userdata('user_id');
		if( is_array($user_id) )
		{
			$user_id = array_shift( $user_id );
		}
		return $user_id;
	}

	public function forgotten_password( $email )
	{
		$this->auth_model->get_by_email( $email );
		if( $this->auth_model->exists() )
		{
			$new_password = mt_rand( 100000, 999999 );
			$user = $this->auth_model->all[0];
			$user->password = $new_password;
			$user->confirm_password = $new_password;

			if( $user->save() )
			{
				$CI =& ci_get_instance();

				$msg = new stdClass();
				$msg->subject = lang('auth_password_change_successful');
				$msg->body = array();
				$msg->body[] = lang('common_email') . ': ' . $email;
				$msg->body[] = lang('common_password') . ': ' . $new_password;

				$msg_id = $CI->hc_notifier->add_message( $msg );
				$CI->hc_notifier->enqueue_message( $msg_id, $user );
				return TRUE;
			}
			else
			{
				$this->error = $this->auth_model->string;
				return FALSE;
			}
		}
		else
		{
			$this->error = lang('auth_forgot_password_unsuccessful') . ': ' . $email . ' Not Found';
			return FALSE;
		}
	}

	public function change_password( $new_password )
	{
		$user = $this->user();
		$user->password = $new_password;
		$user->confirm_password = $new_password;

		if( $user->save() )
		{
			return TRUE;
		}
		else
		{
			$this->error = $this->auth_model->string;
			return FALSE;
		}
	}

	public function attempt( $identity, $password, $remember = FALSE )
	{
		$CI =& ci_get_instance();
		$login_with = $CI->app_conf->get('login_with');

		if( $login_with != 'username' )
		{
			$identity_name = 'email';
		}
		else
		{
			$identity_name = 'username';
		}
		$where = array(
			$identity_name	=> $identity,
			);

		$this->auth_model->from_array( $where );
		if( $this->auth_model->check_password($password) )
		{
			$this->login( $this->auth_model->id );
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function login( $user_id )
	{
		$current_user_id = $this->check();
		if( 
			$user_id
			&&
			(
				$user_id != $current_user_id
				OR
				(! isset($_SESSION['NTS_SESSION_REF']))
			)
			)
		{
			$session_data = array(
				'user_id'	=> $user_id
				);
			$this->session->set_userdata($session_data);
			$_SESSION['NTS_SESSION_REF'] = hc_random(16);

			if( method_exists($this->auth_model, 'trigger_event') )
			{
				$this->auth_model->trigger_event( 'after_login', $this->auth_model );
			}
		}
		return TRUE;
	}

	public function user()
	{
		if( NULL == $this->user )
		{
			$user_id = $this->check();
			if( $user_id )
			{
				$this->auth_model->get_by_id( $user_id );
				if( $this->auth_model->exists() )
				{
					$this->user = $this->auth_model->all[0];
				}
			}
		}
		return $this->user;
	}

	public function reset_user()
	{
		$this->user = NULL;
	}

	public function logout()
	{
		$this->session->unset_userdata('user_id');
	}
}