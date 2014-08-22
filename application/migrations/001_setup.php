<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_setup extends CI_Migration {
	public function up()
	{
	// locations
		$this->dbforge->add_field(
			array(
				'id' => array(
					'type' => 'INT',
					'null' => FALSE,
					'unsigned' => TRUE,
					'auto_increment' => TRUE
					),
				'name' => array(
					'type' => 'VARCHAR(100)',
					'null' => FALSE,
					),
				'show_order' => array(
					'type' => 'INT',
					'null' => FALSE,
					'default' => 0,
					),
				)
			);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('locations');

	// shift templates
		$this->dbforge->add_field(
			array(
				'id' => array(
					'type' => 'INT',
					'null' => FALSE,
					'unsigned' => TRUE,
					'auto_increment' => TRUE
					),
				'name' => array(
					'type' => 'VARCHAR(100)',
					'null' => FALSE,
					),
				'start' => array(
					'type' => 'INT',
					'null' => FALSE,
					),
				'end' => array(
					'type' => 'INT',
					'null' => FALSE,
					),
				)
			);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('shift_templates');

	// timeoffs
		$this->dbforge->add_field(
			array(
				'id' => array(
					'type' => 'INT',
					'null' => FALSE,
					'unsigned' => TRUE,
					'auto_increment' => TRUE
					),
				'date' => array(
					'type' => 'INT',
					'null' => FALSE,
					),
				'start' => array(
					'type' => 'INT',
					'null' => FALSE,
					),
				'end' => array(
					'type' => 'INT',
					'null' => FALSE,
					),
				'status' => array(
					'type' => 'TINYINT',
					'null' => FALSE,
					'default'	=> 2, //TIMEOFF_MODEL::STATUS_PENDING,
					),
				'date_end' => array(
					'type' => 'INT',
					'null' => FALSE,
					),
			/* relations */
				'user_id' => array(
					'type'		=> 'INT',
					'null'		=> TRUE,
					),
				)
			);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('timeoffs');

	// shifts
		$this->dbforge->add_field(
			array(
				'id' => array(
					'type' => 'INT',
					'null' => FALSE,
					'unsigned' => TRUE,
					'auto_increment' => TRUE
					),
				'date' => array(
					'type' => 'INT',
					'null' => FALSE,
					),
				'start' => array(
					'type' => 'INT',
					'null' => FALSE,
					),
				'end' => array(
					'type' => 'INT',
					'null' => FALSE,
					),
				'status' => array(
					'type'		=> 'TINYINT',
					'null'		=> FALSE,
					'default'	=> 2, //SHIFT_MODEL::STATUS_DRAFT
					),
				'group_id' => array(
					'type' => 'INT',
					'null' => TRUE,
					),
			/* relations */
				'user_id' => array(
					'type'		=> 'INT',
					'null'		=> TRUE,
					),
				'location_id' => array(
					'type'		=> 'INT',
					'null'		=> TRUE,
					),
				)
			);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('shifts');

	// users
		$this->dbforge->add_field(
			array(
				'id' => array(
					'type' => 'INT',
					'null' => FALSE,
					'unsigned' => TRUE,
					'auto_increment' => TRUE
					),
				'first_name' => array(
					'type' => 'VARCHAR(100)',
					'null' => TRUE,
					),
				'last_name' => array(
					'type' => 'VARCHAR(100)',
					'null' => TRUE,
					),
				'email' => array(
					'type' => 'VARCHAR(255)',
					'null' => TRUE,
					),
				'password' => array(
					'type' => 'VARCHAR(80)',
					'null' => FALSE,
					),
				'level' => array(
					'type' => 'TINYINT',
					'null' => FALSE,
					'default' => 1,
					),
				'active' => array(
					'type' => 'TINYINT',
					'null' => FALSE,
					'default' => 1,
					),
				)
			);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users');

	// shift trades
		$this->dbforge->add_field(
			array(
				'id' => array(
					'type' => 'INT',
					'null' => FALSE,
					'unsigned' => TRUE,
					'auto_increment' => TRUE
					),
				'status' => array(
					'type' => 'TINYINT',
					'null' => FALSE,
					'default'	=> 1, //TRADE_MODEL::STATUS_PENDING,
					),
				'created' => array(
					'type' => 'INT',
					'null' => TRUE,
					),
/* relationship fields */
				'shift_id' => array(
					'type' => 'INT',
					'null' => TRUE,
					),
				'to_user_id' => array(
					'type' => 'INT',
					'null' => TRUE,
					),
				)
			);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('trades');

	/* RELATIONS */
		$relations = array(
			);
		reset( $relations );
		foreach( $relations as $rel )
		{
			$this->dbforge->add_field(
				array(
					'id' => array(
						'type' => 'INT',
						'null' => FALSE,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
						),
					$rel[0] => array(
						'type' => 'INT',
						'null' => FALSE,
						),
					$rel[1] => array(
						'type' => 'INT',
						'null' => FALSE,
						),
					)
				);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($rel[2]);
		}

	/* location */
	$this->load->model( 'Location_model' );
	$this->Location_model->name = 'Our Location';
	$this->Location_model->save();
	}

	public function down()
	{
		$this->dbforge->drop_table('locations');
		$this->dbforge->drop_table('shift_templates');
		$this->dbforge->drop_table('users');
		$this->dbforge->drop_table('timeoffs');
		$this->dbforge->drop_table('schedules');
	}
}
