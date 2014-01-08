<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Wordpress_Auth_controller extends Front_Controller
{
	function __construct()
	{
		$app = $this->config->item('nts_app');

	// redirect to wp login page
		$return_to = isset( $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] ) ? $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] : get_bloginfo('wpurl');
		$to = wp_login_url( $return_to );
		ci_redirect( $to, 'refresh');
		exit;
	}
}
