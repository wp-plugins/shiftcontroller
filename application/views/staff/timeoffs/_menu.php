<?php
$menu = array(
	'index'	=> ci_anchor( array($this->conf['path']), '<i class="icon-list"></i>' . ' ' . lang('common_view') ),
	'add'	=> ci_anchor( array($this->conf['path'], 'add'), '<i class="icon-plus-sign"></i>' . ' ' . lang('common_add') ),
	);
?>
<?php require( NTS_SYSTEM_APPPATH . 'views/_boilerplate/_print_menu.php' ); ?>