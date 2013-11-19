<?php
class Hc_events 
{
	var $events;

	function __construct()
	{
		$CI =& ci_get_instance();
		$CI->config->load( 'events', TRUE );
		$this->events = $CI->config->item( 'events' );
	}

	function trigger( $event, $payload )
	{
		$CI =& ci_get_instance();
		if( isset($this->events[$event]) )
		{
			$args = func_get_args();
			array_shift( $args );

			reset( $this->events[$event] );
			foreach( $this->events[$event] as $call )
			{
				if( $CI->load->module_file($call['file']) )
				{
					if( ! class_exists($call['class']) )
					{
						// if class doesn't exist check that the function is callable
						// could be just a helper function
						if(is_callable($call['method']))
						{
							call_user_func_array( $call['method'], $args );
						}
						continue;
					}

					$class = new $call['class'];

					if( ! is_callable( array($class, $call['method']) ))
					{
						unset($class);
						continue;
					}
					call_user_func_array( array($class, $call['method']), $args );
					unset($class);
				}
				else
				{
				}
			}
		}
	}
}