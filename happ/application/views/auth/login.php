<div class="page-header">
<h2><?php echo lang('login'); ?></h2>
</div>

<?php
$identity['placeholder'] = lang('common_email');
$password['placeholder'] = lang('common_password');
?>

<?php echo form_open('auth/login', array('class' => 'form-horizontal form-condensed')); ?>

<?php
echo Hc_html::wrap_input(
	lang('common_email'),
	$this->hc_form->build_input($identity)
	);
?>

<?php
echo Hc_html::wrap_input(
	lang('common_password'),
	$this->hc_form->build_input($password)
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
		'Login'
		)
	);
?>

<?php echo form_close();?>

<p>
	<a href="<?php echo ci_site_url('auth/forgot_password'); ?>">
		<?php echo lang('auth_login_form_forgot_password'); ?>
	</a>
</p>