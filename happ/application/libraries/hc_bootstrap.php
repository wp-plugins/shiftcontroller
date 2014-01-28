<?php
class Hc_bootstrap
{
	static function form_actions( $buttons )
	{
		$out = array();
		$out[] = '<div class="form-actions">';
		$out[] = $buttons;
		$out[] = '</div>';

		$out = join( "\n", $out );
		return $out;
	}

	static function input( $input, $label = '', $error = FALSE, $field = array() )
	{
		$out = '';

		if( 
			$field && 
			isset($field['type']) &&
			$field['type'] == 'hidden'
			)
		{
			$out .= $input;
			return $out;
		}

		if( strlen($label) )
			$label = hc_parse_lang( $label );
		if( $error )
			$out .=	'<div class="control-group error">';
		else
			$out .=	'<div class="control-group">';
		$out .=		'<label class="control-label"> ' . $label . '</label>';
		$out .=		'<div class="controls">';
		$out .=		$input;
		$out .=		'</div>';
		$out .=	'</div>';
		return $out;
	}

	static function nav_tabs( $tabs, $shown = '', $field = '' )
	{
		if( ! $shown )
		{
			$all_tabs = array_keys($tabs);
			$shown = $all_tabs[0];
		}

		$id = hc_random();
		$out = array();
		$out[] = '<ul class="nav nav-tabs" id="' . $id . '">';
		reset( $tabs );

		reset( $tabs );
		foreach( $tabs as $tab_id => $t )
		{
			$title = trim(strip_tags($t));

			if( $tab_id == $shown )
				$out[] = '<li class="active">';
			else
				$out[] = '<li>';

			if( substr($tab_id, 0, 1) == '_' )
				$out[] = '<a title="' . $title . '">' . $t . '</a>';
			else
				$out[] = '<a href="#' . $tab_id . '" data-toggle="tab" title="' . $title . '">' . $t . '</a>';
			$out[] = '</li>';
		}
		$out[] = '</ul>';

		if( $field )
		{
			$out[] = '<script language="JavaScript">';
			$out[] = <<<EOT
jQuery('#$id a[data-toggle="tab"]').on('shown', function (e)
{
	var active_tab = e.target.hash.substr(1); // strip the starting #
	jQuery(this).closest('form').find('[name=$field]').val( active_tab );
});
EOT;
			$out[] = '</script>';
		}

		$out = join( "\n", $out );
		return $out;
	}

	static function tab_content( $tabs, $shown = '', $id = '' )
	{
		if( ! strlen($id) )
			$id = hc_random();

		if( ! $shown )
		{
			$all_tabs = array_keys($tabs);
			$shown = $all_tabs[0];
		}

		$out = array();
		$out[] = '<div class="tab-content" style="overflow: visible;">';
		reset( $tabs );
		foreach( $tabs as $tab_id => $t )
		{
//			$full_tab_id = $id . '_' . $tab_id;
			if( ! is_array($t) )
			{
				$t = array( 'content' => $t );
			}

			if( ! isset($t['attr']) )
			{
				$t['attr'] = array();
			}

			if( ! isset($t['attr']['class']) )
				$t['attr']['class'] = array();
			else
				$t['attr']['class'] = array( $t['attr']['class'] );

			$t['attr']['class'][] = 'tab-pane';
			if( $tab_id == $shown )
				$t['attr']['class'][] = 'active';
			$t['attr']['class'] = join( ' ', $t['attr']['class'] );

			$full_tab_id = $tab_id;
			$t['attr']['id'] = $full_tab_id;

			$attr = array();
			foreach( $t['attr'] as $k => $v )
			{
				$attr[] = $k . '="' . $v . '"';
			}

			$full_tab_id = $tab_id;
			$out[] = '<div ' . join( ' ', $attr ) . '>';
			$out[] = $t['content'];
			$out[] = '</div>';
		}
		$out[] = '</div>';

		$out = join( "\n", $out );
		return $out;
	}
}