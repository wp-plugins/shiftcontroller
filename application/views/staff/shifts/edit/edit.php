<?php
$conflicts = $object->conflicts();
$notes_count = 0;
if( $this->hc_modules->exists('notes') )
	$notes_count = $object->note->count();

$tabs = array();
$tab_content = array();

/* EDIT */
$tabs['edit'] = '<i class="icon-edit"></i> ' . lang('common_view');

$this->hc_time->setDateDb( $object->date );

$tab_content['edit'] = array();
$tab_content['edit'][] = 
	'<p>' . $object->prop_text('status', TRUE) .
	'<p>' . '<i class="icon-calendar"></i> ' . $this->hc_time->formatDate() . 
	'<p>' . '<i class="icon-time"></i> ' . $this->hc_time->formatTimeOfDay($object->start) . ' - ' .  $this->hc_time->formatTimeOfDay($object->end) . 
	'<p>' . '<i class="icon-home"></i> ' . $object->location->get()->title()
	;

$tab_content['edit'] = join( "\n", $tab_content['edit'] );

/* CONFLICTS */
if( $conflicts )
{
	$tabs['conflicts'] = '<i class="icon-exclamation-sign text-error"></i> ' . lang('shift_conflicts');

	$targets = array(
		'timeoff'	=> 'staff/timeoffs/edit',
//		'shift'		=> 'staff/shifts/edit'
		);
	$tab_content['conflicts'] = array();
	$tab_content['conflicts'][] = '<ul class="unstyled">';
	foreach( $conflicts as $c )
	{
		$tab_content['conflicts'][] = '<li class="alert alert-error">';
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
	$tabs['_conflicts'] = '<i class="icon-ok text-success"></i> ' . lang('shift_no_conflicts');
}

/* NOTES */
if( $this->hc_modules->exists('notes') )
{
	$tabs['notes'] = '<i class="icon-comment-alt"></i> ' . lang('common_notes') . ' [' . $notes_count . ']';
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