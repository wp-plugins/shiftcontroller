<ul class="nav nav-list">
<?php foreach( $menu as $k => $v ) : ?>
<?php	if( substr($k, 0, strlen('_header')) == '_header' ) : ?>
	<li class="nav-header">
<?php	elseif( substr($k, 0, strlen('_divider')) == '_divider' ) : ?>
	<li class="divider">
<?php	elseif( $k == $current_view ) : ?>
	<li class="active">
<?php	else : ?>
	<li>
<?php	endif; ?>
<?php echo $v; ?>
</li>
<?php endforeach; ?>
</ul>