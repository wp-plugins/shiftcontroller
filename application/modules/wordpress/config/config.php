<?php
// set timezone
$tz = get_option('timezone_string');
if( strlen($tz) )
{
	date_default_timezone_set( $tz );
}
?>