<?php
$this_method = $this->router->fetch_method();
?>
<div>
<?php if ( $this->auth && ($this->auth->check()) ) : ?>
	<?php
	$user = $this->auth->user();
	?>
	<ul class="nav nav-pills">
		<li>
			<a href="<?php echo ci_site_url('auth/profile'); ?>">
				<i class="fa fa-user"></i> <?php echo $user->email; ?>
			</a>
		</li>
		<li class="divider">&nbsp;</li>
		<li>
			<a href="<?php echo ci_site_url('auth/logout'); ?>">
				<?php echo lang('menu_logout'); ?> <i class="fa fa-sign-out"></i>
			</a>
		</li>
	</ul>
<?php else : ?>
	<ul class="nav nav-pills">
		<li>
		<?php if( $this_method != 'login' ) : ?>
			<a href="<?php echo ci_site_url('auth/login'); ?>">
				<i class="fa fa-sign-in"></i> <?php echo lang('login'); ?>
			</a>
		<?php else : ?>
			<a href="<?php echo ci_site_url(''); ?>">
				<?php echo lang('common_home'); ?>
			</a>
		<?php endif; ?>
		</li>
	</ul>
<?php endif; ?>
</div>