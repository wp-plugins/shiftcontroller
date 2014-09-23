<?php
class MY_model extends DataMapper
{
	static $titles = array();
	static $relations = array();

	var $table = 'users';
	var $build_title = array(
		'_my_class()_',
		': ',
		'_id_'
		);
	var $field_view = array();
	var $allow_none = TRUE;
	var $_old = array();

	function __construct( $id = NULL )
	{
		parent::__construct( $id );
		$my_class = $this->my_class();

	/* runtime relations configuration */
		if( empty(self::$relations) )
		{
			$this->config->load('relations', TRUE, TRUE );
		}

		if( ! isset(self::$relations[$my_class]) )
		{
			self::$relations[$my_class] = array(
				'has_many' => array(),
				'has_one' => array(),
				);
		}

		$schema = $this->config->item( $my_class, 'relations' );

		if( $schema )
		{
			if( isset($schema['has_many']) )
			{
				self::$relations[$my_class]['has_many'] = array_merge( self::$relations[$my_class]['has_many'], $schema['has_many'] );
			}

			if( isset($schema['has_one']) )
			{
				self::$relations[$my_class]['has_one'] = array_merge( self::$relations[$my_class]['has_one'], $schema['has_one'] );
			}
		}

		reset( self::$relations[$my_class]['has_many'] );
		foreach( self::$relations[$my_class]['has_many'] as $c => $rel )
		{
			$this->has_many( $c, $rel );
		}

		reset( self::$relations[$my_class]['has_one'] );
		foreach( self::$relations[$my_class]['has_one'] as $c => $rel )
		{
			$this->has_one( $c, $rel );
		}

		/* fill in defaults */
		if( ! $id )
		{
			if( $my_class == 'shift' )
			{
				$my_fields = $this->get_fields();
				foreach( $my_fields as $mf )
				{
					if( isset($mf['default']) )
					{
						$this->{$mf['name']} = $mf['default'];
					}
				}
			}
		}
	}

	function remove_validation( $what )
	{
		unset( $this->validation[$what] );
	}

	function remove_validation_rule( $what, $rule )
	{
		if( ! isset($this->validation[$what]) )
		{
			return;
		}

		$rule_keys = array_keys($this->validation[$what]['rules']);
		foreach( $rule_keys as $k )
		{
			$remove_this = FALSE;
			if( is_numeric($k) ) // compare value
			{
				if( $rule == $this->validation[$what]['rules'][$k] )
				{
					$remove_this = TRUE;
					break;
				}
			}
			else // compare key
			{
				if( $rule == $k )
				{
					$remove_this = TRUE;
					break;
				}
			}
			if( $remove_this )
			{
				unset( $this->validation[$what]['rules'][$k] );
				break;
			}
		}
	}

	function title(){
		$return = array();
		reset( $this->build_title );
		foreach( $this->build_title as $bt )
		{
			if( 
				(substr($bt, 0, 1) == '_') &&
				(substr($bt, -1) == '_')
				)
			{
				$prop = substr($bt, 1, -1);
				if( substr($prop, -2) == '()' ) // my method
				{
					$func = substr($prop, 0, -2);
					$return[] = $this->{$func}();
				}
				else // my prop
				{
					$return[] = $this->{$prop};
				}
			}
			else // just string like space or brackets
			{
				$return[] = $bt;
			}
		}
		$return = join( '', $return );
		return $return;
	}

	private function _build_title_select()
	{
		$return = array();
		reset( $this->build_title );
		foreach( $this->build_title as $bt )
		{
			if( 
				(substr($bt, 0, 1) == '_') &&
				(substr($bt, -1) == '_')
				)
			{
				$prop = substr($bt, 1, -1);
				if( substr($prop, -2) == '()' )
				{
				}
				else
				{
					$return[] = $prop;
				}
			}
			else
			{
				$return[] = "\"" . $bt . "\"";
			}
		}
		$return = join( ',', $return );
		return $return;
	}

	function titles()
	{
		$class = get_class($this);
		if( ! isset(self::$titles[$class]) )
		{
			self::$titles[$class] = $this->_load_titles();
		}
		return self::$titles[$class];
	}

