<?php
/**
 * @package ShiftController
 * @author HitCode
 */
/*
Plugin Name: ShiftController
Plugin URI: http://www.shiftcontroller.com/
Description: Staff scheduling plugin.
Author: HitCode
Version: 2.3.7
Author URI: http://www.hitcode.com/
*/

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

register_uninstall_hook( __FILE__, array('ShiftController', 'uninstall') );

class ShiftController extends hcWpBase4
{
	public function __construct()
	{
		parent::__construct(
			strtolower(get_class()),
			__FILE__,
			'',
			'ci'
			);
		$this->query_prefix = '?/';
	}

	public function admin_menu()
	{
		parent::admin_menu();

		$menu_title = ucfirst($this->app);
		$page = add_menu_page(
			$menu_title,
			$menu_title,
			'read',
			$this->slug,
			array( $this, 'admin_view' ),
			'dashicons-calendar'
			);
	}

	static function uninstall( $prefix = 'shiftcontroller' )
	{
		$prefix = 'shiftcontroller';
		hcWpBase4::uninstall( $prefix );
	}
}

$sh = new ShiftController();
?>