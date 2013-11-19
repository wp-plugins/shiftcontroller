<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// conf
$this->dbforge->add_field(
	array(
		'id' => array(
			'type' => 'INT',
			'null' => FALSE,
			'unsigned' => TRUE,
			'auto_increment' => TRUE
			),
		'name' => array(
			'type' => 'VARCHAR(32)',
			'null' => FALSE,
			),
		'value' => array(
			'type' => 'TEXT',
			'null' => TRUE,
			),
		)
	);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('conf');