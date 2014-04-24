<?php
class Timeoffs_notify
{
	function save( $object, $relations = NULL )
	{
		$CI =& ci_get_instance();
		if( ! isset($CI->hc_notifier) )
			return;

		$disable_email = $CI->app_conf->get('disable_email');
		if( $disable_email )
			return;

		$changes = $object->get_changes( $relations );

	/* published? */
		if( 
			$changes
			)
		{
			$this->_send( $object, $relations);
		}
	}

	private function _send( $timeoff, $relations = NULL )
	{
		$CI =& ci_get_instance();
		$staff = NULL;

		if( $relations && isset($relations['user']) )
		{
			$staff = $relations['user'];
		}
		else
		{
			$timeoff->user->get();
			if( $timeoff->user->exists() )
			{
				$staff = $timeoff->user;
			}
			else
			{
				$timeoff->user = new User_model;
				if( $timeoff->user_id )
				{
					$timeoff->user->get_by_id( $timeoff->user_id );
				}
			}
			$staff = $timeoff->user->get_clone();
		}
		$staff_view = $staff->title();

	/* compile message */
		$text = $timeoff->view_text();
	// a hack to overcome the wrong staff problem for new timeoffs
		$text['user'][1] = $staff_view;

		$msg = new stdClass();
		$msg->subject = lang('timeoff') . ': ' . $timeoff->prop_text('status');
		$msg->body = array();
		foreach( $text as $ta )
		{
			$msg->body[] = $ta[0] . ': ' . $ta[1];
		}
		$msg_id = $CI->hc_notifier->add_message( $msg );

		$group_id = 'save_timeoff';
		$CI->hc_notifier->enqueue_message( $msg_id, $staff, $group_id );

	// send to all admins too
		$um = new User_model;
		$um
			->where_in( 'level',	array(USER_MODEL::LEVEL_MANAGER, USER_MODEL::LEVEL_ADMIN) )
			->where( 'active',	USER_MODEL::STATUS_ACTIVE )
			;
		$um->get();
		foreach( $um as $u )
		{
			$CI->hc_notifier->enqueue_message( $msg_id, $u, $group_id );
		}
	}
}