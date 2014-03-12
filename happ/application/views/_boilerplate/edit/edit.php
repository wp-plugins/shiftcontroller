<?php 
echo form_open( 
	join('/', array($this->conf['path'], 'save', $object->id)),
	array(
		'class' => 'form-horizontal form-condensed'
		)
	);
?>

<?php foreach( $fields as $f ) : ?>
	<?php
	echo Hc_html::wrap_input(
		$f['label'],
		$this->hc_form->build_input($f)
		);
	?>
<?php endforeach; ?>

<?php
$orphan_errors = hc_orphan_errors( $this->hc_form->errors(), $fields );
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
	lang('common_save')
	);

if( ($this->{$this->model}->allow_none) OR ($this->{$this->model}->count() > 1) )
{
	$actions[] = 
		ci_anchor( 
			array($this->conf['path'], 'delete', $object->id),
			lang('common_delete'),
			'class="btn btn-danger btn-sm hc-confirm"'
			)
		;
}
$actions = join( '&nbsp;', $actions );
?>

<?php
echo hc_html::wrap_input(
	'',
	$actions
	);
?>

<?php echo form_close(); ?>