<?php
include_once( dirname(__FILE__) . '/Backend_controller.php' );
class Backend_controller_crud extends Backend_controller
{
	protected $per_page;
	public $fields;
	public $model; // model name

	function __construct( $user_level = 0, $default_path = '' )
	{
/*
$this->conf = array(
	'model'			=> 'Category_model',
	'path'			=> 'admin/categories',
	);
*/
		parent::__construct( $user_level, $default_path );
		$this->per_page = 0;

		$this->load->model( $this->conf['model'] );
		$this->model = $this->conf['model'];
	}

	protected function _parse_search( $search )
	{
		$this->{$this->model}->like( $search );
		return $search;
	}

	function index(){
		$args = func_get_args();
		if( $this->{$this->model}->count() <= 0 )
		{
			$target = array($this->conf['path'], 'add');
			$target = array_merge( $target, $args );
			ci_redirect( $target );
			return;
		}

		if( count($args) == 2 )
		{
			$page = $args[0];
			$search = $args[1];
		}
		elseif( count($args) == 1 )
		{
			$page = $args[0];
			$search = '';
		}
		else
		{
			$page = 1;
			$search = '';
		}
		if( ! $page )
			$page = 1;
		if( $search )
			$search = urldecode( $search );

		$show_search = $search;

		if( $this->per_page )
		{
			$total_count = $this->{$this->model}->count();
			if( $search )
			{
				$show_search = $this->_parse_search( $search );
			}
			$matched_count = $this->{$this->model}->count();

			$pager_config = array(
				'per_page' 		=> $this->per_page,
				'total_rows'	=> $matched_count,
				);

			if( $search ){
				$pager_config['uri_segment'] = 5;
				$pager_config['base_url'] = ci_site_url( array($this->conf['path'], 'search', $search) );
				}
			else {
				$pager_config['uri_segment'] = 3;
				$pager_config['base_url'] = ci_site_url( $this->conf['path'] );
				}
			$this->pagination->initialize($pager_config);

			$this->{$this->model}->db->offset( ($page - 1) * $this->per_page );
			$this->{$this->model}->db->limit( $this->per_page );
		}

		if( $search )
		{
			$show_search = $this->_parse_search( $search );
		}

		$this->data['search'] = $search;
		$this->data['show_search'] = $show_search;
		
		$entries = array();
		$this->{$this->model}->get();
		if( ! $this->{$this->model}->exists() )
		{
			// nothing
		}
		else
		{
			foreach( $this->{$this->model} as $e )
			{
				$entries[] = $e;
			}
		}

		$this->data['entries'] = $entries;
		$this->data['total_count'] = $this->{$this->model}->result_count();

		$this->set_include( 'index' );		
		$this->data['index_child'] = $this->get_view('index_child');

		$this->load->view( $this->template, $this->data);
		}

	function export()
	{
		$separator = $this->app_conf->get( 'csv_separator' );

	// header
		$headers = array();
		reset( $this->fields );
		foreach( $this->fields as $f )
		{
			$headers[] = $f['name'];
		}

		$data = array();
		$data[] = join( $separator, $headers );

	// entries
		$entries = $this->{$this->model}->get_all( $headers );
		reset( $entries );
		foreach( $entries as $e )
		{
			$data[] = hc_build_csv( array_values($e), $separator );
		}

	// output
		$out = join( "\n", $data );

		$file_name = isset( $this->conf['export'] ) ? $this->conf['export'] : 'export';
		$file_name .= '-' . date('Y-m-d_H-i') . '.csv';

		$this->load->helper('download');
		force_download($file_name, $out);
		return;
	}

	function search()
	{
		$search = $this->input->post( 'search' );
		$search = trim( $search );
		if( $search ){
			$search = urlencode( $search );
			ci_redirect( $this->conf['path'] . '/search/' . $search );
			}
		else {
			ci_redirect( $this->conf['path'] );
			}
		return;
	}

	function add()
	{
		$args = func_get_args();

		if( ! isset($this->data['fields']) )
		{
			$this->data['fields'] = $this->{$this->model}->get_form_fields();
		}

	// prefill args
		if( ! isset($this->data['fixed']) )
			$this->data['fixed'] = array();

		$fnames = $this->{$this->model}->get_field_names();
		for( $ii = 0; $ii < count($args); $ii = $ii + 2 )
		{
			if( isset($args[$ii + 1]) )
			{
				$k = $args[$ii];
				$v = $args[$ii + 1];

				$this->hc_form->set_default( $k, $v );
				$this->{$this->model}->{$k} = $v;
				if( $v )
					$this->data['fixed'][] = $k;
			}
		}
		$this->data['args'] = $args;
		$this->data['object'] = $this->{$this->model};

		$this->set_include( 'add' );
		$this->load->view( $this->template, $this->data);
	}

	function save_prepare()
	{
		$args = func_get_args();
		$id = count($args) ? array_shift($args) : 0;
		if( $id )
		{
			$this->_load( $id );

		/* populate has_one */
			foreach( array_keys($this->{$this->model}->has_one) as $k )
			{
				$this->{$this->model}->{$k}->get();
			}
		}
		else
		{
//			$this->{$this->model}->clear();
		}

		if( ! isset($this->data['fields']) )
		{
			$this->data['fields'] = $this->{$this->model}->get_form_fields();
		}

		$post = array();
		$related_fields = array_merge( $this->{$this->model}->has_one, $this->{$this->model}->has_many );

	/* fill in defaults by args */
		$fnames = $this->{$this->model}->get_field_names();
		for( $ii = 0; $ii < count($args); $ii = $ii + 2 )
		{
			if( isset($args[$ii + 1]) )
			{
				$k = $args[$ii];
				$v = $args[$ii + 1];

				if( ! array_key_exists($k, $related_fields) )
				{
					$this->{$this->model}->{$k} = $v;
				}
				else
				{
					$this->data['supplied'][$k] = $v;
				}

				if( $v )
					$this->data['fixed'][] = $k;
			}
		}

		$this->data['args'] = $args;

		reset( $this->data['fields'] );
		foreach( $this->data['fields'] as $f )
		{
			$fname = $f['name'];

			$supplied = $this->input->post($fname);
			if( $supplied === FALSE )
			{
				if( isset($this->data['supplied'][$fname]) )
				{
					$supplied = $this->data['supplied'][$fname];
				}
			}

			if( $supplied !== FALSE )
			{
				$post[$fname] = $supplied;
				if( ! array_key_exists($fname, $related_fields) )
				{
					$this->{$this->model}->{$fname} = $supplied;
				}
			}
		}
		return $post;
	}

	function save_prepare_relations()
	{
		$relations = array();
		$related_fields = array_merge( $this->{$this->model}->has_one, $this->{$this->model}->has_many );

	/* compile relations */
		reset( $related_fields );
		foreach( $related_fields as $fname => $rel_props )
		{
			if( isset($this->data['supplied'][$fname]) && strlen($this->data['supplied'][$fname]) )
			{
				$supplied = $this->data['supplied'][$fname];
			}
			else
			{
				$supplied = $this->input->post($fname);
			}

			if( $supplied === FALSE )
			{
				if( isset($this->{$this->model}->{$fname}) && (! is_object($this->{$this->model}->{$fname})) )
				{
					$supplied = $this->{$this->model}->{$fname};
				}
			}

//			if( ($supplied === 0) OR ($supplied === '0') ) // delete relation
//			{
//				$relations[$fname] = NULL;
//			}
//			elseif( $supplied !== FALSE )
			if( $supplied !== FALSE )
			{
				$model_class = $rel_props['class'];
				if( is_array($supplied) )
				{
					$relations[$fname] = array();
					foreach( $supplied as $this_supplied )
					{
						$r = new $model_class;
						$r->get_by_id( $this_supplied );
						$relations[$fname][] = $r;
					}
				}
				else
				{
					$r = new $model_class;
					$r->get_by_id( $supplied );
					$relations[$fname] = $r;
				}
//				$this->{$this->model}->{$fname} = $supplied;
			}
			else
			{
				$relations[$fname] = $this->{$this->model}->{$fname};
			}
		}
		return $relations;
	}

