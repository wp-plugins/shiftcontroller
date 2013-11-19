<?php
if( ! isset($display_as) )
	$display_as = $display;

$conflicts = $sh->conflicts( $this->data['shifts'], $this->data['timeoffs'] );

if( $sh->user_id )
{
	$status_class = ( $sh->status == SHIFT_MODEL::STATUS_ACTIVE ) ? 'alert-success' : '';
	if( count($conflicts) )
	{
		$status_class .= ' alert-error2';
	}
}
else
{
	$status_class = 'alert-error';
}
?>
<?php
$menu = array();

$menu['1'] = array(
	'',
	array(
		'title'	=> $sh->prop_text('status'),
		'class'	=> 'alert alert-condensed ' . $status_class
		)
	);

$trade_icon = '';
$trade_id = $sh->trade_id ? $sh->trade_id : 0;
if(
	$trade_id && 
	(! in_array($sh->trade_status, array(
		TRADE_MODEL::STATUS_DENIED,
		TRADE_MODEL::STATUS_COMPLETED
		)
	))
	)
{
	$icon_class = array(
		TRADE_MODEL::STATUS_PENDING		=> 'error',
		TRADE_MODEL::STATUS_APPROVED	=> 'warning',
		TRADE_MODEL::STATUS_ACCEPTED	=> 'success'
		);
	$trade_icon = '<i class="icon-exchange text-' . $icon_class[$sh->trade_status] . '" title="' . lang('shift_has_trade') . '"></i>';
}

/* add title */
$title = '';
$title = array(
	'staff'		=> '',
	'location'	=> '',
	);

$title['location'] .= $sh->location_name;
if( $sh->user_id && isset($staffs[$sh->user_id]) )
{
	$title['staff'] .= $staffs[$sh->user_id]->full_name();
}
else
{
	$title['staff'] .= '________';
}

$add_title = '';
switch( $display_as )
{
	case 'staff':
		$add_title .= $trade_icon ? $trade_icon : '<i class="icon-home"></i>';
		$add_title .= ' ' . $title['location'];
		break;

	case 'location':
		$add_title .= $trade_icon ? $trade_icon : '<i class="icon-user"></i>';
		$add_title .= ' ' . $title['staff'];
		break;

	case 'all':
		$add_title .= $trade_icon ? $trade_icon : '<i class="icon-user"></i>';
		$add_title .= ' ' . $title['staff'];
		break;

	case 'browse':
		$add_title .= '<i class="icon-home"></i>';
		$add_title .= ' ' . $title['location'];
		$add_title .= '<br>';
		$add_title .= $trade_icon ? $trade_icon : '<i class="icon-user"></i>';
		$add_title .= ' ' . $title['staff'];
		break;
}

$menu['1'][0] .= $add_title;

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
}

/* PUBLISH */
if( $sh->user_id )
{
	if( $sh->status != SHIFT_MODEL::STATUS_ACTIVE )
	{
		$menu['1_20'] = array(
			'<i class="icon-ok text-success"></i> ' . lang('shift_publish'),
			array(
				'href'	=> ci_site_url( array('admin/shifts/publish', $sh->id) ),
				'title'	=> lang('shift_publish'),
				),
			);
	}
	else
	{
		$menu['1_20'] = array(
			'<i class="icon-reply text-warning"></i> ' . lang('shift_unpublish'),
			array(
				'href'	=> ci_site_url( array('admin/shifts/publish', $sh->id) ),
				'title'	=> lang('shift_unpublish'),
				),
			);
	}
}
/* ASSIGN STAFF */
else
{
	$free_staff = $sh->find_staff();
	if( $free_staff )
	{
		$menu['1_20'] = array(
			'<i class="icon-signin"></i> ' . lang('shift_assign_staff'),
			array(
				'title'	=> lang('shift_assign_staff'),
				),
			);
		$count = 1;
		foreach( $free_staff as $st )
		{
			$href = ci_site_url( 
				array(
					'admin/shifts/save',
					$sh->id,
					'user', $st->id,
					)
				);
			if( $st->warning )
			{
				if( ($st->warning->start <= $sh->start) && ($st->warning->end >= $sh->end) )
					$warning_level = 'text-error';
				else
					$warning_level = 'text-warning';
				$warning_label = $st->warning->title();

				$menu['1_20_' . $count] = array(
					'<i class="icon-user ' . $warning_level . '"></i> ' . $st->full_name() . '<br>' . $warning_label,
					array(
						'title'	=> $warning_label,
						'href'	=> $href,
						'class'	=> 'hc-confirm'
						),
					);
			}
			else
			{
				$menu['1_20_' . $count] = array(
					'<i class="icon-user text-success"></i> ' . $st->full_name(),
					array(
						'title'	=> lang('shift_assign_staff'),
						'href'	=> $href
						),
					);
			}
			$count++;
		}
	}
	else
	{
		$menu['1_20'] = array(
			'<i class="icon-exclamation-sign text-error"></i> ' . lang('shift_no_staff'),
			array(
				'title'	=> lang('shift_no_staff'),
				),
			);
	}
}

