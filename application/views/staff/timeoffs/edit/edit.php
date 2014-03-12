<?php
$conflicts = $object->conflicts();
$notes_count = 0;
if( $this->hc_modules->exists('notes') )
	$notes_count = $object->note->count();

$tabs = array();
$tab_content = array();

/* EDIT */
$tabs['edit'] = '<i class="fa fa-edit"></i> ' . lang('common_edit');

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
	Hc_html::wrap_input(
		$fields['id']['label'],
		$this->hc_form->build_input( $fields['id'] )
		) 
	;

if( $object->date == $object->date_end )
{
	$tab_content['edit'][] = 
		Hc_html::wrap_input(
			$fields['date']['label'],
			$this->hc_form->build_input( $fields['date'] )
			) . 
		Hc_html::wrap_input(
			lang('time'),
			array(
				$this->hc_form->build_input($fields['start']),
				' - ',
				$this->hc_form->build_input($fields['end'])
				)
			)
		;
}
else
{
	$tab_content['edit'][] = 
		Hc_html::wrap_input(
			lang('time_dates'),
			array(
				$this->hc_form->build_input($fields['date']),
				' - ',
				$this->hc_form->build_input($fields['date_end'])
				)
			)
		;
}

$tab_content['edit'][] = 
	Hc_html::wrap_input(
		'',
		form_submit(
			array(
				'name' => 'submit',
				'class' => 'btn btn-default'
				),
			lang('common_save')
			) . '&nbsp;' . 
		ci_anchor( 
			array($this->conf['path'], 'delete', $object->id),
			lang('common_delete'),
			'class="btn btn-danger btn-sm hc-confirm"'
			)
		)
	;
$tab_content['edit'][] = form_close();

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