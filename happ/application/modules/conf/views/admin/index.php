<?php
$fields = $this->config->items('settings');
reset( $fields );
?>

<div class="page-header">
<h2><?php echo lang('menu_conf_settings'); ?></h2>
</div>

<?php
$orphan_errors = hc_orphan_errors( $this->hc_form->errors(), $fields );
?>
<?php if( $orphan_errors ) : ?>
<div class="alert alert-error">
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
		$f['name'] = $fn;
		echo hc_bootstrap::input(
			$this->hc_form->input($f),
			$f['label'],
			$this->hc_form->error($f['name'])
			);
?>
<?php endforeach; ?>

<div class="form-actions">
<?php echo form_submit( array('name' => 'submit', 'class' => 'btn btn-primary'), lang('common_save'));?>
</div>

<?php echo form_close();?>
