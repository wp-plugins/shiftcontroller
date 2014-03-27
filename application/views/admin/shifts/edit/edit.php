<?php
$conflicts = $object->conflicts();
$user = $object->user->get();
$user_id = $user->id;

$tabs = array();
$tab_content = array();

/* EDIT */
$tabs['edit'] = '<i class="fa fa-edit"></i> ' . lang('common_edit');
if( count($shift_templates) )
{
	$templates_label = array();
	$templates_label[] = '<div class="dropdown">';
	$templates_label[] = '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . lang('time') . ' <b class="caret"></b></a>';
	$templates_label[] = '<ul class="dropdown-menu" role="menu">';
	foreach( $shift_templates as $sht )
	{
		$end = ($sht->end > 24*60*60) ? ($sht->end - 24*60*60) : $sht->end;

		$templates_label[] = '<li>';
		$templates_label[] = '<a href="#" class="hc-shift-templates" data-start="' . $sht->start . '" data-end="' . $end . '">';
		$templates_label[] = $sht->name . '<br>' . hc_format_time_of_day($sht->start) . ' - ' . hc_format_time_of_day($sht->end);
		$templates_label[] = '</a>';
		$templates_label[] = '</li>';
	}
	$templates_label[] = '</ul>';
	$templates_label[] = '</div>';
	$templates_label = join( "\n", $templates_label );
}
else
{
	$templates_label = lang('time');
}

/* EDIT */
ob_start();
require( dirname(__FILE__) . '/edit_edit.php' );
$tab_content['edit'] = ob_get_contents();
ob_end_clean();

/* ASSIGN STAFF */
$label = $object->user_id ? lang('shift_change_staff') : lang('shift_assign_staff');
$tabs['assign'] = '<i class="fa fa-user"></i> ' . $label;
$tab_content['assign'] = 
	'<ul class="list-unstyled list-separated">' . 
	Modules::run('admin/shifts/edit/assign', $object->id) .
	'</u>'
	;

/* CONFLICTS */
if( $object->user_id )
{
	if( $conflicts )
	{
		$tabs['conflicts'] = '<i class="fa fa-exclamation-circle text-danger"></i> ' . lang('shift_conflicts');
		ob_start();
		require( dirname(__FILE__) . '/edit_conflicts.php' );
		$tab_content['conflicts'] = ob_get_contents();
		ob_end_clean();
	}
	else
	{
		$tabs['_conflicts'] = '<i class="fa fa-check text-success"></i> ' . lang('shift_no_conflicts');
	}
}

/* NOTES */
if( $this->hc_modules->exists('notes') )
{
	$notes_count = $object->note->count();
	$tabs['notes'] = '<i class="fa fa-comment-o"></i> ' . lang('common_notes') . ' [' . $notes_count . ']';
	$tab_content['notes'] = array(
		'content'	=> Modules::run('notes/admin/index', 'shift', $object->id),
		'attr'		=> array(
			'class'		=> 'hc-target',
			'data-src'	=> ci_site_url(array('notes/admin/index', 'shift', $object->id)),
			),
		);
}

/* TRADES */
if(
	$user_id && 
	$object->has_trade && 
	$this->hc_modules->exists('shift_trades')
	)
{
	$trade_menu = Modules::run('shift_trades/admin/trade_actions', $object);
	if( $trade_menu )
	{
		$tabs['trades'] = $trade_menu;
	}
}

/* HISTORY */
if(
	$this->hc_modules->exists('logaudit')
	)
{
	$tabs['history'] = '<i class="fa fa-list-ul"></i> ' . lang('common_history');
	$tab_content['history'] = 
		Modules::run('logaudit/admin/index', $object)
		;
}

/* DELETE SERIES */
if( ($group_count > 1) && $this->hc_modules->exists('shift_groups') )
{
	$tabs['delete'] = '<i class="fa fa-times text-danger"></i> ' . lang('common_delete');
	$tab_content['delete'] = Modules::run('shift_groups/admin/delete_form', $object);
}
?>

<?php
$tabs_id = hc_random();
echo Hc_bootstrap::nav_tabs(
	$tabs, '', '', $tabs_id
	);
?>
<?php
echo hc_bootstrap::tab_content(
	$tab_content
	);
?>

<script language="JavaScript">
if ( window.location.hash )
{
	jQuery('#<?php echo $tabs_id; ?> a[href="' + window.location.hash + '"]').tab("show");
}
</script>