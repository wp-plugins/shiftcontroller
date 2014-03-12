<div class="page-header">
<h2><?php echo lang('auth_profile'); ?></h2>
</div>

<?php echo form_open('', array('class' => 'form-horizontal form-condensed')); ?>

<?php
echo Hc_html::wrap_input(
	lang('common_email'),
	$this->hc_form->build_input($email)
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