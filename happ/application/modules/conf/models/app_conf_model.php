<?php
Class App_conf_model extends CI_model
{
	function get_all( )
	{
		$this->db->select('name, value');
		$result	= $this->db->get('conf');

		$return	= array();
		foreach($result->result_array() as $i)
		{
			$return[ $i['name'] ] = $i['value'];
		}
		return $return;
	}

	function save( $pname, $pvalue )
	{
		if( $this->db->get_where('conf', array('name'=>$pname))->row_array() )
		{
			$item = array(
				'value'	=> $pvalue
				);
			$this->db->where('name', $pname);
			$this->db->update('conf', $item);
		}
		else 
		{
			$item = array(
				'name'	=> $pname,
				'value'	=> $pvalue
				);
			$this->db->insert('conf', $item);
		}
	}

	function delete( $pname )
	{
		$this->db->where('name', $pname);
		$this->db->delete('conf', $item);
	}
}