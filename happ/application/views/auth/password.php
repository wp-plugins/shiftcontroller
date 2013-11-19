<div class="page-header">
<h2><?php echo lang('common_change_password'); ?></h2>
</div>

<?php echo form_open('', array('class' => 'form-horizontal form-condensed')); ?>

<?php
echo hc_bootstrap::input(
	$this->hc_form->input($new_password),
	$new_password['label'],
	$this->hc_form->error($new_password['name'])
	);
?>

<?php
echo hc_bootstrap::input(
	$this->hc_form->input($new_password_confirm),
	$new_password_confirm['label'],
	$this->hc_form->error($new_password_confirm['name'])
	);
?>

<div class="form-actions">
<?php echo form_submit( array('name' => 'submit', 'class' => 'btn btn-primary'), lang('common_save'));?>
</div>

<?php echo form_close();?>