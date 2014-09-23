<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* load hclib */
include_once( dirname(__FILE__) . '/../../hclib/_bootstrap.php' );

if ( ! function_exists('hc_ci_before_exit'))
{
	function hc_ci_before_exit()
	{
	/* this is a hack to ensure that post controller and post system hooks are triggered */
		$GLOBALS['EXT']->_call_hook('post_controller');
		$GLOBALS['EXT']->_call_hook('post_system');
	}
}

if ( ! function_exists('hc_basename'))
{
	function hc_basename( $path )
	{
		if( substr($path, -1) == '/' )
			$path = substr($path, 0, -1);

		$return = $path;
		if( preg_match("/^(.+)\/(.+)$/", $path, $ma) )
			$return = $ma[2];
		return $return;
	}
}

if ( ! function_exists('hc_parse_args'))
{
	function hc_parse_args( $args )
	{
		$return = array();
		for( $ii = 0; $ii < count($args); $ii = $ii + 2 )
		{
			if( isset($args[$ii + 1]) )
			{
				$k = $args[$ii];
				$v = $args[$ii + 1];
				$return[ $k ] = $v;
			}
		}
		return $return;
	}
}

if ( ! function_exists('hc_dirname'))
{
	function hc_dirname( $path )
	{
		if( substr($path, -1) == '/' )
			$path = substr($path, 0, -1);

		$return = '';
		if( preg_match("/^(.+)\/.+$/", $path, $ma) )
			$return = $ma[1];
		return $return;
	}
}

if ( ! function_exists('hc_run_notifier'))
{
	function hc_run_notifier()
	{
		$CI =& ci_get_instance();
		if( isset($CI->hc_notifier) )
		{
			$CI->hc_notifier->run();
		}
	}
}

if ( ! function_exists('hc_orphan_errors'))
{
	function hc_orphan_errors( $errors, $fields )
	{
		$return = $errors;
		reset( $fields );
		foreach( $fields as $f )
		{
			if( 
				isset($return[$f['name']])
				&&
				( (! isset($f['complex'])) OR (! $f['complex']) )
				)
			{
				unset( $return[$f['name']] );
			}
		}
		return $return;
	}
}

if ( ! function_exists('hc_pick_random'))
{
	function hc_pick_random( $array, $many = 1 )
	{
		if( $many > 1 )
		{
			$return = array();
			$ids = array_rand($array, $many );
			foreach( $ids as $id )
				$return[] = $array[$id];
		}
		else
		{
			$id = array_rand($array);
			$return = $array[$id];
		}
		return $return;
	}
}

if ( ! function_exists('hc_random'))
{
	function hc_random( $len = 8 )
	{
		$salt1 = '0123456789';
		$salt2 = 'abcdef';

//		$salt .= 'abcdefghijklmnopqrstuvxyz';
//		$salt .= 'ABCDEFGHIJKLMNOPQRSTUVXYZ';

		srand( (double) microtime() * 1000000 );
		$return = '';
		$i = 1;
		$array = array();

		while ( $i <= ($len - 1) ){
			$num = rand() % strlen($salt1 . $salt2);
			$tmp = substr($salt1 . $salt2, $num, 1);
			$array[] = $tmp;
			$i++;
			}
		shuffle( $array );

	// first is letter
		$num = rand() % strlen($salt2);
		$tmp = substr($salt2, $num, 1);
		array_unshift($array, $tmp);

		$return = join( '', $array );
		return $return;
	}
}

