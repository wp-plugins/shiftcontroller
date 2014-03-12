<ul class="nav nav-pills nav-stacked nav-condensed">
	<?php foreach( $menu as $k => $v ) : ?>
		<?php
		list( $link_title, $link_icon ) = Hc_lib::parse_icon( $v );
		if( $link_icon )
		{
			if( ! preg_match('/fa\-fw/', $link_icon) )
			{
				$new_link_icon = preg_replace( '/class=\"(.+)\"/U', 'class="\1 fa-fw"', $link_icon );
				$v = str_replace( $link_icon, $new_link_icon, $v );
			}
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
			<?php echo $v; ?>
		</li>
	<?php endforeach; ?>
</ul>