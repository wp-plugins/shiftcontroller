<?php
$my_user_id = $this->auth->user()->id;
$is_my = ( $my_user_id == $sh->user_id ) ? TRUE : FALSE;
$conflicts = $is_my ? $sh->conflicts( $this->data['shifts'], $this->data['timeoffs'] ) : array();

$status_class = array();
if( $sh->user_id )
{
	if( count($conflicts) )
	{
		$status_class[] = ' alert-error2';
	}
}
else
{
	$status_class[] = 'alert-error';
}
?>
<?php
$menu = array();

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
		TRADE_MODEL::STATUS_APPROVED	=> 'info',
		TRADE_MODEL::STATUS_ACCEPTED	=> 'success'
		);
	$trade_icon = '<i class="icon-exchange text-' . $icon_class[$sh->trade_status] . '" title="' . lang('shift_has_trade') . '"></i>';
	if( $display != 'my' )
		$status_class[] = 'alert-' . $icon_class[$sh->trade_status];
	else
		$status_class[] = ( $sh->status == SHIFT_MODEL::STATUS_ACTIVE ) ? 'alert-success' : '';
}
else
{
	$status_class[] = ( $sh->status == SHIFT_MODEL::STATUS_ACTIVE ) ? 'alert-success' : '';
}

$status_class = join( ' ', $status_class );
$menu['1'] = array(
	'',
	array(
		'title'	=> $sh->prop_text('status'),
		'class'	=> 'alert alert-condensed ' . $status_class
		)
	);

/* add title */
$title = '';
$title = array(
	'staff'		=> '',
	'location'	=> '',
	);

$title['location'] .= $sh->location_name;

$add_title = '';
$add_title .= $trade_icon ? $trade_icon : '<i class="icon-home"></i>';

$add_title .= ' ' . $title['location'];

if( ! $is_my )
{
	$title['staff'] .= $sh->user->get()->full_name();
	$add_title .= '<br>' . '<i class="icon-signout text-error"></i> ' . $title['staff'];
	if( $sh->trade->get()->to_user->get()->exists() )
	{
		$add_title .= '<br>' . '<i class="icon-signin"></i> ';
		if( $sh->trade->to_user->id == $my_user_id )
		{
			$add_title .= lang('common_me');
		}
		else
		{
			$add_title .= $sh->trade->to_user->full_name();
		}
	}
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
				$href = ci_site_url(array('staff/timeoffs/edit', $c->id));
				break;
			case 'shift':
				$title = $c->title(TRUE);
				$href = ci_site_url(array('staff/shifts/edit', $c->id));
				break;
		}
		$menu['1_10_' . $count++] = array(
			$title,
			array(
				'title'	=> $c->title(),
				'href'	=> $href,
//				'class'	=> 'hc-modal'
				),
			);
		$count++;
	}
}

if( $sh->user_id && $this->hc_modules->exists('shift_trades') )
{
/* SHIFT TRADE - EXISTING */
	if( $trade_id && (! in_array($sh->trade_status, array(TRADE_MODEL::STATUS_DENIED, TRADE_MODEL::STATUS_COMPLETED))) )
	{
		switch( $sh->trade_status )
		{
			case TRADE_MODEL::STATUS_ACCEPTED:
				$menu['1_70'] = array(
					'<i class="icon-exchange text-' . $icon_class[$sh->trade_status] . '"></i> ' . lang('trade'),
					array(
						'title'	=> $sh->trade->prop_text('status', FALSE, $sh->trade_status),
						),
					);
				$to_user = $sh->trade->get()->to_user->get();
				if( $to_user->id == $my_user_id )
				{
					$menu['1_70_20'] = array(
						'<i class="icon-remove text-error"></i> ' . lang('trade_recall'),
						array(
							'title'	=> lang('trade_recall'),
							'href'	=> ci_site_url( array('shift_trades/staff', 'recallme', $sh->id) ),
							),
						);
				}
				else
				{
					$menu['1_70_10'] = array(
						'<i class="icon-user text-success"></i> ' . $to_user->title(),
						array(
							'title'	=> lang('trade_status_accepted'),
							),
						);
					$menu['1_70_20'] = array(
						'<i class="icon-remove text-error"></i> ' . lang('trade_recall'),
						array(
							'title'	=> lang('trade_recall'),
							'href'	=> ci_site_url( array('shift_trades/staff', 'recall', $sh->id) ),
							),
						);
				}
				break;

			case TRADE_MODEL::STATUS_PENDING :
			case TRADE_MODEL::STATUS_APPROVED :
				$menu['1_70'] = array(
					'<i class="icon-exchange text-' . $icon_class[$sh->trade_status] . '"></i> ' . lang('trade'),
					array(
						'title'	=> $sh->trade->prop_text('status', FALSE, $sh->trade_status),
						),
					);
				if( $is_my )
				{
					$menu['1_70_10'] = array(
						'<i class="icon-remove text-error"></i> ' . lang('trade_recall'),
						array(
							'title'	=> lang('trade_recall'),
							'href'	=> ci_site_url( array('shift_trades/staff', 'recall', $sh->id) ),
							),
						);
				}
				else
				{
					$menu['1_70_10'] = array(
						'<i class="icon-ok text-success"></i> ' . lang('trade_pick_up'),
						array(
							'title'	=> lang('trade_pick_up'),
							'href'	=> ci_site_url( array('shift_trades/staff', 'pickup', $sh->id) ),
							),
						);
				}
				break;
		}
	}
	/* LIST FOR TRADE */
	elseif( $sh->status == SHIFT_MODEL::STATUS_ACTIVE )
	{
		$menu['1_70'] = array(
			'<i class="icon-exchange text-info"></i> ' . lang('shift_list_trade'),
			array(
				'title'	=> lang('shift_list_trade'),
				'href'	=> ci_site_url( array('shift_trades/staff', 'list_trade', $sh->id) ),
				),
			);
	}
}
?>

<?php echo hc_dropdown_menu($menu, 'li'); ?>