function hc_form_input(
	$field,
	$defaults = array(),
	$errors = array(),
	$show_label = TRUE
	)
{
	$out = '';

	if( isset($field['hide']) && $field['hide'] )
		return;

	$keep = array( 'conf', 'data', 'id', 'name', 'value', 'type', 'required', 'label', 'options', 'readonly', 'disabled', 'style', 'extra', 'text_before', 'text_after', 'cols', 'rows', 'help');
	$unset = array( 'type', 'label', 'extra', 'text_before', 'text_after' );

	reset( $keep );
	$f = array();
	foreach( $keep as $k )
	{
		if( isset($field[$k]) )
			$f[$k] = $field[$k];
	}

	$error = isset($errors[$f['name']]) ? $errors[$f['name']] : '';
	$class = $error ? 'control-group error' : 'control-group';

	$required = ( (isset($f['required']) && $f['required']) OR (isset($field['rules']) && is_array($field['rules']) && in_array('required', $field['rules'])) ) ? TRUE : FALSE;

	$type = isset($f['type']) ? $f['type'] : '';
	$label = isset($f['label']) ? $f['label'] : '';
	$text_before = isset($f['text_before']) ? $f['text_before'] : '';
	$text_after = isset($f['text_after']) ? $f['text_after'] : '';
	$label = isset($f['label']) ? $f['label'] : '';
	$extra = '';

	if( isset($field['size']) )
	{
		if( isset($f['extra']['style']) )
		{
			$f['extra']['style'] .= '; width: ' . $field['size'] . 'em;';
		}
		else
		{
			$f['extra']['style'] = 'width: ' . $field['size'] . 'em;';
		}
	}

	if( isset($f['extra']) )
	{
		$extra = array();
		reset( $f['extra'] );
		foreach( $f['extra'] as $k => $v )
		{
			$extra[] = $k . '="' . $v . '"';
		}
		$extra = join( ' ', $extra );
	}

	if( isset($defaults[$f['name']]) )
		$default = $defaults[$f['name']];
	elseif( (substr($f['name'], -2) == '[]') && isset($defaults[substr($f['name'], 0, -2)]) )
		$default = $defaults[substr($f['name'], 0, -2)];
	elseif( isset($field['default']) )
		$default = $field['default'];
	else
		$default = NULL;

	foreach( $unset as $k )
	{
		unset( $f[$k] );
	}

	if( $type == 'hidden' )
	{
		$out .= form_hidden($f['name'], $default);
	}
	else
	{
		$label = Hc_lib::parse_lang( $label );

		if( $show_label )
		{
			$out .= '<div class="' . $class . '">';
			$out .= '<label class="control-label" for="' . $f['name'] . '">' . $label;
			if( $required )
			{
				$out .= ' *';
			}
			$out .= '</label>';
			$out .= '<div class="controls">';
		}

		switch( $type )
		{
			case 'plain':
				if( isset($f['options']) && isset($f['options'][$default]) )
				{
					$view = $f['options'][$default];
				}
				else
				{
					$view = $default;
				}
				$out .= $view;
				break;

			case 'label':
				if( isset($f['options']) && isset($f['options'][$default]) )
				{
					$view = $f['options'][$default];
				}
				else
				{
					$view = $default;
				}
				$out .= '<span class="uneditable-input">' . $view . '</span>';
				break;

			case 'dropdown':
				if( isset($f['id']) )
					$extra .= ' id="' . $f['id'] . '"';

				$this_name = $f['name'];
				$multiple = (strpos($extra, 'multiple') !== FALSE);
				if( $multiple )
				{
					$this_name = $f['name'] . '[]';
				}
				$out .= form_dropdown($this_name, $f['options'], set_value($f['name'], $default), $extra);

				if( $text_after )
					$out .= '&nbsp' . $text_after;
				break;

			case 'textarea':
				$out .= form_textarea( $f, set_value($f['name'], $default), $extra );
				break;

			case 'radio':
				$out .= '<div class="radio">';
				foreach( $f['options'] as $fk => $fv )
				{
					$out .= '<label style="display: block;">';
					$ff = $f;
					unset( $ff['options'] );
					$checked = ($default == $fk) ? TRUE : FALSE;

					$out .= form_radio( $ff, $fk, $checked, $extra );
					$out .= Hc_lib::parse_lang($fv);
					$out .= '</label>';
				}
				$out .= '</div>';
				break;

			case 'date':
				$out .= hc_form_date( $f, $default, $extra );
				break;

			case 'time':
				$out .= hc_form_time( $f, $default, $extra );
				break;

			case 'timeframe':
				$out .= hc_form_timeframe( $f, $default, $extra );
				break;

			case 'weekday':
				$out .= hc_form_weekday( $f, $default );
				break;

			case 'checkbox':
				if( $text_before )
					$out .= $text_before . '&nbsp';

				$value = isset($f['value']) ? $f['value'] : 1;

				if( is_array($default) )
					$checked = in_array($value, $default) ? TRUE : FALSE;
				else
					$checked = ($default == $value) ? TRUE : FALSE;

				if( isset($f['readonly']) && $f['readonly'] )
				{
					$out .= form_hidden( $f['name'], $value );
					$extra .= ' disabled="disabled"';
					$out .= form_checkbox( $f['name'] . '_justview', $value, $checked, $extra );
				}
				else
				{
					$out .= form_checkbox( $f['name'], $value, $checked, $extra );
				}

				if( $text_after )
					$out .= '&nbsp' . $text_after;
				break;

			case 'checkbox_set':
				if( $text_before )
					$out .= $text_before . '&nbsp';

				$value = isset($f['value']) ? $f['value'] : $default;
				if( ! is_array($value) )
					$value = array($value);
				$this_out = array();
				foreach( $f['options'] as $option_value => $option_label )
				{
					$checkbox_field = array(
						'name'		=> $f['name'] . '[]',
						'type'		=> 'checkbox',
						'value'		=> $option_value,
						);

					$checked = in_array($option_value, $value) ? TRUE : FALSE;
					$this_out[] = '<div class="checkbox checkbox-inline">';
					$this_out[] = 
						'<label>' . 
						form_checkbox( $checkbox_field, $option_value, $checked ) .
						$option_label .
						'</label>';
					$this_out[] = '</div>';
				}
				$out .= join( ' ', $this_out );

				if( $text_after )
					$out .= '&nbsp' . $text_after;
				break;

			case 'password':
				$f['type'] = 'password';
			default:
				if( $text_before )
					$out .= $text_before . '&nbsp';
				$out .= form_input( $f, $default, $extra );
				if( $text_after )
					$out .= '&nbsp' . $text_after;
				break;
		}

		if( $error )
		{
			$out .= '<span class="help-inline">' . $error . '</span>';
		}

		if( isset($f['help']) && (! $error) )
		{
			$out .= '<span class="help-block"><em>' . Hc_lib::parse_lang($f['help']) . '</em></span>';
		}
	}
	return $out;
}