	function save()
	{
		$args = func_get_args();
		$id = count($args) ? $args[0] : 0;

		$post = call_user_func_array( array($this, 'save_prepare'), $args );
		$relations = $this->save_prepare_relations();

		if( $this->{$this->model}->save($relations) )
		{
			$this->_save_complete( TRUE, $id, $post, $args );
		}
		else
		{
			$this->_save_complete( FALSE, $id, $post, $args );
		}
	}

	protected function _save_complete( $ok, $id, $post, $args )
	{
		if( $ok )
		{
		// redirect to list
			$msg = $id ? lang('common_update') : lang('common_add');
			$this->session->set_flashdata( 'message', $msg . ': ' . lang('common_ok') );

			$redirect_to = method_exists($this, 'after_save') ? $this->after_save() : array($this->conf['path']);
			$this->redirect( $redirect_to );
			return;
		}
		else
		{
			$this->hc_form->set_errors( $this->{$this->model}->error->all );
			$this->hc_form->set_defaults( $post );
			if( $this->{$this->model}->id )
				$this->edit( $this->{$this->model}->id );
			else
			{
				array_shift( $args );
				call_user_func_array( array($this, 'add'), $args );
			}
		}
	}

	protected function _load( $id )
	{
//		$related_fields = array_merge( $this->{$this->model}->has_one, $this->{$this->model}->has_many );
		$related_fields = array_merge( $this->{$this->model}->has_one );
		if( ! $this->{$this->model}->id )
		{
			reset( $related_fields );
			foreach( $related_fields as $fname => $rel_props )
			{
				$this->{$this->model}->include_related($fname, 'id');
			}
			$this->{$this->model}->get_by_id($id);
		}

		if( ! $this->{$this->model}->exists() )
		{
			$this->session->set_flashdata( 'message', sprintf(lang('common_not_found'), get_class($this->{$this->model}), $id) );
			ci_redirect( $this->conf['path'] );
			return FALSE;
		}

		if( $this->{$this->model}->id )
		{
			if( ! $this->_permission() )
			{
				$this->session->set_flashdata('message', 'You are not allowed to access this page');
				ci_redirect( $this->conf['path'] );
				return FALSE;
			}
		}
		return TRUE;
	}

	protected function _permission()
	{
		return TRUE;
	}

	function up( $id )
	{
		if( ! $this->_load($id) )
			return;

	/* my order */
		$my_order = $this->{$this->model}->show_order;

	/* check which one is upper then flip */
		$other_one = new $this->model;
		$other_one
			->where( 'show_order <=', $my_order )
			->where( 'id <>', $id )
			->order_by( 'show_order', 'desc' )
			->limit(1)
			->get();
		if( $other_one->exists() )
		{
			$new_order = $other_one->show_order;
			$other_id = $other_one->id;
			if( $new_order == $my_order )
			{
				$my_order = $new_order + 1;
			}
		/* update other_one */
			$other_one->show_order = $my_order;
			$other_one->save();
		/* update me */
			$this->{$this->model}->show_order = $new_order;
			$this->{$this->model}->save();
		}

	$msg = array(
		$this->{$this->model}->title(),
		lang('common_move_up'),
		lang('common_ok')
		);
	$msg = join( ': ', $msg );
	$this->session->set_flashdata( 'message', $msg );
	$redirect_to = array($this->conf['path']);
	$this->redirect( $redirect_to );
	return;
	}

