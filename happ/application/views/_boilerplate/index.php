<?php if( count($entries) ) : ?>

<?php if( isset($tabs) && (count($tabs) > 1) ) : ?>
	<ul class="nav nav-tabs">
<?php 	foreach( $tabs as $t => $count ) : ?>
<?php
			$active = ($t == $status) ? TRUE : FALSE;
			$class = $active ? ' class="active"' : '';
?>
		<li<?php echo $class; ?>>
<?php
			$label = $this->{$this->model}->prop_text('status', $active, $t);
			$label .= ' [' . $count . ']';
			echo ci_anchor( 
				array($this->conf['path'], 'index', $t),
				$label,
				'title="' . $this->{$this->model}->prop_text('status', FALSE, $t) . '"'
		);
?>
		</li>
<?php	endforeach; ?>
	</ul>
<?php endif; ?>

<?php
if( $include_submenu )
{
	$per_row = 3;
	$span = 'col-sm-4';
}
else
{
	$per_row = 4;
	$span = 'col-sm-3';
}
$row_open = FALSE;
?>

<?php for( $ii = 1; $ii <= count($entries); $ii++ ) : ?>

<?php if( 1 == ($ii % $per_row) ) : ?>
	<div class="row">
	<?php $row_open = TRUE; ?>
<?php endif; ?>

<?php	$e = $entries[$ii - 1]; ?>
	<div class="<?php echo $span; ?>">
<?php
	$this->load->view( $index_child, array('e' => $e) );
?>
	</div>

<?php if( ! ($ii % $per_row) ) : ?>
	</div>
	<?php $row_open = FALSE; ?>
<?php endif; ?>

<?php endfor; ?>
<?php if( $row_open ) : ?>
	</div>
<?php endif; ?>

<?php else : ?>
<p>
<?php echo lang('common_none'); ?>
</p>
<?php endif; ?>