function hc_format_time_of_day( $ts )
{
	$CI =& ci_get_instance();
	$CI->hc_time->setDateDb( 20130118 );
	$CI->hc_time->modify( '+' . $ts . ' seconds' );
	$return = $CI->hc_time->formatTime();
	return $return;
}

function hc_form_time($data = '', $value = '', $extra = '')
{
	$step = 15 * 60;
	$out = '';
	$options = array();
	$CI =& ci_get_instance();
	$CI->hc_time->setDateDb( 20130118 );

	$start_with = 0;
	$end_with = 24 * 60 * 60;
	if( isset($data['conf']['min']) && ($data['conf']['min'] > $start_with) )
	{
		$start_with = $data['conf']['min'];
	}
	if( isset($data['conf']['max']) && ($data['conf']['max'] < $end_with) )
	{
		$end_with = $data['conf']['max'];
	}
	if( $end_with < $start_with )
	{
		$end_with = $start_with;
	}

	if( $value && ($value > $end_with) )
	{
		$value = $value - 24 * 60 * 60;
	}

	if( $start_with )
		$CI->hc_time->modify( '+' . $start_with . ' seconds' );

	$no_of_steps = ( $end_with - $start_with) / $step;
	for( $ii = 0; $ii <= $no_of_steps; $ii++ )
	{
		$sec = $start_with + $ii * $step;
		$options[ $sec ] = $CI->hc_time->formatTime();
		$CI->hc_time->modify( '+' . $step . ' seconds' );
	}

	$value = set_value($data['name'], $value);

	$extra = $extra ? array($extra) : array();
	$extra[] = 'style="width: 8em;"';
	if( isset($data['readonly']) && $data['readonly'] )
	{
		$extra[] = ' readonly="readonly" disabled="disabled"';
	}
	$extra = join( ' ', $extra );
	$out .= form_dropdown( $data['name'], $options, $value, $extra );
	return $out;
}

