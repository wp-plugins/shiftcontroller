<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//class Shifts_controller extends Backend_controller
class Shifts_controller extends Backend_controller_crud
{
	function __construct()
	{
		$this->conf = array(
			'model'		=> 'Shift_model',
			'path'		=> 'staff/shifts',
			'entity'	=> 'shift',
			);
		parent::__construct( USER_MODEL::LEVEL_STAFF );
		$this->{$this->model} = $this->auth->user()->shift;
		$this->{$this->model}->user = $this->auth->user();
//		$this->data['fields'] = $this->process_fields();

//		parent::__construct( USER_MODEL::LEVEL_STAFF, 'staff/shifts' );

	/* check how many locations do we have */
		$lm = new Location_Model;
		$location_count = $lm->count();
		$this->data['location_count'] = $location_count;
	}

	function pickup( $id )
	{
		$sm = new Shift_Model;
		$sm->include_related( 'user', 'id' );
		$sm->get_by_id( $id );

		if( ! $sm->exists() )
			return;

		/* check if staff can pick up shifts */
		if( ! $this->app_conf->get('staff_pick_shifts') )
			return;

		/* if it already has staff and not listed for trade then no */
		if( $sm->user_id && (! $sm->has_trade) )
			return;

		$staff = $this->auth->user();
		$relations = array(
			'user'	=> $staff,
			);
		$sm->has_trade = 0;

		$approval_required = $this->app_conf->get( 'approve_pick_shifts' );
		if( $approval_required )
			$sm->status = SHIFT_MODEL::STATUS_DRAFT;

		$msg = lang('shift_pick_up');
		if( $sm->save($relations) )
		{
			$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );
		}
		else
		{
			$error = $sm->error->all;
			$this->session->set_flashdata( 'error', $msg . ': ' . $error );
		}

		$return_to = $this->agent->referrer();
		$this->redirect( $return_to );
		return;
	}

	function index( $display = 'my' )
	{
		$my_user_id = $this->auth->user()->id;

		$this->hc_time->setNow(); 
		$today = $this->hc_time->formatDate_Db();

		switch( $display )
		{
			case 'my':
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

		switch( $display )
		{
			case 'pickup':
				$sm
					->include_related( 'user', 'id' )

					->group_start()
						->or_group_start()
							->where( 'user_id <> ', $my_user_id )
							->where( 'has_trade <>', 0 )
						->group_end()

						->or_group_start()
							->or_where( 'user_id IS ', 'NULL', FALSE )
							->or_where( 'user_id', 0 )
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