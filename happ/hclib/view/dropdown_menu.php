<?php
if( ! $menu )
	return;
?>
<ul class="dropdown-menu">
	<?php foreach( $menu as $k2 => $m2 ) : ?>
		<?php if( ((! is_array($m2)) && ($m2 == '-divider-')) OR ($m2['title'] == '-divider-') ) : ?>
			<li class="divider"></li>
		<?php else : ?>
			<?php
			list( $link_title, $link_icon ) = Hc_lib::parse_icon( $m2['title'] );
			$class = ( isset($m2['class']) && strlen($m2['class']) ) ? ' class="' . $m2['class'] . '"' : '';
			?>
			<?php if( isset($m2['href']) ) : ?>
				<li>
					<a href="<?php echo $m2['href']; ?>" title="<?php echo $link_title; ?>"<?php echo $class; ?>>
						<?php echo $link_icon; ?><?php echo $link_title; ?>
					</a>
				</li>
			<?php else : ?>
				<li class="dropdown-header">
					<?php echo $link_icon; ?><?php echo $link_title; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>