function hc_form_timeframe($data = '', $value = '', $extra = '')
{
	$out = '';
	$my_name = $data['name'];
	$data['name'] = $my_name . '_start';
	$out .= hc_form_time( $data, $value, $extra );

	$out .= ' - ';

	$data['name'] = $my_name . '_end';
	$out .= hc_form_time( $data, $value, $extra );

	return $out;
}

function hc_form_weekday($data = '', $value = array(), $extra = '')
{
	$out = '';
	$CI =& ci_get_instance();
	$CI->hc_time->setNow();
	$CI->hc_time->setStartWeek();
	
	if( ! is_array($value) )
	{
		if( strlen($value) )
			$value = array( $value );
		else
			$value = array();
	}

	for( $ii = 0; $ii <= 6; $ii++ )
	{
		$this_value = $CI->hc_time->getWeekday();
		$checked = in_array($this_value, $value) ? TRUE : FALSE;
		$out .= '<label class="checkbox inline">';
		$out .= form_checkbox( $data['name'] . '[]', $this_value, $checked );
		$out .= $CI->hc_time->formatWeekdayShort();
		$out .= '</label>';
		$CI->hc_time->modify( '+1 day' );
	}

//	$value = set_value($data['name'], $value);
//	$out .= form_dropdown( $data['name'], $options, $value, 'style="width: 8em;"' );
	return $out;
}

function hc_form_date($data = '', $value = '', $extra = '')
{
	$out = '';
	$id = 'nts-' . $data['name'];
	$display_name = $data['name'] . '_display';
	$display_id = 'nts-' . $display_name;

	$CI =& ci_get_instance();
	if( $value )
	{
		$CI->hc_time->setDateDb( $value );
	}
	else
	{
		$CI->hc_time->setNow();
	}
	$datepicker_format = $CI->hc_time->formatToDatepicker();

	$data['id'] = $id;
	$data['data-date-format'] = $datepicker_format;
	$data['data-date-week-start'] = $CI->hc_time->weekStartsOn;
	$data['style'] = 'width: 8em;';

/* init value */
	if( $value )
	{
		$CI->hc_time->setDateDb( $value );
	}
	else
	{
		$CI->hc_time->setNow();
	}
	$value = $CI->hc_time->formatDate_Db();

	$options = array();
	if( isset($data['options']) )
	{
		reset( $data['options'] );
		foreach( $data['options'] as $k => $v )
		{
			switch( $k )
			{
				case 'startDate':
					if( $v > $value )
					{
						$value = $v;
					}
					$CI->hc_time->setDateDb( $v );
					$v = $CI->hc_time->formatDate();
					break;
			}
			$options[] = "$k: \"$v\"";
		}
	}
	$options[] = "weekStart: " . $CI->hc_time->weekStartsOn;

	unset( $data['options'] );
	$CI->hc_time->setDateDb( $value );
	$options = join( ",\n", $options );

	$value = set_value($data['name'], $value);

// hidden
	$out .= form_input( 
		array(
			'name' => $data['name'],
			'type'=>'hidden',
			'id' => $id
			),
		$value
		);

// display
	$value = $CI->hc_time->formatDate();
	$value = set_value($data['name'], $value);

	$data['id'] = $display_id;
	$data['name'] = $display_name;
	$out .= form_input( $data, $value, $extra );
	$out .= <<<EOT

<script language="JavaScript">
jQuery('#$display_id').datepicker({
	$options,
	dateFormat: '$datepicker_format',
	autoclose: true
	})
	.on('changeDate', function(ev)
		{
		var dbDate = 
			ev.date.getFullYear() 
			+ "" + 
			("00" + (ev.date.getMonth()+1) ).substr(-2)
			+ "" + 
			("00" + ev.date.getDate()).substr(-2);
		jQuery('#$id').val( dbDate );
		});
</script>

EOT;
	return $out;
}

