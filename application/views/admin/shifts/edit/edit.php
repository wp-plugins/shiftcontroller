<?php
$conflicts = $object->conflicts();

$tabs = array();
$tab_content = array();

/* EDIT */
$tabs['edit'] = '<i class="icon-edit"></i> ' . lang('common_edit');
if( count($shift_templates) )
{
	$templates_label = array();
	$templates_label[] = '<div class="dropdown">';
	$templates_label[] = '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . lang('time') . ' <b class="caret"></b></a>';
	$templates_label[] = '<ul class="dropdown-menu" role="menu">';
	foreach( $shift_templates as $sht )
	{
		$templates_label[] = '<li>';
		$templates_label[] = '<a href="#" class="hc-shift-templates" data-start="' . $sht->start . '" data-end="' . $sht->end . '">';
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

$tab_content['edit'] = 
	form_open( 
		join('/', array($this->conf['path'], 'save', $object->id)),
		array(
			'class' => 'form-horizontal form-condensed'
			)
		) . 
	hc_bootstrap::input(
		$this->hc_form->input( $fields['id'] ),
		$fields['id']['label']
		) . 
	hc_bootstrap::input(
		$this->hc_form->input( $fields['date'] ),
		$fields['date']['label']
		) . 
	hc_bootstrap::input(
		$this->hc_form->input($fields['start']) . 
		' - ' .
		$this->hc_form->input($fields['end'])
		,
		$templates_label,
		$this->hc_form->error('start') OR $this->hc_form->error('end')
		) . 
	hc_bootstrap::input(
		$this->hc_form->input( $fields['location'] ),
		$fields['location']['label'],
		$this->hc_form->error('location'),
		$fields['location']
		) . 
	hc_bootstrap::input(
		$this->hc_form->input( $fields['user'] ),
		$fields['user']['label']
		);

$btns = array();
$btns[] = form_submit(
	array(
		'name' => 'submit',
		'class' => 'btn btn-primary'
		),
	lang('common_save')
	);

if( $object->status == SHIFT_MODEL::STATUS_DRAFT )
{
	$btns[] = ci_anchor( 
		array($this->conf['path'], 'save', $object->id, 'status', SHIFT_MODEL::STATUS_ACTIVE),
		'<i class="icon-ok"></i> ' . lang('shift_publish'),
		'class="btn btn-success"'
		);
}

$btns[] = ci_anchor( 
	array($this->conf['path'], 'delete', $object->id),
	lang('common_delete'),
	'class="btn btn-danger hc-confirm"'
	);
$btns = join( '&nbsp;', $btns );

$tab_content['edit'] .= 
	hc_bootstrap::form_actions(
		$btns
		)
	;

$tab_content['edit'] .= form_close();

/* CONFLICTS */
if( $conflicts )
{
	$tabs['conflicts'] = '<i class="icon-exclamation-sign text-error"></i> ' . lang('shift_conflicts');

	$targets = array(
		'timeoff'	=> 'admin/timeoffs/edit',
		'shift'		=> 'admin/shifts/edit'
		);
	$tab_content['conflicts'] = array();
	$tab_content['conflicts'][] = '<ul class="unstyled">';
	foreach( $conflicts as $c )
	{
		$tab_content['conflicts'][] = '<li class="alert alert-error">';
		$tab_content['conflicts'][] = ci_anchor( 
			array($targets[$c->my_class()], $c->id),
			$c->title(TRUE)
//			$c->title(TRUE),
//			'class="hc-modal"'
			);
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
	$notes_count = $object->note->count();
	$tabs['notes'] = '<i class="icon-comment-alt"></i> ' . lang('common_notes') . ' [' . $notes_count . ']';
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
	$this->hc_modules->exists('shift_trades') && 
	($trade_label = trim(Modules::run('shift_trades/admin/status_label', $object))) 
	)
{
	$tabs['trades'] = $trade_label;
	$tab_content['trades'] = 
		Modules::run('shift_trades/admin/view', $object)
		;
}

/* DELETE SERIES */
if( ($group_count > 1) && $this->hc_modules->exists('shift_groups') )
{
	$tabs['delete'] = '<i class="icon-remove text-error"></i> ' . lang('common_delete');
	$tab_content['delete'] = Modules::run('shift_groups/admin/delete_form', $object);
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

<script language="JavaScript">
jQuery('.hc-shift-templates').on('click', function(e)
{
	jQuery(this).closest('form').find('[name=start]').val( jQuery(this).data('start') );
	jQuery(this).closest('form').find('[name=end]').val( jQuery(this).data('end') );

	jQuery(this).closest('.dropdown').find('.dropdown-toggle').dropdown('toggle');
	return false;
});
</script>