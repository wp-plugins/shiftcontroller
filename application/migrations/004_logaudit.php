<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_logaudit extends CI_Migration {
	public function up()
	{
		$CI =& ci_get_instance();
		if( $CI->hc_modules->exists('logaudit') )
		{
			$module_dir = $CI->hc_modules->module_dir('logaudit');
			$migration_file = $module_dir . '/migrations/001_setup.php';
			require( $migration_file );
		}
	}

	public function down()
	{
	}
}
