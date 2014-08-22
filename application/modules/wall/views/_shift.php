<?php
if( ! isset($wide_view) )
{
	$wide_view = FALSE;
}

$notes = array();
if( $this->hc_modules->exists('notes') )
{
	$notes = $this->access_manager->filter_see( $sh->note->get()->all );
}

$this->hc_time->setNow();
$today = $this->hc_time->formatDate_Db();

/* CLASS */
$class = array();
$class[] = 'alert';
$class[] = 'alert-condensed';

if( $sh->user_id )
{
	$class[] = ( $sh->status == SHIFT_MODEL::STATUS_ACTIVE ) ? 'alert-success' : 'alert-warning';
}
else
{
	$class[] = 'alert-danger';
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
	list( $this_title_title, $this_title_icon ) = Hc_lib::parse_icon( $title['location'] );
	$final_title[] = '<li class="squeeze-in" title="' . $this_title_title . '">';
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
							if( $trade_label )
								$this_title_title = $trade_label;
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


if( count($notes) > 0 )
{
	foreach( $notes as $n )
	{
		$final_title[] = '<li style="font-style: italic;">';
		$final_title[] = '<i class="fa-fw fa fa-comment-o"></i>';
		$final_title[] = $n->content;
		$final_title[] = '</li>';
	}
}

$final_title[] = '</ul>';

$title = join( '', $final_title );
?>
<div class="<?php echo $class; ?>">
	<?php echo $title; ?>

	<?php
	if(
		($today <= $sh->date) &&
		$this->auth &&
		$this->auth->user()
		) :
	?>
		<?php
		if( 
			(
				(! $sh->user_id) &&
				$this->app_conf->get('staff_pick_shifts')
			)
			OR
			(
				$sh->has_trade &&
				$this->hc_modules->exists('shift_trades')
			)
		) :
		?>
			<a class="btn btn-default btn-sm" href="<?php echo ci_site_url( array('staff/shifts', 'pickup', $sh->id) ); ?>">
				<i class="fa fa-check text-success"></i> <?php echo lang('shift_pick_up'); ?>
			</a>
		<?php endif; ?>
	<?php endif; ?>
</div>