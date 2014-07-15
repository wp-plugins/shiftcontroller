<div class="page-header">
<h2><?php echo lang('menu_conf_settings'); ?></h2>
</div>

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

<?php echo form_open('', array('class' => 'form-horizontal form-condensed')); ?>

<?php foreach( $fields as $fn => $f ) : ?>
	<?php
	echo hc_html::wrap_input(
		$f['label'],
		$this->hc_form->build_input($f)
		);
	?>
<?php endforeach; ?>

<?php
echo hc_html::wrap_input(
	'',
	form_submit( 
		array(
			'name' => 'submit',
			'class' => 'btn btn-default'
			),
		lang('common_save'))
	);
?>

<?php echo form_close();?>
