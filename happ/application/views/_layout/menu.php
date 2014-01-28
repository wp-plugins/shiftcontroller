<?php
$brand_title = $this->config->item('nts_app_title');
$brand_url = $this->config->item('nts_app_url');
$menu = $this->config->item('menu');

$this_menu = array();
if( $this->auth && $this->auth->user() )
{
	if( $this->auth->user()->active )
	{
		$this_menu = isset($menu[$this->auth->user()->level]) ? $menu[$this->auth->user()->level] : array();
		usort( $this_menu, create_function('$a, $b', 'return ($a[2] - $b[2]);' ) );
	}
	else
	{
		$this_menu = array();
	}
}
?>

<?php if( $this_menu ) : ?>
	<p>
	<a href="<?php echo $brand_url; ?>">
		<?php echo $brand_title; ?> <small><?php echo HC_APP_VERSION; ?></small>
	</a>
<?php endif; ?>

<?php if( ! $ri ) : ?>
	<?php require( dirname(__FILE__) . '/profile.php' ); ?>
<?php endif; ?>

<?php if( $this_menu ) : ?>
<p>
<div class="navbar">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
			</a>

<?php if( $this_menu ) : ?>
			<div class="nav-collapse">
				<ul class="nav nav-pills">
<?php foreach( $this_menu as $m ) : ?>
<?php		if( ! $m[1] ) : ?>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $m[0]; ?><b class="caret"></b></a>
						<ul class="dropdown-menu">  
<?php			foreach( $m as $k => $m2 ) : ?>
<?php				if( is_array($m2) ) : ?>
							<li>
								<a href="<?php echo ci_site_url($m2[1]); ?>"><?php echo $m2[0]; ?></a>
							</li>
<?php				elseif( $m2 == '_divider' ) : ?>
							<li class="divider"></li>
<?php				endif; ?>
<?php			endforeach; ?>
						</ul>
					</li>
<?php		else : ?>
<?php			if( substr($m[1], 0, strlen('http://')) == 'http://' ) : ?>
					<li>
						<a target="_blank" href="<?php echo $m[1]; ?>"><span class="alert alert-success"><?php echo $m[0]; ?> <i class="icon-external-link"></i></span></a>
					</li>
<?php			else : ?>
					<li>
						<a href="<?php echo ci_site_url($m[1]); ?>"><?php echo $m[0]; ?></a>
					</li>
<?php			endif; ?>
<?php		endif; ?>
<?php endforeach; ?>
				</ul>
			</div><!-- /.nav-collapse -->
<?php endif; ?>

		</div><!-- /.container -->
	</div><!-- /navbar-inner -->
</div><!-- /navbar -->

<?php endif; ?>

