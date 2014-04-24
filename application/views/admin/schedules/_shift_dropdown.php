<?php
$use_color_code = $this->app_conf->get( 'color_code_staff' );
if( ! isset($wide_view) )
{
	$wide_view = FALSE;
}

/* CLASS */
$class = array();
$class[] = 'dropdown-toggle';
$class[] = 'alert';
$class[] = 'alert-condensed';

$conflicts = $sh->conflicts( $this->data['shifts'], $this->data['timeoffs'] );
if( count($conflicts) )
	$class[] = 'alert-danger2';

if( $sh->user_id )
{
	if( $sh->status == SHIFT_MODEL::STATUS_ACTIVE )
		$class[] = 'alert-success';
	else
		$class[] = 'alert-info';
}
else
{
	if( $sh->status == SHIFT_MODEL::STATUS_ACTIVE )
		$class[] = 'alert-danger';
	else
		$class[] = 'alert-warning';
}
$class = join( ' ', $class );

$trade_label = '';
$trade_icon = '';
if( $sh->user_id && $sh->has_trade && $this->hc_modules->exists('shift_trades') )
{
	$trade_label = lang('trade_request');
	$trade_icon = '<i class="fa fa-exchange"></i>';
}

/* TITLES */
$title = array();

/* TITLE - TIME */
if( in_array('time', $titles) )
{
	$time_key = $sh->start . '-' . $sh->end;
	$time_title = isset($shift_template_titles[$time_key]) ? $shift_template_titles[$time_key] : $this->hc_time->formatTimeOfDay($sh->start) . ' - ' . $this->hc_time->formatTimeOfDay($sh->end);
//	$time_title = '<i class="fa fa-clock-o"></i> ' . $time_title;
	$time_title = $time_title;
	$title['time'] = $time_title;
}

/* TITLE - LOCATION */
if( in_array('location', $titles) )
{
	$icon = ( $trade_icon && (! in_array('staff', $titles)) ) ? $trade_icon : '<i class="fa fa-home"></i>';
	$title['location'] = $icon . ' ' . $sh->location_name;
}

/* TITLE - STAFF */
if( in_array('staff', $titles) )
{
	/* TITLE - STAFF */
	$icon = $trade_icon ? $trade_icon : '<i class="fa fa-user"></i>';
	if( $sh->user_id && isset($staffs[$sh->user_id]) )
	{
		$title['staff'] = $icon . ' ' . $staffs[$sh->user_id]->full_name();
	}
	elseif( count($titles) <= 1 )
	{
		$title['staff'] = $icon . ' ' . '________';
	}
	else
	{
		$title['staff'] = '';
	}
}

$final_title = array();
$final_title[] = '<ul class="list-unstyled">';

if( isset($title['location']) && isset($title['staff']) )
{
	$final_title[] = '<li>';
	$final_title[] = $title['location'];
	$final_title[] = '</li>';
}

if( $wide_view && ( isset($title['staff']) OR isset($title['location']) ) )
{
	$final_title[] = '<li>';
	$final_title[]		= '<ul class="list-inline">';

							list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $title['time'] );
	$final_title[]			= '<li class="squeeze-in pull-left" style="width: 50%;" title="' . $this_title_title . '">';
	$final_title[]				= $title['time'];
	$final_title[]			= '</li>';

							$this_title = '&nbsp;';
							if( isset($title['staff']) )
								$this_title = $title['staff'];
							elseif( isset($title['location']) )
								$this_title = $title['location'];
							list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $this_title );
	$final_title[]			= '<li class="squeeze-in pull-right" style="width: 50%;" title="' . $this_title_title . '">';
	$final_title[]				= $this_title;
	$final_title[]			= '</li>';

	$final_title[]		= '</ul>';
	$final_title[]		= '<div class="clearfix"></div>';
	$final_title[] = '</li>';
}
else
{
	if( isset($title['time']) )
	{
		$final_title[] = '<li>';
		$final_title[] = $title['time'];
		$final_title[] = '</li>';
	}
	if( isset($title['staff']) )
	{
		$final_title[] = '<li>';
		$final_title[] = $title['staff'];
		$final_title[] = '</li>';
	}
	elseif( isset($title['location']) )
	{
		$final_title[] = '<li>';
		$final_title[] = $title['location'];
		$final_title[] = '</li>';
	}
}
$final_title[] = '</ul>';

