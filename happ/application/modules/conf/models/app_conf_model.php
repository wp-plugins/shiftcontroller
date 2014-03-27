<?php
/* copy some base stuff from CI_Model not to explicitely initialize MY_Model */

//class App_conf_model extends CI_model
class App_conf_model
{
	function get_all( )
	{
		$this->db->select('name, value');
		$result	= $this->db->get('conf');

		$return	= array();
		foreach($result->result_array() as $i)
		{
			if( isset($return[$i['name']]) )
			{
				if( ! is_array($return[$i['name']]) )
					$return[$i['name']] = array( $return[$i['name']] );
				if( ! in_array($i['value'], $return[$i['name']]) )
					$return[$i['name']][] = $i['value'];
			}
			else
			{
				$return[$i['name']] = $i['value'];
			}
		}
		return $return;
	}

	function save( $pname, $pvalue )
	{
		if( is_array($pvalue) )
		{
			$this->db->where( 'name', $pname );
			$this->db->select('name, value');
			$result	= $this->db->get('conf');

			$current = array();
			foreach($result->result_array() as $i)
			{
				$current[] = $i['value'];
			}

			$to_delete = array_diff( $current, $pvalue );
			$to_add = array_diff( $pvalue, $current );
			foreach( $to_add as $v )
			{
				$item = array(
					'name'	=> $pname,
					'value'	=> $v
					);
				$this->db->insert('conf', $item);
			}
			foreach( $to_delete as $v )
			{
				$this->db->where('name', $pname);
				$this->db->where('value', $v);
				$this->db->delete('conf');
			}
		}
		else
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
	}

	function delete( $pname )
	{
		$this->db->where('name', $pname);
		$this->db->delete('conf', $item);
	}

	function __get($key)
	{
		$CI =& ci_get_instance();
		return $CI->$key;
	}
}