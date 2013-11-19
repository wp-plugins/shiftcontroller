<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class My_Form_validation extends CI_Form_validation {
	public function error_array()
	{
		return $this->_error_array;
	}

	public function dropdown_selected($str)
	{
		return $this->is_natural_no_zero($str); 
	}

	public function greater_than_field($str, $field)
	{
		if ( ! isset($_POST[$field]))
			return FALSE;
		if ( ! is_numeric($str))
			return FALSE;
		$min = $_POST[$field];
		return $str > $min;
	}

	public function not_greater_than_field($str, $field)
	{
		if ( ! isset($_POST[$field]))
			return FALSE;
		if ( ! is_numeric($str))
			return FALSE;
		$min = $_POST[$field];
		return $str <= $min;
	}

	public function less_than_field($str, $field)
	{
		if ( ! isset($_POST[$field]))
			return FALSE;
		if ( ! is_numeric($str))
			return FALSE;
		$max = $_POST[$field];
		return $str < $max;
	}
	
	public function not_less_than_field($str, $field)
	{
		if ( ! isset($_POST[$field]))
			return FALSE;
		if ( ! is_numeric($str))
			return FALSE;
		$min = $_POST[$field];
		return $str >= $min;
	}

	public function differs($str, $field)
	{
		return ! $this->matches($str, $field);
	}

	public function is_unique($str, $field)
	{
		list($table, $field)=explode('.', $field);

		$sql = 'SHOW KEYS FROM ' . $this->CI->db->dbprefix($table) . " WHERE Key_name = 'PRIMARY'";
		$q = $this->CI->db->query($sql)->row();
		$primary_key = $q->Column_name;

		if($this->CI->input->post($primary_key) > 0) {
			$query = $this->CI->db->limit(1)->get_where($table, array($field => $str,$primary_key.' !='=>$this->CI->input->post($primary_key)));
			}
		else {
			$query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
			}
	return $query->num_rows() === 0;
	}
}