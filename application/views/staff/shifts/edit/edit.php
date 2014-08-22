<?php
$show_end_time_for_staff = $this->app_conf->get( 'show_end_time_for_staff' );

$conflicts = $object->conflicts();
$notes_count = 0;
if( $this->hc_modules->exists('notes') )
{
	$notes = $this->access_manager->filter_see( $object->note->get()->all );
	$notes_count = count($notes);
}

$tabs = array();
$tab_content = array();

/* EDIT */
$tabs['edit'] = '<i class="fa fa-edit"></i> ' . lang('common_view');

$this->hc_time->setDateDb( $object->date );
$date_view = $this->hc_time->formatDateFull();

$time_view = $this->hc_time->formatTimeOfDay($object->start);
if( $show_end_time_for_staff )
{
	$time_view .= ' - ' .  $this->hc_time->formatTimeOfDay($object->end);
}

$tab_content['edit'] = array();
$tab_content['edit'][] = 
	'<ul class="list-unstyled list-separated">' . 
		'<li>' . 
			$object->prop_text('status', TRUE) .
		'</li>' . 
		'<li>' . 
			'<i class="fa-fw fa fa-calendar"></i> ' . $date_view . 
		'</li>' . 
		'<li>' . 
			'<i class="fa-fw fa fa-clock-o"></i> ' . $time_view . 
		'</li>' . 
		'<li>' . 
			'<i class="fa-fw fa fa-home"></i> ' . $object->location->get()->title() .
		'</li>' . 
	'</ul>'
	;

$tab_content['edit'] = join( "\n", $tab_content['edit'] );

/* CONFLICTS */
if( $conflicts )
{
	$tabs['conflicts'] = '<i class="fa fa-exclamation-circle text-danger"></i> ' . lang('shift_conflicts');

	$targets = array(
		'timeoff'	=> 'staff/timeoffs/edit',
//		'shift'		=> 'staff/shifts/edit'
		);
	$tab_content['conflicts'] = array();
	$tab_content['conflicts'][] = '<ul class="list-unstyled">';
	foreach( $conflicts as $c )
	{
		$tab_content['conflicts'][] = '<li class="alert alert-danger">';
		if( isset($targets[$c->my_class()]) )
		{
			$tab_content['conflicts'][] = ci_anchor( 
				array($targets[$c->my_class()], $c->id),
				$c->title(TRUE)
				);
		}
		else
		{
			$tab_content['conflicts'][] = $c->title(TRUE);
		}
		$tab_content['conflicts'][] = '</li>';
	}
	$tab_content['conflicts'][] = '</ul>';
	$tab_content['conflicts'] = join( "\n", $tab_content['conflicts'] );
}
else
{
	$tabs['_conflicts'] = '<i class="fa fa-check text-success"></i> ' . lang('shift_no_conflicts');
}

/* NOTES */
if( $this->hc_modules->exists('notes') )
{
	$tabs['notes'] = '<i class="fa fa-comment-o"></i> ' . lang('common_notes') . ' [' . $notes_count . ']';
	$tab_content['notes'] = array(
		'content'	=> Modules::run('notes/admin/index', $object->my_class(), $object->id),
		'attr'		=> array(
			'class'		=> 'hc-target',
			'data-src'	=> ci_site_url(array('notes/admin/index', $object->my_class(), $object->id)),
			),
		);
}
?>

<?php
echo Hc_bootstrap::nav_tabs(
	$tabs
	);
?>
<?php
echo hc_bootstrap::tab_content(
	$tab_content
	);
?>