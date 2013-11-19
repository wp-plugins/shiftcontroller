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
		$user_level = $this->app_conf->get('wall_schedule_display');
		if( $user_level )
		{
			$this->check_level( $user_level );
		}
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

		$shift_model->where('user_id IS NOT ', 'NULL', FALSE);
		$shift_model->where('status', SHIFT_MODEL::STATUS_ACTIVE);

		$this->data['shifts'] = $shift_model
			->get()->all;

//$shift_model->check_last_query();

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

	function index( $date = '', $end_date = '' )
	{
		$display = 'all';
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
			$this->hc_time->setStartWeek();
			$start_date = $this->hc_time->formatDate_Db();
			$this->hc_time->setEndWeek();
			$end_date = $this->hc_time->formatDate_Db();
		}

		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;

		$um = new User_Model;
		$staffs = $um
			->where('active', USER_MODEL::STATUS_ACTIVE)
			->get()->all;
		$this->data['staffs'] = array();
		foreach( $staffs as $sta )
		{
			$this->data['staffs'][ $sta->id ] = $sta;
		}

	/* decide which view */
		switch( $display )
		{
			case 'all':
				$view = 'index';
				break;

			case 'staff':
				$view = 'index_staff';
				break;

			case 'browse':
				$view = 'index_browse';
				break;

			default:
				$view = 'index';
				break;
		}

		$this->data['display'] = $display;

	/* load shifts so that they can be reused in module displays to save queries */
		$this->_load_shifts( array($start_date, $end_date) );

		$this->set_include( $view );
		$this->load->view( $this->template, $this->data);
		return;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */