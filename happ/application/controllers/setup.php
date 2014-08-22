<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_controller extends MX_Controller
{
	public $conf = array();

	function __construct()
	{
		parent::__construct();

		if( defined('NTS_DEVELOPMENT') )
		{
//			$this->output->enable_profiler(TRUE);
		}

		if( ! isset($this->conf) )
			$this->conf = array();
		if( ! isset($this->conf['path']) )
			$this->conf['path'] = '';

		$this->load->database();
		$this->load->helper( array('url', 'language', 'form', 'hitcode', 'language', 'form') );
		$this->load->library( array('form_validation', 'session', 'hc_bootstrap') );
		$this->load->library( 'hc_form' );
		$this->load->library( 'hc_modules' );
		$this->form_validation->set_error_delimiters('<div class="hc-form-error">', '</div>');

	// conf
		$this->auth = NULL;
		
		$this->data = array();
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['error'] = $this->session->flashdata('error');

		$this->session->set_flashdata('referrer', current_url());
		$this->set_include( '' );
		$this->data['page_title'] = $this->config->item('nts_app_title') . ' :: ' . 'Installation';

	/* add module models paths for autoloading */
		$modules = $this->config->get_modules();
		if( is_array($modules) )
		{
			reset($modules);
			foreach( $modules as $module )
			{
				$mod_dir = $this->hc_modules->module_dir($module);
				if( $mod_dir )
				{
					Datamapper::add_model_path( $mod_dir );
				}
			}
		}
	}

	protected function _drop_tables()
	{
		$app = $this->config->item('nts_app');
		$my_table_prefix = isset($GLOBALS['NTS_CONFIG'][$app]['DB_TABLES_PREFIX']) ? $GLOBALS['NTS_CONFIG'][$app]['DB_TABLES_PREFIX'] : NTS_DB_TABLES_PREFIX;
		$tables = array();
		$sth = $this->db->query("SHOW TABLES LIKE '" . $my_table_prefix . "%'");
		foreach( $sth->result_array() as $r )
		{
			reset( $r );
			foreach( $r as $k => $v )
			{
				$tables[] = $v;
			}
		}
		reset( $tables );
		foreach( $tables as $t )
		{
			$this->db->query("DROP TABLE " . $t . "");
		}
	}

	function index()
	{
	// check if already setup
		if( $this->is_setup() )
		{
			ci_redirect();
			return;
		}

		$this_module = CI::$APP->router->fetch_module();
		if( $this_module )
		{
			$this->data['include'] = $this_module . '/setup';
		}
		else
		{
			$this->data['include'] = 'setup';
		}
		$this->load->view( '_layout/index_no_menu', $this->data );
	}

	function run()
	{
		$app = $this->config->item('nts_app');
		$validation = array(
		   array(
				'field'   => 'first_name',
				'label'   => 'lang:user_first_name',
				'rules'   => 'trim|required'
				),
		   array(
				'field'   => 'last_name',
				'label'   => 'lang:user_last_name',
				'rules'   => 'trim|required'
				),
		   array(
				'field'   => 'email',
				'label'   => 'lang:common_email',
				'rules'   => 'trim|required|valid_email'
				),
		   array(
				'field'   => 'password',
				'label'   => 'lang:common_password',
				'rules'   => 'trim|required|matches[confirm_password]'
				),
		   array(
				'field'   => 'confirm_password',
				'label'   => 'lang:common_password_confirm',
				'rules'   => 'trim|required'
				),
			);
		$fields = array('first_name', 'last_name', 'email', 'password', 'confirm_password');

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
			/* admin user */
				$this->load->model( 'User_model' );
				$this->User_model->from_array( $post );
				$this->User_model->level = USER_MODEL::LEVEL_ADMIN;

				if( $this->User_model->save() )
				{
					$email_from = $post['email'];
					$email_from_name = $post['first_name'] . ' ' . $post['last_name'];
				}
				else
				{
					$this->hc_form->set_errors( $this->User_model->error->all );
					$this->hc_form->set_defaults( $post );
					$setup_ok = FALSE;
				}

				if( $setup_ok )
				{
				/* default settings */
					$this->app_conf->set( 'email_from',			$email_from );
					$this->app_conf->set( 'email_from_name',	$email_from_name );

					$this->session->set_flashdata( 'message', lang('ok') );
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

	function set_include( $file )
	{
		$this->data['include'] = '';
		$this->data['include_submenu'] = '';
	}
}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */