<?php
/**
 * @package ShiftController
 * @author ShiftController
 * @version 2.2.4
 */
/*
Plugin Name: ShiftController
Plugin URI: http://www.shiftcontroller.com/
Description: Staff scheduling plugin.
Author: ShiftController
Version: 2.2.4
Author URI: http://www.shiftcontroller.com/
*/
error_reporting( E_ERROR & ~E_NOTICE );

global $wp_version;
if (version_compare($wp_version, "3.3", "<"))
{
	exit('ShiftController requires WordPress 3.3 or newer, yours is ' . $wp_version);
}

if( file_exists(dirname(__FILE__) . '/db.php') )
{
	$nts_no_db = TRUE;
	include_once( dirname(__FILE__) . '/db.php' );
}

if( defined('NTS_DEVELOPMENT') )
	$happ_path = NTS_DEVELOPMENT;
else
	$happ_path = dirname(__FILE__) . '/happ';
include_once( $happ_path . '/hclib/hcWpBase.php' );

class ShiftController extends hcWpBase2
{
	var $happ_path = '';
	var $happ_web_dir = '';

	public function __construct()
	{
		if( defined('NTS_DEVELOPMENT') )
		{
			$this->happ_path = NTS_DEVELOPMENT;
			$this->happ_web_dir = 'http://localhost';
		}
		else
		{
			$this->happ_path = dirname(__FILE__) . '/happ';
			$this->happ_web_dir = plugins_url('', __FILE__);
		}

		$GLOBALS['NTS_APPPATH'] = dirname(__FILE__) . '/application';

		parent::__construct( 
			strtolower(get_class()),
			dirname(__FILE__),
			array(),
			TRUE
			);
		$this->query_prefix = '?/';

		require( $this->happ_path . '/assets/files.php' );
		reset( $css_files );
		foreach( $css_files as $f )
		{
			if( is_array($f) )
				$f[0] = $this->happ_web_dir . '/' . $f[0];
			else
				$f = $this->happ_web_dir . '/' . $f;
			$this->register_admin_style($f);
		}

		reset( $js_files );
		foreach( $js_files as $f )
		{
			if( is_array($f) )
				$f[0] = $this->happ_web_dir . '/' . $f[0];
			else
				$f = $this->happ_web_dir . '/' . $f;
			$this->register_admin_script($f);
		}

		add_shortcode( $this->app, array($this, 'front_view'));
		add_action('wp', array($this, 'front_init') );
		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
	}

	public function admin_menu()
	{
		$page = add_menu_page(
			'ShiftController',
			'ShiftController',
			'read',
			$this->app,
			array( $this, 'admin_view' )
			);
	}

	public function admin_init()
	{
		if( $this->is_me_admin() )
		{
			parent::admin_init();
		// action
			require( $this->happ_path . '/application/index_action.php' );
		}
	}

	public function front_init()
	{
		if( ! is_admin() )
		{
			if( parent::front_init() )
			{
				$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_USER_LEVEL'] = 0;
			// action
				require( $this->happ_path . '/application/index_action.php' );
				$GLOBALS['NTS_CONFIG'][$this->app]['ACTION_STARTED'] = 1;
			}
		}
	}

	public function admin_view()
	{
		$file = $this->happ_path . '/application/index_view.php';
		require( $file );
	}

	public function front_view()
	{
		if( 
			isset($GLOBALS['NTS_CONFIG'][$this->app]['ACTION_STARTED']) && 
			$GLOBALS['NTS_CONFIG'][$this->app]['ACTION_STARTED']
			)
		{
			$file = $this->happ_path . '/application/index_view.php';
			require( $file );
		}
	}
}

$sh = new ShiftController();
?>