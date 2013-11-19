<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shifts_controller extends Backend_controller
{
	function __construct()
	{
		parent::__construct( USER_MODEL::LEVEL_STAFF, 'staff/shifts' );
	}

	function index( $display = 'my' )
	{
		$my_user_id = $this->auth->user()->id;

		$this->hc_time->setNow(); 
		$today = $this->hc_time->formatDate_Db();

		switch( $display )
		{
			case 'my':
			case 'trades':
				$sm = $this->auth->user()->shift;
				break;
			case 'pickup':
				$sm = new Shift_Model;
				break;
		}

		$sm
			->include_related( 'location', 'show_order' )
			->include_related( 'location', 'id' )
			->include_related( 'location', 'name' )
			->include_related( 'user', 'id' )

			->where( 'date >=', $today )
			->where( 'status', SHIFT_MODEL::STATUS_ACTIVE )

			->order_by( 'date', 'ASC' )
			->order_by( 'start', 'ASC' )
			->order_by( 'location_show_order', 'ASC' )
			;

		if( $this->hc_modules->exists('shift_trades') )
		{
			$sm->include_related( 'trade', 'id' );
			$sm->include_related( 'trade', 'status' );
		}

		switch( $display )
		{
			case 'trades':
				$sm
					->where_related( 'trade', 'id IS NOT ', 'NULL', FALSE )
					->where_not_in_related( 'trade', 'status', array(TRADE_MODEL::STATUS_COMPLETED) )
					;
				break;
			case 'pickup':
				$sm
					->where_related( 'trade', 'id IS NOT ', 'NULL', FALSE )
					->group_start()
						->or_group_start()
							->where_related( 'trade', 'status', TRADE_MODEL::STATUS_APPROVED )
							->where_not_in_related( 'user', 'id', array($my_user_id) )
						->group_end()
						->or_group_start()
							->where_related( 'trade', 'status', TRADE_MODEL::STATUS_ACCEPTED )
							->where_related( 'trade/to_user', 'id', $my_user_id )
						->group_end()
					->group_end()
					;
				break;
		}
		$this->data['shifts'] = $sm->get()->all;

	// timeoffs
		$this->data['timeoffs'] = $this->auth->user()->timeoff
			->where( 'date_end >=', $today )
			->where( 'status', TIMEOFF_MODEL::STATUS_ACTIVE )
			->order_by( 'date', 'ASC' )
			->get()->all;

		$this->data['display'] = $display;

		$this->set_include( 'index' );
		$this->load->view( $this->template, $this->data);
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */