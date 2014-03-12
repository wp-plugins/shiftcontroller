<?php
$CI =& ci_get_instance();
$ri = $CI->remote_integration();
?>
<?php if( ! $ri ) : ?>
<?php	require( dirname(__FILE__) . '/head.php' ); ?>
<?php endif; ?>

<div id="nts">
<?php if( $ri ) : ?>
	<div class="container-fluid">
<?php else : ?>
	<div class="container">
<?php endif; ?>

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
</div><!-- /hc -->

<?php if( ! $ri ) : ?>
</body>
</html>
<?php endif; ?>