<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Schedules_controller extends Backend_controller
{
	function __construct()
	{
		$this->conf = array(
			'path'	=> 'admin/schedules',
			);
		parent::__construct( User_model::LEVEL_MANAGER );

	/* check how many locations do we have */
		$lm = new Location_Model;
		$location_count = $lm->count();
		$this->data['location_count'] = $location_count;

	/* check how many staff do we have */
		$um = new User_Model;
		$staff_count = $um->count_staff();
		$this->data['staff_count'] = $staff_count;

		$um = new User_Model;
		$staffs = $um->get()->all;
		$this->data['staffs'] = array();
		foreach( $staffs as $sta )
		{
			$this->data['staffs'][ $sta->id ] = $sta;
		}

	/* also get all shift templates */
		$shift_template_titles = array();

		$stm = new Shift_Template_Model;
		$stm->get();
		foreach( $stm->get() as $st )
		{
//			$shift_template_titles[ $st->start . '-' . $st->end ] = $st->name;
		}
		$this->data['shift_template_titles'] = $shift_template_titles;
	}

	function publish()
	{
		$args = hc_parse_args( func_get_args() );

		$sm = new Schedule_Model;
		$sm->start = $args['start'];
		$sm->end = $args['end'];
		$sm->staff_id = isset($args['staff']) ? $args['staff'] : 0;
		$sm->location_id = isset($args['location']) ? $args['location'] : 0;

		$count = $sm->publish();

		if( $count > 0 )
		{
			$msg = array();
			$label = $count . ' ';
			$label .= ($count > 1) ? lang('shifts') : lang('shift');

			$msg[] = $label;
			$msg[] = lang('shift_publish');
			$msg[] = lang('common_ok');
			$msg = join( ': ', $msg );
			$this->session->set_flashdata( 'message', $msg );
		}

		$this->load->library('user_agent');
		if ($this->agent->is_referral())
		{
			$redirect_to = $this->agent->referrer();
		}
		else
		{
			$redirect_to = array('admin/schedules/index/all', $start_date);
		}
		$this->redirect( $redirect_to );
		return;
	}

	function publishdraft()
	{
		$args = hc_parse_args( func_get_args() );

		$sm = new Schedule_Model;
		$sm->start = $args['start'];
		$sm->end = $args['end'];
		$sm->staff_id = isset($args['staff']) ? $args['staff'] : 0;
		$sm->location_id = isset($args['location']) ? $args['location'] : 0;

		$count = $sm->publishdraft();

		if( $count > 0 )
		{
			$msg = array();
			$label = $count . ' ';
			$label .= ($count > 1) ? lang('shifts') : lang('shift');

			$msg[] = $label;
			$msg[] = lang('shift_publish');
			$msg[] = lang('common_ok');
			$msg = join( ': ', $msg );
			$this->session->set_flashdata( 'message', $msg );
		}

		$this->load->library('user_agent');
		if ($this->agent->is_referral())
		{
			$redirect_to = $this->agent->referrer();
		}
		else
		{
			$redirect_to = array('admin/schedules/index/all', $start_date);
		}
		$this->redirect( $redirect_to );
		return;
	}

	function unpublish( $start_date, $end_date )
	{
		$args = hc_parse_args( func_get_args() );

		$sm = new Schedule_Model;
		$sm->start = $args['start'];
		$sm->end = $args['end'];
		$sm->staff_id = isset($args['staff']) ? $args['staff'] : 0;
		$sm->location_id = isset($args['location']) ? $args['location'] : 0;

		$count = $sm->unpublish();

		if( $count > 0 )
		{
			$msg = array();
			$label = $count . ' ';
			$label .= ($count > 1) ? lang('shifts') : lang('shift');
			
			$msg[] = $label;
			$msg[] = lang('shift_unpublish');
			$msg[] = lang('common_ok');
			$msg = join( ': ', $msg );
			$this->session->set_flashdata( 'message', $msg );
		}

		$this->load->library('user_agent');
		if ($this->agent->is_referral())
		{
			$redirect_to = $this->agent->referrer();
		}
		else
		{
			$redirect_to = array('admin/schedules/index/all', $start_date);
		}
		$this->redirect( $redirect_to );
		return;
	}

	function delete( $start_date, $end_date )
	{
		$sm = new Shift_Model;
		$sm
			->where( 'date >=', $start_date )
			->where( 'date <=', $end_date )
			->get()
			->delete_all();

		$msg = array();
		$msg[] = lang('common_delete');
		$msg[] = lang('common_ok');
		$msg = join( ': ', $msg );
		$this->session->set_flashdata( 'message', $msg );

		$this->load->library('user_agent');
//		if ($this->agent->is_referral())
//		{
//			$redirect_to = $this->agent->referrer();
//		}
//		else
//		{
			$redirect_to = array('admin/schedules/index/browse', $start_date, $end_date);
//		}
		$this->redirect( $redirect_to );
		return;
	}

	private function _load_shifts( $date = 0, $staff_id = 0 )
	{
		if( $staff_id )
		{
			$um = new User_Model;
			$um->get_by_id( $staff_id );
			$shift_model = $um->shift;
			$timeoff_model = $um->timeoff;
		}
		else
		{
			$shift_model = new Shift_Model;
			$timeoff_model = new Timeoff_Model;
		}

		if( $date )
		{
			if( is_array($date) ) // to-from
			{
				$this->hc_time->setDateDb( $date[1] );
				$this->hc_time->modify( '+1 day' );
				$tomorrow = $this->hc_time->formatDate_Db();
				$this->hc_time->setDateDb( $date[0] );
				$this->hc_time->modify( '-1 day' );
				$yesterday = $this->hc_time->formatDate_Db();
			}
			else
			{
				$this->hc_time->setDateDb( $date );
				$this->hc_time->modify( '+1 day' );
				$tomorrow = $this->hc_time->formatDate_Db();
				$this->hc_time->setDateDb( $date );
				$this->hc_time->modify( '-1 day' );
				$yesterday = $this->hc_time->formatDate_Db();
			}

			$shift_model->where('date <=', $tomorrow);
			$shift_model->where('date >=', $yesterday);

			$timeoff_model->where('date <', $tomorrow);
			$timeoff_model->where('date_end >', $yesterday);
		}

		$shift_model
			->include_related( 'location', 'show_order' )
			->include_related( 'location', 'id' )
			->include_related( 'location', 'name' )
			->include_related( 'user', 'id' )
			->order_by( 'date', 'ASC' )
			->order_by( 'start', 'ASC' )
			->order_by( 'location_show_order', 'ASC' );

		$this->data['shifts'] = $shift_model
			->get()->all;

		$this->data['timeoffs'] = $timeoff_model
			->where_in('status', array(TIMEOFF_MODEL::STATUS_ACTIVE))
			->include_related( 'user', 'id' )
			->order_by( 'date', 'ASC' )
			->order_by( 'start', 'ASC' )
			->get()->all;

		/* load the shifts group ids count and dates*/
		$groups = $shift_model
			->select( 'group_id, COUNT(id) AS count, MIN(date) AS min_date, MAX(date) AS max_date' )
			->group_by( 'group_id' )
			->get();

		$this->data['shift_groups'] = array();
		foreach( $groups as $g )
		{
			if( ! $g->group_id )
				continue;
			$this->data['shift_groups'][$g->group_id] = array(
				'count'		=> $g->count,
				'min_date'	=> $g->min_date,
				'max_date'	=> $g->max_date,
				);
		}
	}

	function day_staff()
	{
		$args = $this->parse_args( func_get_args() );

		$display = isset($args['display']) ? $args['display'] : 'staff';
		$range = isset($args['range']) ? $args['range'] : 'week'; // or month
		$date = isset($args['start']) ? $args['start'] : '';
		$staff_id = isset($args['id']) ? $args['id'] : 0;

		$this->data['display'] = $display;
		$this->data['range'] = $range;
		$this->data['date'] = $date;

		$this->data['staff_id'] = $staff_id;

	/* load shifts if needed */
		if( ! isset($this->data['shifts']) )
		{
			$this->_load_shifts( $date );
		}

	/* my shifts */
		$this->data['my_shifts'] = array();
		/* filter all shifts */
		foreach( $this->data['shifts'] as $sh )
		{
			if( $sh->date > $date )
				break;
			if( $sh->date < $date )
				continue;
			if( $sh->user_id != $staff_id )
				continue;
			$this->data['my_shifts'][] = $sh;
		}

	/* my timeoffs */
		$this->data['my_timeoffs'] = array();
		/* filter all timeoffs */
		foreach( $this->data['timeoffs'] as $to )
		{
			if( $to->date > $date )
				break;
			if( $to->user_id != $staff_id )
				continue;
			if( $to->date_end < $date )
				continue;
			$this->data['my_timeoffs'][] = $to;
		}

		$this->data['date'] = $date;

		$this->set_include( 'day_staff' );
		$this->load->view( $this->template, $this->data);
	}

	function day_location()
	{
		$args = $this->parse_args( func_get_args() );

		$display = isset($args['display']) ? $args['display'] : 'location';
		$range = isset($args['range']) ? $args['range'] : 'week'; // or month
		$date = isset($args['start']) ? $args['start'] : '';
		$location_id = isset($args['id']) ? $args['id'] : 0;

		$this->data['display'] = $display;
		$this->data['range'] = $range;
		$this->data['date'] = $date;

		$this->data['location_id'] = $location_id;

	/* load shifts if needed */
		if( ! isset($this->data['shifts']) )
		{
			$this->_load_shifts( $date );
		}

	/* my shifts */
		$this->data['my_shifts'] = array();
		/* filter all shifts */
		foreach( $this->data['shifts'] as $sh )
		{
			if( $sh->date > $date )
				break;
			if( $sh->date < $date )
				continue;
			if( $sh->location_id != $location_id )
				continue;
			$this->data['my_shifts'][] = $sh;
		}

	/* my timeoffs */
		$this->data['my_timeoffs'] = array();

		$this->data['date'] = $date;

		$this->set_include( 'day_location' );
		$this->load->view( $this->template, $this->data);
	}

	function day()
	{
		$args = $this->parse_args( func_get_args() );

		$display = isset($args['display']) ? $args['display'] : 'calendar';
		$range = isset($args['range']) ? $args['range'] : 'week'; // or month
		$date = isset($args['start']) ? $args['start'] : '';

		$this->data['display'] = $display;
		$this->data['range'] = $range;
		$this->data['date'] = $date;

		$sm = new Shift_Model;

	/* load shifts if needed */
		if( ! isset($this->data['shifts']) )
		{
			$this->_load_shifts( $date );
		}

	/* filter all shifts */
		$this->data['my_shifts'] = array();
		foreach( $this->data['shifts'] as $sh )
		{
			if( $sh->date > $date )
				break;
			if( $sh->date < $date )
				continue;
			$this->data['my_shifts'][] = $sh;
		}

		$this->data['date'] = $date;

		$this->set_include( 'day' );
		$this->load->view( $this->template, $this->data);
	}

	function browse()
	{
		$start = $this->input->post( 'start' );
		$end = $this->input->post( 'end' );
		$display = $this->input->post( 'display' );
		$filter = $this->input->post( 'filter' );
		$id = $this->input->post( 'id' );
		if( ! $display )
			$display = 'stats';

		$redirect_to = array( 
			$this->conf['path'],
			'index',
			'display',	$display,
			'start',	$start,
			'end',		$end,
			);
		if( $filter )
		{
			$redirect_to[] = 'filter';
			$redirect_to[] = $filter;
		}
		if( $id )
		{
			$redirect_to[] = 'id';
			$redirect_to[] = $id;
		}

		$this->redirect( $redirect_to );
		return;
	}

	function status()
	{
		$args = hc_parse_args( func_get_args() );
		$count = array();
		$this->data['location_id'] = 0;
		$this->data['staff_id'] = 0;
		if( isset($args['staff']) && $args['staff'] )
		{
			$um = new User_Model;
			$um->get_by_id( $args['staff'] );
			$sm = $um->shift;
			$this->data['staff_id'] = $args['staff'];
		}
		elseif( isset($args['location']) && $args['location'] )
		{
			$lm = new Location_Model;
			$lm->get_by_id( $args['location'] );
			$sm = $lm->shift;
			$this->data['location_id'] = $args['location'];
		}
		else
		{
			$sm = new Shift_Model;
		}

	/* duration */
		$sm->clear();
		$sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->select_sum( 'end' )
			->select_sum( 'start' )
			->get();
		$count['duration'] = ($sm->end - $sm->start);

	/* ACTIVE	- published with staff */
		$sm->clear();
		$sm->include_related( 'user', 'id' );
		$count['active'] = $sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->where( 'status', SHIFT_MODEL::STATUS_ACTIVE )
			->where( 'user_id IS NOT ', 'NULL', FALSE )
			->count();

	/* OPEN		- published with no staff */
		$sm->clear();
		$sm->include_related( 'user', 'id' );
		$count['open'] = $sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->where( 'status', SHIFT_MODEL::STATUS_ACTIVE )
			->where( 'user_id IS ', 'NULL', FALSE )
			->count();

	/* PENDING	- not published with staff */
		$sm->clear();
		$count['pending'] = $sm
			->include_related( 'user', 'id' )
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->where( 'status <>', SHIFT_MODEL::STATUS_ACTIVE )
			->where( 'user_id IS NOT ', 'NULL', FALSE )
			->count();

	/* DRAFT	- not published with no staff */
		$sm->clear();
		$sm->include_related( 'user', 'id' );
		$count['draft'] = $sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->where( 'status <>', SHIFT_MODEL::STATUS_ACTIVE )
			->group_start()
				->or_where( 'user_id IS ', 'NULL', FALSE )
				->or_where( 'user_id', 0 )
			->group_end()
			->count();

	/* total */
		$sm->clear();
		$count['total'] = $sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->count();

		$this->data['count'] = $count;
		$this->data['start_date'] = $args['start'];
		$this->data['end_date'] = $args['end'];

		$this->set_include( 'status' );
		$this->load->view( $this->template, $this->data);
	}

	function index()
	{
		$args = $this->parse_args( func_get_args() );

		$display = isset($args['display']) ? $args['display'] : 'calendar';
		$filter = isset($args['filter']) ? $args['filter'] : 'all';
		$range = isset($args['range']) ? $args['range'] : 'week'; // or month
		$date = isset($args['start']) ? $args['start'] : '';
		$end_date = isset($args['end']) ? $args['end'] : '';

		$this->data['id'] = isset($args['id']) ? $args['id'] : 0;

	/* check if schedule for this date exists */
		if( $end_date )
		{
			$start_date = $date;
			if( $end_date < $start_date )
				$end_date = $start_date;
		}
		else
		{
			if( $date )
			{
				$this->hc_time->setDateDb( $date );
			}
			else
			{
				$this->hc_time->setNow();
			}

			switch( $range )
			{
				case 'week':
					$this->hc_time->setStartWeek();
					$start_date = $this->hc_time->formatDate_Db();
					$this->hc_time->setEndWeek();
					$end_date = $this->hc_time->formatDate_Db();
					break;

				case 'month':
					$this->hc_time->setStartMonth();
					$start_date = $this->hc_time->formatDate_Db();
					$this->hc_time->setEndMonth();
					$end_date = $this->hc_time->formatDate_Db();
					break;
			}
		}

		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['range'] = $range;

	/* working staff */
		$um = new User_Model;
		$this->data['working_staff'] = $um->get_staff();

		$lm = new Location_Model;
		$locations = $lm
			->get()->all;
		$this->data['locations'] = array();
		foreach( $locations as $loc )
		{
			$this->data['locations'][ $loc->id ] = $loc;
		}

		$this->data['display'] = $display;
		$this->data['filter'] = $filter;

	/* load shifts so that they can be reused in module displays to save queries */
		$filter_staff_id = ($filter == 'staff') ? $this->data['id'] : 0;
		$this->_load_shifts( array($start_date, $end_date), $filter_staff_id );

	/* save view */
		$this->session->set_userdata( 
			array(
				'schedule_view' => $args
				)
			);

		switch( $filter )
		{
			case 'location':
				if( isset($args['id']) && $args['id'] )
					$location_id = $args['id'];
				else
				{
					$ids = array_keys( $this->data['locations'] );
					$location_id = $ids[0];
				}
				$this->data['current_location'] = $this->data['locations'][$location_id];
				break;

			case 'staff':
				if( isset($args['id']) && $args['id'] )
					$staff_id = $args['id'];
				else
				{
					$ids = array_keys( $this->data['staffs'] );
					$staff_id = $ids[0];
				}
				$this->data['current_staff'] = $this->data['staffs'][$staff_id];
				break;
		}

	/* decide which view */
		switch( $display )
		{
			case 'calendar':
				switch( $filter )
				{
					case 'location':
						$view = 'index_location';
						break;

					case 'staff':
						$view = 'index_staff';
						break;

					default:
						$view = 'index';
						break;
				}
				$view = 'index_calendar';
				break;

			case 'browse':
				$view = 'index_browse';
				break;

			case 'exportbrowse':
				return $this->export_browse();
				break;

			case 'exportstats':
			case 'stats':
				$stats_shifts = array();
				$stats_drafts = array();

				reset( $this->data['staffs'] );
				foreach( $this->data['staffs'] as $sta )
				{
					if( $filter == 'staff' )
					{
						if( $sta->id != $this->data['current_staff']->id )
							continue;
					}
					$stats_shifts[$sta->id] = array( 0, 0 );
					$stats_drafts[$sta->id] = array( 0, 0 );
				}

				reset( $this->data['shifts'] );
				foreach( $this->data['shifts'] as $sh )
				{
					if( ! $sh->user_id )
						continue;

					if( $sh->date < $this->data['start_date'] )
						continue;
					if( $sh->date > $this->data['end_date'] )
						continue;

					if( $filter == 'location' )
					{
						if( $sh->location_id != $this->data['current_location']->id )
							continue;
					}

					if( $filter == 'staff' )
					{
						if( $sh->user_id != $this->data['current_staff']->id )
							continue;
					}

					if( ! isset($stats_shifts[$sh->user_id]) )
					{
						continue;
//						$stats_shifts[$sh->user_id] = array( 0, 0 );
//						$stats_drafts[$sh->user_id] = array( 0, 0 );
					}

					if( $sh->status == SHIFT_MODEL::STATUS_ACTIVE )
					{
						$stats_shifts[$sh->user_id][0] += 1;
						$stats_shifts[$sh->user_id][1] += $sh->get_duration();
					}
					else
					{
						$stats_drafts[$sh->user_id][0] += 1;
						$stats_drafts[$sh->user_id][1] += $sh->get_duration();
					}
				}

			/* filter archived staff if they have no shifts */
				$archived_users = array();
				$um = new User_Model;
				$um
					->select( 'id' )
					->where( 'active', USER_MODEL::STATUS_ARCHIVE )
					->get();
				foreach( $um as $u )
				{
					$archived_users[] = $u->id;
				}
				if( $archived_users )
				{
					$all_users = array_keys( $stats_shifts );
					foreach( $all_users as $uid )
					{
						if( 
							in_array($uid, $archived_users) &&
							( $stats_shifts[$uid][0] == 0 ) &&
							( $stats_drafts[$uid][0] == 0 )
						)
						{
							unset( $stats_shifts[$uid] );
							unset( $stats_drafts[$uid] );
						}
					}
				}

				$this->data['stats_shifts'] = $stats_shifts;
				$this->data['stats_drafts'] = $stats_drafts;

			/* sort by duration */
				uasort( $this->data['stats_shifts'],
					create_function(
						'$a, $b',
						'return ($b[1] - $a[1]);'
						)
					);

				if( $display == 'exportstats' )
				{
					return $this->export_stats();
				}
				else
				{
					$view = 'index_stats';
				}
				break;

			default:
				$view = 'index_calendar';
				break;
		}

		$this->set_include( $view );
		$this->load->view( $this->template, $this->data);
		return;
	}

	function export_browse()
	{
		$separator = $this->app_conf->get( 'csv_separator' );

	// header
		$headers = array(
			lang('time_date'),
			lang('time'),
			lang('time_duration'),
			lang('user_level_staff'),
			lang('location'),
			lang('shift_status')
			);

		$data = array();
		$data[] = join( $separator, $headers );

	// shifts
		reset( $this->data['shifts'] );
		foreach( $this->data['shifts'] as $sh )
		{
			if( $sh->date < $this->data['start_date'] )
				continue;
			if( $sh->date > $this->data['end_date'] )
				continue;
			if( ($this->data['filter'] == 'location') && ($sh->location_id != $this->data['current_location']->id) )
				continue;

			$values = array();

		// date
			$this->hc_time->setDateDb( $sh->date );
			$date_view = '';
			$date_view .= $this->hc_time->formatWeekdayShort();
			$date_view .= ', ';
			$date_view .= $this->hc_time->formatDate();
			$values[] = $date_view;

		// time
			$time_view = hc_format_time_of_day($sh->start) . ' - ' . hc_format_time_of_day($sh->end);
			$values[] = $time_view;

		// duration
			$values[] = $this->hc_time->formatPeriodShort($sh->get_duration(), 'hour');

		// staff
			if( $sh->user_id && isset($this->data['staffs'][$sh->user_id]) )
			{
				$staff_title = $this->data['staffs'][$sh->user_id]->full_name();
			}
			else
			{
				$staff_title = '';
			}
			$values[] = $staff_title;

		// location
			$values[] = $sh->location_name;

		// status
			$conflicts = $sh->conflicts( $this->data['shifts'], $this->data['timeoffs'] );
			if( $sh->user_id && count($conflicts) )
			{
				$status = lang('shift_conflict');
			}
			else
			{
				$status = $sh->prop_text('status', FALSE, $sh->get_status());
			}
			$values[] = $status;

		/* add csv line */
			$data[] = hc_build_csv( array_values($values), $separator );
		}

	// output
		$out = join( "\n", $data );

		$file_name = isset( $this->conf['export'] ) ? $this->conf['export'] : 'export';
		$file_name .= '-' . date('Y-m-d_H-i') . '.csv';

		$this->load->helper('download');
		force_download($file_name, $out);
		return;
	}

	function export_stats()
	{
		$separator = $this->app_conf->get( 'csv_separator' );

	// header
		$headers = array(
			lang('user_level_staff'),
			lang('shifts'),
			lang('time_duration'),
			);

		$data = array();
		$data[] = join( $separator, $headers );

	// shifts
		foreach( $this->data['stats_shifts'] as $staff_id => $array )
		{
			$staff = $this->data['staffs'][ $staff_id ];
			$values = array();
			$values[] = $staff->title();
			$values[] = $this->data['stats_shifts'][$staff->id][0];
			$values[] = $this->hc_time->formatPeriodShort($this->data['stats_shifts'][$staff->id][1], 'hour');

			$data[] = hc_build_csv( array_values($values), $separator );
		}

	// output
		$out = join( "\n", $data );

		$file_name = 'stats-';
		$file_name .= $this->data['start_date'] . '-' . $this->data['end_date'] . '.csv';

		$this->load->helper('download');
		force_download($file_name, $out);
		return;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */