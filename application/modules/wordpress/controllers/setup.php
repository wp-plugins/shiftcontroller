<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
include_once( NTS_SYSTEM_APPPATH . 'controllers/setup.php' );

class Wordpress_setup_controller extends Setup_controller
{
	function index()
	{
		$wum = new Wordpress_User_Model;
		$wordpress_roles = $wum->wp_roles();
		$this->data['wordpress_roles'] = $wordpress_roles;
		$this->data['wordpress_count_users'] = count_users();
		return parent::index();
	}

	function run()
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
			/* run setup */	
			/* reset tables */
				$this->_drop_tables();

			/* setup tables */
				$this->load->library('migration');
				if ( ! $this->migration->current())
				{
					show_error($this->migration->error_string());
					return false;
				}
				$this->load->library( 'conf/app_conf' );

				$setup_ok = TRUE;

			/* save settings */
				reset( $post );
				foreach( $post as $k => $v )
				{
					$this->app_conf->set( 'wordpress_' . $k, $v );
				}

			/* users */
				$append_role_name = $this->input->post('append_role_name');
				$count_users = 0;

				/* this user */
				$current_user = wp_get_current_user();
				$user = new User_Model;
				$user->email = $current_user->user_email;
				$user->id = $current_user->ID;

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

				$user->password = hc_random();
				$user->level = USER_MODEL::LEVEL_ADMIN;

				
				if( $user->save_as_new() )
				{
					$count_users++;
				}
				else
				{
					$setup_ok = FALSE;
					$this->_drop_tables();

					$err_msg = array();
					$err_msg[] = $current_user->user_email;
					$err_msg = array_merge($err_msg, array_values($user->error->all) );
					$this->session->set_flashdata( 'error', $err_msg );

					ci_redirect( 'wordpress/setup' );
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

							$user->password = hc_random();
							$user->level = $our_level;

							if( $user->save_as_new() )
							{
								$count_users++;
							}
							else
							{
								$setup_ok = FALSE;
								$this->_drop_tables();

								$err_msg = array();
								$err_msg[] = $wuser->user_email;
								$err_msg = array_merge($err_msg, array_values($user->error->all) );
								$this->session->set_flashdata( 'error', $err_msg );
								ci_redirect( 'wordpress/setup' );
								return;
							}
						}
					}
				}

				if( $setup_ok )
				{
				/* default settings */
					$email_from = get_bloginfo('admin_email');
					$email_from_name = get_bloginfo('name');

					$this->app_conf->set( 'email_from',			$email_from );
					$this->app_conf->set( 'email_from_name',	$email_from_name );

					$msg = 'Imported ' . $count_users . ' ';
					$msg .= ($count_users > 1) ? 'users' : 'user';

					$this->session->set_flashdata( 'message', $msg );
					ci_redirect( 'setup/ok' );
					return;
				}
			}
		}

		$this->data['include'] = 'setup';
		$this->load->view( '_layout/index_no_menu', $this->data );
		return;
	}

	function ok()
	{
		$this->data['include'] = 'setup_ok';
		$this->load->view( '_layout/index_no_menu', $this->data);
		return;
	}
}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */