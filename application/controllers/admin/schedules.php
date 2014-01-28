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

		if( $this->hc_modules->exists('shift_trades') )
		{
			$shift_model->include_related( 'trade', 'id' );
			$shift_model->include_related( 'trade', 'status' );
		}
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

	function day_staff( $date, $staff_id )
	{
		$this->data['display'] = 'staff';
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

	function day_location( $date, $location_id )
	{
		$this->data['display'] = 'location';
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

	function day( $date )
	{
		$this->data['display'] = 'all';
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

		$um = new User_Model;
		$staffs = $um->get()->all;
		$this->data['staffs'] = array();
		foreach( $staffs as $sta )
		{
			$this->data['staffs'][ $sta->id ] = $sta;
		}

		$this->data['date'] = $date;

		$this->set_include( 'day' );
		$this->load->view( $this->template, $this->data);
	}

	function browse()
	{
		$start = $this->input->post( 'start' );
		$end = $this->input->post( 'end' );
		$redirect_to = array( $this->conf['path'], 'index/browse', $start, $end );
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
//		$sm->include_related( 'user', 'id' );
		$sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->select_sum( 'end' )
			->select_sum( 'start' )
			->get();
		$count['duration'] = ($sm->end - $sm->start);

	/* count unpublished shifts */
		$sm->clear();
		$sm->include_related( 'user', 'id' );
		$count['active'] = $sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->where( 'status', SHIFT_MODEL::STATUS_ACTIVE )
			->where( 'user_id IS NOT ', 'NULL', FALSE )
			->count();

	/* published */
		$sm->clear();
		$sm->include_related( 'user', 'id' );
		$count['draft'] = $sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->where( 'status <>', SHIFT_MODEL::STATUS_ACTIVE )
			->where( 'user_id IS NOT ', 'NULL', FALSE )
			->count();

	/* total */
		$sm->clear();
		$count['total'] = $sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->count();

	/* not assigned */
		$sm->clear();
		$sm->include_related( 'user', 'id' );
		$count['not_assigned'] = $sm
			->where( 'date >=', $args['start'] )
			->where( 'date <=', $args['end'] )
			->where( 'user_id IS ', 'NULL', FALSE )
			->count();

		$this->data['count'] = $count;
		$this->data['start_date'] = $args['start'];
		$this->data['end_date'] = $args['end'];

		$this->set_include( 'status' );
		$this->load->view( $this->template, $this->data);
	}

	function index( $display = 'all', $date = '', $end_date = '' )
	{
		$range = 'month'; // may also be 'week'

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

		$um = new User_Model;
		if( $display == 'staff' )
		{
			$um->where('active', USER_MODEL::STATUS_ACTIVE);
		}
		$staffs = $um->get()->all;

		$this->data['staffs'] = array();
		foreach( $staffs as $sta )
		{
			$this->data['staffs'][ $sta->id ] = $sta;
		}

		$lm = new Location_Model;
		$locations = $lm
			->get()->all;
		$this->data['locations'] = array();
		foreach( $locations as $loc )
		{
			$this->data['locations'][ $loc->id ] = $loc;
		}

		$this->data['display'] = $display;

	/* load shifts so that they can be reused in module displays to save queries */
		$this->_load_shifts( array($start_date, $end_date) );

	/* decide which view */
		switch( $display )
		{
			case 'all':
				$view = 'index';
				break;

			case 'location':
				$view = 'index_location';
				break;

			case 'staff':
				$view = 'index_staff';
				break;

			case 'browse':
				$view = 'index_browse';
				break;

			case 'export':
				return $this->export();
				break;

			default:
				$view = 'index';
				break;
		}

		$this->set_include( $view );
		$this->load->view( $this->template, $this->data);
		return;
	}

	function export()
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
			if( $sh->user_id )
			{
				$status = ( $sh->status == SHIFT_MODEL::STATUS_ACTIVE ) ? lang('shift_status_active') : lang('shift_status_draft');
				if( count($conflicts) )
				{
					$status = lang('shift_conflict');
				}
			}
			else
			{
				$status = lang('shift_not_assigned');
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
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */