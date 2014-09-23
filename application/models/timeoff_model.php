<?php
include_once( dirname(__FILE__) . '/_timeblock_model.php');
class Timeoff_model extends _Timeblock_model
{
	const STATUS_ACTIVE = 1;
	const STATUS_PENDING = 2;
	const STATUS_DENIED = 3;
	const STATUS_ARCHIVE = 4;
	const STATUS_CONFLICT = 5;

	var $table = 'timeoffs';
	var $default_order_by = array('date' => 'ASC', 'start' => 'ASC', 'id' => 'ASC');

	var $has_one = array(
		'user' => array(
			'class'			=> 'user_model',
			'other_field'	=> 'timeoff',
			)
		);

	var $validation = array(
		'user'	=> array(
			'label'	=> 'lang:user_level_staff',
			'rules'	=> array('required'),
			),
		'date'	=> array(
			'label'	=> 'lang:time_date_from',
			'rules'	=> array('required', 'trim', 'less_equal_than_field' => 'date_end'),
			),
		'date_end'	=> array(
			'label'	=> 'lang:time_date_to',
			'rules'	=> array(
				'required', 'trim', 'greater_equal_than_field' => 'date', 
//				'conflict'
				),
			),
		'start'	=> array(
			'label'	=> 'lang:shift_start',
			'rules'	=> array('required', 'trim', 'less_than_field' => 'end'),
			),
		'end'	=> array(
			'label'	=> 'lang:shift_end',
			'rules'	=> array(
				'required', 'trim', 'greater_than_field' => 'start', 
//				'conflict'
				),
			),
		'status'	=> array(
			'label'	=> 'lang:timeoff_status',
			'rules'	=> array(
				'enum' => array(
					self::STATUS_ACTIVE,
					self::STATUS_PENDING,
					self::STATUS_DENIED,
					)
				),
			),
		);

	var $prop_text = array(
		'status'	=> array(
			self::STATUS_ACTIVE 	=> array( 'lang:timeoff_status_active',		'success' ),
			self::STATUS_PENDING	=> array( 'lang:timeoff_status_pending',	'warning' ),
			self::STATUS_DENIED		=> array( 'lang:timeoff_status_denied',		'info' ),
			self::STATUS_ARCHIVE	=> array( 'lang:timeoff_status_archive',	'default' )
			),
		);

	var $my_fields = array(
		array(
			'name'		=> 'user',
			'type'		=> 'dropdown',
			'label'		=> 'lang:user_level_staff',
			),
		array(
			'name'		=> 'status',
			'label'		=> 'lang:timeoff_status',
			'type'		=> 'dropdown',
			),
		array(
			'name'		=> 'date',
			'type'		=> 'date',
			'required'	=> TRUE,
			'label'		=> 'lang:time_date',
			),
		array(
			'name'		=> 'date_end',
			'type'		=> 'date',
			'required'	=> TRUE,
			'label'		=> 'lang:time_date_to',
			),
		array(
			'name'		=> 'start',
			'type'		=> 'time',
			'required'	=> TRUE,
			'label'		=> 'lang:shift_start',
			),
		array(
			'name'		=> 'end',
			'type'		=> 'time',
			'required'	=> TRUE,
			'label'		=> 'lang:shift_end',
			),
		);

	public function get_form_fields()
	{
		$return = parent::get_form_fields();

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


	public function title( $html = FALSE )
	{
		$return = '';
		if( $html )
		{
			$return .= '<i class="fa-fw fa fa-coffee"></i> ';
		}
		else
		{
			$return .= lang('timeoff') . ': ';
		}

		$return .= $this->date_view();
		return $return;
	}

	public function view_text( $skip = array() )
	{
		$return = parent::view_text( $skip );
		unset( $return['date_end'] );
		unset( $return['start'] );
		unset( $return['end'] );
		$return['date'][1] = $this->date_view();
		return $return;
	}

	public function date_view()
	{
		$return = '';
		$CI =& ci_get_instance();
		$CI->hc_time->setDateDb( $this->date );

		$return .= $CI->hc_time->formatWeekdayShort() . ', ' . $CI->hc_time->formatDate();
		if( $this->date == $this->date_end )
		{
			$return .= ' [' . $CI->hc_time->formatPeriodOfDay($this->start, $this->end) . ']';
		}
		else
		{
			$CI->hc_time->setDateDb( $this->date_end );
			$return .= ' - ' . $CI->hc_time->formatWeekdayShort() . ', ' . $CI->hc_time->formatDate();;
		}
		return $return;
	}

	function is_passed()
	{
		$CI =& ci_get_instance();
		$CI->hc_time->setNow();
	/* if this week then not yet expired */
		$CI->hc_time->setStartWeek();
		$check_with = $CI->hc_time->formatDate_Db();

		$return = ( $check_with > $this->date_end ) ? TRUE : FALSE;
		return $return;
	}

	public function id_label()
	{
		return $this->prop_text('status', TRUE);
	}

	function get_status()
	{
		$return = $this->status;
		switch( $this->status )
		{
			case self::STATUS_ACTIVE:
				if( $this->is_passed() )
					$return = self::STATUS_ARCHIVE;
				break;
		}
		return $return;
	}

	public function conflicts( $shifts = NULL, $timeoffs = NULL, $force_date = NULL )
	{
		return parent::conflicts( TRUE, FALSE, $shifts, $timeoffs, $force_date );
	}

	public function get_duration()
	{
		if( $this->date_end == $this->date )
			$return = $this->end - $this->start;
		else
		{
			$CI =& ci_get_instance();
			$CI->hc_time->setDateDb( $this->date_end );
			$days_differ = $CI->hc_time->differ( $this->date );
			$return = 24*60*60 * ($days_differ + 1);
		}
		return $return;
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