	function down( $id )
	{
		if( ! $this->_load($id) )
			return;

	/* my order */
		$my_order = $this->{$this->model}->show_order;

	/* check which one is lower then flip */
		$other_one = new $this->model;
		$other_one
			->where( 'show_order >=', $my_order )
			->where( 'id <>', $id )
			->order_by( 'show_order', 'asc' )
			->limit(1)
			->get();
		if( $other_one->exists() )
		{
			$new_order = $other_one->show_order;
			$other_id = $other_one->id;
			if( $new_order == $my_order )
			{
				$my_order = $new_order - 1;
			}
		/* update other_one */
			$other_one->show_order = $my_order;
			$other_one->save();
		/* update me */
			$this->{$this->model}->show_order = $new_order;
			$this->{$this->model}->save();
		}

	$msg = array(
		$this->{$this->model}->title(),
		lang('common_move_up'),
		lang('common_ok')
		);
	$msg = join( ': ', $msg );
	$this->session->set_flashdata( 'message', $msg );
	$redirect_to = array($this->conf['path']);
	$this->redirect( $redirect_to );
	return;
	}

	function edit( $id, $view = 'edit' )
	{
		$related_fields = array_merge( $this->{$this->model}->has_one, $this->{$this->model}->has_many );
		if( ! $this->_load($id) )
			return;
		$this->data['object'] = $this->{$this->model};
		if( ! isset($this->data['fields']) )
		{
			$this->data['fields'] = $this->{$this->model}->get_form_fields();
		}

		$defaults = array();
		$defaults = array_merge( $defaults, $this->{$this->model}->to_array() );

	/* related defaults */
		reset( $related_fields );
		foreach( $related_fields as $fname => $rel_props )
		{
			$defaults[ $fname ] = $this->{$this->model}->{$fname . '_id'};
		}

	/* id field */
		$defaults['id'] = $this->{$this->model}->id;
		$id_field = array(
			'name'		=> 'id',
			'label'		=> 'ID',
			'size'		=> 4,
			'readonly'	=> 'readonly',
			'style' 	=> 'width: 4em;'
			);

		reset( $defaults );
		foreach( $defaults as $k => $v )
		{
			if( ! $this->hc_form->is_set_default($k) )
				$this->hc_form->set_default( $k, $v );
		}

	/* add status label */
		if( method_exists($this->{$this->model}, 'id_label') )
		{
			$id_field['text_after'] = $this->{$this->model}->id_label();
		}
//		array_unshift( $this->data['fields'], $id_field );
		$this->data['fields'] = array('id' => $id_field) + $this->data['fields'];

		$this->set_include( 'edit/' . $view );
		$this->load->view( $this->template, $this->data);
	}

	function delete($id)
	{
		if( ! $this->{$this->model}->id )
			$this->{$this->model}->get_by_id($id);
		if( $this->{$this->model}->delete() )
		{
			$this->session->set_flashdata( 'message', lang('common_delete') . ': ' . lang('common_ok') );
		}
		else
		{
			$this->session->set_flashdata( 'error', lang('common_delete') . ': ' . lang('common_error') );
		}

		$redirect_to = method_exists($this, 'after_delete') ? $this->after_delete() : array($this->conf['path']);
		$this->redirect( $redirect_to );
		return;
	}

	function deleterel($id, $relname, $relid)
	{
		if( ! $this->{$this->model}->id )
			$this->{$this->model}->get_by_id($id);

		$rel = $this->{$this->model}->{$relname}->get_by_id($relid);
		if( $this->{$this->model}->delete($rel, $relname) )
		{
			$this->session->set_flashdata( 'message', lang('common_delete') . ': ' . lang('common_ok') );
		}
		else
		{
			$this->session->set_flashdata( 'error', lang('common_delete') . ': ' . lang('common_error') );
		}

		$redirect_to = method_exists($this, 'after_delete') ? $this->after_delete() : array($this->conf['path']);
		$this->redirect( $redirect_to );
		return;
	}
}
