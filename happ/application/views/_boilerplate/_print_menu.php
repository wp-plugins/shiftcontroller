<?php
$CI =& ci_get_instance();
$disabled_panels = $CI->config->item('disabled_panels');
if( ! $disabled_panels )
	$disabled_panels = array();
?>

<ul class="nav nav-pills nav-stacked nav-condensed">
	<?php foreach( $menu as $k => $v ) : ?>
		<?php
		if( is_array($v) )
		{
			$link_slug = $v[0];
			if( is_array($link_slug) )
			{
				$link_slug = join( '/', $link_slug );
			}

			$this_disabled = FALSE;
			reset( $disabled_panels );
			foreach( $disabled_panels as $dp )
			{
				if( substr($link_slug, 0, strlen($dp)) == $dp )
				{
					$this_disabled = TRUE;
					break;
				}
			}

			if( $this_disabled )
				continue;

			$link_title = $v[1];
			list( $link_title, $link_icon ) = Hc_lib::parse_icon( $link_title );

			$link_view = ci_anchor( 
				$link_slug,
				$link_icon . $link_title
				);
		}
		else
		{
			$link_view = $v;
		}
		?>

		<?php if( substr($k, 0, strlen('_header')) == '_header' ) : ?>
			<li class="dropdown-header">
		<?php elseif( substr($k, 0, strlen('_divider')) == '_divider' ) : ?>
			<li class="divider">
		<?php elseif( $k == $current_view ) : ?>
			<li class="active">
		<?php else : ?>
			<li>
		<?php endif; ?>
			<?php echo $link_view; ?>
		</li>

	<?php endforeach; ?>
</ul>