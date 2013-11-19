<?php
abstract class _Timeblock_model extends MY_model
{
/* gets conflicting shifts and timeoffs */
	public function conflicts( 
		$use_shifts = TRUE,
		$use_timeoffs = TRUE,
		$shifts = NULL,
		$timeoffs = NULL,
		$force_date = NULL
		)
	{
		$return = array();

	/* if no user is set yet then none */
		if( ! isset($this->user_id) )
		{
			if( is_object($this->user) )
			{
				if( ! $this->user->id )
				{
					$this->user->get();
				}
			}
			else
			{
				$user_id = $this->user;
				$this->user = new User_model;
				$this->user->get_by_id( $user_id );
			}
			$this->user_id = $this->user->id;
		}
		if( ! $this->user_id )
			return $return;

		if( ($this->my_class() == 'timeoff') && in_array($this->status, array(TIMEOFF_MODEL::STATUS_DENIED) ) )
			return $return;

		if( ($shifts === NULL) OR ($timeoffs === NULL) )
		{
			if( is_object($this->user) )
			{
				if( ! $this->user->id )
				{
					$this->user->get();
				}
			}
			else
			{
				$user_id = $this->user;
				$this->user = new User_model;
				$this->user->get_by_id( $user_id );
				$this->user_id = $this->user->id;
			}
		}

		$date = $this->date;
		$start = $this->start;
		$end = $this->end;

		$CI =& ci_get_instance();

		if( $force_date )
		{
			$CI->hc_time->setDateDb( $force_date );
			$CI->hc_time->modify( '-1 day' );
			$yesterday = $CI->hc_time->formatDate_Db();

			$CI->hc_time->modify( '+2 days' );
			$tomorrow = $CI->hc_time->formatDate_Db();
		}
		else
		{
			$CI->hc_time->setDateDb( $date );
			$CI->hc_time->modify( '-1 day' );
			$yesterday = $CI->hc_time->formatDate_Db();

			if( $this->date_end )
			{
				$CI->hc_time->setDateDb( $this->date_end );
				$CI->hc_time->modify( '+1 day' );
				$tomorrow = $CI->hc_time->formatDate_Db();
			}
			else
			{
				$CI->hc_time->modify( '+2 days' );
				$tomorrow = $CI->hc_time->formatDate_Db();
			}
		}

	/* if we load shifts here */
		if( $use_shifts )
		{
			if( $shifts === NULL )
			{
				$this->user->shift
					->include_related( 'user', 'id' )
					->where( 'date >=', $yesterday )
					->where( 'date <=', $tomorrow );

				if( $this->id && ($this->my_class() == 'shift') )
					$this->user->shift->where( 'id <>', $this->id );

				$shifts = $this->user->shift->get()->all;
			}
		}
		else
		{
			$shifts = array();
		}

	/* if we load timeoffs here */
		if( $use_timeoffs )
		{
			if( $timeoffs === NULL )
			{
				$this->user->timeoff
					->include_related( 'user', 'id' )
					->where( 'date <=', $tomorrow )
					->where( 'date_end >=', $yesterday )
					->where_not_in( 'status', array(TIMEOFF_MODEL::STATUS_DENIED) );
				if( $this->id && ($this->my_class() == 'timeoff') )
					$this->user->timeoff->where( 'id <>', $this->id );

				$timeoffs = $this->user->timeoff->get()->all;
			}
		}
		else
		{
			$timeoffs = array();
		}

	/* now check shifts */
		reset( $shifts );
		foreach( $shifts as $sh )
		{
			if( $sh->date > $tomorrow )
				break;
			if( $sh->date < $yesterday )
				continue;

			if( $sh->user_id != $this->user_id )
				continue;
			if( ($sh->id == $this->id) && ($sh->my_class() == $this->my_class()) )
				continue;

			if( $sh->date == $yesterday )
			{
				if( ($sh->end - 24*60*60) > $start ) // overnight
					$return[] = $sh;
			}
			elseif( $sh->date == $tomorrow )
			{
				if( ($end - 24*60*60) > $sh->start ) // this overnight
					$return[] = $sh;
			}
			elseif( $sh->date == $date )
			{
				if( ($sh->start < $end) && ($sh->end > $start) ) // overlaps today
				{
					$return[] = $sh;
				}
			}
			else
			{
				$return[] = $sh;
			}
		}

	/* now check timeoffs */
		reset( $timeoffs );
		foreach( $timeoffs as $sh )
		{
			if( $sh->date > $tomorrow )
				break;
			if( $sh->date_end < $yesterday )
				continue;

			if( $sh->user_id != $this->user_id )
				continue;
			if( ($sh->id == $this->id) && ($sh->my_class() == $this->my_class()) )
				continue;

			if( $sh->date_end == $yesterday )
			{
				if( ($sh->end - 24*60*60) > $start ) // overnight
					$return[] = $sh;
			}
			elseif( $sh->date == $tomorrow )
			{
				if( ($end - 24*60*60) > $sh->start ) // this overnight
					$return[] = $sh;
			}
			elseif( $sh->date == $date )
			{
				if( ($sh->start < $end) && ($sh->end > $start) ) // overlaps today
				{
					$return[] = $sh;
				}
			}
			else
			{
				$return[] = $sh;
			}
		}
		return $return;
	}

/* validation */
	public function _conflict( $field )
	{
	// check if this staff is having a timeoff or other shift
		if ( isset($this->{$field}) )
		{
			$conflicts = $this->conflicts();
			if( $conflicts )
			{
				$result = array();
				reset( $conflicts );
				foreach( $conflicts as $c )
				{
					$result[] = $c->title();
				}
				$result = join( '<br>', $result );
				return $result;
			}
			return TRUE;
		}
		return FALSE;
	}

	public function _check_time( $field )
	{
	// if overnight
		if( $this->end < $this->start )
		{
			$this->end = 24 * 60 * 60 + $this->end;
		}

		$return = ( $this->end != $this->start ) ? TRUE : FALSE;
		if( ! $return )
		{
			$return = lang('shift_error_end_start');
		}
		return $return;
	}
}
