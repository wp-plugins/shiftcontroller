<?php
class Shifts_notify
{
	var $notifications = array(
		array(
			'msg'		=> 'shifts_published',
			'to'		=> 'current',
			'change'	=> array('user_id'),
			'when'		=> array(
				'status'	=> SHIFT_MODEL::STATUS_ACTIVE
				)
			),
		array(
			'msg'		=> 'shifts_published',
			'to'		=> 'current',
			'change'	=> array('status'),
			'when'		=> array(
				'status'	=> SHIFT_MODEL::STATUS_ACTIVE
				)
			),
		array(
			'msg'		=> 'shifts_cancelled',
			'to'		=> 'old',
			'change'	=> array('user_id'),
			'nochange'	=> array('id'),
			'when'		=> array(
				'status'	=> SHIFT_MODEL::STATUS_ACTIVE
				)
			),
		array(
			'msg'		=> 'shifts_cancelled',
			'to'		=> 'current',
			'change'	=> array('status'),
			'nochange'	=> array('id', 'user_id'),
			'when'		=> array(
				'status'	=> SHIFT_MODEL::STATUS_DRAFT
				)
			),
		array(
			'msg'		=> 'shifts_changed',
			'to'		=> 'current',
			'nochange'	=> array('user_id', 'status'),
			'when'		=> array(
				'status'	=> SHIFT_MODEL::STATUS_ACTIVE
				)
			),
		);

	function save( $object, $relations = NULL )
	{
		$CI =& ci_get_instance();
		if( ! isset($CI->hc_notifier) )
			return;

		$disable_email = $CI->app_conf->get('disable_email');
		if( $disable_email )
			return;

		$changes = $object->get_changes( $relations );

		if( ! $changes )
		{
			return;
		}

		$msgs = array();
		reset( $this->notifications );
		$nii = -1;
		foreach( $this->notifications as $n )
		{
			$nii++;
			$skip = FALSE;
			if( isset($n['change']) )
			{
				$skip = TRUE;
				foreach( $n['change'] as $c )
				{
					if( array_key_exists($c, $changes) )
					{
						$skip = FALSE;
						break;
					}
				}
			}
			if( $skip )
				continue;

			$skip = FALSE;
			if( isset($n['nochange']) )
			{
				$skip = FALSE;
				foreach( $n['nochange'] as $c )
				{
					if( array_key_exists($c, $changes) )
					{
						$skip = TRUE;
						break;
					}
				}
			}
			if( $skip )
				continue;

			$skip = FALSE;
			if( isset($n['when']) )
			{
				foreach( $n['when'] as $wk => $wv )
				{
					if( $object->{$wk} != $wv )
					{
						$skip = TRUE;
						break;
					}
				}
			}
			if( $skip )
				continue;

			if( ! isset( $msgs[$n['msg']] ) )
				$msgs[$n['msg']] = array();
			$msgs[$n['msg']][] = $n['to'];
		}

		$skip_keys = array('user', 'has_trade');
		$show_end_time_for_staff = $CI->app_conf->get('show_end_time_for_staff');
		if( ! $show_end_time_for_staff )
			$skip_keys[] = 'end';

		$text = $object->view_text( $skip_keys );
		foreach( $msgs as $key => $staffs )
		{
		/* compile message */
			$msg = new stdClass();
			$msg->body = array();
			foreach( $text as $ta )
			{
				$msg->body[] = $ta[0] . ': ' . $ta[1];
			}
			$msg->subject = lang($key);
			$msg_id = $CI->hc_notifier->add_message( $msg );

			foreach( $staffs as $staff_type )
			{
				$staff = NULL;
				if( $staff_type == 'old' )
				{
					if( $changes['user_id'] )
					{
						$staff = new User_Model;
						$staff->get_by_id( $changes['user_id'] );
					}
				}
				else
				{
					$staff = $object->user->get();
				}
				if( (! $staff) OR (! $staff->exists()) )
					continue;

				$CI->hc_notifier->enqueue_message( $msg_id, $staff, $key );
			}
		}
	}

	private function _notify( $shift, $reason )
	{
		$CI =& ci_get_instance();

		$text = $shift->view_text( array('user') );
		$changes = $shift->get_changes();

		$staff = $shift->user->get();
		$msgs = array();

		switch( $reason )
		{
			case 'new':
				if( $staff->exists() )
				{
					$msgs['shifts_published'] = array($staff);
				}
				break;

			case 'staff_change':
				$old_staff = new User_Model;
				$old_staff->get_by_id( $changes['user_id'] );

				if( $old_staff->exists() )
				{
					$msgs['shifts_cancelled'] = array($old_staff);
				}
				if( $staff->exists() )
				{
					$msgs['shifts_published'] = array($staff);
				}
				break;

			case 'change':
				if( $staff->exists() )
				{
					$msgs['shifts_changed'] = array($staff);
				}
				break;
		}

		foreach( $msgs as $key => $staffs )
		{
		/* compile message */
			$msg = new stdClass();
			$msg->body = array();
			foreach( $text as $ta )
			{
				$msg->body[] = $ta[0] . ': ' . $ta[1];
			}
			$msg->subject = lang($key);

			$msg_id = $CI->hc_notifier->add_message( $msg );
			foreach( $staffs as $staff )
			{
				$CI->hc_notifier->enqueue_message( $msg_id, $staff, $key );
			}
		}
	}
}
