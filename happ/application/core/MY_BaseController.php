<?php
include_once( NTS_SYSTEM_APPPATH."third_party/MX/Controller.php" );

//class MY_Controller extends CI_Controller 
class MY_BaseController extends MX_Controller 
{
	public $conf = array();
	protected $builtin_views;
	protected $inherit_views;
	public $is_module = FALSE;
	public $default_params = array();

	function __construct()
	{
		parent::__construct();
		$this->builtin_views = '_boilerplate';
		$this->load->helper( array('language', 'form') );
		if( defined('NTS_DEVELOPMENT') )
		{
			if( ! ($this->input->is_ajax_request() OR $this->is_module()) )
			{
				$this->output->enable_profiler(TRUE);
			}
		}

		if( ! isset($this->conf) )
			$this->conf = array();
		if( ! isset($this->conf['path']) )
			$this->conf['path'] = '';

		$this->load->database();
		$this->load->helper( array('url') );
		$skip_setup = array('setup', 'demo');

		if(
			( ! in_array($this->router->fetch_class(), $skip_setup)) AND
			( ! $this->is_setup() )
			){

			$setup_redirect = 'setup';
			$remote_integration = $this->remote_integration();
			if( $remote_integration )
				$setup_redirect = $remote_integration . '/setup';

			ci_redirect( $setup_redirect );
			exit;
			}

		$this->load->helper( array('hitcode') );
		$this->load->library( array('form_validation', 'session', 'hc_bootstrap') );
		$this->load->library( 'hc_modules' );
		$this->load->library( 'conf/app_conf' );
		$this->load->library('user_agent');

	/* add module models paths for autoloading */
		$modules = $this->config->get_modules();
		$modules_locations = $this->config->item('modules_locations');

		if( is_array($modules) )
		{
			reset($modules);
			foreach( $modules as $module )
			{
				reset( $modules_locations );
				foreach( $modules_locations as $ml )
				{
					$mod_dir = $ml . $module;
					if( file_exists($mod_dir) )
					{
						Datamapper::add_model_path( $mod_dir );
						$this->load->add_package_path( $mod_dir );
					}
				}
			}
		}

	/* reload config paths */
		$this->app_conf->init();

		$this->load->library( 'hc_modules' );

	/* events and notifiers */
		$this->load->library( array('hc_events', 'hc_notifier', 'hc_email') );
		$this->hc_email->from = $this->app_conf->get('email_from');
		$this->hc_email->fromName = $this->app_conf->get('email_from_name');

		$this->form_validation->set_error_delimiters('<div class="hc-form-error">', '</div>');

	// table
		$this->load->library('table');
		$table_tmpl = array (
			'table_open'          => '<table class="table table-striped">',
			);
		$this->table->set_template( $table_tmpl );

	// pagination
		$this->load->library('pagination');

	// conf
		$this->load->library( 'hc_auth', NULL, 'auth' );

		$this->load->library(
			'hc_access_manager', 
			array(
				'auth'	=> $this->auth,
				),
			'access_manager'
			);

		$this->data = array();
		$this->data['page_title'] = $this->config->item('nts_app_title');

		$this->data['message'] = $this->session->flashdata('message');
		$this->data['debug_message'] = $this->session->flashdata('debug_message');
		$this->data['error'] = $this->session->flashdata('error');

	/* menu */
		$this->config->load('menu', TRUE, TRUE );
//		$this->config->load('config', TRUE, TRUE );

		$this->session->set_flashdata('referrer', current_url());
		$this->set_include( '' );

		$this->set_layout();
	}

/* access level to notes and other */
	function access_levels_to( $what )
	{
		$return = array();
		return $return;
	}

	function inherit_views( $from )
	{
		$this->inherit_views = $from;
	}

