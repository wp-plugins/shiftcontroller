<?php
class Backend_controller extends MY_Controller 
{
	function __construct( $user_level = 0, $default_path = '' )
	{
		parent::__construct();

		$this->load->library('migration');
		if ( ! $this->migration->current())
		{
//			show_error($this->migration->error_string());
			return false;
		}
		$this->load->library( 'conf/app_conf' );
		$this->load->library( 'hc_time' );
		$this->load->library( 'hc_form' );

		$app = $this->config->item('nts_app');

		if( isset($GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID']) )
		{
			$id = $GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID'];
			$this->auth->login( $id );
		}

		if ( ! $this->auth->check() )
		{
			ci_redirect('auth/login');
			exit;
		}

	/* check user active */
		$user_active = 0;
		if( $test_user = $this->auth->user() )
		{
			$user_active = $test_user->active;
		}

		if( ! $user_active )
		{
			$to = 'auth/notallowed';
			ci_redirect( $to );
			exit;
		}

	/* check user level */
		if( $user_level )
		{
			$this->check_level( $user_level );
			if( $default_path )
				$this->conf['path'] = $default_path;
		}

	/* check license code */
		if( $this->hc_modules->exists('license') )
		{
			$license_model = new Hitcode_license_model;
			$code = $license_model->get();
			if( ! $code )
			{
				$to = 'license/admin';

				$current_slug = $this->get_current_slug();
				if( $current_slug != $to )
				{
					$this->session->set_flashdata( 
						'error', 
						lang('license_code_required')
						);

					ci_redirect( $to );
					exit;
				}
			}
		}

	}
}
