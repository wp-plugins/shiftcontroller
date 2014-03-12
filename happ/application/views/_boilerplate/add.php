<?php echo form_open( join('/', array_merge(array($this->conf['path'], 'save', 0), $args) ), array('class' => 'form-horizontal form-condensed')); ?>

<?php foreach( $fields as $f ) : ?>
	<?php
	if( in_array($f['name'], $fixed) )
	{
		if( ! isset($f['type']) )
			$f['type'] = 'text';
		switch( $f['type'] )
		{
			case 'time':
				$f['readonly'] = 'readonly';
				break;
			default:
				$f['type'] = 'label';
				break;
		}
	}
	?>
	<?php
	echo Hc_html::wrap_input(
		$f['label'],
		$this->hc_form->build_input($f)
		);
	?>
<?php endforeach; ?>

<?php
$orphan_errors = hc_orphan_errors( $this->hc_form->errors(), $fields );
$orphan_errors = array();
?>
<?php if( $orphan_errors ) : ?>
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<ul>
	<?php foreach( $orphan_errors as $f => $error ) : ?>
	<li><?php echo $error; ?></li>
	<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>

<?php
$actions = array();
$actions[] = form_submit(
	array(
		'name' => 'submit',
		'class' => 'btn btn-default'
		),
	lang($this->conf['entity'] . '_add')
	);
?>

<?php
echo hc_html::wrap_input(
	'',
	$actions
	);
?>

<?php echo form_close(); ?>