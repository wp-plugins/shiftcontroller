<?php
class Schedule_model extends MY_Model_Virtual
{
	var $start;
	var $end;
	var $staff_id;

	function shifts()
	{
		if( $this->staff_id )
		{
			$um = new User_Model;
			$um->get_by_id( $this->staff_id );
			$sm = $um->shift;
		}
		elseif( $this->location_id )
		{
			$lm = new Location_Model;
			$lm->get_by_id( $this->location_id );
			$sm = $lm->shift;
		}
		else
		{
			$sm = new Shift_Model;
		}

		if( $this->start )
			$sm->where( 'date >=', $this->start );
		if( $this->end )
			$sm->where( 'date <=', $this->end );

		$sm
			->order_by( 'date', 'ASC' )
			->order_by( 'start', 'ASC' )
			->include_related( 'user', 'id' )
//			->where( 'user_id IS NOT ', 'NULL', FALSE )
			;

		return $sm->get();
	}

	function unpublish()
	{
		$count = 0;
		foreach( $this->shifts() as $sh )
		{
			if( $sh->status == SHIFT_MODEL::STATUS_DRAFT )
				continue;
			if( $sh->unpublish() )
			{
				$count++;
			}
			else
			{
				$error = $sh->error->string;
			}
		}
		return $count;
	}

	function publish()
	{
		$count = 0;
		foreach( $this->shifts() as $sh )
		{
			if( $sh->status == SHIFT_MODEL::STATUS_ACTIVE )
				continue;
			if( ! $sh->user_id )
				continue;
			if( $sh->publish() )
			{
				$count++;
			}
			else
			{
				$error = $sh->error->string;
			}
		}
		return $count;
	}

	function publishdraft()
	{
		$count = 0;
		foreach( $this->shifts() as $sh )
		{
			if( $sh->status == SHIFT_MODEL::STATUS_ACTIVE )
				continue;
			if( $sh->user_id )
				continue;
			if( $sh->publish() )
			{
				$count++;
			}
			else
			{
				$error = $sh->error->string;
			}
		}
		return $count;
	}
}
