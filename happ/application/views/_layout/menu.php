<?php
$brand_title = $this->config->item('nts_app_title');
$brand_url = $this->config->item('nts_app_url');
?>
<p>
<a href="<?php echo $brand_url; ?>">
	<?php echo $brand_title; ?> <small><?php echo HC_APP_VERSION; ?></small>
</a>
</p>

<?php if( ! $ri ) : ?>
	<?php require( dirname(__FILE__) . '/profile.php' ); ?>
<?php endif; ?>

<?php
$menu_conf = $this->config->item('menu');
$menu = new Hc_main_menu;
$menu->set_menu( $menu_conf );

$this_uri = $this->uri->uri_string();
$menu->set_current( $this_uri );

if( 
	$this->auth && 
	$this->auth->user() &&
	$this->auth->user()->active
	)
{
	echo $menu->display( $this->auth->user()->level . '/' );
}
?>