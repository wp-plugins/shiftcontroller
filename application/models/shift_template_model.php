<?php
class Shift_template_model extends MY_model
{
	var $table = 'shift_templates';
	var $default_order_by = array('start' => 'ASC');

	var $validation = array(
		'name'	=> array(
			'label'	=> 'lang:shift_template_name',
			'rules'	=> array('required', 'trim', 'max_length' => 50, 'unique'),
			),
		'start'	=> array(
			'label'	=> 'lang:shift_start',
			'rules'	=> array('required', 'trim'),
			),
		'end'	=> array(
			'label'	=> 'lang:shift_end',
			'rules'	=> array('required', 'trim', 'differs' => 'start'),
			),
		);

	var $my_fields = array(
		array(
			'name'		=> 'name',
			'label'		=> 'lang:shift_template_name',
			'size'		=> 24,
			'required'	=> TRUE,
			),
		array(
			'name'		=> 'start',
			'type'		=> 'time',
			'label'		=> 'lang:shift_start',
			'required'	=> TRUE,
			),
		array(
			'name'		=> 'end',
			'type'		=> 'time',
			'label'		=> 'lang:shift_end',
			'required'	=> TRUE,
			),
		);

	public function get_duration()
	{
		if( $this->end > $this->start )
			$return = $this->end - $this->start;
		else
			$return = $this->end + (24*60*60 - $this->start);
		return $return;
	}

	public function title()
	{
		$return = lang('shift_template');
		return $return;
	}
}