<div class="page-header">
<h2><?php echo lang('common_change_password'); ?></h2>
</div>

<?php echo form_open('', array('class' => 'form-horizontal form-condensed')); ?>

<?php
echo Hc_html::wrap_input(
	$new_password['label'],
	$this->hc_form->build_input($new_password)
	);
?>

<?php
echo Hc_html::wrap_input(
	$new_password_confirm['label'],
	$this->hc_form->build_input($new_password_confirm)
	);
?>

<?php
echo hc_html::wrap_input(
	'',
	form_submit( 
		array(
			'name' => 'submit',
			'class' => 'btn btn-default'
			),
		lang('common_save')
		)
	);
?>

<?php echo form_close();?>