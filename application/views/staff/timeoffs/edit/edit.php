<?php
$conflicts = $object->conflicts();
$notes_count = 0;
if( $this->hc_modules->exists('notes') )
	$notes_count = $object->note->count();

$tabs = array();
$tab_content = array();

/* EDIT */
$tabs['edit'] = '<i class="icon-edit"></i> ' . lang('common_edit');

$tab_content['edit'] = array();
$tab_content['edit'][] = 
	form_open( 
		join('/', array($this->conf['path'], 'save', $object->id)),
		array(
			'class' => 'form-horizontal form-condensed'
			)
		)
	;
$tab_content['edit'][] = 
	hc_bootstrap::input(
		$this->hc_form->input( $fields['id'] ),
		$fields['id']['label']
		) 
	;

if( $object->date == $object->date_end )
{
	$tab_content['edit'][] = 
		hc_bootstrap::input(
			$this->hc_form->input( $fields['date'] ),
			$fields['date']['label']
			) . 
		hc_bootstrap::input(
			$this->hc_form->input($fields['start']) . 
			' - ' .
			$this->hc_form->input($fields['end'])
			,
			lang('time'),
			$this->hc_form->error('start') OR $this->hc_form->error('end')
			)
		;
}
else
{
	$tab_content['edit'][] = 
		hc_bootstrap::input(
			$this->hc_form->input($fields['date']) . 
			' - ' .
			$this->hc_form->input($fields['date_end'])
			,
			lang('time_dates'),
			$this->hc_form->error('date') OR $this->hc_form->error('date_end')
			)
		;
}

$tab_content['edit'][] = 
	hc_bootstrap::form_actions(
		form_submit(
			array(
				'name' => 'submit',
				'class' => 'btn btn-primary'
				),
			lang('common_save')
			) . '&nbsp;' . 
		ci_anchor( 
			array($this->conf['path'], 'delete', $object->id),
			lang('common_delete'),
			'class="btn btn-danger hc-confirm"'
			)
		)
	;
$tab_content['edit'][] = form_close();

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
		'content'	=> Modules::run('notes/admin/index', 'timeoff', $object->id),
		'attr'		=> array(
			'class'		=> 'hc-target',
			'data-src'	=> ci_site_url(array('notes/admin/index', 'timeoff', $object->id)),
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