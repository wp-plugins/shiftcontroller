<div class="page-header">
<h2><?php echo lang('login'); ?></h2>
</div>

<?php echo form_open('auth/login', array('class' => 'well')); ?>
<?php
$identity['placeholder'] = lang('common_email');
$error = form_error($identity['name']);
$class = $error ? 'control-group error' : 'control-group';
?>
<div class="<?php echo $class; ?>">
<div class="controls">  
<?php echo form_input( $identity ); ?>
<?php if( $error ) : ?>
<span class="help-inline"><?php echo $error; ?></span>
<?php endif; ?>
</div>  
</div>

<?php
$password['placeholder'] = lang('common_password');
$error = form_error($password['name']);
$class = $error ? 'control-group error' : 'control-group';
?>
<div class="<?php echo $class; ?>">
<div class="controls">  
<?php echo form_input( $password ); ?>
<?php if( $error ) : ?>
<span class="help-inline"><?php echo $error; ?></span>
<?php endif; ?>
</div>  
</div>

<p>
<?php echo form_submit( array('name' => 'submit', 'class' => 'btn btn-primary'), 'Login');?>
</p>

<?php echo form_close();?>

<p><a href="<?php echo ci_site_url('auth/forgot_password'); ?>"><?php echo lang('auth_login_form_forgot_password'); ?></a></p>