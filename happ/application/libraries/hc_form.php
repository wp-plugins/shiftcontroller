<?php
class Hc_form
{
	var $defaults;
	var $errors;

	function set_defaults( $defaults )
	{
		reset( $defaults );
		foreach( $defaults as $k => $v )
		{
			$this->set_default( $k, $v );
		}
	}

	function set_default( $name, $value )
	{
		$this->defaults[$name] = $value;
	}

	function is_set_default( $name )
	{
		return isset($this->defaults[$name]);
	}

	function get_default( $name )
	{
		$return = isset($this->defaults[$name]) ? $this->defaults[$name] : NULL;
		return $return;
	}

	function get_defaults()
	{
		return $this->defaults;
	}

	function set_errors( $errors )
	{
		$this->errors = $errors;
	}

	function input( $field )
	{
		return hc_form_input(
			$field,
			$this->defaults,
			$this->errors,
			FALSE
			);
	}

	function error( $name )
	{
		if( isset($this->errors[$name]) )
			$return = $this->errors[$name];
		else
			$return = FALSE;
		return $return;
	}

	function errors()
	{
		return $this->errors;
	}
}