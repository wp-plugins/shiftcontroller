<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_location_description extends CI_Migration {
	public function up()
	{
		if( ! $this->db->field_exists('description', 'locations') )
		{
			$this->dbforge->add_column(
				'locations',
				array(
					'description' => array(
						'type'		=> 'TEXT',
						'null'		=> TRUE,
//						'default'	=> '',
						),
					)
				);
		}

		$lm = new Location_model;
		$lm->reinitialize_model();
	}

	public function down()
	{
	}
}