if ( ! function_exists('_print_r'))
{
	function _print_r( $thing )
	{
		echo '<pre>';
		print_r( $thing );
		echo '</pre>';
	}
}

function hc_format_price( $amount, $calculated_price = '' ){
	$CI =& ci_get_instance();

	$before_sign = $CI->app_conf->get( 'currency_sign_before' );
	$currency_format = $CI->app_conf->get( 'currency_format' );
	list( $dec_point, $thousand_sep ) = explode( '||', $currency_format );
	$after_sign = $CI->app_conf->get( 'currency_sign_after' );

	$amount = number_format( $amount, 2, $dec_point, $thousand_sep );
	$return = $before_sign . $amount . $after_sign;

	if( strlen($calculated_price) && ($amount != $calculated_price) ){
		$calc_format = $before_sign . number_format( $calculated_price, 2, $dec_point, $thousand_sep ) . $after_sign;
		$return = $return . ' <span style="text-decoration: line-through;">' . $calc_format . '</span>';
		}
	return $return;
	}

if ( ! function_exists('hc_list_subfolders'))
{
	function hc_list_subfolders( $dirName )
	{
		if( ! is_array($dirName) )
			$dirName = array( $dirName );

		$return = array();
		reset( $dirName );
		foreach( $dirName as $thisDirName ){
			if ( file_exists($thisDirName) && ($handle = opendir($thisDirName)) ){
				while ( false !== ($f = readdir($handle)) ){
					if( substr($f, 0, 1) == '.' )
						continue;
					if( is_dir( $thisDirName . '/' . $f ) ){
						if( ! in_array($f, $return) )
							$return[] = $f;
						}
					}
				closedir($handle);
				}
			}

		sort( $return );
		return $return;
	}
}

if ( ! function_exists('hc_list_files'))
{
	function hc_list_files( $dirName, $extension = '' )
	{
		if( ! is_array($dirName) )
			$dirName = array( $dirName );

		$files = array();
		foreach( $dirName as $thisDirName )
		{	
			if ( file_exists($thisDirName) && ($handle = opendir($thisDirName)) )
			{
				while ( false !== ($f = readdir($handle)) )
				{
					if( substr($f, 0, 1) == '.' )
						continue;

					if( is_file( $thisDirName . '/' . $f ) )
					{
						if( (! $extension ) || ( substr($f, - strlen($extension)) == $extension ) )
							$files[] = $f;
					}
				}
				closedir($handle);
			}
		}
		sort( $files );
		return $files;
	}
}

function hc_urlify($str)
{
	$return = str_replace( '_', '-', $str );
	return $return;
}

function hc_build_html_attr( $array )
{
	$return = array();
	foreach( $array as $k => $v )
	{
		$return[] = $k . '="' . $v . '"';
	}
	$return = join( ' ', $return );
	return $return;
}

function hc_build_csv( $array, $separator = ',' )
{
	$processed = array();
	reset( $array );
	foreach( $array as $a ){
		if( strpos($a, '"') !== false ){
			$a = str_replace( '"', '""', $a );
			}
		if( strpos($a, $separator) !== false ){
			$a = '"' . $a . '"';
			}
		$processed[] = $a;
		}

	$return = join( $separator, $processed );
	return $return;
}

