<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ShiftExec_Access_Manager extends Hc_Access_Manager
{
	const LEVEL_EVERYONE = 0;
	const LEVEL_ALL_STAFF = 1;
	const LEVEL_OWNER_STAFF = 2;
	const LEVEL_ADMIN = 3;

	function access_levels( $what )
	{
		$return = array();
		switch( $what )
		{
			case 'note_shift';
				if( $this->auth && $this->auth->user() )
				{
					if( $this->auth->user()->level == USER_MODEL::LEVEL_ADMIN )
					{
						$return = array(
							self::LEVEL_EVERYONE	=> lang('common_everyone'),
							self::LEVEL_ALL_STAFF	=> lang('user_level_staff_all'),
							self::LEVEL_OWNER_STAFF	=> lang('shift_staff'),
							self::LEVEL_ADMIN		=> lang('user_level_admin'),
							);
					}
					else
					{
						$return = array(
//							self::LEVEL_EVERYONE	=> lang('common_everyone'),
//							self::LEVEL_ALL_STAFF	=> lang('user_level_staff_all'),
							self::LEVEL_ADMIN		=> lang('user_level_admin'),
							);
					}
				}
				break;
			case 'note_timeoff';
				break;
			default: 
				break;
		}
		return $return;
	}

	function filter_see( $entries )
	{
		$return = array();
		foreach( $entries as $e )
		{
			if( $this->can_see($e) )
			{
				$return[] = $e;
			}
		}
		return $return;
	}

	function can_edit( $what )
	{
		$return = parent::can_see( $what );

		$my_class = $what->my_class();
		switch( $my_class )
		{
			case 'note':
				$return = FALSE;
				if(
					( $this->user_level >= USER_MODEL::LEVEL_MANAGER )
					OR
					( $what->author_id == $this->user_id )
					)
				{
					$return = TRUE;
				}
				break;
		}

		return $return;
	}

	function can_see( $what )
	{
		$return = parent::can_see( $what );

		$my_class = $what->my_class();
		switch( $my_class )
		{
			case 'note':
				$return = FALSE;

				if( $what->access_level == self::LEVEL_EVERYONE )
				{
					$return = TRUE;
				}
				elseif( $this->user_id )
				{
					if(
						( $this->user_level >= USER_MODEL::LEVEL_MANAGER )
						OR
						( $what->author_id == $this->user_id )
						)
					{
						$return = TRUE;
					}
					elseif( $this->user_level == USER_MODEL::LEVEL_STAFF )
					{
						if( $what->access_level == self::LEVEL_ALL_STAFF )
						{
							$return = TRUE;
						}
						elseif( $what->access_level == self::LEVEL_OWNER_STAFF )
						{
							if( $what->shift_id )
							{
								if( $what->shift->get()->user_id == $this->user_id )
								{
									$return = TRUE;
								}
							}
						}
					}
				}
				break;
		}
		return $return;
	}
}