$menu['1_30'] = 'divider';

/* EDIT */
$menu['1_40'] = array(
	'<i class="icon-edit"></i> ' . lang('common_edit'),
	array(
		'href'	=> ci_site_url( array('admin/shifts/edit', $sh->id) ),
		'title'	=> lang('common_edit'),
		'class'	=> 'hc-parent-loader'
		),
	);

/* UNASSIGN STAFF */
if( $sh->user_id )
{
	$menu['1_50'] = array(
		'<i class="icon-signout text-warning"></i> ' . lang('shift_remove_staff'),
		array(
			'href'	=> ci_site_url( array('admin/shifts/deleterel', $sh->id, 'user', $sh->user_id) ),
			'title'	=> lang('shift_remove_staff'),
			),
		);
}

if( $sh->user_id && $this->hc_modules->exists('shift_trades') )
{
/* SHIFT TRADE - EXISTING */
	if( $trade_id && (! in_array($sh->trade_status, array(TRADE_MODEL::STATUS_DENIED, TRADE_MODEL::STATUS_COMPLETED))) )
	{
		$menu['1_60'] = 'divider';
		switch( $sh->trade_status )
		{
			case TRADE_MODEL::STATUS_ACCEPTED:
				$menu['1_70'] = array(
					'<i class="icon-exchange text-success"></i> ' . lang('trade'),
					array(
						'title'	=> $sh->trade->prop_text('status', FALSE, $sh->trade_status),
						),
					);
				$to_user = $sh->trade->get()->to_user->get();
				$menu['1_70_10'] = array(
					'<i class="icon-user text-success"></i> ' . $to_user->title(),
					array(
						'title'	=> lang('trade_status_accepted'),
						),
					);
					$menu['1_70_10_10'] = array(
						'<i class="icon-ok text-success"></i> ' . lang('trade_complete'),
						array(
							'title'	=> lang('trade_complete'),
							'href'	=> ci_site_url( array('shift_trades/admin', 'save', $trade_id, 'status', TRADE_MODEL::STATUS_COMPLETED) ),
							),
						);
					$menu['1_70_10_20'] = array(
						'<i class="icon-remove text-error"></i> ' . lang('common_reject'),
						array(
							'title'	=> lang('common_reject'),
							'href'	=> ci_site_url( array('shift_trades/admin', 'save', $trade_id, 'status', TRADE_MODEL::STATUS_APPROVED) ),
							),
						);
				$menu['1_70_20'] = array(
					'<i class="icon-remove text-error"></i> ' . lang('common_reject'),
					array(
						'title'	=> lang('common_reject'),
						'href'	=> ci_site_url( array('shift_trades/admin', 'save', $trade_id, 'status', TRADE_MODEL::STATUS_DENIED) ),
						),
					);
				break;

			case TRADE_MODEL::STATUS_PENDING :
				$menu['1_70'] = array(
					'<i class="icon-exchange text-error"></i> ' . lang('trade'),
					array(
						'title'	=> $sh->trade->prop_text('status', FALSE, $sh->trade_status),
						),
					);
				$menu['1_70_10'] = array(
					'<i class="icon-ok text-success"></i> ' . lang('common_approve'),
					array(
						'title'	=> lang('common_approve'),
						'href'	=> ci_site_url( array('shift_trades/admin', 'save', $trade_id, 'status', TRADE_MODEL::STATUS_APPROVED) ),
						),
					);
				$menu['1_70_20'] = array(
					'<i class="icon-remove text-error"></i> ' . lang('common_reject'),
					array(
						'title'	=> lang('common_reject'),
						'href'	=> ci_site_url( array('shift_trades/admin', 'save', $trade_id, 'status', TRADE_MODEL::STATUS_DENIED) ),
						),
					);
				break;

			case TRADE_MODEL::STATUS_APPROVED :
				$menu['1_70'] = array(
					'<i class="icon-exchange text-warning"></i> ' . lang('trade'),
					array(
						'title'	=> $sh->trade->prop_text('status', FALSE, $sh->trade_status),
						),
					);

			/* CHOOSE STAFF */
				$free_staff = $sh->find_staff();
				if( $free_staff )
				{
					$menu['1_70_10'] = array(
						'<i class="icon-signin"></i> ' . lang('shift_assign_staff'),
						array(
							'title'	=> lang('shift_assign_staff'),
							),
						);
					$count = 0;
					foreach( $free_staff as $st )
					{
						$add_url = ci_site_url( 
							array(
								'shift_trades/admin/save/' . $trade_id,
								'to_user',	$st->id,
								'status',	TRADE_MODEL::STATUS_COMPLETED,
								)
							);

						$count++;
						if( $st->warning )
						{
							if( ($st->warning->start <= $sh->start) && ($st->warning->end >= $sh->end) )
								$warning_level = 'label-important';
							else
								$warning_level = 'label-warning';
							$warning_label = lang('timeoff') . ': ' . $st->warning->title();

							$menu['1_70_10_'.$count] = array(
								'<span class="label ' . $warning_level . '"><i class="icon-user"></i></span> ' . $st->full_name() . '<br>' . $warning_label,
								array(
									'title'	=> lang('common_add'),
									'href'	=> $add_url,
									'class'	=> 'hc-confirm'
									),
								);
						}
						else
						{
							$menu['1_70_10_'.$count] = array(
								'<span class="label label-success"><i class="icon-user"></i></span> ' . $st->full_name(),
								array(
									'title'	=> lang('common_add'),
									'href'	=> $add_url,
									),
								);
						}
					}
				}

				$menu['1_70_20'] = array(
					'<i class="icon-remove text-error"></i> ' . lang('common_reject'),
					array(
						'title'	=> lang('common_reject'),
						'href'	=> ci_site_url( array('shift_trades/admin', 'save', $trade_id, 'status', TRADE_MODEL::STATUS_DENIED) ),
						),
					);
				break;
		}
	}
	/* LIST FOR TRADE - EXISTING */
	elseif( $sh->status == SHIFT_MODEL::STATUS_ACTIVE )
	{
		$menu['1_60'] = 'divider';
		$menu['1_70'] = array(
			'<i class="icon-exchange text-info"></i> ' . lang('shift_list_trade'),
			array(
				'title'	=> lang('shift_list_trade'),
				'href'	=> ci_site_url( array('shift_trades/admin', 'save', $trade_id, 'status', TRADE_MODEL::STATUS_APPROVED, 'shift', $sh->id) ),
				),
			);
	}
}

/* DELETE */
$menu['1_100'] = 'divider';
$menu['1_110'] = array(
	'<i class="icon-remove text-error"></i> ' . lang('shift_delete'),
	array(
		'href'	=> ci_site_url( array('admin/shifts', 'delete', $sh->id) ),
		'title'	=> lang( 'shift_delete' ),
		'class'	=> 'hc-confirm',
		)
	);
?>

<?php echo hc_dropdown_menu($menu, 'li'); ?>
