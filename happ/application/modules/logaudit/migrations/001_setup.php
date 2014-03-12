<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if( $this->db->table_exists('logaudit') )
	return;

// conf
$this->dbforge->add_field(
	array(
		'id' => array(
			'type' => 'INT',
			'null' => FALSE,
			'unsigned' => TRUE,
			'auto_increment' => TRUE
			),
		'user_id' => array(
			'type' => 'INT',
			'null' => FALSE,
			),
		'action_time' => array(
			'type' => 'INT',
			'null' => FALSE,
			),
		'object_class' => array(
			'type' => 'VARCHAR(32)',
			'null' => TRUE,
			),
		'object_id' => array(
			'type' => 'INT',
			'null' => TRUE,
			),

		'property_name' => array(
			'type' => 'VARCHAR(32)',
			'null' => TRUE,
			),
		'old_value' => array(
			'type' => 'TEXT',
			'null' => TRUE,
			),
		)
	);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('logaudit');