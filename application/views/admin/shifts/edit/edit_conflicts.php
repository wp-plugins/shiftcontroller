<?php
$targets = array(
	'timeoff'	=> 'admin/timeoffs/edit',
	'shift'		=> 'admin/shifts/edit'
	);
?>
<ul class="list-unstyled">
	<?php foreach( $conflicts as $c ) : ?>
		<li class="alert alert-danger">
			<?php
			echo ci_anchor( 
				array($targets[$c->my_class()], $c->id),
				$c->title(TRUE)
				);
			?>
		</li>
	<?php endforeach; ?>
</ul>