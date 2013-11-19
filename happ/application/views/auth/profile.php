<div class="page-header">
<h2><?php echo lang('auth_profile'); ?></h2>
</div>

<?php echo form_open('', array('class' => 'form-horizontal form-condensed')); ?>

<?php
echo hc_bootstrap::input(
	$this->hc_form->input($email),
	lang('common_email'),
	$this->hc_form->error($email['name'])
	);
?>

<div class="form-actions">
<?php echo form_submit( array('name' => 'submit', 'class' => 'btn btn-primary'), lang('common_save'));?>
</div>

<?php echo form_close();?>