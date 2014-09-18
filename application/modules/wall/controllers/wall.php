<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Wall_wall_controller extends Front_controller
{
	function __construct()
	{
		$this->conf = array(
			'path'	=> 'wall',
			);
		parent::__construct();
		$this->load->library( 'hc_time' );

	/* check user level */
		$remote_integration = $this->remote_integration();
		if( $remote_integration )
		{
			$user_level = 0;
		}
		else
		{
			$user_level = $this->app_conf->get('wall_schedule_display');
		}

		if( $user_level )
		{
			$this->check_level( $user_level );
		}

	/* check how many locations do we have */
		$lm = new Location_Model;
		$location_count = $lm->count();
		$this->data['location_count'] = $location_count;
	}

	private function _load_shifts( 
		$dates = array(),
		$staff_id = array(),
		$location_id = array()
		)
	{
		if( $staff_id && (count($staff_id) == 1) )
		{
			$um = new User_Model;
			$um->get_by_id( $staff_id[0] );
			$shift_model = $um->shift;
			$timeoff_model = $um->timeoff;
		}
		else
		{
			$shift_model = new Shift_Model;
			$timeoff_model = new Timeoff_Model;
		}

		if( $dates )
		{
			$shift_model->where_in('date', $dates);
		}

		$shift_model
			->include_related( 'location', 'show_order' )
			->include_related( 'location', 'id' )
			->include_related( 'location', 'name' )
			->include_related( 'user', 'id' )
			->order_by( 'date', 'ASC' )
			->order_by( 'start', 'ASC' )
			->order_by( 'location_show_order', 'ASC' );

		if( $location_id )
		{
			$shift_model->where_related( 'location', 'id', $location_id );
		}

		$shift_model->group_start();
			$shift_model->where('status', SHIFT_MODEL::STATUS_ACTIVE);
			if( 
				$this->auth->check() && 
				$this->app_conf->get('staff_pick_shifts')
				)
			{
//				$shift_model->or_where('user_id IS ', 'NULL', FALSE);
			}
			else
			{
				$shift_model->where('user_id IS NOT ', 'NULL', FALSE);
			}
		$shift_model->group_end();

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

	function day( $date, $location_id = array() )
	{
		$this->data['display'] = 'all';
		$sm = new Shift_Model;

	/* load shifts if needed */
		if( ! isset($this->data['shifts']) )
		{
			$this->_load_shifts( $date, 0, $location_id );
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

	function index()
	{
		$args = $this->parse_args( func_get_args() );
		$args = array_merge( $this->default_params, $args );

		$display = 'all';

		$location_id = array();
		$supplied_location_id = isset($args['location']) ? $args['location'] : '';
		if( strlen($supplied_location_id)  )
		{
			if( strpos($supplied_location_id, ',') !== FALSE )
			{
				$location_id = explode(',', $supplied_location_id);
				array_walk( $location_id, 'intval' );
			}
			else
			{
				if( $location_id )
					$location_id = array($supplied_location_id);
				else
					$location_id = $supplied_location_id; // 0 for all
			}
		}

		$staff_id = array();
		$supplied_staff_id = isset($args['staff']) ? $args['staff'] : '';
		if( $supplied_staff_id )
		{
			if( strpos($supplied_staff_id, ',') !== FALSE )
			{
				$staff_id = explode(',', $supplied_staff_id);
				array_walk( $staff_id, 'intval' );
			}
			elseif( $supplied_staff_id == '_current_user_id_' )
			{
				if( $this->auth && $this->auth->user() )
					$staff_id = array( $this->auth->user()->id );
			}
			else
			{
				$staff_id = array($supplied_staff_id);
			}
		}

		if( isset($args['start']) )
			$start_date = $args['start'];
		else
			$start_date = $this->hc_time->setNow()->formatDate_Db();

		if( isset($args['end']) )
			$end_date = $args['end'];
		else
			$end_date = '';

		$this->hc_time->setDateDb( $start_date );
		$range = isset($args['range']) ? $args['range'] : '';
		if( $range )
		{
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

				default:
					$this->hc_time->modify('+' . $range);
					$this->hc_time->modify('-1 day');
					$end_date = $this->hc_time->formatDate_Db();
					break;
			}
		}

	/* find dates that we have shifts */
		$shift_model = new Shift_Model;
		$shift_model->select( 'date' );

		$shift_model->where('date >=', $start_date);
		if( $end_date )
		{
			$shift_model->where('date <=', $end_date);
		}

		$shift_model->group_start();
			$shift_model->where('status', SHIFT_MODEL::STATUS_ACTIVE);
			if( 
				$this->auth->check() && 
				$this->app_conf->get('staff_pick_shifts')
				)
			{
//				$shift_model->or_where('user_id IS ', 'NULL', FALSE);
			}
			else
			{
				$shift_model->where('user_id IS NOT ', 'NULL', FALSE);
			}
		$shift_model->group_end();

		if( $location_id )
		{
			$shift_model->where_related( 'location', 'id', $location_id );
		}
		if( $staff_id )
		{
			$shift_model->where_related( 'user', 'id', $staff_id );
		}

		$shift_model->distinct();
//		$shift_model->limit( 3 );

		$shift_model->order_by( 'date', 'ASC' );
		$shift_model->get();

//		$shift_model->check_last_query();
//		exit;

		$dates = array();
		foreach( $shift_model as $s )
		{
			$dates[] = $s->date;
		}
		$this->data['dates'] = $dates;

	/* preload staff information */
		$um = new User_Model;
		$staffs = $um->get_staff();
		$this->data['staffs'] = array();
		foreach( $staffs as $sta )
		{
			$this->data['staffs'][ $sta->id ] = $sta;
		}

	/* preload location information */
		$lm = new Location_Model;
		$locations = $lm
			->get()->all;
		$this->data['locations'] = array();
		foreach( $locations as $loc )
		{
			$this->data['locations'][ $loc->id ] = $loc;
		}

		$this->data['location_id'] = $location_id;
		$this->data['display'] = $display;

	/* load shifts so that they can be reused in module displays to save queries */
		$this->_load_shifts(
			$dates,
			$staff_id,
			$location_id
			);

		$view = 'index';

		$this->set_include( $view );
		$this->load->view( $this->template, $this->data);
		return;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */