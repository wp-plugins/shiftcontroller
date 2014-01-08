<?php
$config = array();
$config[User_model::LEVEL_ADMIN]['conf'] = array(
	lang('menu_conf'),
	'',
	100,
	'wordpress' => array(
		'Shortcode',
		'wordpress/admin/shortcode',
		100
		),
	);
?>