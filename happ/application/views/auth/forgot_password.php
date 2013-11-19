<?php echo form_open('auth/forgot_password', array('class' => 'well')); ?>
<p>Please enter your email address so we can send you an email to reset your password.</p>
<p>
<h2>Forgot Password</h2>
</p>

<?php
$email['placeholder'] = lang('common_email');
$error = form_error($email['name']);
$class = $error ? 'control-group error' : 'control-group';
?>

<div class="<?php echo $class; ?>">
<div class="controls">  
<?php echo form_input( $email ); ?>
<?php if( $error ) : ?>
<span class="help-inline"><?php echo $error; ?></span>
<?php endif; ?>
</div>  
</div>

<p>
<?php echo form_submit( array('name' => 'submit', 'class' => 'btn btn-primary'), 'Submit');?>
</p>
      
<?php echo form_close();?>