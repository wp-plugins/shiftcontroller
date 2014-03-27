<?php
$config = array();

$CI =& ci_get_instance();
$ri = $CI->remote_integration();

if( (! $ri) OR (! $CI->config->item('ri_disable_loginlog')) )
{
	$config[ USER_MODEL::LEVEL_ADMIN . '/conf/loginlog' ] = array(
		'title'	=> '<i class="fa fa-list"></i> ' . 'Login Log',
		'link'	=> 'loginlog/admin',
		);
}
?>