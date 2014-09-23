<?php
include_once( dirname(__FILE__) . '/_timeblock_model.php');
class Shift_model extends _Timeblock_model
{
	var $table = 'shifts';
	var $default_order_by = array(
		'date' => 'ASC',
		'start' => 'ASC',
		'id' => 'ASC'
		);
	var $has_one = array(
		'user' => array(
			'class'			=> 'user_model',
			'other_field'	=> 'shift',
			),
		'location' => array(
			'class'			=> 'location_model',
			'other_field'	=> 'shift',
			),
		);

	const STATUS_ACTIVE = 1; // Published, With Staff
	const STATUS_DRAFT = 2; // Not Published, No Staff
	const STATUS_OPEN = 3; // not saved - Published, No Staff
	const STATUS_PENDING = 4; // not saved - Not Published, With Staff

	var $prop_text = array(
		'status'	=> array(
			self::STATUS_ACTIVE 	=> array( 'lang:shift_status_active',	'success' ),
			self::STATUS_DRAFT		=> array( 'lang:shift_status_draft',	'warning' ),
			self::STATUS_OPEN		=> array( 'lang:shift_status_open',		'danger' ),
			self::STATUS_PENDING	=> array( 'lang:shift_status_pending',	'info' ),
			),
		);

	var $validation = array(
		'date'	=> array(
			'label'	=> 'lang:time_date',
			'rules'	=> array('required', 'trim'),
			),
		'location'	=> array(
			'label'	=> 'lang:location',
			'rules'	=> array('required'),
			),
		'end'	=> array(
			'label'	=> 'lang:shift_end',
			'rules'	=> array('check_time', 'conflict'),
			),
		'status'	=> array(
			'label'	=> 'lang:shift_status',
			'rules'	=> array(
				'enum' => array(
					self::STATUS_ACTIVE,
					self::STATUS_DRAFT,
					)
				),
			),
		);

	var $my_fields = array(
	 	array(
			'name'		=> 'date',
			'type'		=> 'date',
			'label'		=> 'lang:time_date',
//			'hide'		=> TRUE,
			),
		array(
			'name'		=> 'location',
			'label'		=> 'lang:location',
			'type'		=> 'dropdown',
			),
		array(
			'name'		=> 'user',
			'label'		=> 'lang:user_level_staff',
			'type'		=> 'dropdown',
			),
		array(
			'name'		=> 'start',
			'type'		=> 'time',
			'label'		=> 'lang:shift_start',
			'required'	=> TRUE,
			'rules'		=> array(),
			),
		array(
			'name'		=> 'end',
			'type'		=> 'time',
			'label'		=> 'lang:shift_end',
			'required'	=> TRUE,
			'rules'		=> array('check_time', 'conflict'),
			),
		array(
			'name'		=> 'status',
			'label'		=> 'lang:shift_status',
			'hide'		=> TRUE,
			),
		array(
			'name'		=> 'has_trade',
			'label'		=> 'lang:trade_request',
			'type'		=> 'boolean',
			'hide'		=> TRUE,
			'default'	=> 0,
			),
		);

	public function get_status()
	{
		/* ACTIVE	- published with staff */
		/* OPEN		- published with no staff */
		/* PENDING	- not published with staff */
		/* DRAFT	- not published with no staff */

		if( $this->status == self::STATUS_ACTIVE )
		{
			if( $this->user_id )
				$return = self::STATUS_ACTIVE;
			else
				$return = self::STATUS_OPEN;
		}
		else
		{
			if( $this->user_id )
				$return = self::STATUS_PENDING;
			else
				$return = self::STATUS_DRAFT;
		}
		return $return;
	}

	public function view_text( $skip = array() )
	{
		$return = parent::view_text( $skip );
		unset( $return['start'] );
		unset( $return['end'] );
		unset( $return['status'] );
		$return['date'][1] = $this->date_view($skip);

	/* optimize it sometime later */
		$lm = new Location_Model;
		$location_count = $lm->count();
		if( $location_count < 2 )
		{
			unset( $return['location'] );
		}

		return $return;
	}

