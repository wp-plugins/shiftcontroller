<?php
class Location_model extends MY_model
{
	var $table = 'locations';
	var $allow_none = FALSE;
	var $default_order_by = array('show_order' => 'ASC');
	var $build_title = array(
		'_name_'
		);

	var $has_many = array( 
		'shift' => array(
			'class'			=> 'shift_model',
			'other_field'	=> 'location',
			),
		);

	var $validation = array(
		'name'	=> array(
			'label'	=> 'lang:location_name',
			'rules'	=> array('required', 'trim', 'max_length' => 50, 'unique')
			),
		);

	var $my_fields = array(
		array(
			'name'		=> 'name',
			'label'		=> 'lang:location_name',
			'size'		=> 24,
			'required'	=> TRUE,
			),
		array(
			'name'		=> 'description',
			'type'		=> 'textarea',
			'label'		=> 'lang:common_description',
			),
		);

	public function title( $html = FALSE )
	{
		$return = '';
		if( $html )
		{
			$return .= '<i class="fa fa-home"></i> ';
		}

		$return .= parent::title();
		return $return;
	}
}