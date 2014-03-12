<div class="page-header">
<h2><?php echo lang('auth_forgot_password'); ?></h2>
</div>

<?php echo form_open('auth/forgot_password', array('class' => 'form-horizontal form-condensed')); ?>

<p>
<?php echo lang('auth_forgot_password_help'); ?>
</p>

<?php
echo Hc_html::wrap_input(
	lang('common_email'),
	$this->hc_form->build_input($email)
	);
?>

<?php
echo Hc_html::wrap_input(
	'',
	form_submit( 
		array(
			'name' => 'submit',
			'class' => 'btn btn-default'
			),
		lang('auth_forgot_password_send')
		)
	);
?>

<?php echo form_close();?>