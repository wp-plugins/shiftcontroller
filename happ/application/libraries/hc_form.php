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

	function input( $field, $add_class = TRUE )
	{		
		if( $add_class )
		{
			if( ! isset($field['extra']['class']) )
				$field['extra']['class'] = '';
			if( $field['extra']['class'] )
				$field['extra']['class'] .= ' ';
			if( ! (isset($field['type']) && ( in_array($field['type'], array('checkbox', 'radio', 'hidden')) ) ) )
			{
				$field['extra']['class'] .= 'form-control';
			}
		}

		return hc_form_input(
			$field,
			$this->defaults,
			$this->errors,
			FALSE
			);
	}

	function build_input( $field, $show_error = FALSE )
	{
		if( ! isset($field['extra']['class']) )
			$field['extra']['class'] = '';
		if( $field['extra']['class'] )
			$field['extra']['class'] .= ' ';
		if( ! (isset($field['type']) && ( in_array($field['type'], array('checkbox', 'radio', 'hidden')) ) ) )
		{
			$field['extra']['class'] .= 'form-control';
		}

		if( $show_error )
		{
			$view = hc_form_input(
				$field,
				$this->defaults,
				$this->errors,
				FALSE
				);
		}
		else
		{
			$view = hc_form_input(
				$field,
				$this->defaults,
				array(),
				FALSE
				);
		}

		$type = isset($field['type']) ? $field['type'] : '';
		$error = isset( $this->errors[$field['name']] ) ? $this->errors[$field['name']] : '';
		$help = isset($field['help']) ? $field['help'] : '';

		$return = new stdClass;
		$return->type = $type;
		$return->view = $view;
		$return->error = $error;
		$return->help = isset($field['help']) ? $field['help'] : '';
		return $return;
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