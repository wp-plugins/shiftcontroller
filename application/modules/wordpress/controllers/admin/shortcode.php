<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Wordpress_Shortcode_controller extends Backend_controller
{
	function index()
	{
		$app = $this->config->item('nts_app');
		$this->data[ 'shortcode' ] = '[' . $app . ']';
		$this->set_include( 'shortcode', 'wordpress/admin' );

		$this->load->view( $this->template, $this->data);
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */