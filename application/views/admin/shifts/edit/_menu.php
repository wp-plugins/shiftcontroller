<?php
$menu = array(
	'index'	=> ci_anchor( array('admin/schedules/index/all', $object->date), '<i class="fa fa-calendar"></i>' . ' ' . lang('schedules') ),
	);
?>
<?php require( NTS_SYSTEM_APPPATH . 'views/_boilerplate/_print_menu.php' ); ?>