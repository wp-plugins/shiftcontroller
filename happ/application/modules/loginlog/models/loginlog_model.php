<?php
class Loginlog_model extends MY_model
{
	var $table = 'loginlog';
	var $default_order_by = array('action_time' => 'DESC');

	var $has_one = array(
		'user' => array(
			'class'			=> 'user_model',
			'other_field'	=> 'loginlog',
			)
		);

	public function log( $user )
	{
		$this->user_id = $user->id;
		$this->action_time = time();
		$this->remote_ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';
		return $this->save();
	}
}