<?php
class Backend_controller extends MY_Controller 
{
	function __construct( $user_level = 0, $default_path = '' )
	{
		parent::__construct();

		$this->load->library('migration');
		if ( ! $this->migration->current()){
//			show_error($this->migration->error_string());
			return false;
			}
		$this->load->library( 'conf/app_conf' );
		$this->load->library( 'hc_time' );
		$this->load->library( 'hc_form' );

		$app = $this->config->item('nts_app');

		if( isset($GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID']) )
		{
			$id = $GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID'];
			$this->auth->login( $id );
		}

		if ( ! $this->auth->check() )
		{
			ci_redirect('auth/login');
			exit;
		}

	/* check user active */
		$user_active = 0;
		if( $test_user = $this->auth->user() )
		{
			$user_active = $test_user->active;
		}

		if( ! $user_active )
		{
			$to = 'auth/notallowed';
			ci_redirect( $to );
			exit;
		}

	/* check user level */
		if( $user_level )
		{
			$this->check_level( $user_level );
			if( $default_path )
				$this->conf['path'] = $default_path;
		}
	}

	function get_view( $view, $path = '' )
	{
		$include = '';
		$view_before = $view . '_before';
		$force_builtin = FALSE;
		if( isset($this->data[$view_before]) && $this->data[$view_before] )
		{
//			$force_builtin = TRUE;
		}

		if( ! $path )
			$path = $this->conf['path'];
		$my_view = $this->fix_path($path) . '/' . $view;
		$module_view = $this->fix_path($path) . '/' . $view;
		$builtin_view = $this->builtin_views . '/' . $view;

		if( (! $force_builtin) && $this->load->view_exists($my_view) )
			$include = $my_view;
		elseif( $this->load->view_exists($builtin_view) )
			$include = $builtin_view;

		return $include;
	}

	function set_include( $view, $path = '' )
	{
		$include = '';
		$include_submenu = '';
		$include_tabs = '';
		$include_header = '';

		if( $path )
		{
		}
		else
		{
			$path = $this->conf['path'];
		}

		$view_dirname = hc_dirname($view);
		$current_view = hc_basename($view);

		$include = $this->get_view( $view, $path );
		if( $this->input->is_ajax_request() OR $this->is_module() )
		{
			// no submenu or tabs for ajax calls and inline modules
		}
		else
		{
		/* header */
			$file = $view_dirname ? $view_dirname . '/_header' : '_header';
			$my = $path . '/' . $file;
			$builtin = $this->builtin_views . '/' . $file;
			if( $this->load->view_exists($my) )
				$include_header = $my;
			elseif( $this->load->view_exists($builtin) )
				$include_header = $builtin;

		/* submenu */
//			$no_submenu = $path . '/' . $view . '_nomenu';
			$no_submenu = $path . '/_nomenu';
			if( ! $this->load->view_exists($no_submenu) )
			{
				$file = $view_dirname ? $view_dirname . '/_menu' : '_menu';
				$my = $path . '/' . $file;
				$builtin = $this->builtin_views . '/' . $file;
				if( $this->load->view_exists($my) )
					$include_submenu = $my;
				elseif( $this->load->view_exists($builtin) )
					$include_submenu = $builtin;

				$my_tabs = $path . '/' . $view . '_tabs';
				$builtin_tabs = $this->builtin_views . '/' . $view . '_tabs';
				if( $this->load->view_exists($my_tabs) )
					$include_tabs = $my_tabs;
				elseif( $this->load->view_exists($builtin_tabs) )
					$include_tabs = $builtin_tabs;
			}
		}

		$this->data['include'] = $include;
		$this->data['include_submenu'] = $include_submenu;
		$this->data['include_tabs'] = $include_tabs;
		$this->data['include_header'] = $include_header;
		$this->data['current_view'] = $current_view;
	}
}
