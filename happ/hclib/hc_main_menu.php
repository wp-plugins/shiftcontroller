<?php
class Hc_Main_Menu
{
	var $menu = array();
	var $disabled = array();
	var $current = '';
	var $engine = 'ci'; // can also be 'nts'

	public function __construct( $engine = 'ci' )
	{
		$this->engine = $engine;
	}

	public function set_menu( $menu )
	{
		$this->menu = $menu;
	}

	public function set_disabled( $disabled = array() )
	{
		if( $disabled )
			$this->disabled = $disabled;
	}

	public function set_current( $current )
	{
		$this->current = $current;
	}

	private function _prepare_menu()
	{
		$order = 1;
		$menu_keys = array_keys($this->menu);
		reset( $menu_keys );
		foreach( $menu_keys as $k )
		{
			if( ! is_array($this->menu[$k]) )
			{
				$this->menu[$k] = array(
					'title'	=> $this->menu[$k]
					);
			}
			if( ! isset($this->menu[$k]['order']) )
			{
				$this->menu[$k]['order'] = $order++;
			}

			if( ! 
				(
					(isset($this->menu[$k]['external']) && $this->menu[$k]['external']) OR 
					(isset($this->menu[$k]['href']) && $this->menu[$k]['href'])
				)
				)
			{
				switch( $this->engine )
				{
					case 'ci':
						$this->menu[$k]['slug'] = $this->menu[$k]['link'];
						$this->menu[$k]['href'] = ci_site_url( $this->menu[$k]['link'] );
						break;
					case 'nts':
						if( ! isset($this->menu[$k]['panel']) )
						{
							$this->menu[$k]['panel'] = $k;
						}

						$this->menu[$k]['slug'] = $this->menu[$k]['panel'];
						$this->menu[$k]['href'] = ntsLink::makeLink( $this->menu[$k]['panel'], '', array(), FALSE, TRUE );
						break;
				}
			}

			if( $this->disabled )
			{
				if( in_array($this->menu[$k]['slug'], $this->disabled) )
				{
//					echo "DISABLE " . $this->menu[$k]['slug'] . '<br>';
					unset( $this->menu[$k] );
				}
			}

			/* check if current */
			if( $this->current )
			{
				$slug = isset($this->menu[$k]['slug']) ? $this->menu[$k]['slug'] : '';
				$current = $this->current;
				if(
					(
						($current == $slug)
					)
					OR
					( 
						( substr($current, 0, strlen($slug)) == $slug ) &&
						( substr($current, strlen($slug), 1) == '/' )
					)
					)
				{
					$this->menu[$k]['active'] = TRUE;
				}
			}
		}
		uasort( $this->menu, create_function('$a, $b', 'return ($a["order"] - $b["order"]);' ) );
	}

	private function _get_menu( $root )
	{
		$this->_prepare_menu();
		$return = array();

		$menu_keys = array_keys($this->menu);
		reset( $menu_keys );
		foreach( $menu_keys as $k )
		{
			$this_level = substr_count( $k, '/' );
			if( $this_level > 1 )
				continue;
			if( substr($k, 0, strlen($root)) != $root )
				continue;

			$this_m = $this->menu[$k];

			$children = array();
			$has_children = FALSE;
			reset( $menu_keys );
			foreach( $menu_keys as $k2 )
			{
				if( $k == $k2 )
					continue;
				if( substr($k2, 0, strlen($k)) == $k )
				{
					$their_level = substr_count( $k2, '/' );
					if( $their_level == ($this_level + 1) )
					{
						$has_children = TRUE;
						$their_m = $this->menu[$k2];
						$children[$k2] = $their_m;
					}
				}
			}

			if( $children )
			{
				if( count($children) == 1 )
				{
					$chkeys = array_keys($children);
					$this_m = $children[ $chkeys[0] ];
				}
				else
				{
					$this_m['children'] = $children;
				}
			}
			$return[ $k ] = $this_m;
		}
		return $return;
	}

	public function display( $root )
	{
		$menu = $this->_get_menu( $root );

		$renderer = new Hc_renderer;
		$view_file = dirname(__FILE__) . '/view/hc_main_menu.php';
		return $renderer->render( $view_file, array('menu' => $menu) );
	}
}
