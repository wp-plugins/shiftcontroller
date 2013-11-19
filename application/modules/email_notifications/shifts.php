<?php
class Shifts_notify
{
	function save( $object, $relations = NULL )
	{
		$changes = $object->get_changes( $relations );

	/* published? */
		if( 
			$changes && 
			($object->status == SHIFT_MODEL::STATUS_ACTIVE) &&
			(
				in_array('status',	array_keys($changes)) OR
				in_array('user',	array_keys($changes))
			)
			)
		{
			$this->_publish( $object );
		}
	}

	private function _publish( $shift )
	{
		$CI =& ci_get_instance();

		$text = $shift->view_text();

	/* compile message */
		$msg = new stdClass();
		$msg->subject = lang('shifts_published');
		$msg->body = array();
		foreach( $text as $ta )
		{
			$msg->body[] = $ta[0] . ': ' . $ta[1];
		}

		$msg_id = $CI->hc_notifier->add_message( $msg );

		$staff = $shift->user->get();
		$group_id = 'publish_shift';
		$CI->hc_notifier->enqueue_message( $msg_id, $staff, $group_id );
	}
}