	function check_level( $require_level )
	{
		if( 
			! (
			$this->auth && 
			$this->auth->user() &&
			($this->auth->user()->level >= $require_level)
			)
		)
		{
			$this->session->set_flashdata('error', 'You are not allowed to access this page');
			ci_redirect('');
			exit;
		}
	}

	function set_layout()
	{
		if( $this->input->is_ajax_request() )
			$this->template = '_layout/index_ajax';
		elseif( $this->is_module() )
			$this->template = '_layout/index_module';
		else
			$this->template = '_layout/index';
	}

	function is_module()
	{
		return $this->is_module;
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
			$inherit = $this->inherit_views ? $this->inherit_views . '/' . $file : '';
			$builtin = $this->builtin_views . '/' . $file;

			$conf = array();
			if( strlen($view) )
			{
				$my_conf = $path . '/_conf.php';
				if( $full_my_conf = $this->load->view_exists($my_conf) )
				{
					require( $full_my_conf );
				}
			}

			if( ! (isset($conf['header']) && (! $conf['header'])) )
			{
				if( $inherit && $this->load->view_exists($inherit) )
					$include_header = $inherit;
				elseif( $this->load->view_exists($my) )
					$include_header = $my;
				elseif( $this->load->view_exists($builtin) )
					$include_header = $builtin;
			}

		/* submenu */
//			$no_submenu = $path . '/' . $view . '_nomenu';
			$no_submenu = $path . '/_nomenu';
			if( ! $this->load->view_exists($no_submenu) )
			{
				$file = $view_dirname ? $view_dirname . '/_menu' : '_menu';
				$my = $path . '/' . $file;
				$inherit = $this->inherit_views ? $this->inherit_views . '/' . $file : '';
				$builtin = $this->builtin_views . '/' . $file;

				if( $this->load->view_exists($my) )
					$include_submenu = $my;
				elseif( $inherit && $this->load->view_exists($inherit) )
					$include_submenu = $inherit;
				elseif( $this->load->view_exists($builtin) )
					$include_submenu = $builtin;

				$my_tabs = $path . '/' . $view . '_tabs';
				$inherit_tabs = $this->inherit_views ? $this->inherit_views . '/' . $view . '_tabs' : '';
				$builtin_tabs = $this->builtin_views . '/' . $view . '_tabs';

				if( $this->load->view_exists($my_tabs) )
					$include_tabs = $my_tabs;
				elseif( $inherit_tabs && $this->load->view_exists($inherit_tabs) )
					$include_tabs = $inherit_tabs;
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

	function get_current_slug()
	{
		$parts = array();
		$parts[] = $this->uri->segment(1);
		$parts[] = $this->uri->segment(2);
		$return = join( '/', $parts );
		return $return;
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

	function is_setup()
	{
		$return = TRUE;
		if( $this->db->table_exists('conf') ){
			$return = TRUE;
			}
		else {
			$return = FALSE;
			}
		return $return;
	}

	function fix_path( $path )
	{
		$return = str_replace( '-', '_', $path );
		return $return;
	}

	protected function parse_args( $args )
	{
		$return = array();
		for( $ii = 0; $ii < count($args); $ii = $ii + 2 )
		{
			if( isset($args[$ii + 1]) )
			{
				$k = $args[$ii];
				$v = $args[$ii + 1];
				$return[ $k ] = $v;
			}
		}
		return $return;
	}

	function redirect( $to )
	{
		if( $this->input->is_ajax_request() )
		{
//			if( $this->input->post() )
//			{
				// clear flash
				$this->session->set_flashdata( 'message', NULL );
				$this->session->set_flashdata( 'error', NULL );
//			}

			$to = ci_site_url($to);
			$out = array(
				'redirect'	=> $to,
				);
			$this->output->set_content_type('application/json');
			$this->output->enable_profiler(FALSE);
			echo json_encode($out);
			hc_ci_before_exit();
			exit;
//			return;
		}
		else
		{
			ci_redirect($to);
			return;
		}
		return;
	}
}