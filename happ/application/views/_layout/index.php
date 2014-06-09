<?php
$CI =& ci_get_instance();
$ri = $CI->remote_integration();

/* check if forced header or footer exist */
$force_head = $GLOBALS['NTS_APPPATH'] . '/../theme/head.php';
if( ! file_exists($force_head) )
	$force_head = '';
$force_header = $GLOBALS['NTS_APPPATH'] . '/../theme/header.php';
if( ! file_exists($force_header) )
	$force_header = '';
$force_footer = $GLOBALS['NTS_APPPATH'] . '/../theme/footer.php';
if( ! file_exists($force_footer) )
	$force_footer = '';
?>
<?php
if( ! $ri )
{
	require( dirname(__FILE__) . '/head.php' );
}
if( $force_header )
{
	require( $force_header );
}
?>
<div id="nts">
<?php if( $ri ) : ?>
	<div class="container-fluid">
<?php else : ?>
	<div class="container">
<?php endif; ?>

<?php	require( dirname(__FILE__) . '/menu.php' ); ?>
<?php	require( dirname(__FILE__) . '/main.php' ); ?>

<!-- Modal -->
<div id="hc-modal" class="modal hide modal-body" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-header">
<a href="#" class="close btn btn-danger" data-dismiss="modal" aria-hidden="true"><?php echo lang('common_close'); ?></a>
<div class="clearfix"></div>
</div>
<div class="modal-body hc-container"></div>
</div>

<?php	require( dirname(__FILE__) . '/footer.php' ); ?>

</div><!-- /container -->
</div><!-- /nts -->

<?php
if( $force_footer )
{
	require( $force_footer );
}
?>

<?php if( ! $ri ) : ?>
</body>
</html>
<?php endif; ?>