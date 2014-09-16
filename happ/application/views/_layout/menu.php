<?php
$brand_title = $this->config->item('nts_app_title');
$brand_url = $this->config->item('nts_app_url');
?>
<?php if( (! $ri) && strlen($brand_title) ) : ?>
	<p>
	<a href="<?php echo $brand_url; ?>">
		<?php echo $brand_title; ?> <small><?php echo HC_APP_VERSION; ?></small>
	</a>
	</p>
<?php elseif( $ri ) : ?>
	<p>&nbsp;</p>
<?php endif; ?>

<?php if( ! $ri ) : ?>
	<?php require( dirname(__FILE__) . '/profile.php' ); ?>
<?php endif; ?>

<?php
$menu_conf = $this->config->item('menu');
$menu = new Hc_main_menu;
$menu->set_menu( $menu_conf );

$disabled_panels = $this->config->item('disabled_panels');
$menu->set_disabled( $disabled_panels );

$this_uri = $this->uri->uri_string();
$menu->set_current( $this_uri );

if( 
	$this->auth && 
	$this->auth->user() &&
	$this->auth->user()->active
	)
{
	$user_level = $this->auth->user()->level;
	$app = $this->config->item('nts_app');
	if( isset($GLOBALS['NTS_CONFIG'][$app]['FORCE_USER_LEVEL']) )
	{
		$user_level = $GLOBALS['NTS_CONFIG'][$app]['FORCE_USER_LEVEL'];
	}
	echo $menu->display( $user_level . '/' );
}
?>