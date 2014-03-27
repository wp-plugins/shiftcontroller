<?php
class Wordpress_User_Model
{
	public function wp_roles()
	{
		global $wp_roles;
		if( ! isset($wp_roles) )
		{
			$wp_roles = new WP_Roles();
		}
		$return = $wp_roles->get_names();
		return $return;
	}

	public function sync( $id )
	{
		$wuser = get_user_by( 'id', $id );

		$user = new User_Model;
		$user->get_by_id( $id );
		$is_new = $user->exists() ? FALSE : TRUE;

		if( $is_new )
		{
			/* check new user level */
			$CI =& ci_get_instance();

			$wp_role = ( $wuser->roles && is_array($wuser->roles) && isset($wuser->roles[0]) ) ? $wuser->roles[0] : '';

			$k = 'wordpress_' . 'role_' . $wp_role;
			$user_level = $CI->app_conf->get( $k );
			if( $wp_role == 'administrator' )
			{
				$user_level = USER_MODEL::LEVEL_ADMIN;
			}

			if( ! $user_level )
				return;

			$user->level = $user_level;
			$user->password = hc_random();
		}

		if( $wuser->user_firstname )
		{
			$user->first_name = $wuser->user_firstname;
			$user->last_name = $wuser->user_lastname;
		}
		else
		{
			$user->first_name = $wuser->display_name;
		}

		if( $wuser->user_email )
		{
			if( $is_new OR ($wuser->user_email != $user->email) )
			{
				// check if this email already exists
				$um = new User_Model;
				$um->where( 'email', $wuser->user_email )->get();
				if( $um->exists() )
				{
					if( $is_new )
					{
						// update id in our table
						$um->where('id', $um->id)->update('id', $id);
						$user->id = $id;
					}
					else
					{
						$user->id = $um->id;
					}
					$is_new = FALSE;
				}
				$user->email = $wuser->user_email;
			}
		}

		$user->active = USER_MODEL::STATUS_ACTIVE;
		if(
			( $is_new && $user->save_as_new() )
			OR
			( (! $is_new) && $user->save() )
			)
		{
			$return = TRUE;
		}
		else
		{
			$return = TRUE;
		}
		return $return;
	}
}
