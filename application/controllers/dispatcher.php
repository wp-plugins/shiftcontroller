<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dispatcher_controller extends Front_controller
{
	function __construct()
	{
		parent::__construct();
		$app = $this->config->item('nts_app');

		if ( ! $this->auth->check() )
		{
			if( isset($GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID']) )
			{
				$id = $GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID'];
				$this->auth->login( $id );
			}
		}

	// sync user account
		$ri = $this->remote_integration();
		if( $ri )
		{
			$id = $this->auth->check();
			if( $id )
			{
				$model_name = $ri . '_User_Model';
				$um = new $model_name;
				$um->sync( $id );
				$this->auth->reset_user();
			}
		}

	// check user level
		$user_level = 0;
		$user_id = 0;
		if( $this->auth->check() )
		{
			if( $test_user = $this->auth->user() )
			{
				$user_id = $test_user->id;
				$user_level = $test_user->level;
			}
		}

		if( isset($GLOBALS['NTS_CONFIG'][$app]['FORCE_USER_LEVEL']) )
		{
			$user_level = $GLOBALS['NTS_CONFIG'][$app]['FORCE_USER_LEVEL'];
		}

		$remote_integration = $this->remote_integration();
		if( $remote_integration )
		{
			$wall_schedule_display = 0;
		}
		else
		{
			$wall_schedule_display = $this->app_conf->get('wall_schedule_display');
		}

		$allowed = FALSE;
		switch( $user_level )
		{
			case 0:
				if( $wall_schedule_display <= $user_level )
					$to = 'wall';
				else
				{
					if( $user_id )
						$to = 'auth/notallowed';
					else
						$to = 'auth/login';
				}
				break;
			case USER_MODEL::LEVEL_ADMIN:
				$to = 'admin/schedules';
				break;
			case USER_MODEL::LEVEL_MANAGER:
				$to = 'admin/schedules';
				break;
			case USER_MODEL::LEVEL_STAFF:
				$to = 'staff/shifts';
				break;
		}

		ci_redirect( $to );
		exit;
	}
}