$title = join( '', $final_title );
?>

<?php
/* MENU */
$menu = array();

/* ASSIGN STAFF */
if( $sh->user_id )
{
	$menu[] = array(
		'href'	=> ci_site_url( array('admin/shifts/deleterel', $sh->id, 'user', $sh->user_id) ),
		'title'	=> '<i class="fa fa-sign-out text-warning"></i> ' . lang('shift_remove_staff'),
		);
	$menu[] = array(
		'href'	=> ci_site_url( array('admin/shifts/assign', $sh->id) ),
		'title'	=> '<i class="fa fa-sign-in"></i> ' . lang('shift_change_staff'),
		'class'	=> 'hc-ajax-loader'
		);
}
else
{
	$menu[] = array(
		'href'	=> ci_site_url( array('admin/shifts/assign', $sh->id) ),
		'title'	=> '<i class="fa fa-sign-in"></i> ' . lang('shift_assign_staff'),
		'class'	=> 'hc-ajax-loader'
		);
}

/* PUBLISH */
if( $sh->status == SHIFT_MODEL::STATUS_ACTIVE )
{
	$menu[] = array(
		'href'	=> ci_site_url( array('admin/shifts/publish', $sh->id) ),
		'title'	=> '<i class="fa fa-reply text-warning"></i> ' . lang('shift_unpublish'),
		);
}
else
{
	$menu[] = array(
		'href'	=> ci_site_url( array('admin/shifts/publish', $sh->id) ),
		'title'	=> '<i class="fa fa-check text-success"></i> ' . lang('shift_publish'),
		);
}

if( $conflicts )
{
	/* EDIT */
	$menu[] = '-divider-';
	$menu[] = array(
		'href'	=> ci_site_url( array('admin/shifts/edit', $sh->id, '#conflicts') ),
		'title'	=> '<i class="fa fa-exclamation-circle text-danger"></i> ' . lang('shift_conflicts'),
		'class'	=> 'hc-parent-loader'
		);
}

/* EDIT */
$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ci_site_url( array('admin/shifts/edit', $sh->id) ),
	'title'	=> '<i class="fa fa-edit"></i> ' . lang('common_edit'),
	'class'	=> 'hc-parent-loader'
	);

/* SHIFT TRADES */
if( $sh->user_id && $sh->has_trade && $this->hc_modules->exists('shift_trades') )
{
	$trade_menu = Modules::run('shift_trades/admin/trade_actions', $sh);
	if( $trade_menu )
	{
		$menu[] = '-divider-';
		$menu = array_merge( $menu, $trade_menu );
	}
}

/* DELETE */
$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ci_site_url( array('admin/shifts', 'delete', $sh->id) ),
	'title'	=> '<i class="fa fa-times text-danger"></i> ' . lang('shift_delete'),
	'class'	=> 'hc-confirm',
	);

/* add color to border to highlight different staff */
$more_style = '';
if( $use_color_code )
{
	if( (! isset($current_staff)) && ($staff_count > 1) )
	{
		$more_style = '';
		if( $sh->user_id )
		{
			$random_color = Hc_lib::random_html_color( $sh->user_id );
			$more_style = 'border-left: ' . $random_color . ' 5px solid;';
		}
	}
}
?>

<a class="<?php echo $class; ?>" href="#" data-toggle="dropdown" style="<?php echo $more_style; ?>">
	<?php echo $title; ?>
</a>

<?php
echo Hc_html::dropdown_menu($menu);
?>