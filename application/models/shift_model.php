<?php
include_once( dirname(__FILE__) . '/_timeblock_model.php');
class Shift_model extends _Timeblock_model
{
	var $table = 'shifts';
	var $default_order_by = array('date' => 'ASC', 'start' => 'ASC', 'id' => 'ASC');
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

	const STATUS_ACTIVE = 1;
	const STATUS_DRAFT = 2;

	var $prop_text = array(
		'status'	=> array(
			self::STATUS_ACTIVE 	=> array( 'lang:shift_status_active',	'success' ),
			self::STATUS_DRAFT		=> array( 'lang:shift_status_draft',	'warning' ),
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
		);

	public function view_text()
	{
		$return = parent::view_text();
		unset( $return['start'] );
		unset( $return['end'] );
		unset( $return['status'] );
		$return['date'][1] = $this->date_view();
		return $return;
	}

	public function title( $html = FALSE )
	{
		$return = '';
		if( $html )
		{
			$return .= '<i class="icon-time"></i> ';
		}
		else
		{
			$return .= lang('shift') . ': ';
		}

		$return .= $this->date_view();
		return $return;
	}

	public function date_view()
	{
		$return = '';
		$CI =& ci_get_instance();
		$CI->hc_time->setDateDb( $this->date );

		$return .= $CI->hc_time->formatDate();
		$return .= ' [' . $CI->hc_time->formatTimeOfDay($this->start) . ' - ' .  $CI->hc_time->formatTimeOfDay($this->end) . ']';
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

	public function find_staff()
	{
	/* should have date, start, end set */
		$um = new User_Model;
		$all_staff = $um
			->where('active', USER_MODEL::STATUS_ACTIVE)
			->get()->all;

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

	/* delete trades */
		if( $CI->hc_modules->exists('shift_trades') )
		{
			$this->trade->get()->delete_all();
		}
	}

	protected function _after_save()
	{
		$changes = $this->get_changes();
		if( isset($changes['id']) )
		{
			$log = array(
				'action_name'		=> 'create',
				'action_details'	=> ''
				);
			$this->_add_log( $log );
		}
	}
}
