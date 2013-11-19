<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Test_controller extends CI_controller
{
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		ci_redirect('');
		return;

		$target1 = ci_site_url('load');
		$html  =<<<EOT
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
</head>
<body>
<script type="text/javascript" src="$target1"></script>
</body>
</html>

EOT;
		echo $html;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */