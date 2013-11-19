<?php
$decorate = isset($_dropdown_decorate) ? $_dropdown_decorate : TRUE;
$main_toggler = isset($_dropdown_toggler) ? $_dropdown_toggler : 'a';

$status_class = ( $to->status == TIMEOFF_MODEL::STATUS_ACTIVE ) ? 'alert-archive-success' : '';
$status_class2 = array();
$status_class2[] = 'alert alert-archive';
$status_class2[] = $status_class;

if( count($conflicts) )
{
	$status_class2[] = 'alert-error2';
}
$status_class2 = join( ' ', $status_class2 );

$class = $decorate ? 'alert-condensed ' . $status_class2 : '';
?>
<?php
$menu = array();
$menu['1'] = array(
	'<i class="icon-coffee"></i> ' . 
	$this->hc_time->formatPeriodOfDay($to->start, $to->end),
	array(
		'title'	=> $to->prop_text('status'),
		'class'	=> $class
		)
	);

/* CONFLICTS */
if( $conflicts )
{
	$menu['1_10'] = array(
		'<i class="icon-exclamation-sign text-error"></i> ' . lang('shift_conflicts'),
		array(
			'title'	=> lang('shift_conflicts'),
			),
		);
	$count = 1;
	foreach( $conflicts as $c )
	{
		$title = '';
		$href = '';
		switch( $c->my_class() )
		{
			case 'timeoff':
				$title = $c->title(TRUE);
				$href = ci_site_url(array('admin/timeoffs/edit', $c->id));
				break;
			case 'shift':
				$title = $c->title(TRUE);
				$href = ci_site_url(array('admin/shifts/edit', $c->id));
				break;
		}
		$menu['1_10_' . $count++] = array(
			$title,
			array(
				'title'	=> $c->title(),
				'href'	=> $href,
				'class'	=> 'hc-parent-loader'
//				'class'	=> 'hc-modal'
				),
			);
		$count++;
	}
	$menu['1_20'] = 'divider';
}

/* STATUS */
$menu['1_30'] = array(
	'<i class="icon-flag ' . $status_class . '"></i> ' . lang('common_set_status'),
	array(
		'title'	=> lang('common_set_status'),
		),
	);

if( $to->status != TIMEOFF_MODEL::STATUS_ACTIVE )
{
	$menu['1_30_10'] = array(
		$to->prop_text('status', TRUE, TIMEOFF_MODEL::STATUS_ACTIVE),
		array(
			'href'	=> ci_site_url( array('admin/timeoffs/save', $to->id, 'status', TIMEOFF_MODEL::STATUS_ACTIVE) ),
			'title'	=> lang('timeoff_status_active'),
			),
		);
}
else
{
	$menu['1_30_10'] = array(
		$to->prop_text('status', TRUE, TIMEOFF_MODEL::STATUS_PENDING),
		array(
			'href'	=> ci_site_url( array('admin/timeoffs/save', $to->id, 'status', TIMEOFF_MODEL::STATUS_PENDING) ),
			'title'	=> lang('timeoff_status_pending'),
			),
		);
}
$menu['1_30_20'] = array(
	$to->prop_text('status', TRUE, TIMEOFF_MODEL::STATUS_DENIED),
	array(
		'href'	=> ci_site_url( array('admin/timeoffs/save', $to->id, 'status', TIMEOFF_MODEL::STATUS_DENIED) ),
		'title'	=> lang('timeoff_status_denied'),
		),
	);

/* EDIT */
$menu['1_50'] = array(
	'<i class="icon-edit"></i> ' . lang('common_edit'),
	array(
		'href'	=> ci_site_url( array('admin/timeoffs/edit', $to->id) ),
		'title'	=> lang('common_edit'),
		'class'	=> 'hc-parent-loader'
//		'class'	=> 'hc-modal'
		),
	);

/* DELETE */
$menu['1_100'] = 'divider';
$menu['1_110'] = array(
	'<i class="icon-remove text-error"></i> ' . lang('common_delete'),
	array(
		'href'	=> ci_site_url( array('admin/timeoffs/delete', $to->id) ),
		'title'	=> lang( 'common_delete' ),
		'class'	=> 'hc-confirm',
		)
	);
?>
<?php echo hc_dropdown_menu($menu, 'li', '', $main_toggler); ?>