function hc_dropdown_menu( $menu, $wrap = 'li', $me = '', $toggler = '' )
{
	if( ! $me )
		$level = 0;
	elseif(	strpos($me, '_') === FALSE )
		$level = 1;
	else
		$level = 2;

	$start = $me ? $me . '_' : $me;
	reset( $menu );

	if( $me && (! is_array($menu[$me])) && ($menu[$me] == 'divider') )
	{
		$out = '<li class="divider"></li>' . "\n";
		return $out;
	}

	if( $me && (is_array($menu[$me])) && (! is_array($menu[$me][1])) && ($menu[$me][1] == 'header') )
	{
		$out = '<li class="nav-header">' . $menu[$me][0] . '</li>' . "\n";
		return $out;
	}

	$out = array();

// get items for this 
	$my_items = array();
	foreach( array_keys($menu) as $k )
	{
		if( substr($k, 0, strlen($start)) == $start )
		{
			$left = substr($k, strlen($start));
			if(	strpos($left, '_') === FALSE ) // not further child
			{
				$my_items[] = $k;
			}
		}
	}

	switch( $level )
	{
/* zero level */
		case 0:
			if( $my_items )
			{
				reset( $my_items );
				foreach( $my_items as $mi )
				{
					$subitems = FALSE;
					$substart = $mi . '_';
					foreach( array_keys($menu) as $k )
					{
						if( substr($k, 0, strlen($substart)) == $substart )
						{
							$subitems = TRUE;
							break;
						}
					}

					if( $subitems )
						$out[] = '<' . $wrap . ' class="dropdown">';
					else
						$out[] = '<' . $wrap . '>';

					$out[] = hc_dropdown_menu( $menu, $wrap, $mi, $toggler );

					$out[] = '</' . $wrap . '>';
				}
			}
			break;

/* first level */
		case 1:
			if( ! $my_items )
			{
				$la = isset($menu[$me][1]) ? $menu[$me][1] : array();
				$out[] = '<a ' . hc_build_html_attr($la) . '>' . $menu[$me][0] . '</a>';
			}
			else
			{
				$la = isset($menu[$me][1]) ? $menu[$me][1] : array();
				$la['href'] = '#';
				$la['data-toggle'] = 'dropdown';
				$la['class'] = isset($la['class']) ? $la['class'] . ' dropdown-toggle' : 'dropdown-toggle';
				$label = $menu[$me][0];

				if( $toggler == 'btn' )
				{
					$la['class'] .= ' btn btn-default btn-sm';
					$label = lang('common_actions');
				}

				$out[] = '<a ' . hc_build_html_attr($la) . '>' . $label . ' <span class="caret"></span>';
				if( isset($menu[$me][2]) && $menu[$me][2] )
				{
					$float_right = $menu[$me][2];
					$out[] = '<div class="pull-right">' . $float_right . '</div>';
				}

				$out[] = '</a>';
				$out[] = '<ul class="dropdown-menu" style="overflow: visible;">';
				reset( $my_items );
				foreach( $my_items as $mi )
				{
					$out[] = hc_dropdown_menu( $menu, $wrap, $mi );
				}
				$out[] = '</ul>';
			}

			break;

/* next levels */
		default :
			if( $my_items )
			{
				$out[] = '<li class="dropdown-submenu">';

				$la = isset($menu[$me][1]) ? $menu[$me][1] : array();
				$la['href'] = '#';
				$out[] = '<a ' . hc_build_html_attr($la) . '>' . $menu[$me][0] . '</a>';

				$out[] = '<ul class="dropdown-menu" style="overflow: visible;">';
				reset( $my_items );
				foreach( $my_items as $mi )
				{
					$out[] = hc_dropdown_menu( $menu, $wrap, $mi );
				}
				$out[] = '</ul>';

				$out[] = '</li>';
			}
			else
			{
				$out[] = '<li>';

				$la = isset($menu[$me][1]) ? $menu[$me][1] : array();
				$out[] = '<a ' . hc_build_html_attr($la) . '>' . $menu[$me][0] . '</a>';

				$out[] = '</li>';
			}
			break;
	}

	$out = join( "\n", $out );
	return $out;
}

/* End of file array_helper.php */
/* Location: ./application/helpers/hitcode.php */