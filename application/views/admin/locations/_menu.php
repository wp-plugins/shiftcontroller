<?php
$menu = array(
	'index'	=> array(
		array($this->conf['path']),
		'<i class="fa fa-list"></i>' . ' ' . lang('common_view')
		),
	'add'	=> array(
		array($this->conf['path'], 'add'),
		'<i class="fa fa-plus-square-o"></i>' . ' ' . lang('common_add')
		),
	);
?>
<?php require( NTS_SYSTEM_APPPATH . 'views/_boilerplate/_print_menu.php' ); ?>