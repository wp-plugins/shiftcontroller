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
