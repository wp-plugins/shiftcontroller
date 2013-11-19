<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stats_controller extends Backend_controller
{
	function __construct()
	{
		$this->conf = array(
			'path'		=> 'admin/stats',
			);
		parent::__construct( User_model::LEVEL_ADMIN );
	}

	function staff( $id, $display = 'week' )
	{
		$um = new User_Model;
		$um->get_by_id( $id );
		if( ! $um->exists() )
			return;

		$this->data['object'] = $um;

		$um->shift->where( 'status', SHIFT_MODEL::STATUS_ACTIVE );

	/* find min and max date */
		$max_date = $um->shift->select_max('date')->get()->date;
		$min_date = $um->shift->select_min('date')->get()->date;

		$shifts = $um->shift
			->get_iterated();

	/* compile dates */
		$dates = array();

		$date = $min_date;

		$this->hc_time->setDateDb( $date );
		switch( $display )
		{
			case 'week':
				$this->hc_time->setStartWeek();
				break;
			case 'month':
				$this->hc_time->setStartMonth();
				break;
		}
		$date = $this->hc_time->formatDate_Db();

		while( $date <= $max_date )
		{
			switch( $display )
			{
				case 'week':
					$start = $this->hc_time->formatDate_Db();
					$this->hc_time->setEndWeek();
					$end = $this->hc_time->formatDate_Db();
					break;
				case 'month':
					$start = $this->hc_time->formatDate_Db();
					$this->hc_time->setEndMonth();
					$end = $this->hc_time->formatDate_Db();
					break;
			}

			$dates[ $start . '-'. $end ] = array(
				'shift_count'		=> 0,
				'shift_duration'	=> 0,
				'timeoff_count'		=> 0,
				'timeoff_duration'	=> 0,
				);

			$this->hc_time->modify( '+1 day' );
			$date = $this->hc_time->formatDate_Db();
		}

		foreach( $shifts as $sh )
		{
			reset( $dates );
			foreach( array_keys($dates) as $dk )
			{
				list( $start, $end ) = explode( '-', $dk );
				if( 
					($sh->date >= $start) && 
					($sh->date <= $end)
					)
				{
					$dates[$dk]['shift_count']++;
					$dates[$dk]['shift_duration'] += $sh->get_duration();
				}
			}
		}

		$this->data['dates'] = $dates;
		$this->data['display'] = $display;

//		$this->conf['path'] = 'admin/users';
		$this->set_include( 'edit/stats', 'admin/users' );
		$this->load->view( $this->template, $this->data);
	}
}