	protected function _load_titles()
	{
		$return = array();
		$this->clear();
		$select = $this->_build_title_select();
//		$this->select( 'id' );
		$this->select( '*' );
		$this->select( 'CONCAT(' . $select . ') AS title', FALSE );
		$this->get();

		foreach( $this as $u )
		{
			$return[ $u->id ] = $u->title;
		}
		return $return;
	}

	function csv_upload( $file_name = 'userfile', $separator = ',' )
	{
		$return = NULL;
		$fields = $this->get_fields();
		reset( $fields );
		foreach( $fields as $f )
		{
			$my_fields[] = $f['name'];
		}

		if( isset($_FILES[$file_name]) && is_uploaded_file($_FILES[$file_name]['tmp_name']) )
		{
			$tmp_name = $_FILES[$file_name]['tmp_name'];
			$return = array();

			$parse_error = FALSE;
			if( ($handle = fopen($tmp_name, "r")) !== FALSE)
			{
				$line_no = 0;
				while( ($line = fgetcsv($handle, 1000, $separator)) !== FALSE )
				{
					// titles
					if( ! $line_no )
					{
						$prop_names = $line;
						for( $ii = 0; $ii < count($prop_names); $ii++ )
						{
							reset( $fields );
							foreach( $fields as $f )
							{
								if( strtolower($prop_names[$ii]) == $f['name'] )
								{
									$prop_names[$ii] = strtolower($prop_names[$ii]);
								}
							}
						}
						$prop_count = count( $prop_names );

					// check for mandatory fields
						$missing_fields = array();
						reset( $fields );
						foreach( $fields as $f )
						{
							if( isset($f['required']) && $f['required'] ){
								if( ! in_array($f['name'], $prop_names) ){
									$missing_fields[] = $f['name'];
									}
								}
						}
						if( $missing_fields )
						{
							$err_msg = lang('conf_import_error_fields_missing') . ': ' . join(', ', $missing_fields);
							$this->error_message( 'import', $err_msg );
							$parse_error = TRUE;
							break;
						}

					// check if any fields are not parsed
						$not_parsed_fields = array();
						reset( $prop_names );
						foreach( $prop_names as $f )
						{
							$f = trim( $f );
							if( ! $f )
								continue;
								
							if( ! (in_array($f, $my_fields) OR in_array(strtolower($f), $my_fields)) )
							{
								$not_parsed_fields[] = $f;
							}
						}
						if( $not_parsed_fields )
						{
							$err_msg = lang('conf_import_message_fields_not_recognized') . ': ' . join(', ', $not_parsed_fields);
							$this->error_message( 'import', $err_msg );
						}
					}
					else
					{
						$values = array();
						for( $i = 0; $i < $prop_count; $i++ )
						{
							$check_name = strtolower($prop_names[$i]);
							if( in_array($check_name, $my_fields) )
							{
								if( isset($line[$i]) )
									$values[ $check_name ] = $line[$i];
								else
									$values[ $check_name ] = '';
							}
						}
						$return[] = $values;
					}
					$line_no++;
				}
				fclose($handle);
			}

			if( $parse_error )
			{
				$return = NULL;
			}
		}
		return $return;
	}

	function csv( $separator = ',', $skip = array() )
	{
		$related_fields = array_merge( $this->has_one, $this->has_many );

	// header
		$headers = array();
		$fields = $this->get_fields();
		reset( $fields );
		foreach( $fields as $f )
		{
			if( in_array($f['name'], $skip) )
				continue;
//			$headers[ $f['name'] ] = Hc_lib::parse_lang( $f['label'] );
			$headers[ $f['name'] ] = $f['name'];
		}

		$data = array();
		$data[] = join( $separator, $headers );

	// entries
		$keys = array_keys( $headers );
		$this->clear();
		$this->get();
		foreach( $this as $s )
		{
			$e = array();
			reset( $keys );
			foreach( $keys as $k )
			{
				if( isset($related_fields[$k]) )
				{
					$e[] = $s->{$k}->get()->title();
				}
				else
				{
					$e[] = $s->{$k};
				}
			}
			$data[] = hc_build_csv( $e, $separator );
		}
		$return = join( "\n", $data );
		return $return;
	}
	
    function my_class()
	{
		$return = get_class($this);
		$return = strtolower($return);
		$suffix = '_model';
		if( substr($return, -strlen($suffix)) == $suffix )
		{
			$return = substr( $return, 0, -strlen($suffix) );
		}
		return $return;
	}

	public function get_fields()
	{
		return $this->my_fields;
	}

	public function get_form_fields()
	{
		$return = array();

		$fields = $this->get_fields();
		foreach( $fields as $tf )
		{
			$return[$tf['name']] = $tf;
		}

		$fnames = array_keys($return);
	/* assign options to dropdowns */
		foreach( $fnames as $fn )
		{
			if( 
				( isset($return[$fn]['type']) && ($return[$fn]['type'] != 'dropdown') ) OR 
				( isset($return[$fn]['options']) )
				)
				{
				continue;
				}

			$options = array();
		/* enum fields */
			if(
				isset($this->validation[$fn]) && isset($this->validation[$fn]['rules']) && 
				array_key_exists('enum', $this->validation[$fn]['rules']) &&
				is_array($this->validation[$fn]['rules']['enum'])
				)
			{
				$options = array();
				foreach( $this->validation[$fn]['rules']['enum'] as $o_id )
				{
					$options[ $o_id ] = $this->prop_text($fn, FALSE, $o_id);
				}
			}
		/* has one ? */
			elseif( isset($this->has_one[$fn]) )
			{
				$rel_props = $this->has_one[$fn];
				$other_model = new $rel_props['class'];

				$options = array();
				$select_label = '';
				if( 
					isset($this->validation[$fn]) && 
					isset($this->validation[$fn]['rules']) &&
					in_array('required', $this->validation[$fn]['rules'])
					)
				{
					if( ! $this->id )
					{
						$select_label = lang('common_select');
					}
				}
				else
				{
					$select_label = lang('common_select_later');
				}

				if( $select_label )
				{
					$options[0] = ' - ' . $select_label . ' - ';
				}

				$other_titles = $other_model->titles();
				if( count($other_titles) > 1 )
				{
					reset( $other_titles );
					foreach( $other_titles as $other_id => $other_title )
					{
						$options[ $other_id ] = $other_title;
					}
				}
				else
				{
					$other_ids = array_keys( $other_titles );
					$return[$fn]['type'] = 'hidden';
					$return[$fn]['default'] = $other_ids[0];
					$options = NULL;
				}
			}
			if( $options === NULL )
				unset( $return[$fn]['options'] );
			else
				$return[$fn]['options'] = $options;
		}
		return $return;
	}

	function get_field( $pname )
	{
		$return = array();
		$fields = $this->get_fields();
		reset( $fields );
		foreach( $fields as $f )
		{
			if( $pname == $f['name'] )
			{
				$return = $f;
				break;
			}
		}
		return $return;
	}

	function prop_name( $pname )
	{
		if( substr($pname, -3) == '_id' )
		{
			$short_pname = substr($pname, 0, -3);
			if(
				isset($this->has_one[$short_pname]) OR
				isset($this->has_many[$short_pname])
			)
			{
				$pname = $short_pname;
			}
		}
		return $pname;
	}

	function prop_label( $pname )
	{
		$return = '';
		$pname = $this->prop_name( $pname );

		$field = $this->get_field( $pname );
		if( ! $field )
			return;

		if( isset($field['label']) )
		{
			$return = $field['label'];
			$return = Hc_lib::parse_lang( $return );
		}
		else
		{
			$return = $field['name'];
		}
		return $return;
	}

	function view_text( $skip = array() )
	{
		$return = array();
		$fields = $this->get_fields();
		reset( $fields );
		foreach( $fields as $f )
		{
			if( in_array($f['name'], $skip) )
				continue;
			$label = isset($f['label']) ? $f['label'] : $f['name'];
			$label = Hc_lib::parse_lang( $label );
			$return[ $f['name'] ] = array( $label, $this->prop_text($f['name']) );
		}
		return $return;
	}

	function prop_text_class( $pname, $force_value = NULL )
	{
		$return = '';
		if( isset($args[1]) )
		{
			$value = $args[1];
		}
		else
		{
			$method = 'get_' . $pname;
			if( method_exists($this, $method) )
				$value = $this->{$method}();
			else
				$value = $this->{$pname};
		}

	/* prop_text explicitely defined */
		if( (! is_object($value)) && isset($this->prop_text[$pname][$value]) && is_array($this->prop_text[$pname][$value]) )
		{
			$return = $this->prop_text[$pname][$value][1];
		}
		return $return;
	}

	function decorate_prop( $pname, $value = NULL, $text = NULL, $add_title = '' )
	{
		if( $value === NULL )
			$value = $this->{$pname};
		if( $text === NULL )
			$text = $value;
		$return = $text;
		if( isset($this->prop_text[$pname][$value][1]) )
		{
			$class = $this->prop_text[$pname][$value][1];
			$title = Hc_lib::parse_lang( $this->prop_text[$pname][$value][0] );
			if( $add_title )
				$title = $add_title . ': ' . $title;
			$return = '<span title="' . $title . '" class="label label-' . $class . '">' . $return . '</span>';
		}
		return $return;
	}

	function prop_text( $pname, $decorate = FALSE, $force_value = NULL )
	{
		$return = '';
		$CI =& ci_get_instance();

		$args = func_get_args();
		if( isset($args[2]) )
		{
			$value = $args[2];
		}
		else
		{
			$method = 'get_' . $pname;
			if( method_exists($this, $method) )
				$value = $this->{$method}();
			else
				$value = $this->{$pname};
		}

	/* prop_text explicitely defined */
		if( (! is_object($value)) && isset($this->prop_text[$pname][$value])  )
		{
			$return = is_array($this->prop_text[$pname][$value]) ? $this->prop_text[$pname][$value][0] : $this->prop_text[$pname][$value]; 
			$return = Hc_lib::parse_lang( $return );
			if( $decorate && is_array($this->prop_text[$pname][$value]) )
			{
				$class = $this->prop_text[$pname][$value][1];
				$return = '<span class="label label-' . $class . '">' . $return . '</span>';
			}
			return $return;
		}

		$f = $this->get_field( $pname );
		if( ! $f )
			return $value;

		$type = isset($f['type']) ? $f['type'] : 'text';
		switch( $type )
		{
			case 'date':
				$CI->load->library( 'hc_time' );
				$CI->hc_time->setDateDb( $value );
				$return = $CI->hc_time->formatDateFull();
				break;
			case 'time':
				$CI->load->library( 'hc_time' );
				$return = $CI->hc_time->formatTimeOfDay( $value );
				break;
			case 'boolean':
				$return = $value ? lang('common_yes') : lang('common_no');
				break;
			default:
				if( is_object($value) )
				{
					$return = $value->get()->title();
				}
				else
				{
					$return = $value;
				}
				break;
		}
		return $return;
	}

	function trigger_event( $event, $payload )
	{
		/* check if we also have a method here */
		$method = '_' . $event;
		if( method_exists($this, $method))
		{
			$this->{$method}();
		}

		$CI =& ci_get_instance();
		if( isset($CI->hc_events) )
		{
			$event = $this->my_class() . '.' . $event;
			$args = func_get_args();
			$args[0] = $event;
			call_user_func_array( array($CI->hc_events, 'trigger'), $args );
		}
	}

/* with triggered events */
	public function save($object = '', $related_field = '')
	{
		$this->trigger_event( 'before_save', $this );

	/* keep copy of the stored because it resets to new after save */
		$this->_keep_old();

		$return = parent::save($object, $related_field);
		if( $return )
		{
			$this->trigger_event( 'after_save', $this );
		}
		return $return;
	}

	public function delete($object = '', $related_field = '')
	{
		$this->trigger_event( 'before_delete', $this );
		$return = parent::delete($object, $related_field);
		if( $return )
		{
			$this->trigger_event( 'after_delete', $this );
		}
		return $return;
	}

	private function _keep_old()
	{
		$this->_old = (array) $this->stored;
	/* also include has_one */
		reset( $this->has_one );
		foreach( array_keys($this->has_one) as $k )
		{
			if( is_object($this->{$k}) )
				$this->_old[$k] = $this->{$k}->id;
			else
				$this->_old[$k] = $this->{$k};
		}
	}

/* gives the array of changed properties with their old values, useful after save */
	public function get_changes( $relations = NULL )
	{
		$return = array();
		$new = $this->to_array();
		if( $relations )
		{
			reset( $relations );
			foreach( $relations as $k => $o )
			{
				$new[ $k ] = $o->id;
			}
		}

		foreach( $new as $k => $v )
		{
			if( array_key_exists($k, $this->_old) )
			{
				if( $this->_old[$k] !== $v )
					$return[$k] = $this->_old[$k];
			}
			else
			{
				$return[$k] = NULL;
			}
		}
		return $return;
	}

	function get_field_names()
	{
		$return = array();
		$fields = $this->get_fields();
		reset( $fields );
		foreach( $fields as $f )
		{
			$return[] = $f['name'];
		}
		return $return;
	}

	function is_changed( $check = array(), $post = array() )
	{
		$copy = $this->get_clone();

		$return = array();
		if( ! $check )
		{
			$check = array();
			$all_fields = $copy->get_fields();
			foreach( $all_fields as $f )
			{
				$check[] = $f['name'];
			}
		}

		reset( $check );
		foreach( $check as $c )
		{
			if( ! isset($post[$c]) )
				continue;
			$old[$c] = $copy->{$c};
			$copy->{$c} = $post[$c];
		}

		$copy->validate();

		reset( $check );
		foreach( $check as $c )
		{
			if( ! isset($post[$c]) )
				continue;
			if( $copy->{$c} != $old[$c] )
			{
				$return[$c] = array( $copy->{$c}, $old[$c] );
			}
		}
		return $return;
	}

/* validation */
	public function _save_array( $field )
	{
		if ( ! empty($this->{$field}) )
		{
			$this->{$field} = join( '||', $this->{$field} );
		}
		else
		{
			$this->{$field} = '';
		}
		return TRUE;
	}

	public function _load_array( $field )
	{
		if ( ! empty($this->{$field}) )
		{
			$this->{$field} = explode( '||', $this->{$field} );
		}
		else
		{
			$this->{$field} = array();
		}
	}

	public function _enum( $field, $compare )
	{
		if (
			( isset($this->{$field}) )
			)
		{
			return in_array( $this->{$field}, $compare );
		}
		return FALSE;
	}

	public function _greater_equal_than_field($me, $other)
	{
		if ( (! is_numeric($this->{$me})) OR (! is_numeric($this->{$other})) )
			return FALSE;
		return $this->{$me} >= $this->{$other};
	}

	public function _less_equal_than_field($me, $other)
	{
		if ( (! is_numeric($this->{$me})) OR (! is_numeric($this->{$other})) )
			return FALSE;
		return $this->{$me} <= $this->{$other};
	}

	public function _greater_than_field($me, $other)
	{
		if ( (! is_numeric($this->{$me})) OR (! is_numeric($this->{$other})) )
			return FALSE;
		return $this->{$me} > $this->{$other};
	}

	public function _less_than_field($me, $other)
	{
		if ( (! is_numeric($this->{$me})) OR (! is_numeric($this->{$other})) )
			return FALSE;
		return $this->{$me} < $this->{$other};
	}

	protected function _after_save()
	{
/*
		$CI =& ci_get_instance();
		if( $this->keep_log && $CI->hc_modules->exists('logaudit') )
		{
			$log_changes = array();
			$changes = $this->get_changes();
			reset( $changes );
			foreach( $changes as $property_name => $old_value )
			{
				if( in_array($property_name, $this->keep_log) )
				{
					$log_changes[ $property_name ] = $old_value;
				}
			}

			if( $log_changes )
			{
				$log = new Logaudit_model;
				$log->log( $this, $log_changes );
			}
		}
*/
	}
}

class MY_Model_Virtual
{
    function my_class()
	{
		$return = get_class($this);
		$return = strtolower($return);
		$suffix = '_model';
		if( substr($return, -strlen($suffix)) == $suffix )
		{
			$return = substr( $return, 0, -strlen($suffix) );
		}
		return $return;
	}

	function trigger_event( $event, $payload )
	{
		$CI =& ci_get_instance();
		if( isset($CI->hc_events) )
		{
			$event = $this->my_class() . '.' . $event;
			$args = func_get_args();
			$args[0] = $event;
			call_user_func_array( array($CI->hc_events, 'trigger'), $args );
		}
	}

	public function save($object = '', $related_field = '')
	{
		$this->trigger_event( 'before_save', $this );

	/* keep copy of the stored because it resets to new after save */
		$this->_keep_old();

		$return = parent::save($object, $related_field);
		if( $return )
		{
			$this->trigger_event( 'after_save', $this );
		}
		return $return;
	}
}
