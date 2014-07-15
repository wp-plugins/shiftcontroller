<div class="page-header">
<h2><?php echo lang('login'); ?></h2>
</div>

<?php
if( $this->app_conf->get('login_with') == 'username' )
{
	$identity_label = lang('common_username');
}
else
{
	$identity_label = lang('common_email');
}

$identity['placeholder'] = $identity_label;
$password['placeholder'] = lang('common_password');
?>

<?php echo form_open('auth/login', array('class' => 'form-horizontal form-condensed')); ?>

<?php
echo Hc_html::wrap_input(
	$identity_label,
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