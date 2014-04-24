<?php
if( ! $menu )
	return;
if( ! $class )
	$class = 'dropdown-menu';
?>
<ul class="<?php echo $class; ?>">
	<?php foreach( $menu as $k2 => $m2 ) : ?>
		<?php if( ((! is_array($m2))) OR ($m2['title'] == '-divider-') ) : ?>
			<?php if( ((! is_array($m2)) && ($m2 == '-divider-')) OR ($m2['title'] == '-divider-') ) : ?>
				<li class="divider"></li>
			<?php else : ?>
				<?php list( $link_title, $link_icon ) = Hc_lib::parse_icon($m2); ?>
				<li>
					<span title="<?php echo $link_title; ?>"><?php echo $m2; ?></span>
				</li>
			<?php endif; ?>
		<?php else : ?>
			<?php
			list( $link_title, $link_icon ) = Hc_lib::parse_icon( $m2['title'] );
			$class = '';
			$li_class = '';
			if( isset($m2['class']) && strlen($m2['class']) )
			{
				$class = ' class="' . $m2['class'] . '"';
				if( $m2['class'] == 'hc-ajax-loader' )
					$li_class = ' class="hc-ajax-parent"';
			}
			?>

			<?php if( isset($m2['href']) ) : ?>

				<li<?php echo $li_class; ?>>
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