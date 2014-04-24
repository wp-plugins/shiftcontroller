<?php
class Hc_bootstrap
{
	static function nav_tabs( $tabs, $shown = '', $field = '', $id = '', $extra = '' )
	{
		if( ! $shown )
		{
			$all_tabs = array_keys($tabs);
			$shown = $all_tabs[0];
		}

		$id = $id ? $id : hc_random();
		$out = array();

		$startup = '<ul class="nav nav-tabs" id="' . $id . '"';
		if( $extra )
			$startup .= ' ' . $extra;
		$startup .= '>';
		$out[] = $startup;

		reset( $tabs );

		reset( $tabs );
		foreach( $tabs as $tab_id => $t )
		{
			if( $tab_id == $shown )
				$out[] = '<li class="active">';
			else
				$out[] = '<li>';

			if( is_array($t) )
			{
				$t1 = array_shift($t);

				$title = trim(strip_tags($t1['title']));
				$out[] = '<a title="' . $title . '" class="dropdown-toggle" data-toggle="dropdown" href="#">';
				$out[] = $t1['title'];
				$out[] = ' <span class="caret"></span>';
				$out[] = '</a>';

				$out[] = '<ul class="dropdown-menu">';
				foreach( $t as $t2 )
				{
					list( $link_title, $link_icon ) = Hc_lib::parse_icon( $t2['title'] );
					$title = trim(strip_tags($t2['title']));
					$out[] = '<li>';
					$out[] = '<a title="' . $link_title . '" href="' . $t2['href'] . '">';
					$out[] = $link_icon . $link_title;
					$out[] = '</a>';
					$out[] = '</li>';
				}
				$out[] = '</ul>';
			}
			else
			{
				$title = trim(strip_tags($t));
				if( substr($tab_id, 0, 1) == '_' )
					$out[] = '<a title="' . $title . '">' . $t . '</a>';
				else
					$out[] = '<a href="#' . $tab_id . '" data-toggle="tab" title="' . $title . '">' . $t . '</a>';
			}

			$out[] = '</li>';
		}
		$out[] = '</ul>';

		if( $field )
		{
			$out[] = '<script language="JavaScript">';
			$out[] = <<<EOT
jQuery('#$id a[data-toggle="tab"]').on('shown.bs.tab', function (e)
{
	var active_tab = e.target.hash.substr(1); // strip the starting #
	jQuery(this).closest('form').find('[name=$field]').val( active_tab );
});
EOT;

			if( $shown )
			{
			$out[] = <<<EOT
	jQuery('#{$id}').closest('form').find('[name=$field]').val( "$shown" );
EOT;
			}
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