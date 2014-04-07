<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Wordpress_Users_controller extends Backend_controller
{
	function edit( $id )
	{
		// redirect to WP admin user edit
		$link = get_edit_user_link( $id );
		$this->redirect( $link );
		exit;
	}

	function add()
	{
		// redirect to WP admin user add
		$link = admin_url( 'user-new.php' );
		$this->redirect( $link );
		exit;
	}

	function sync()
	{
		$wum = new Wordpress_User_Model;
		$wordpress_roles = $wum->wp_roles();
		$this->data['wordpress_roles'] = $wordpress_roles;
		$this->data['wordpress_count_users'] = count_users();

		foreach( $wordpress_roles as $role_value => $role_name )
		{
			$field_name = 'role_' . $role_value;
			$default = $this->app_conf->get( 'wordpress_' . $field_name );
			$defaults[ $field_name ] = $default;
		}
		$this->hc_form->set_defaults( $defaults );

		$this->set_include( 'sync', 'wordpress/admin/users' );
		$this->load->view( $this->template, $this->data);
	}

	function syncrun()
	{
		$fields = array();
		$validation = array();

		$wum = new Wordpress_User_Model;
		$wordpress_roles = $wum->wp_roles();
		foreach( $wordpress_roles as $role_value => $role_name )
		{
			$field_name = 'role_' . $role_value;

			$fields[] = $field_name;
			$validation[] = array(
				'field'   => $field_name,
				'label'   => $role_name,
				'rules'   => 'trim|required'
				);
		}
		$this->form_validation->set_rules( $validation );

		if( $this->input->post() )
		{
			$post = array();
			reset( $fields );
			foreach( $fields as $f )
			{
				$post[$f] = $this->input->post($f);
			}
			$this->hc_form->set_defaults( $post );

			if( $this->form_validation->run() == FALSE )
			{
				$errors = array();
				reset( $fields );
				foreach( $fields as $f )
				{
					$errors[$f] = form_error($f);
				}
				$this->hc_form->set_errors( $errors );
			}
			else
			{
				$append_role_name = $this->input->post('append_role_name');

			/* save settings */
				reset( $post );
				foreach( $post as $k => $v )
				{
					$this->app_conf->set( 'wordpress_' . $k, $v );
				}

				$setup_ok = TRUE;

			/* users */
				$count_users = 0;
				$processed_users = array();

				/* this user */
				$current_user = wp_get_current_user();
				$user = new User_Model;
				$user->get_by_id( $current_user->ID );

				$user->email = $current_user->user_email;
				if( $current_user->user_firstname )
				{
					$user->first_name = $current_user->user_firstname;
					$user->last_name = $current_user->user_lastname;
				}
				else
				{
					$user->first_name = $current_user->display_name;
				}

				if( $append_role_name )
				{
					$wp_role = ( $current_user->roles && is_array($current_user->roles) && isset($current_user->roles[0]) ) ? $current_user->roles[0] : '';
					if( strlen($wp_role) )
						$user->first_name = '[' . $wp_role . '] ' . $user->first_name;
				}
				$user->level = USER_MODEL::LEVEL_ADMIN;

				if( $user->save() )
				{
					$processed_users[] = $user->id;
					$count_users++;
				}
				else
				{
					$setup_ok = FALSE;

					$err_msg = array();
					$err_msg[] = $current_user->user_email;
					$err_msg = array_merge($err_msg, array_values($user->error->all) );
					$this->session->set_flashdata( 'error', $err_msg );

					ci_redirect( 'admin/users' );
					return;
				}

				if( $setup_ok )
				{
					/* now by roles */
					reset( $wordpress_roles );
					foreach( $wordpress_roles as $role_value => $role_name )
					{
						$our_level = $post['role_' . $role_value];
						if( ! $our_level )
							continue;

						$args = array(
							'role'		=> $role_value,
							'exclude'	=> $current_user->ID
							);
						$wordpress_users = get_users( $args );
						foreach( $wordpress_users as $wuser )
						{
							if( (! $role_value) && $wuser->roles )
							{
								continue;
							}

							$user = new User_Model;
							$user->get_by_id( $wuser->ID );
							$is_new = $user->exists() ? FALSE : TRUE;

							$user->email = $wuser->user_email;
							$user->id = $wuser->ID;

							if( $wuser->user_firstname )
							{
								$user->first_name = $wuser->user_firstname;
								$user->last_name = $wuser->user_lastname;
							}
							else
							{
								$user->first_name = $wuser->display_name;
							}

							if( $append_role_name )
							{
								$wp_role = ( $wuser->roles && is_array($wuser->roles) && isset($wuser->roles[0]) ) ? $wuser->roles[0] : '';
								if( strlen($wp_role) )
									$user->first_name = '[' . $wp_role . '] ' . $user->first_name;
							}

							if( $is_new )
								$user->password = hc_random();
							$user->level = $our_level;

							if( 
								( $is_new && $user->save_as_new() )
								OR
								( (! $is_new) && $user->save() )
								)
							{
								$processed_users[] = $user->id;
								$count_users++;
							}
							else
							{
								$setup_ok = FALSE;

								$err_msg = array();
								$err_msg[] = $wuser->user_email;
								$err_msg = array_merge($err_msg, array_values($user->error->all) );
								$this->session->set_flashdata( 'error', $err_msg );
								ci_redirect( 'admin/users' );
								return;
							}
						}
					}

				/* those that are deleted in WordPress make archived */
					$um = new User_Model;
					$um->where_not_in( 'id', $processed_users );
					$um->update( 'active', USER_MODEL::STATUS_ARCHIVE );
					$archived_count = $um->db->affected_rows();
				}

				if( $setup_ok )
				{
					$msg = 'Synchronized ' . $count_users . ' ';
					$msg .= ($count_users > 1) ? 'users' : 'user';
					
					if( $archived_count )
					{
						$msg .= '<br>Archived ' . $archived_count . ' ';
						$msg .= ($archived_count > 1) ? 'users' : 'user';
					}

					$this->session->set_flashdata( 'message', $msg );
					ci_redirect( 'admin/users' );
					return;
				}
			}
		}
		return $this->sync();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */