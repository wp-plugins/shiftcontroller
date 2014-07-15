<?php
include_once( NTS_SYSTEM_APPPATH . '/core/MY_Model.php' );
class User_model extends MY_model
{
	const LEVEL_STAFF = 1;
	const LEVEL_MANAGER = 2;
	const LEVEL_ADMIN = 3;

	const STATUS_ACTIVE = 1;
	const STATUS_ARCHIVE = 0;

	var $salt_length = 10;
	var $table = 'users';
	var $default_order_by = array('last_name' => 'ASC', 'first_name' => 'ASC');
	var $build_title = array(
		'_first_name_', ' ', '_last_name_'
		);

	var $has_many = array( 
		'timeoff' => array(
			'class'			=> 'timeoff_model',
			'other_field'	=> 'user',
			),
		'shift' => array(
			'class'			=> 'shift_model',
			'other_field'	=> 'user',
			),
		);

	var $validation = array(
		'first_name'	=> array(
			'label'	=> 'lang:user_first_name',
			'rules'	=> array('required', 'trim', 'max_length' => 50)
			),
		'last_name'	=> array(
			'label'	=> 'lang:user_last_name',
//			'rules'	=> array('required', 'trim', 'max_length' => 50)
			'rules'	=> array('trim', 'max_length' => 50)
			),
		'email'	=> array(
			'label'	=> 'lang:common_email',
			'rules' => array('required', 'trim', 'valid_email', 'unique'),
			),
		'username'	=> array(
			'label'	=> 'lang:common_username',
			'rules' => array('default_username', 'required', 'trim', 'unique'),
			),
		'password'	=> array(
			'label'	=> 'lang:common_password',
			'rules' => array('required', 'trim', 'hash_password'),
			),
		'confirm_password'	=> array(
			'label'	=> 'lang:common_password_confirm',
			'rules'	=> array('trim', 'hash_password', 'matches' => 'password'),
			),
		'level'	=> array(
			'label'	=> 'lang:user_level',
			'rules'	=> array(
				'enum' => array(
					self::LEVEL_STAFF,
					self::LEVEL_MANAGER,
					self::LEVEL_ADMIN
					)
				),
			),
		'active'	=> array(
			'label'	=> 'lang:user_status',
			'rules'	=> array(
				'enum' => array(
					self::STATUS_ACTIVE,
					self::STATUS_ARCHIVE
					)
				),
			),
		);

	var $prop_text = array(
		'level'	=> array(
			self::LEVEL_STAFF 	=> 'lang:user_level_staff',
			self::LEVEL_MANAGER	=> 'lang:user_level_manager',
			self::LEVEL_ADMIN	=> 'lang:user_level_admin',
			),
		'active'	=> array(
			self::STATUS_ACTIVE 	=> array( 'lang:user_status_active',	'success' ),
			self::STATUS_ARCHIVE	=> array( 'lang:user_status_archived',	'default' ),
			),
		);

	var $my_fields = array(
		array(
			'name'		=> 'first_name',
			'label'		=> 'lang:user_first_name',
			'size'		=> 16,
			'required'	=> TRUE,
			),
		array(
			'name'		=> 'last_name',
			'label'		=> 'lang:user_last_name',
			'size'		=> 16,
			'required'	=> TRUE,
			),
		array(
			'name'		=> 'email',
			'size'		=> 24,
			'required'	=> TRUE,
			'label'		=> 'lang:common_email',
			),
		array(
			'name'		=> 'username',
			'size'		=> 16,
			'required'	=> TRUE,
			'label'		=> 'lang:common_username',
			),
		array(
			'name'		=> 'password',
			'type'		=> 'password',
			'label'		=> 'lang:common_password',
			'size'		=> 16,
			'required'	=> TRUE,
			),
		array(
			'name'		=> 'confirm_password',
			'type'		=> 'password',
			'label'		=> 'lang:common_password_confirm',
			'size'		=> 16,
			'required'	=> TRUE,
			),
		array(
			'name'		=> 'level',
			'label'		=> 'lang:user_level',
			'type'		=> 'dropdown',
			),
		);

	public function id_label()
	{
		return $this->prop_text('active', TRUE);
	}

/* remove userneme */
	public function get_form_fields()
	{
		$return = parent::get_form_fields();

		$CI =& ci_get_instance();
		if( $CI->app_conf->get('login_with') != 'username' )
		{
			unset( $return['username'] );
		}
		return $return;
	}

	public function count_staff()
	{
		$CI =& ci_get_instance();
		$working_levels = $CI->app_conf->get('working_levels');

		$this->clear();
	/* get those users who can be assigned to shifts */
		$this->where('active', self::STATUS_ACTIVE);
		if( $working_levels )
		{
			if( ! is_array($working_levels) )
				$working_levels = array( $working_levels );
			$this->where_in('level', $working_levels);
		}
		$return = $this->count();
		return $return;
	}

	public function get_staff()
	{
		$CI =& ci_get_instance();
		$working_levels = $CI->app_conf->get('working_levels');

		$this->clear();
	/* get those users who can be assigned to shifts */
		$this->where('active', self::STATUS_ACTIVE);
		if( $working_levels )
		{
			if( ! is_array($working_levels) )
				$working_levels = array( $working_levels );
			$this->where_in('level', $working_levels);
		}
		$return = $this->get()->all;
		return $return;
	}

	/* redefine the drop down in forms */
	protected function _load_titles()
	{
		$return = array();
		$staff = $this->get_staff();
		foreach( $staff as $sta )
		{
			$return[ $sta->id ] = $sta->title();
		}
		return $return;
	}

	public function delete($object = '', $related_field = '')
	{
	// if something is given, then just pass it over, the caller must be knowing what he's doing
		if( $object )
		{
			return parent::delete( $object, $related_field );
		}
	// if empty then delete all has_many and has_one
		else
		{
			$has = array_merge( array_keys($this->has_one), array_keys($this->has_many) );
			foreach ( $has as $rfield )
			{
				$this->{$rfield}->get()->delete_all();
			}
			return parent::delete();
		}
	}

	public function title( $html = FALSE )
	{
		$return = '';
		if( $html )
		{
			$return .= '<i class="fa fa-user"></i> ';
		}
		$return .= $this->first_name . ' ' . $this->last_name;
//		$return .= ' [' . $this->email . ']';
		return $return;
	}

	public function full_name()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

/* check password */
	function check_password( $pass )
	{
		if( isset($this->username) && strlen($this->username) )
		{
			$this->get_by_username( $this->username );
		}
		else
		{
			$this->get_by_email( $this->email );
		}

        if ( ! $this->exists() )
        {
			return FALSE;
        }

		$this->salt = substr($this->password, 0, $this->salt_length);
		$this->password = $pass;

		$this->validate();
		$this->get();

        if ( ! $this->exists() )
        {
            return FALSE;
        }
		return TRUE;
	}

/* validation methods */
	function _hash_password( $field )
	{
		if (!empty($this->{$field}))
		{
			// Generate a random salt if empty
			if (empty($this->salt))
			{
				$this->salt = substr( md5(uniqid(rand(), true)), 0, $this->salt_length );
			}
			$this->{$field} =  $this->salt . substr( sha1($this->salt . $this->{$field}), 0, -$this->salt_length );
		}
	}

	function _default_username( $field )
	{
		if( empty($this->{$field}) )
		{
			$this->{$field} = $this->email;
		}
	}
}