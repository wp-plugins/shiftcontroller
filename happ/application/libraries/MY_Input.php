<?php
class MY_Input extends CI_Input {
	function post( $index = '', $xss_clean = FALSE )
	{
		if($index === '')
        {
			$return = $_POST ? TRUE : FALSE;
			return $return;
		}
		return parent::post($index, $xss_clean);
    }
}