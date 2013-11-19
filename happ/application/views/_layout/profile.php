<?php
$this_method = $this->router->fetch_method();
?>
<?php if ( $this->auth && ($this->auth->check()) ) : ?>
	<?php
	$user = $this->auth->user();
	?>
	<div>
		<small>
		<?php echo lang('logged_in_as'); ?>: <a href="<?php echo ci_site_url('auth/profile'); ?>"><?php echo $user->email; ?></a>
		<a href="<?php echo ci_site_url('auth/logout'); ?>"><?php echo lang('menu_logout');?></a>
		</small>
	</div><!-- /profile -->
<?php else : ?>
	<div><small>
	<?php if( $this_method != 'login' ) : ?>
		<a href="<?php echo ci_site_url('auth/login'); ?>"><?php echo lang('login'); ?></a>
	<?php else : ?>
		<a href="<?php echo ci_site_url(''); ?>"><?php echo lang('common_home'); ?></a>
	<?php endif; ?>
	</small></div><!-- /login -->
<?php endif; ?>