	public function title( $html = FALSE )
	{
		$return = '';
		if( $html )
		{
			$return .= '<i class="fa fa-clock-o"></i> ';
		}
		else
		{
			$return .= lang('shift') . ': ';
		}

		$return .= $this->date_view();
		return $return;
	}

	public function date_view( $skip = array() )
	{
		$return = '';
		$CI =& ci_get_instance();
		$CI->hc_time->setDateDb( $this->date );

		$return .= $CI->hc_time->formatWeekdayShort() . ', ' . $CI->hc_time->formatDate();
		
		$return .= ' [' . $CI->hc_time->formatTimeOfDay($this->start);
		if( ! in_array('end', $skip) )
		{
			$return .= ' - ' .  $CI->hc_time->formatTimeOfDay($this->end);
		}
		$return .= ']';
		return $return;
	}

	public function id_label()
	{
		return $this->prop_text('status', TRUE);
	}

	public function get_duration()
	{
		if( $this->end > $this->start )
			$return = $this->end - $this->start;
		else
			$return = $this->end + (24*60*60 - $this->start);
		return $return;
	}

	public function get_form_fields()
	{
		$return = parent::get_form_fields();

	/* remove archived users if any */
		$remove_users = array();
		$um = new User_Model;
		$um
			->select( 'id' )
			->where( 'active', USER_MODEL::STATUS_ARCHIVE )
			->get();
		foreach( $um as $u )
		{
			$remove_users[] = $u->id;
		}

		if( $remove_users )
		{
			reset( $remove_users );
			foreach( $remove_users as $rid )
			{
				unset( $return['user']['options'][$rid] );
			}
		}

	/* adjust min and max time */
		$CI =& ci_get_instance();
		$time_min = $CI->app_conf->get( 'time_min' );
		$time_max = $CI->app_conf->get( 'time_max' );

		$time_min = $time_min ? $time_min : 0;
		$time_max = $time_max ? $time_max : 24 * 60 * 60;

		$return['start']['conf']['min'] = $time_min;
		$return['start']['conf']['max'] = $time_max;
		$return['end']['conf']['min'] = $time_min;
		$return['end']['conf']['max'] = $time_max;

		return $return;
	}

	public function find_staff()
	{
	/* should have date, start, end set */
		$um = new User_Model;
		$all_staff = $um->get_staff();

		$return = array();
		foreach( $all_staff as $u )
		{
			$u->warning = array();
			$return[ $u->id ] = $u;
		}

	/* find users with shifts that fully overlap this one */
		$um->clear();
		$bad_staff = $um
			->distinct()
			->select('id')
			->where_in( 'id', array_keys($return) )
			->where_related( 'shift', 'date', $this->date )
			->where_related( 'shift', 'start <=', $this->start )
			->where_related( 'shift', 'end >=', $this->end )
			->get()->all;
		foreach( $bad_staff as $u )
		{
			unset( $return[$u->id] );
		}

	/* find overlapping timeoffs */
		$tm = new Timeoff_Model;
		$timeoffs = $tm
			->where_related( 'user', 'id', array_keys($return) )
			->include_related( 'user', 'id' )

			->where( 'date <=', $this->date )
			->where( 'date_end >=', $this->date )
			->where( 'start <', $this->end )
			->where( 'end >', $this->start )
			->get()->all;

		foreach( $timeoffs as $to )
		{
			$return[$to->user_id]->warning = $to; 
		}

		return $return;
	}

/* gets conflicting shifts and timeoffs */
	public function conflicts( $shifts = NULL, $timeoffs = NULL )
	{
		return parent::conflicts( TRUE, TRUE, $shifts, $timeoffs );
	}

	function publish()
	{
		$this->status = SHIFT_MODEL::STATUS_ACTIVE;
		return $this->save();
	}

	function unpublish()
	{
		$this->status = SHIFT_MODEL::STATUS_DRAFT;
		return $this->save();
	}

	protected function _before_delete()
	{
		$CI =& ci_get_instance();

	/* delete notes */
		if( $CI->hc_modules->exists('notes') )
		{
			$this->note->get()->delete_all();
		}
	}
}
